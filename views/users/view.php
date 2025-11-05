<?php
/**
 * User View Details
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/user.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'View User - ' . APP_NAME;

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

$permissions = $userModel->getPermissions($userId);
if (empty($permissions)) {
    $permissions = DEFAULT_PERMISSIONS[$user['role']] ?? [];
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">User Details</h1>
                <p class="text-muted">View user information</p>
            </div>
            <a href="/new-stock-system/index.php?page=users" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle text-primary" style="font-size: 80px;"></i>
                    <h4 class="mt-3"><?php echo htmlspecialchars($user['name']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                    <span class="badge bg-primary">
                        <?php echo USER_ROLES[$user['role']] ?? $user['role']; ?>
                    </span>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Account Info
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>User ID:</th>
                            <td>#<?php echo $user['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?php echo $user['updated_at'] ? formatDate($user['updated_at']) : 'Never'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php if (hasPermission(MODULE_USER_MANAGEMENT, ACTION_EDIT)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="/new-stock-system/index.php?page=users_edit&id=<?php echo $user['id']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit User
                    </a>
                    <a href="/new-stock-system/index.php?page=users_permissions&id=<?php echo $user['id']; ?>" class="btn btn-secondary">
                        <i class="bi bi-shield-lock"></i> Manage Permissions
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-shield-check"></i> User Permissions
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($permissions as $module => $actions): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <strong><?php echo PERMISSION_MODULES[$module] ?? $module; ?></strong>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($actions as $action): ?>
                                    <span class="badge bg-success me-1 mb-1">
                                        <i class="bi bi-check-circle"></i> <?php echo ucfirst($action); ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
