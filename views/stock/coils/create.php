<?php
/**
 * Create Coil Form - Updated with Meters and Gauge fields
 */

require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Create Coil - ' . APP_NAME;

// Get active colors from database
$colorModel = new Color();
$colors = $colorModel->getActive();

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create New Coil</h1>
                <p class="text-muted">Add a new coil to inventory</p>
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
                    <i class="bi bi-box-seam"></i> Coil Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/coils/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Coil Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       placeholder="e.g., COL-001" required>
                                <div class="invalid-feedback">Please provide a coil code.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Coil Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g., Premium Steel Coil" required>
                                <div class="invalid-feedback">Please provide a name.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color_id" class="form-label">Color <span class="text-danger">*</span></label>
                                <select class="form-select" id="color_id" name="color_id" required>
                                    <option value="">Select color</option>
                                    <?php foreach ($colors as $color): ?>
                                    <option value="<?php echo $color['id']; ?>">
                                        <?php echo htmlspecialchars($color['name']); ?>
                                        <?php if (!empty($color['hex_code'])): ?>
                                            <span style="background-color: <?php echo htmlspecialchars($color['hex_code']); ?>; display: inline-block; width: 15px; height: 15px; border-radius: 3px; margin-left: 5px;"></span>
                                        <?php endif; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a color.</div>
                                <?php if (empty($colors)): ?>
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> No colors available. 
                                    <a href="/new-stock-system/index.php?page=colors_create">Add colors first</a>
                                </small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select category</option>
                                    <?php foreach (STOCK_CATEGORIES as $catKey => $catName): ?>
                                    <option value="<?php echo $catKey; ?>"><?php echo $catName; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="net_weight" class="form-label">Net Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="net_weight" name="net_weight" 
                                       step="0.01" min="0" placeholder="e.g., 1500.50" required>
                                <div class="invalid-feedback">Please provide net weight.</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="meters" class="form-label">Meters </label>
                                <input type="number" class="form-control" id="meters" name="meters" 
                                       step="0.01" min="0" placeholder="e.g., 500.00">
                                <small class="text-muted">Approximate meters per coil</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="gauge" class="form-label">Gauge </label>
                                <input type="text" class="form-control" id="gauge" name="gauge" 
                                       placeholder="e.g., 0.45mm">
                                <small class="text-muted">Material thickness</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> The coil will be created with a default status of "Available". 
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=coils" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Coil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Stock Categories
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php foreach (STOCK_CATEGORIES as $catName): ?>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> <?php echo $catName; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-rulers"></i> Common Gauges
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Example gauge values:</p>
                    <ul class="list-unstyled small">
                        <li>• 0.40mm</li>
                        <li>• 0.45mm</li>
                        <li>• 0.50mm</li>
                        <li>• 0.55mm</li>
                        <li>• 0.60mm</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>