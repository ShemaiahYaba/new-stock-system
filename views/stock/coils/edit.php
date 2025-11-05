<?php
/**
 * Edit Coil Form
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Edit Coil - ' . APP_NAME;

$coilId = (int)($_GET['id'] ?? 0);

if ($coilId <= 0) {
    setFlashMessage('error', 'Invalid coil ID.');
    header('Location: /new-stock-system/index.php?page=coils');
    exit();
}

$coilModel = new Coil();
$coil = $coilModel->findById($coilId);

if (!$coil) {
    setFlashMessage('error', 'Coil not found.');
    header('Location: /new-stock-system/index.php?page=coils');
    exit();
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Coil</h1>
                <p class="text-muted">Update coil information</p>
            </div>
            <a href="/new-stock-system/index.php?page=coils" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Coils
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Coil Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/coils/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $coil['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Coil Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="<?php echo htmlspecialchars($coil['code']); ?>" required>
                                <div class="invalid-feedback">Please provide a coil code.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Coil Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($coil['name']); ?>" required>
                                <div class="invalid-feedback">Please provide a name.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color" class="form-label">Color <span class="text-danger">*</span></label>
                                <select class="form-select" id="color" name="color" required>
                                    <?php foreach (COIL_COLORS as $colorKey => $colorName): ?>
                                    <option value="<?php echo $colorKey; ?>" <?php echo $coil['color'] === $colorKey ? 'selected' : ''; ?>>
                                        <?php echo $colorName; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a color.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="net_weight" class="form-label">Net Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="net_weight" name="net_weight" 
                                       step="0.01" min="0" value="<?php echo $coil['net_weight']; ?>" required>
                                <div class="invalid-feedback">Please provide net weight.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <?php foreach (STOCK_CATEGORIES as $catKey => $catName): ?>
                                    <option value="<?php echo $catKey; ?>" <?php echo $coil['category'] === $catKey ? 'selected' : ''; ?>>
                                        <?php echo $catName; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <?php foreach (STOCK_STATUSES as $statusKey => $statusName): ?>
                                    <option value="<?php echo $statusKey; ?>" <?php echo $coil['status'] === $statusKey ? 'selected' : ''; ?>>
                                        <?php echo $statusName; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a status.</div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Note:</strong> Changing the status may affect sales and stock entries.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=coils" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Coil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
