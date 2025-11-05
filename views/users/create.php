<?php
/**
 * User Create Form View
 */

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Create User - ' . APP_NAME;

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create New User</h1>
                <p class="text-muted">Add a new user to the system</p>
            </div>
            <a href="/new-stock-system/index.php?page=users" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-person-plus"></i> User Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/users/create/index.php" 
                          method="POST" 
                          class="needs-validation" 
                          novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   placeholder="Enter full name"
                                   required>
                            <div class="invalid-feedback">
                                Please provide a name.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   placeholder="Enter email address"
                                   required>
                            <div class="invalid-feedback">
                                Please provide a valid email address.
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Enter password"
                                   minlength="6"
                                   required>
                            <div class="invalid-feedback">
                                Password must be at least 6 characters long.
                            </div>
                            <small class="form-text text-muted">Minimum 6 characters</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="">Select a role</option>
                                <?php foreach (USER_ROLES as $roleKey => $roleName): ?>
                                <option value="<?php echo $roleKey; ?>"><?php echo $roleName; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">
                                Please select a role.
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> Default permissions for the selected role will be assigned. 
                            You can customize permissions after creating the user.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=users" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-shield-check"></i> Role Permissions
                </div>
                <div class="card-body">
                    <h6>Default Role Permissions:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>Super Admin:</strong>
                            <small class="text-muted d-block">Full system access</small>
                        </li>
                        <li class="mb-2">
                            <strong>HR Director:</strong>
                            <small class="text-muted d-block">User management</small>
                        </li>
                        <li class="mb-2">
                            <strong>Accountant:</strong>
                            <small class="text-muted d-block">View stock & sales</small>
                        </li>
                        <li class="mb-2">
                            <strong>Sales Manager:</strong>
                            <small class="text-muted d-block">Manage customers & sales</small>
                        </li>
                        <li class="mb-2">
                            <strong>Stock Manager:</strong>
                            <small class="text-muted d-block">Manage stock</small>
                        </li>
                        <li class="mb-2">
                            <strong>Viewer:</strong>
                            <small class="text-muted d-block">Read-only access</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
