<?php
/**
 * Create Customer Form
 */

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Create Customer - ' . APP_NAME;

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create New Customer</h1>
                <p class="text-muted">Add a new customer to the system</p>
            </div>
            <a href="/new-stock-system/index.php?page=customers" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Customers
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-plus"></i> Customer Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/customers/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   placeholder="e.g., John Doe" required>
                            <div class="invalid-feedback">Please provide customer name.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       placeholder="e.g., john@example.com">
                                <small class="form-text text-muted">Optional</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       placeholder="e.g., 08012345678" required>
                                <div class="invalid-feedback">Please provide phone number.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company" name="company" 
                                   placeholder="e.g., ABC Industries">
                            <small class="form-text text-muted">Optional</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3" 
                                      placeholder="Enter customer address"></textarea>
                            <small class="form-text text-muted">Optional</small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> Customer information can be used when creating sales transactions.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=customers" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Customer
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
                            <i class="bi bi-check-circle text-success"></i> Full Name
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> Phone Number
                        </li>
                    </ul>
                    <hr>
                    <h6>Optional Fields</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-circle text-muted"></i> Email Address
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-circle text-muted"></i> Company Name
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-circle text-muted"></i> Address
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
