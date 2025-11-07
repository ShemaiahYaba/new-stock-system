<?php
/**
 * Create Warehouse Form
 */

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Create Warehouse - ' . APP_NAME;

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create New Warehouse</h1>
                <p class="text-muted">Add a new warehouse location</p>
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
                    <i class="bi bi-building"></i> Warehouse Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/warehouses/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="e.g., Main Warehouse" required>
                            <div class="invalid-feedback">Please provide warehouse name.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <textarea class="form-control" id="location" name="location" rows="3" 
                                      placeholder="Enter warehouse address"></textarea>
                            <small class="form-text text-muted">Optional</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="contact" class="form-label">Contact Information</label>
                            <input type="text" class="form-control" id="contact" name="contact" 
                                   placeholder="e.g., Phone number or email">
                            <small class="form-text text-muted">Optional</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Active Warehouse
                                </label>
                                <small class="form-text text-muted d-block">Only active warehouses can be used for production and delivery.</small>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> Warehouses are used to manage production and delivery locations.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=warehouses" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Warehouse
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Required Fields
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> Warehouse Name
                        </li>
                    </ul>
                    <hr>
                    <h6>Optional Fields</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-circle text-muted"></i> Location
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-circle text-muted"></i> Contact Information
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-circle text-muted"></i> Active Status
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
