<?php
/**
 * KZinc Stock Entries List
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'K-Zinc Stock Entries - ' . APP_NAME;

$currentPage  = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$filterCoilId = isset($_GET['coil_id']) ? (int)$_GET['coil_id'] : null;

$coilModel       = new Coil();
$stockEntryModel = new StockEntry();

// Get all KZinc coils for the filter dropdown
$kzincCoils = $coilModel->getAll(STOCK_CATEGORY_KZINC, 1000, 0);

// Load entries
// We query all entries belonging to KZinc coils (unit_type != meters)
$db = Database::getInstance()->getConnection();

$where  = "se.deleted_at IS NULL AND c.category = :category AND (se.unit_type != 'meters' OR se.unit_type IS NULL)";
$params = [':category' => STOCK_CATEGORY_KZINC];

if ($filterCoilId) {
    $where  .= ' AND se.coil_id = :coil_id';
    $params[':coil_id'] = $filterCoilId;
}

$offset = ($currentPage - 1) * RECORDS_PER_PAGE;

$countSql = "SELECT COUNT(*) FROM stock_entries se
             JOIN coils c ON se.coil_id = c.id
             WHERE $where";
$countStmt = $db->prepare($countSql);
$countStmt->execute($params);
$totalEntries = (int)$countStmt->fetchColumn();

$sql = "SELECT se.*, c.code AS coil_code, c.name AS coil_name, u.name AS created_by_name
        FROM stock_entries se
        JOIN coils c ON se.coil_id = c.id
        LEFT JOIN users u ON se.created_by = u.id
        WHERE $where
        ORDER BY se.created_at DESC
        LIMIT :limit OFFSET :offset";

$stmt = $db->prepare($sql);
foreach ($params as $k => $v) {
    $stmt->bindValue($k, $v);
}
$stmt->bindValue(':limit',  RECORDS_PER_PAGE, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,          PDO::PARAM_INT);
$stmt->execute();
$entries = $stmt->fetchAll();

$paginationData = getPaginationData($totalEntries, $currentPage);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">K-Zinc Stock Entries</h1>
                <p class="text-muted">(<?php echo $totalEntries; ?> total)</p>
            </div>
            <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=kzinc_stock_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Stock Entry
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coil Filter -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="/new-stock-system/index.php" class="d-flex align-items-center gap-2">
                <input type="hidden" name="page" value="kzinc_stock">
                <label class="mb-0 text-muted small">Filter by coil:</label>
                <select name="coil_id" class="form-select form-select-sm" style="max-width:280px;"
                        onchange="this.form.submit()">
                    <option value="">All K-Zinc Coils</option>
                    <?php foreach ($kzincCoils as $c): ?>
                    <option value="<?php echo $c['id']; ?>"
                        <?php echo $filterCoilId == $c['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['code']); ?> — <?php echo htmlspecialchars($c['name']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($filterCoilId): ?>
                <a href="/new-stock-system/index.php?page=kzinc_stock" class="btn btn-sm btn-secondary">
                    <i class="bi bi-x"></i> Clear
                </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><i class="bi bi-list-check"></i> Stock Entries</div>
        <div class="card-body p-0">
            <?php if (empty($entries)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i>
                No K-Zinc stock entries found.
                <a href="/new-stock-system/index.php?page=kzinc_stock_create">Add one now.</a>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Coil</th>
                            <th>Unit Type</th>
                            <th>Quantity</th>
                            <th>Total Pieces</th>
                            <th>Remaining Pieces</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                        <?php
                        $rem = (int)($entry['pieces_remaining'] ?? 0);
                        $piecesTotal = (int)($entry['pieces_total'] ?? 0);
                        $displayStatus = ($rem <= 0 && $piecesTotal > 0) ? 'sold' : ($entry['status'] ?? 'available');
                        $unitLabel = $entry['unit_type'] ?? null;

                        ?>
                        <tr>
                            <td><small class="text-muted"><?php echo $entry['id']; ?></small></td>
                            <td>
                                <a href="/new-stock-system/index.php?page=kzinc_coils_view&id=<?php echo $entry['coil_id']; ?>">
                                    <strong><?php echo htmlspecialchars($entry['coil_code']); ?></strong>
                                </a>
                                <small class="d-block text-muted"><?php echo htmlspecialchars($entry['coil_name']); ?></small>
                            </td>
                            <td>
                                <?php if ($unitLabel): ?>
                                <span class="badge bg-info text-capitalize">
                                    <?php echo htmlspecialchars($unitLabel); ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($entry['quantity'] ?? 0, 0); ?></td>
                            <td><?php echo number_format($piecesTotal); ?></td>
                            <td>
                                <span class="badge <?php echo $rem > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo number_format($rem); ?> pcs
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($displayStatus); ?>">
                                    <?php echo STOCK_STATUSES[$displayStatus] ?? ucfirst(str_replace('_', ' ', $displayStatus)); ?>
                                </span>
                                <a href="/new-stock-system/index.php?page=kzinc_stock_ledger&entry_id=<?php echo $entry['id']; ?>"
                                   class="btn btn-sm btn-outline-primary ms-1"
                                   title="View stock card for entry #<?php echo $entry['id']; ?>">
                                    <i class="bi bi-journal-text"></i> Stock Card
                                </a>
                            </td>
                            <td><small><?php echo formatDate($entry['created_at']); ?></small></td>
                            <td><small><?php echo htmlspecialchars($entry['created_by_name'] ?? '—'); ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if ($totalEntries > RECORDS_PER_PAGE): ?>
        <div class="card-footer">
            <?php
            $queryParams = ['page' => 'kzinc_stock'];
            if ($filterCoilId) $queryParams['coil_id'] = $filterCoilId;
            include __DIR__ . '/../../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
