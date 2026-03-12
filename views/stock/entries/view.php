<?php
/**
 * Stock Entry View Details
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'View Stock Entry - ' . APP_NAME;

$entryId = (int)($_GET['id'] ?? 0);

if ($entryId <= 0) {
    setFlashMessage('error', 'Invalid stock entry ID.');
    header('Location: /new-stock-system/index.php?page=stock_entries');
    exit();
}

$stockEntryModel = new StockEntry();
$entry = $stockEntryModel->findById($entryId);

if (!$entry) {
    setFlashMessage('error', 'Stock entry not found.');
    header('Location: /new-stock-system/index.php?page=stock_entries');
    exit();
}

$isKzincEntry = ($entry['unit_type'] ?? 'meters') !== 'meters';

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock Entry Details</h1>
                <p class="text-muted">View stock entry information</p>
            </div>
            <a href="/new-stock-system/index.php?page=stock_entries" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Stock Entries
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Entry Information
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th>Entry ID:</th>
                            <td>#<?php echo $entry['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Coil Code:</th>
                            <td><strong><?php echo htmlspecialchars($entry['coil_code']); ?></strong></td>
                        </tr>
                        <tr>
                            <th>Coil Name:</th>
                            <td><?php echo htmlspecialchars($entry['coil_name']); ?></td>
                        </tr>
                        <tr>
                            <th>Coil Status:</th>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($entry['coil_status']); ?>">
                                    <?php echo STOCK_STATUSES[$entry['coil_status']] ?? $entry['coil_status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php if ($isKzincEntry): ?>
                        <tr>
                            <th>Unit Type:</th>
                            <td><?php echo htmlspecialchars(ucfirst($entry['unit_type'])); ?></td>
                        </tr>
                        <tr>
                            <th>Quantity:</th>
                            <td><strong><?php echo number_format($entry['quantity'] ?? 0, 2); ?> <?php echo htmlspecialchars($entry['unit_type']); ?></strong></td>
                        </tr>
                        <tr>
                            <th>Total Pieces:</th>
                            <td><?php echo number_format($entry['pieces_total'] ?? 0, 0); ?> pcs</td>
                        </tr>
                        <tr>
                            <th>Pieces Remaining:</th>
                            <td>
                                <span class="badge <?php echo ($entry['pieces_remaining'] ?? 0) > 0 ? 'bg-success' : 'bg-secondary'; ?>" style="font-size: 1rem;">
                                    <?php echo number_format($entry['pieces_remaining'] ?? 0, 0); ?> pcs
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Pieces Used:</th>
                            <td><?php echo (int)($entry['pieces_total'] ?? 0) - (int)($entry['pieces_remaining'] ?? 0); ?> pcs</td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <th>Total Meters:</th>
                            <td><strong><?php echo number_format($entry['meters'], 2); ?>m</strong></td>
                        </tr>
                        <tr>
                            <th>Meters Remaining:</th>
                            <td>
                                <span class="badge <?php echo $entry['meters_remaining'] > 0 ? 'bg-success' : 'bg-secondary'; ?>" style="font-size: 1rem;">
                                    <?php echo number_format($entry['meters_remaining'], 2); ?>m
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Meters Used:</th>
                            <td><?php echo number_format($entry['meters'] - $entry['meters_remaining'], 2); ?>m</td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDate($entry['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?php echo $entry['updated_at'] ? formatDate($entry['updated_at']) : 'Never'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-graph-up"></i> Usage Statistics
                </div>
                <div class="card-body">
                    <?php
                    if ($isKzincEntry) {
                        $total     = (int)($entry['pieces_total'] ?? 0);
                        $remaining = (int)($entry['pieces_remaining'] ?? 0);
                        $used      = $total - $remaining;
                        $usedPercentage      = $total > 0 ? ($used / $total) * 100 : 0;
                        $remainingPercentage = 100 - $usedPercentage;
                        $totalLabel     = number_format($entry['quantity'] ?? 0, 2) . ' ' . $entry['unit_type'] . ' (' . $total . ' pcs)';
                        $remainingLabel = $remaining . ' pcs';
                    } else {
                        $usedPercentage      = $entry['meters'] > 0 ? (($entry['meters'] - $entry['meters_remaining']) / $entry['meters']) * 100 : 0;
                        $remainingPercentage = 100 - $usedPercentage;
                        $totalLabel     = number_format($entry['meters'], 2) . 'm';
                        $remainingLabel = number_format($entry['meters_remaining'], 2) . 'm';
                    }
                    ?>

                    <div class="mb-3">
                        <label class="form-label">Usage Progress</label>
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-danger" role="progressbar"
                                 style="width: <?php echo $usedPercentage; ?>%">
                                <?php echo number_format($usedPercentage, 1); ?>% Used
                            </div>
                            <div class="progress-bar bg-success" role="progressbar"
                                 style="width: <?php echo $remainingPercentage; ?>%">
                                <?php echo number_format($remainingPercentage, 1); ?>% Remaining
                            </div>
                        </div>
                    </div>

                    <div class="row text-center mt-4">
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted"><?php echo $isKzincEntry ? 'Total Stock' : 'Total Meters'; ?></h6>
                                    <h3 class="text-primary"><?php echo $totalLabel; ?></h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-muted">Remaining</h6>
                                    <h3 class="text-success"><?php echo $remainingLabel; ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-link"></i> Related Links
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="/new-stock-system/index.php?page=coils_view&id=<?php echo $entry['coil_id']; ?>" class="btn btn-outline-primary">
                        <i class="bi bi-box-seam"></i> View Coil Details
                    </a>
                    <a href="/new-stock-system/index.php?page=stock_entries&coil_id=<?php echo $entry['coil_id']; ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-list"></i> View All Entries for This Coil
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
