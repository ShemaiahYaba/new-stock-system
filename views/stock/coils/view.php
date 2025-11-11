<?php
/**
 * Coil View Details
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'View Coil - ' . APP_NAME;

$coilId = (int)($_GET['id'] ?? 0);

if ($coilId <= 0) {
    setFlashMessage('error', 'Invalid coil ID.');
    header('Location: /new-stock-system/index.php?page=coils');
    exit();
}

$coilModel = new Coil();
$colorModel = new Color();
$stockEntryModel = new StockEntry();

$coil = $coilModel->findById($coilId);

// Get color name
$colorName = 'Unknown';
if (!empty($coil['color_id'])) {
    $color = $colorModel->findById($coil['color_id']);
    $colorName = $color ? $color['name'] : 'Unknown';
}

if (!$coil) {
    setFlashMessage('error', 'Coil not found.');
    header('Location: /new-stock-system/index.php?page=coils');
    exit();
}

$stockEntries = $stockEntryModel->getByCoil($coilId, 10, 0);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Coil Details</h1>
                <p class="text-muted">View coil information</p>
            </div>
            <a href="/new-stock-system/index.php?page=coils" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Coils
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-box-seam text-primary" style="font-size: 80px;"></i>
                    <h4 class="mt-3"><?php echo htmlspecialchars($coil['code']); ?></h4>
                    <p class="text-muted"><?php echo htmlspecialchars($coil['name']); ?></p>
                    <span class="badge <?php echo getStatusBadgeClass($coil['status']); ?>">
                        <?php echo STOCK_STATUSES[$coil['status']] ?? $coil['status']; ?>
                    </span>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Coil Information
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Category:</th>
                            <td><span class="badge bg-info"><?php echo STOCK_CATEGORIES[$coil['category']]; ?></span></td>
                        </tr>
                        <tr>
                            <th>Color:</th>
                            <td><?php echo COIL_COLORS[$coil['color']] ?? $coil['color']; ?></td>
                        </tr>
                        <tr>
                            <th>Net Weight:</th>
                            <td><?php echo number_format($coil['net_weight'], 2); ?> kg</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDate($coil['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?php echo $coil['updated_at'] ? formatDate($coil['updated_at']) : 'Never'; ?></td>
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
                    <a href="/new-stock-system/index.php?page=coils_edit&id=<?php echo $coil['id']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Coil
                    </a>
                    <a href="/new-stock-system/index.php?page=stock_entries_create&coil_id=<?php echo $coil['id']; ?>" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Stock Entry
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-list-check"></i> Stock Entries
                </div>
                <div class="card-body">
                    <?php if (empty($stockEntries)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No stock entries for this coil yet.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Meters</th>
                                    <th>Remaining</th>
                                    <th>Created By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stockEntries as $entry): ?>
                                <tr>
                                    <td>#<?php echo $entry['id']; ?></td>
                                    <td><?php echo number_format($entry['meters'], 2); ?>m</td>
                                    <td>
                                        <span class="badge <?php echo $entry['meters_remaining'] > 0 ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo number_format($entry['meters_remaining'], 2); ?>m
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($entry['created_by_name']); ?></td>
                                    <td><?php echo formatDate($entry['created_at']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW)): ?>
                    <div class="mt-3">
                        <a href="/new-stock-system/index.php?page=stock_entries&coil_id=<?php echo $coil['id']; ?>" class="btn btn-sm btn-outline-primary">
                            View All Entries <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
