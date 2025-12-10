<?php
/**
 * Warehouses List View
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/warehouse.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Warehouses - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';

$warehouseModel = new Warehouse();

if (!empty($searchQuery)) {
    $warehouses = $warehouseModel->search($searchQuery, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalWarehouses = $warehouseModel->countSearch($searchQuery);
} else {
    $warehouses = $warehouseModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalWarehouses = $warehouseModel->count();
}

$paginationData = getPaginationData($totalWarehouses, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Warehouse Management</h1>
                <p class="text-muted">Manage warehouse locations</p>
            </div>
            <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=warehouses_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Warehouse
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-building"></i> Warehouses List
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="warehouses">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search warehouses..." value="<?php echo htmlspecialchars(
                                   $searchQuery,
                               ); ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="/new-stock-system/index.php?page=warehouses" class="btn btn-sm btn-secondary ms-2">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($warehouses)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No warehouses found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Location</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($warehouses as $warehouse): ?>
                        <tr>
                            <td><?php echo $warehouse['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($warehouse['name']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($warehouse['location'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($warehouse['contact'] ?? '-'); ?></td>
                            <td>
                                <?php if ($warehouse['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatDate($warehouse['created_at']); ?></td>
                            <td>
                                <?php
                                $id = $warehouse['id'];
                                $module = 'warehouses';
                                $canView = hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW);
                                $canEdit = hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT);
                                $canDelete = hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_DELETE);
                                include __DIR__ . '/../../layout/quick_action_buttons.php';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($warehouses)): ?>
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
