<?php
/**
 * User Permissions Management
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/user.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Manage Permissions - ' . APP_NAME;

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

$currentPermissions = $userModel->getPermissions($userId);
if (empty($currentPermissions)) {
    $currentPermissions = DEFAULT_PERMISSIONS[$user['role']] ?? [];
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Manage Permissions</h1>
                <p class="text-muted">Configure permissions for <?php echo htmlspecialchars($user['name']); ?></p>
            </div>
            <a href="/new-stock-system/index.php?page=users" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-person-circle text-primary" style="font-size: 60px;"></i>
                    <h5 class="mt-2"><?php echo htmlspecialchars($user['name']); ?></h5>
                    <span class="badge bg-primary"><?php echo USER_ROLES[$user['role']] ?? $user['role']; ?></span>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-shield-lock"></i> Permission Settings
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/users/permissions/index.php" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Select which modules and actions this user can access.
                        </div>
                        
                        <?php foreach (PERMISSION_MODULES as $moduleKey => $moduleName): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-red">
                                <div class="form-check">
                                    <input class="form-check-input module-checkbox" 
                                           type="checkbox" 
                                           id="module_<?php echo $moduleKey; ?>"
                                           data-module="<?php echo $moduleKey; ?>"
                                           <?php echo isset($currentPermissions[$moduleKey]) ? 'checked' : ''; ?>>
                                    <label class="form-check-label fw-bold" for="module_<?php echo $moduleKey; ?>">
                                        <?php echo $moduleName; ?>
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach (PERMISSION_ACTIONS as $actionKey => $actionName): ?>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input action-checkbox" 
                                                   type="checkbox" 
                                                   name="permissions[<?php echo $moduleKey; ?>][]" 
                                                   value="<?php echo $actionKey; ?>"
                                                   data-module="<?php echo $moduleKey; ?>"
                                                   id="<?php echo $moduleKey; ?>_<?php echo $actionKey; ?>"
                                                   <?php echo isset($currentPermissions[$moduleKey]) && in_array($actionKey, $currentPermissions[$moduleKey]) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="<?php echo $moduleKey; ?>_<?php echo $actionKey; ?>">
                                                <?php echo $actionName; ?>
                                            </label>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="/new-stock-system/index.php?page=users" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Permissions
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-check/uncheck actions when module is toggled
document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
    moduleCheckbox.addEventListener('change', function() {
        const module = this.dataset.module;
        const actionCheckboxes = document.querySelectorAll(`.action-checkbox[data-module="${module}"]`);
        actionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
});

// Update module checkbox when actions change
document.querySelectorAll('.action-checkbox').forEach(actionCheckbox => {
    actionCheckbox.addEventListener('change', function() {
        const module = this.dataset.module;
        const moduleCheckbox = document.getElementById(`module_${module}`);
        const actionCheckboxes = document.querySelectorAll(`.action-checkbox[data-module="${module}"]`);
        const anyChecked = Array.from(actionCheckboxes).some(cb => cb.checked);
        moduleCheckbox.checked = anyChecked;
    });
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
