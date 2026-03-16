<?php
/**
 * K-Zinc Stock Card (Ledger) — mirrors views/stock/ledger/index.php
 * Uses pieces columns (inflow_pieces / outflow_pieces / balance_pieces) added in migration 024.
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'K-Zinc Stock Card - ' . APP_NAME;

$stockEntryModel = new StockEntry();
$coilModel       = new Coil();
$db              = Database::getInstance()->getConnection();

$selectedEntryId = isset($_GET['entry_id']) ? (int)$_GET['entry_id'] : null;

// All KZinc stock entries (with or without ledger records)
$stockEntriesWithLedger = $db->query(
    "SELECT se.*, c.code AS coil_code, c.name AS coil_name
     FROM stock_entries se
     JOIN coils c ON se.coil_id = c.id
     WHERE se.deleted_at IS NULL
       AND c.category = '" . STOCK_CATEGORY_KZINC . "'
     ORDER BY se.created_at DESC"
)->fetchAll();

$ledgerEntries = [];
$summary = [
    'total_inflow'    => 0,
    'total_outflow'   => 0,
    'current_balance' => 0,
];
$selectedEntry = null;
$selectedCoil  = null;

if ($selectedEntryId) {
    $selectedEntry = $stockEntryModel->findById($selectedEntryId);

    if ($selectedEntry) {
        $selectedCoil = $coilModel->findById($selectedEntry['coil_id']);

        $summaryStmt = $db->prepare(
            "SELECT
                COALESCE(SUM(inflow_pieces), 0)  AS total_inflow,
                COALESCE(SUM(outflow_pieces), 0) AS total_outflow,
                COALESCE((
                    SELECT balance_pieces FROM stock_ledger
                    WHERE stock_entry_id = ?
                    ORDER BY created_at DESC, id DESC
                    LIMIT 1
                ), 0) AS current_balance
             FROM stock_ledger
             WHERE stock_entry_id = ?"
        );
        $summaryStmt->execute([$selectedEntryId, $selectedEntryId]);
        $summaryRow = $summaryStmt->fetch();
        if ($summaryRow) {
            $summary = [
                'total_inflow'    => (int)$summaryRow['total_inflow'],
                'total_outflow'   => (int)$summaryRow['total_outflow'],
                'current_balance' => (int)$summaryRow['current_balance'],
            ];
        }

        $entriesStmt = $db->prepare(
            "SELECT sl.*, u.name AS created_by_name
             FROM stock_ledger sl
             LEFT JOIN users u ON sl.created_by = u.id
             WHERE sl.stock_entry_id = ?
             ORDER BY sl.created_at ASC, sl.id ASC
             LIMIT 200"
        );
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
                <h1 class="page-title">K-Zinc Stock Card</h1>
                <p class="text-muted">Individual accounting for each K-Zinc stock entry</p>
            </div>
            <a href="/new-stock-system/index.php?page=kzinc_stock" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Stock Entries
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-funnel"></i> Select Stock Card
                </div>
                <div class="card-body">
                    <form method="GET" action="/new-stock-system/index.php">
                        <input type="hidden" name="page" value="kzinc_stock_ledger">
                        <div class="row">
                            <div class="col-md-10">
                                <select class="form-select" name="entry_id" required>
                                    <option value="">Select a stock card to view transactions</option>
                                    <?php foreach ($stockEntriesWithLedger as $e):
                                        $rem = (int)($e['pieces_remaining'] ?? 0);
                                        $tot = (int)($e['pieces_total']     ?? 0);
                                        $qty = (int)($e['quantity']         ?? 0);

                                        // Get current piece balance from ledger
                                        $balStmt = $db->prepare(
                                            "SELECT COALESCE((SELECT balance_pieces FROM stock_ledger
                                              WHERE stock_entry_id = ? ORDER BY created_at DESC, id DESC LIMIT 1), 0) AS bal"
                                        );
                                        $balStmt->execute([$e['id']]);
                                        $balance = (int)$balStmt->fetchColumn();

                                        $statusLabel = ($rem <= 0 && $tot > 0) ? 'SOLD OUT' : 'Available';
                                    ?>
                                    <option value="<?php echo $e['id']; ?>"
                                        <?php echo $selectedEntryId == $e['id'] ? 'selected' : ''; ?>>
                                        Entry #<?php echo $e['id']; ?> -
                                        <?php echo htmlspecialchars($e['coil_code']); ?> -
                                        <?php echo htmlspecialchars($e['coil_name']); ?>
                                        (<?php echo number_format($qty); ?> bundles, <?php echo number_format($tot); ?> pcs)
                                        [<?php echo $statusLabel; ?>]
                                        - Balance: <?php echo number_format($balance); ?> pcs
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> View Stock Card
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
                    <i class="bi bi-receipt"></i> Stock Card #<?php echo $selectedEntry['id']; ?> -
                    <?php echo htmlspecialchars($selectedCoil['code']); ?> -
                    <?php echo htmlspecialchars($selectedCoil['name']); ?>
                    <?php $remPcs = (int)($selectedEntry['pieces_remaining'] ?? 0); ?>
                    <?php if ($remPcs <= 0): ?>
                    <span class="badge bg-danger ms-2">SOLD OUT</span>
                    <?php else: ?>
                    <span class="badge bg-success ms-2">AVAILABLE</span>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <strong>Entry Details:</strong><br>
                                Total Pieces: <?php echo number_format((int)($selectedEntry['pieces_total'] ?? 0)); ?> pcs |
                                Remaining: <?php echo number_format($remPcs); ?> pcs |
                                Qty: <?php echo number_format($selectedEntry['quantity'] ?? 0); ?> <?php echo htmlspecialchars($selectedEntry['unit_type'] ?? 'bundles'); ?> |
                                Created: <?php echo formatDate($selectedEntry['created_at']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-arrow-down-circle"></i> Total Inflow</h6>
                                    <h2><?php echo number_format($summary['total_inflow']); ?> pcs</h2>
                                    <small>Stock additions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-arrow-up-circle"></i> Total Outflow</h6>
                                    <h2><?php echo number_format($summary['total_outflow']); ?> pcs</h2>
                                    <small>Sales &amp; removals</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6><i class="bi bi-calculator"></i> Current Balance</h6>
                                    <h2><?php echo number_format($summary['current_balance']); ?> pcs</h2>
                                    <small>Available pieces</small>
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
                                    <th class="text-end">Inflow (pcs)</th>
                                    <th class="text-end">Outflow (pcs)</th>
                                    <th class="text-end">Balance (pcs)</th>
                                    <th>Created By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ledgerEntries as $row): ?>
                                <tr>
                                    <td><?php echo formatDate($row['created_at']); ?></td>
                                    <td>
                                        <?php if ($row['transaction_type'] === 'inflow'): ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-arrow-down-circle"></i> Inflow
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="bi bi-arrow-up-circle"></i> Outflow
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td class="text-end">
                                        <?php if ((int)$row['inflow_pieces'] > 0): ?>
                                        <strong class="text-success">+<?php echo number_format((int)$row['inflow_pieces']); ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ((int)$row['outflow_pieces'] > 0): ?>
                                        <strong class="text-danger">-<?php echo number_format((int)$row['outflow_pieces']); ?></strong>
                                        <?php else: ?>
                                        <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <strong><?php echo number_format((int)$row['balance_pieces']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['created_by_name'] ?? '—'); ?></td>
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
                <strong>Select a stock entry above to view its stock card.</strong>
                <p class="mb-0 mt-2">
                    Each K-Zinc stock entry has its own independent stock card. Piece deductions from
                    sales are tracked here so you can see exactly how each entry was consumed.
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
