<?php
/**
 * User Edit Form
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/user.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Edit User - ' . APP_NAME;

$userId = (int)($_GET['id'] ?? 0);

if ($userId <= 0) {
    setFlashMessage('error', 'Invalid user ID.');
    header('Location: /new-stock-system/index.php?page=users');
    exit();
}

$userModel = new User();
$user = $userModel->findById($userId);

if (!$user) {
    setFlashMessage('error', 'User not found.');
    header('Location: /new-stock-system/index.php?page=users');
    exit();
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit User</h1>
                <p class="text-muted">Update user information</p>
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
                    <i class="bi bi-pencil"></i> User Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/users/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="name" 
                                   name="name" 
                                   value="<?php echo htmlspecialchars($user['name']); ?>"
                                   required>
                            <div class="invalid-feedback">Please provide a name.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>"
                                   required>
                            <div class="invalid-feedback">Please provide a valid email.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <?php foreach (USER_ROLES as $roleKey => $roleName): ?>
                                <option value="<?php echo $roleKey; ?>" <?php echo $user['role'] === $roleKey ? 'selected' : ''; ?>>
                                    <?php echo $roleName; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a role.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" 
                                   class="form-control" 
                                   id="password" 
                                   name="password" 
                                   placeholder="Leave blank to keep current password"
                                   minlength="6">
                            <small class="form-text text-muted">Only fill this if you want to change the password</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=users" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
