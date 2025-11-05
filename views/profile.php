<?php
/**
 * User Profile View
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../models/user.php';
require_once __DIR__ . '/../utils/helpers.php';

$pageTitle = 'My Profile - ' . APP_NAME;

$currentUser = getCurrentUser();
$userModel = new User();
$userDetails = $userModel->findById($currentUser['id']);

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <h1 class="page-title">My Profile</h1>
        <p class="text-muted">View and manage your account information</p>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="bi bi-person-circle text-primary" style="font-size: 100px;"></i>
                    </div>
                    <h4><?php echo htmlspecialchars($userDetails['name']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($userDetails['email']); ?></p>
                    <span class="badge bg-primary">
                        <?php echo USER_ROLES[$userDetails['role']] ?? $userDetails['role']; ?>
                    </span>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Account Information
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Member Since:</th>
                            <td><?php echo formatDate($userDetails['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td><?php echo $userDetails['updated_at'] ? formatDate($userDetails['updated_at']) : 'Never'; ?></td>
                        </tr>
                        <tr>
                            <th>User ID:</th>
                            <td>#<?php echo $userDetails['id']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-shield-check"></i> My Permissions
                </div>
                <div class="card-body">
                    <?php
                    $permissions = $currentUser['permissions'];
                    
                    if (empty($permissions)) {
                        echo '<div class="alert alert-info">No specific permissions assigned. Using default role permissions.</div>';
                        $permissions = DEFAULT_PERMISSIONS[$userDetails['role']] ?? [];
                    }
                    ?>
                    
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
                                        <i class="bi bi-check-circle"></i> 
                                        <?php echo ucfirst($action); ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Account Actions
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Note:</strong> To update your profile information or change your password, 
                        please contact your system administrator.
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="/new-stock-system/index.php?page=dashboard" class="btn btn-primary">
                            <i class="bi bi-speedometer2"></i> Go to Dashboard
                        </a>
                        <a href="/new-stock-system/logout.php" class="btn btn-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
