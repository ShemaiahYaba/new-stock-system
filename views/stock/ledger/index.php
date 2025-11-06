<?php
/**
 * Stock Ledger View - BY STOCK ENTRY (Not Coil)
 * Each stock entry has its own independent ledger
 * Replace views/stock/ledger/index.php
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Stock Ledger - ' . APP_NAME;

$stockEntryModel = new StockEntry();
$coilModel = new Coil();
$db = Database::getInstance()->getConnection();

// Get selected stock entry if any
$selectedEntryId = isset($_GET['entry_id']) ? (int)$_GET['entry_id'] : null;

// Get all stock entries that have ledger entries
$sql = "SELECT DISTINCT se.*, c.code as coil_code, c.name as coil_name, c.status as coil_status
        FROM stock_entries se
        LEFT JOIN coils c ON se.coil_id = c.id
        WHERE se.deleted_at IS NULL 
        AND EXISTS (SELECT 1 FROM stock_ledger WHERE stock_entry_id = se.id)
        ORDER BY se.created_at DESC";
$stmt = $db->query($sql);
$stockEntriesWithLedger = $stmt->fetchAll();

// Get ledger data for selected stock entry
$ledgerEntries = [];
$summary = [
    'total_inflow' => 0,
    'total_outflow' => 0,
    'current_balance' => 0
];
$selectedEntry = null;
$selectedCoil = null;

if ($selectedEntryId) {
    $selectedEntry = $stockEntryModel->findById($selectedEntryId);
    
    if ($selectedEntry) {
        $selectedCoil = $coilModel->findById($selectedEntry['coil_id']);
        
        // Get summary data for THIS stock entry only
        $summarySql = "SELECT 
                        COALESCE(SUM(inflow_meters), 0) as total_inflow,
                        COALESCE(SUM(outflow_meters), 0) as total_outflow,
                        COALESCE((SELECT balance_meters FROM stock_ledger 
                         WHERE stock_entry_id = ? 
                         ORDER BY created_at DESC, id DESC 
                         LIMIT 1), 0) as current_balance
                    FROM stock_ledger
                    WHERE stock_entry_id = ?";
        
        $summaryStmt = $db->prepare($summarySql);
        $summaryStmt->execute([$selectedEntryId, $selectedEntryId]);
        $summaryResult = $summaryStmt->fetch();
        
        if ($summaryResult) {
            $summary = [
                'total_inflow' => floatval($summaryResult['total_inflow']),
                'total_outflow' => floatval($summaryResult['total_outflow']),
                'current_balance' => floatval($summaryResult['current_balance'])
            ];
        }
        
        // Get ledger entries for THIS stock entry in ASCENDING order (oldest first)
        $entriesSql = "SELECT sl.*, u.name as created_by_name
                FROM stock_ledger sl
                LEFT JOIN users u ON sl.created_by = u.id
                WHERE sl.stock_entry_id = ?
                ORDER BY sl.created_at ASC, sl.id ASC
                LIMIT 100";
        
        $entriesStmt = $db->prepare($entriesSql);
        $entriesStmt->execute([$selectedEntryId]);
        $ledgerEntries = $entriesStmt->fetchAll();
    }
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock Entry Ledger</h1>
                <p class="text-muted">Individual accounting for each stock entry</p>
            </div>
            <a href="/new-stock-system/index.php?page=stock_entries" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Stock Entries
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-funnel"></i> Select Stock Entry
                </div>
                <div class="card-body">
                    <form method="GET" action="/new-stock-system/index.php">
                        <input type="hidden" name="page" value="stock_ledger">
                        <div class="row">
                            <div class="col-md-10">
                                <select class="form-select" name="entry_id" required>
                                    <option value="">Select a stock entry with ledger transactions</option>
                                    <?php foreach ($stockEntriesWithLedger as $entry): ?>
                                    <?php
                                    // Get balance for this stock entry
                                    $balanceSql = "SELECT COALESCE((SELECT balance_meters FROM stock_ledger 
                                                   WHERE stock_entry_id = ? ORDER BY created_at DESC LIMIT 1), 0) as bal";
                                    $balStmt = $db->prepare($balanceSql);
                                    $balStmt->execute([$entry['id']]);
                                    $balResult = $balStmt->fetch();
                                    $balance = $balResult ? floatval($balResult['bal']) : 0;
                                    
                                    // Status badge
                                    $statusBadge = 'Available';
                                    if ($entry['meters_remaining'] <= 0) {
                                        $statusBadge = 'SOLD OUT';
                                    } elseif (($entry['status'] ?? 'available') === 'factory_use') {
                                        $statusBadge = 'Factory Use';
                                    }
                                    ?>
                                    <option value="<?php echo $entry['id']; ?>" <?php echo $selectedEntryId == $entry['id'] ? 'selected' : ''; ?>>
                                        Entry #<?php echo $entry['id']; ?> - 
                                        <?php echo htmlspecialchars($entry['coil_code']); ?> - 
                                        <?php echo htmlspecialchars($entry['coil_name']); ?> 
                                        (<?php echo number_format($entry['meters'], 2); ?>m) 
                                        [<?php echo $statusBadge; ?>]
                                        - Balance: <?php echo number_format($balance, 2); ?>m
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> View Ledger
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($selectedEntry && $selectedCoil): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-receipt"></i> Stock Entry #<?php echo $selectedEntry['id']; ?> - 
                    <?php echo htmlspecialchars($selectedCoil['code']); ?> - 
                    <?php echo htmlspecialchars($selectedCoil['name']); ?>
                    <?php if ($selectedEntry['meters_remaining'] <= 0): ?>
                    <span class="badge bg-danger ms-2">SOLD OUT</span>
                    <?php elseif (($selectedEntry['status'] ?? 'available') === 'factory_use'): ?>
                    <span class="badge bg-warning ms-2">FACTORY USE</span>
                    <?php else: ?>
                    <span class="badge bg-success ms-2">AVAILABLE</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>Entry Details:</strong><br>
                                Total Meters: <?php echo number_format($selectedEntry['meters'], 2); ?>m | 
                                Remaining: <?php echo number_format($selectedEntry['meters_remaining'], 2); ?>m | 
                                Created: <?php echo formatDate($selectedEntry['created_at']); ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-arrow-down-circle"></i> Total Inflow</h6>
                                    <h2><?php echo number_format($summary['total_inflow'], 2); ?>m</h2>
                                    <small>Stock additions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-arrow-up-circle"></i> Total Outflow</h6>
                                    <h2><?php echo number_format($summary['total_outflow'], 2); ?>m</h2>
                                    <small>Sales & removals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-calculator"></i> Current Balance</h6>
                                    <h2><?php echo number_format($summary['current_balance'], 2); ?>m</h2>
                                    <small>Available meters</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-journal-text"></i> Transaction History (Oldest → Newest)
                </div>
                <div class="card-body p-0">
                    <?php if (empty($ledgerEntries)): ?>
                    <div class="alert alert-info m-3">
                        <i class="bi bi-info-circle"></i> No transactions recorded yet for this stock entry.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th class="text-end">Inflow (m)</th>
                                    <th class="text-end">Outflow (m)</th>
                                    <th class="text-end">Balance (m)</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ledgerEntries as $entry): ?>
                                <tr>
                                    <td><?php echo formatDate($entry['created_at']); ?></td>
                                    <td>
                                        <?php if ($entry['transaction_type'] === 'inflow'): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-arrow-down-circle"></i> Inflow
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-arrow-up-circle"></i> Outflow
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($entry['description']); ?></td>
                                    <td class="text-end">
                                        <?php if ($entry['inflow_meters'] > 0): ?>
                                        <strong class="text-success">+<?php echo number_format($entry['inflow_meters'], 2); ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($entry['outflow_meters'] > 0): ?>
                                        <strong class="text-danger">-<?php echo number_format($entry['outflow_meters'], 2); ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <strong><?php echo number_format($entry['balance_meters'], 2); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($entry['created_by_name']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> 
                <strong>Select a stock entry above to view its ledger.</strong>
                <p class="mb-0 mt-2">
                    Each stock entry has its own independent ledger. Multiple entries for the same coil 
                    are tracked separately, so a sold-out entry doesn't affect a new entry of the same coil.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>