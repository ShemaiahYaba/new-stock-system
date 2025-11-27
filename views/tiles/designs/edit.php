<?php
/**
 * ============================================
 * FILE: views/tiles/designs/edit.php
 * Edit existing design
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/design.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Edit Design - ' . APP_NAME;

$designId = (int)($_GET['id'] ?? 0);

if ($designId <= 0) {
    setFlashMessage('error', 'Invalid design ID.');
    header('Location: /new-stock-system/index.php?page=designs');
    exit();
}

$designModel = new Design();
$design = $designModel->findById($designId);

if (!$design) {
    setFlashMessage('error', 'Design not found.');
    header('Location: /new-stock-system/index.php?page=designs');
    exit();
}

// Check if design is used in products
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT COUNT(*) as count FROM tile_products WHERE design_id = ? AND deleted_at IS NULL");
$stmt->execute([$designId]);
$productCount = $stmt->fetch()['count'] ?? 0;

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Design</h1>
                <p class="text-muted">Update design information</p>
            </div>
            <a href="/new-stock-system/index.php?page=designs" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Designs
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Design Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/tiles/designs/update/index.php" 
                          method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="id" value="<?= $design['id'] ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Design Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="<?= htmlspecialchars($design['code']) ?>" required
                                       pattern="[A-Z0-9_]+" style="text-transform: uppercase;"
                                       <?= $productCount > 0 ? 'readonly' : '' ?>>
                                <div class="invalid-feedback">Code is required.</div>
                                <?php if ($productCount > 0): ?>
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> Code cannot be changed (used in <?= $productCount ?> products)
                                </small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= htmlspecialchars($design['name']) ?>" required>
                                <div class="invalid-feedback">Name is required.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3"><?= htmlspecialchars($design['description'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" <?= $design['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Active Design
                                </label>
                            </div>
                        </div>
                        
                        <?php if ($productCount > 0): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Warning:</strong> This design is used by <?= $productCount ?> product(s). 
                            Changes will affect how those products are displayed.
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=designs" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Design
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Design Details
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Design ID:</th>
                            <td>#<?= $design['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?= formatDate($design['created_at']) ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?= $design['updated_at'] ? formatDate($design['updated_at']) : 'Never' ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if ($design['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-box-seam"></i> Usage Statistics
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <i class="bi bi-info-circle text-primary"></i>
                        This design is used by <strong><?= $productCount ?></strong> product(s).
                    </p>
                    <?php if ($productCount > 0): ?>
                    <a href="/new-stock-system/index.php?page=tile_products&design_id=<?= $design['id'] ?>" 
                       class="btn btn-sm btn-outline-primary mt-2 w-100">
                        View Products
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>