<?php
/**
 * Stock Entries List View
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Stock Entries - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$coilId = isset($_GET['coil_id']) ? (int)$_GET['coil_id'] : null;

$stockEntryModel = new StockEntry();

if ($coilId) {
    $entries = $stockEntryModel->getByCoil($coilId, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalEntries = count($stockEntryModel->getByCoil($coilId, 10000, 0));
} else {
    $entries = $stockEntryModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalEntries = $stockEntryModel->count();
}

$paginationData = getPaginationData($totalEntries, $currentPage);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock Entries</h1>
                <p class="text-muted">Manage stock meter entries</p>
            </div>
            <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=stock_entries_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Stock Entry
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list-check"></i> Stock Entries List
        </div>
        <div class="card-body p-0">
            <?php if (empty($entries)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No stock entries found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Coil Code</th>
                            <th>Coil Name</th>
                            <th>Meters</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td>#<?php echo $entry['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($entry['coil_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($entry['coil_name']); ?></td>
                            <td><?php echo number_format($entry['meters'], 2); ?>m</td>
                            <td>
                                <span class="badge <?php echo $entry['meters_remaining'] > 0 ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo number_format($entry['meters_remaining'], 2); ?>m
                                </span>
                            </td>
                            <td>
                                <?php 
                                $status = $entry['status'] ?? 'available';
                                $statusBadge = $status === 'factory_use' ? 'bg-warning' : 'bg-info';
                                $statusText = $status === 'factory_use' ? 'Factory Use' : 'Available';
                                ?>
                                <span class="badge <?php echo $statusBadge; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                                <?php if ($status === 'factory_use' && hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW)): ?>
                                <a href="/new-stock-system/index.php?page=stock_ledger&coil_id=<?php echo $entry['coil_id']; ?>" 
                                   class="btn btn-sm btn-outline-primary ms-1" title="View Ledger">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($entry['created_by_name']); ?></td>
                            <td>
                                <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT) && $entry['meters_remaining'] > 0): ?>
                                <form method="POST" action="/new-stock-system/controllers/stock_entries/toggle_status/index.php" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $status; ?>">
                                    <button type="submit" class="btn btn-sm btn-<?php echo $status === 'available' ? 'warning' : 'info'; ?>" 
                                            title="<?php echo $status === 'available' ? 'Move to Factory Use' : 'Move to Available'; ?>">
                                        <i class="bi bi-arrow-<?php echo $status === 'available' ? 'right' : 'left'; ?>-circle"></i>
                                        <?php echo $status === 'available' ? 'To Factory' : 'To Available'; ?>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <?php
                                $id = $entry['id'];
                                $module = 'stock_entries';
                                $canView = hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW);
                                $canEdit = hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT);
                                $canDelete = hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_DELETE);
                                include __DIR__ . '/../../../layout/quick_action_buttons.php';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($entries)): ?>
        <div class="card-footer">
            <?php $queryParams = $_GET; include __DIR__ . '/../../../layout/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
