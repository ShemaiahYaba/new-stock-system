<?php
/**
 * Warehouse View Details
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/warehouse.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'View Warehouse - ' . APP_NAME;

$warehouseId = (int) ($_GET['id'] ?? 0);

if ($warehouseId <= 0) {
    setFlashMessage('error', 'Invalid warehouse ID.');
    header('Location: /new-stock-system/index.php?page=warehouses');
    exit();
}

$warehouseModel = new Warehouse();
$warehouse = $warehouseModel->findById($warehouseId);

if (!$warehouse) {
    setFlashMessage('error', 'Warehouse not found.');
    header('Location: /new-stock-system/index.php?page=warehouses');
    exit();
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Warehouse Details</h1>
                <p class="text-muted">View warehouse information</p>
            </div>
            <a href="/new-stock-system/index.php?page=warehouses" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Warehouses
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-building text-primary" style="font-size: 80px;"></i>
                    <h4 class="mt-3"><?php echo htmlspecialchars($warehouse['name']); ?></h4>
                    <?php if ($warehouse['is_active']): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Warehouse Information
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th><i class="bi bi-geo-alt"></i> Location:</th>
                            <td><?php echo $warehouse['location']
                                ? nl2br(htmlspecialchars($warehouse['location']))
                                : '-'; ?></td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-telephone"></i> Contact:</th>
                            <td><?php echo htmlspecialchars($warehouse['contact'] ?? '-'); ?></td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-toggle-on"></i> Status:</th>
                            <td>
                                <?php if ($warehouse['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Record Info
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Warehouse ID:</th>
                            <td>#<?php echo $warehouse['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDate($warehouse['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?php echo $warehouse['updated_at']
                                ? formatDate($warehouse['updated_at'])
                                : 'Never'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="/new-stock-system/index.php?page=warehouses_edit&id=<?php echo $warehouse[
                        'id'
                    ]; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Warehouse
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-box-seam"></i> Production History
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Production history for this warehouse will be displayed here once production records are created.
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-truck"></i> Delivery History
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Delivery history for this warehouse will be displayed here once deliveries are recorded.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
