<?php
/**
 * Users List View
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/user.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'User Management - ' . APP_NAME;

// Get pagination parameters
$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';

// Initialize user model
$userModel = new User();

// Get users
if (!empty($searchQuery)) {
    $users = $userModel->search($searchQuery, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalUsers = $userModel->countSearch($searchQuery);
} else {
    $users = $userModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalUsers = $userModel->count();
}

// Get pagination data
$paginationData = getPaginationData($totalUsers, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">User Management</h1>
                <p class="text-muted">Manage system users and their permissions</p>
            </div>
            <?php if (hasPermission(MODULE_USER_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=users_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New User
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-people"></i> Users List
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="users">
                        <input type="text" 
                               name="search" 
                               class="form-control form-control-sm me-2" 
                               placeholder="Search users..."
                               value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="/new-stock-system/index.php?page=users" class="btn btn-sm btn-secondary ms-2">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($users)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No users found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-primary">
                                    <?php echo USER_ROLES[$user['role']] ?? $user['role']; ?>
                                </span>
                            </td>
                            <td><?php echo formatDate($user['created_at']); ?></td>
                            <td>
                                <?php
                                $id = $user['id'];
                                $module = 'users';
                                $canView = hasPermission(MODULE_USER_MANAGEMENT, ACTION_VIEW);
                                $canEdit = hasPermission(MODULE_USER_MANAGEMENT, ACTION_EDIT);
                                $canDelete = hasPermission(MODULE_USER_MANAGEMENT, ACTION_DELETE) && $user['id'] != getCurrentUserId();
                                
                                include __DIR__ . '/../../layout/quick_action_buttons.php';
                                ?>
                                
                                <?php if (hasPermission(MODULE_USER_MANAGEMENT, ACTION_EDIT)): ?>
                                <a href="/new-stock-system/index.php?page=users_permissions&id=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-secondary"
                                   title="Manage Permissions">
                                    <i class="bi bi-shield-lock"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($users)): ?>
        <div class="card-footer">
            <?php
            $queryParams = $_GET;
            include __DIR__ . '/../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
