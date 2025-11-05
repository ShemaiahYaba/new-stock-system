<?php
/**
 * Stock Ledger - Accounting View for Factory-Use Coils
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_ledger.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Stock Ledger - ' . APP_NAME;

$ledgerModel = new StockLedger();
$coilModel = new Coil();

// Get selected coil if any
$selectedCoilId = isset($_GET['coil_id']) ? (int)$_GET['coil_id'] : null;

// Get all factory-use coils
$factoryCoils = $ledgerModel->getFactoryUseCoils();

// Get ledger entries for selected coil
$ledgerEntries = [];
$summary = null;
$selectedCoil = null;

if ($selectedCoilId) {
    $selectedCoil = $coilModel->findById($selectedCoilId);
    $ledgerEntries = $ledgerModel->getByCoil($selectedCoilId, 100, 0);
    $summary = $ledgerModel->getSummary($selectedCoilId);
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock Ledger</h1>
                <p class="text-muted">Accounting for factory-use coils</p>
            </div>
            <a href="/new-stock-system/index.php?page=coils" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Coils
            </a>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-funnel"></i> Select Factory-Use Coil
                </div>
                <div class="card-body">
                    <form method="GET" action="/new-stock-system/index.php">
                        <input type="hidden" name="page" value="stock_ledger">
                        <div class="row">
                            <div class="col-md-10">
                                <select class="form-select" name="coil_id" required>
                                    <option value="">Select a factory-use coil</option>
                                    <?php foreach ($factoryCoils as $coil): ?>
                                    <option value="<?php echo $coil['id']; ?>" <?php echo $selectedCoilId == $coil['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($coil['code']); ?> - 
                                        <?php echo htmlspecialchars($coil['name']); ?> 
                                        (Balance: <?php echo number_format($coil['current_balance'] ?? 0, 2); ?>m)
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
    
    <?php if ($selectedCoil && $summary): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-box-seam"></i> <?php echo htmlspecialchars($selectedCoil['code']); ?> - <?php echo htmlspecialchars($selectedCoil['name']); ?>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-arrow-down-circle"></i> Total Inflow</h6>
                                    <h2><?php echo number_format($summary['total_inflow'] ?? 0, 2); ?>m</h2>
                                    <small>Stock additions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-arrow-up-circle"></i> Total Outflow</h6>
                                    <h2><?php echo number_format($summary['total_outflow'] ?? 0, 2); ?>m</h2>
                                    <small>Sales & removals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-calculator"></i> Current Balance</h6>
                                    <h2><?php echo number_format($summary['current_balance'] ?? 0, 2); ?>m</h2>
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
                    <i class="bi bi-journal-text"></i> Transaction History
                </div>
                <div class="card-body p-0">
                    <?php if (empty($ledgerEntries)): ?>
                    <div class="alert alert-info m-3">
                        <i class="bi bi-info-circle"></i> No transactions recorded yet for this coil.
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
                <strong>Select a factory-use coil above to view its ledger.</strong>
                <p class="mb-0 mt-2">The stock ledger tracks all inflows (additions) and outflows (sales) for coils marked as "Factory Use", maintaining a running balance.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
