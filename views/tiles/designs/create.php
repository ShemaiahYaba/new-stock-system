<?php
/**
 * ============================================
 * FILE: views/tiles/designs/create.php
 * Create new design form
 * ============================================
 */
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Create Design - ' . APP_NAME;

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create New Design</h1>
                <p class="text-muted">Add a new tile design</p>
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
                    <i class="bi bi-palette"></i> Design Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/tiles/designs/create/index.php" 
                          method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Design Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       placeholder="e.g., MILANO, SHINGLE" required
                                       pattern="[A-Z0-9_]+" style="text-transform: uppercase;">
                                <div class="invalid-feedback">Code is required (alphanumeric + underscore).</div>
                                <small class="form-text text-muted">3-50 characters, uppercase letters, numbers, underscores</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g., Milano Tiles" required>
                                <div class="invalid-feedback">Name is required.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" placeholder="Optional description of this design"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" 
                                       name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Active Design
                                </label>
                                <small class="form-text text-muted d-block">Only active designs can be used in products.</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> Design codes must be unique and cannot be changed after products are created.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=designs" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Design
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-lightbulb"></i> Tips
                </div>
                <div class="card-body">
                    <h6>Design Code Guidelines:</h6>
                    <ul class="small">
                        <li>Use short, memorable codes (e.g., MILANO, SHINGLE)</li>
                        <li>Uppercase letters only</li>
                        <li>Underscores allowed for separation</li>
                        <li>No spaces or special characters</li>
                    </ul>
                    
                    <h6 class="mt-3">Examples:</h6>
                    <ul class="small">
                        <li><code>MILANO</code> - Milano design</li>
                        <li><code>SHINGLE</code> - Shingle tiles</li>
                        <li><code>CORONA</code> - Corona design</li>
                        <li><code>STEP_TILE</code> - Step tile design</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-uppercase code input
document.getElementById('code').addEventListener('input', function(e) {
    e.target.value = e.target.value.toUpperCase();
});
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
