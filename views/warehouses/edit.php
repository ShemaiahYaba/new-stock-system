<?php
/**
 * Edit Warehouse Form
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/warehouse.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Edit Warehouse - ' . APP_NAME;

$warehouseId = (int) ($_GET['id'] ?? 0);

if ($warehouseId <= 0) {
    setFlashMessage('error', 'Invalid warehouse ID.');
    header('Location: /new-stock-system/index.php?page=warehouses');
    exit();
}

$warehouseModel = new Warehouse();
$warehouse = $warehouseModel->findById($warehouseId);

if (!$warehouse) {
    setFlashMessage('error', 'Warehouse not found.');
    header('Location: /new-stock-system/index.php?page=warehouses');
    exit();
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Warehouse</h1>
                <p class="text-muted">Update warehouse information</p>
            </div>
            <a href="/new-stock-system/index.php?page=warehouses" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Warehouses
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Warehouse Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/warehouses/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $warehouse['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars(
                                       $warehouse['name'],
                                   ); ?>" required>
                            <div class="invalid-feedback">Please provide warehouse name.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <textarea class="form-control" id="location" name="location" rows="3"><?php echo htmlspecialchars(
                                $warehouse['location'] ?? '',
                            ); ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Information</label>
                            <input type="text" class="form-control" id="contact" name="contact" 
                                   value="<?php echo htmlspecialchars(
                                       $warehouse['contact'] ?? '',
                                   ); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?php echo $warehouse['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active Warehouse
                                </label>
                                <small class="form-text text-muted d-block">Only active warehouses can be used for production and delivery.</small>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=warehouses" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Warehouse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
