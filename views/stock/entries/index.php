<?php
/**
 * Stock Entries List - WITH FILTERING
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Stock Entries - ' . APP_NAME;

// Get filter parameters
$currentPage    = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
$coilId         = isset($_GET['coil_id']) ? (int) $_GET['coil_id'] : null;
$statusFilter   = isset($_GET['status'])   ? sanitize($_GET['status'])   : '';
$searchQuery    = isset($_GET['search'])   ? trim($_GET['search'])        : '';
$categoryFilter = isset($_GET['category']) ? sanitize($_GET['category'])  : '';

// Validate category filter — only allow known non-tile categories
$allowedCategories = array_keys(array_filter(STOCK_CATEGORIES, function ($k) {
    return $k !== STOCK_CATEGORY_TILE;
}, ARRAY_FILTER_USE_KEY));
if (!in_array($categoryFilter, $allowedCategories, true)) {
    $categoryFilter = '';
}

$db = Database::getInstance()->getConnection();

// Show all non-tile stock entries. For KZinc, only show meter-based entries
// (bundle/pallet/piece entries are managed in the dedicated K-Zinc module).
$baseWhere = "se.deleted_at IS NULL AND c.category != '" . STOCK_CATEGORY_TILE . "' AND (c.category != '" . STOCK_CATEGORY_KZINC . "' OR (se.unit_type = '" . STOCK_UNIT_METERS . "' OR se.unit_type IS NULL))";
$baseJoin  = "FROM stock_entries se
              LEFT JOIN coils c ON se.coil_id = c.id
              LEFT JOIN users u ON se.created_by = u.id";

$whereParts = [$baseWhere];
$params     = [];

if ($searchQuery !== '') {
    $q = "%$searchQuery%";
    $whereParts[] = '(c.code LIKE ? OR c.name LIKE ?)';
    $params[] = $q;
    $params[] = $q;
}
if ($coilId) {
    $whereParts[] = 'se.coil_id = ?';
    $params[] = $coilId;
}
if ($statusFilter !== '') {
    $whereParts[] = 'se.status = ?';
    $params[] = $statusFilter;
}
if ($categoryFilter !== '') {
    $whereParts[] = 'c.category = ?';
    $params[] = $categoryFilter;
}

$whereStr = implode(' AND ', $whereParts);
$offset   = ($currentPage - 1) * RECORDS_PER_PAGE;

$countStmt = $db->prepare("SELECT COUNT(*) $baseJoin WHERE $whereStr");
$countStmt->execute($params);
$totalEntries = (int)$countStmt->fetchColumn();

$entriesStmt = $db->prepare(
    "SELECT se.*, c.code AS coil_code, c.name AS coil_name, c.status AS coil_status, u.name AS created_by_name
     $baseJoin
     WHERE $whereStr
     ORDER BY se.created_at DESC
     LIMIT ? OFFSET ?"
);
$entriesStmt->execute(array_merge($params, [(int)RECORDS_PER_PAGE, $offset]));
$entries = $entriesStmt->fetchAll();

$paginationData = getPaginationData($totalEntries, $currentPage);

// Coils for filter dropdown (all non-tile coils, including KZinc meter coils)
$coilModel = new Coil();
$coilsForFilter = array_filter(
    $coilModel->getAll(null, 1000, 0),
    fn($c) => $c['category'] !== STOCK_CATEGORY_TILE
);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock Entries</h1>
                <p class="text-muted">
                    Manage stock entries
                    <?php if ($searchQuery !== ''): ?>
                        <span class="badge bg-info">Search: "<?php echo htmlspecialchars($searchQuery); ?>"</span>
                    <?php endif; ?>
                    <?php if ($statusFilter !== ''): ?>
                        <span class="badge bg-secondary">Status: <?php echo ucfirst(str_replace('_', ' ', $statusFilter)); ?></span>
                    <?php endif; ?>
                    <?php if ($categoryFilter !== ''): ?>
                        <span class="badge bg-info"><?php echo STOCK_CATEGORIES[$categoryFilter] ?? $categoryFilter; ?></span>
                    <?php endif; ?>
                    <?php if ($coilId): ?>
                        <span class="badge bg-primary">Coil Filter Active</span>
                    <?php endif; ?>
                    (<?php echo $totalEntries; ?> total)
                </p>
            </div>
            <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=stock_entries_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add Stock Entry
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if (hasPermission(MODULE_KZINC_MANAGEMENT)): ?>
    <div class="alert alert-info alert-permanent py-2 mb-3">
        <i class="bi bi-layers"></i>
        K-Zinc coil meter entries are shown here. Pallet/bundle/piece entries are managed in the
        <a href="/new-stock-system/index.php?page=kzinc_stock" class="alert-link">K-Zinc module</a>.
    </div>
    <?php endif; ?>

    <!-- Filters Card -->
    <?php
    // Build a shared extra-params string used by filter buttons to preserve the other active filter
    $catExtra    = $categoryFilter !== '' ? '&category=' . urlencode($categoryFilter) : '';
    $statusExtra = $statusFilter   !== '' ? '&status='   . urlencode($statusFilter)   : '';
    $coilExtra   = $coilId                ? '&coil_id='  . $coilId                     : '';
    $searchExtra = $searchQuery    !== '' ? '&search='   . urlencode($searchQuery)    : '';
    ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <!-- Category Filter Buttons -->
                <div class="col-12">
                    <label class="form-label small text-muted">Filter by Category:</label>
                    <div class="btn-group" role="group">
                        <a href="/new-stock-system/index.php?page=stock_entries<?php echo $statusExtra . $coilExtra . $searchExtra; ?>"
                           class="btn btn-sm <?php echo $categoryFilter === '' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            All Categories
                        </a>
                        <?php foreach (STOCK_CATEGORIES as $catKey => $catName):
                            if ($catKey === STOCK_CATEGORY_TILE) continue; ?>
                        <a href="/new-stock-system/index.php?page=stock_entries&category=<?php echo urlencode($catKey); ?><?php echo $statusExtra . $coilExtra . $searchExtra; ?>"
                           class="btn btn-sm <?php echo $categoryFilter === $catKey ? 'btn-info' : 'btn-outline-info'; ?>">
                            <?php echo $catName; ?>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Status Filter Buttons -->
                <div class="col-md-6">
                    <label class="form-label small text-muted">Filter by Status:</label>
                    <div class="btn-group w-100" role="group">
                        <a href="/new-stock-system/index.php?page=stock_entries<?php echo $catExtra . $coilExtra . $searchExtra; ?>"
                           class="btn btn-sm <?php echo $statusFilter === '' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            All
                        </a>
                        <a href="/new-stock-system/index.php?page=stock_entries&status=available<?php echo $catExtra . $coilExtra . $searchExtra; ?>"
                           class="btn btn-sm <?php echo $statusFilter === 'available' ? 'btn-success' : 'btn-outline-success'; ?>">
                            Available
                        </a>
                        <a href="/new-stock-system/index.php?page=stock_entries&status=factory_use<?php echo $catExtra . $coilExtra . $searchExtra; ?>"
                           class="btn btn-sm <?php echo $statusFilter === 'factory_use' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                            Factory Use
                        </a>
                        <a href="/new-stock-system/index.php?page=stock_entries&status=sold<?php echo $catExtra . $coilExtra . $searchExtra; ?>"
                           class="btn btn-sm <?php echo $statusFilter === 'sold' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                            Sold Out
                        </a>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="col-md-6">
                    <label class="form-label small text-muted">Search:</label>
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="stock_entries">
                        <?php if ($coilId): ?>
                        <input type="hidden" name="coil_id" value="<?php echo $coilId; ?>">
                        <?php endif; ?>
                        <?php if ($statusFilter !== ''): ?>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
                        <?php endif; ?>
                        <?php if ($categoryFilter !== ''): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($categoryFilter); ?>">
                        <?php endif; ?>
                        <input type="text"
                               name="search"
                               class="form-control form-control-sm me-2"
                               placeholder="Search by coil code or name..."
                               value="<?php echo htmlspecialchars($searchQuery); ?>"
                               autocomplete="off">
                        <button type="submit" class="btn btn-sm btn-primary" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($searchQuery !== '' || $statusFilter !== '' || $coilId || $categoryFilter !== ''): ?>
                        <a href="/new-stock-system/index.php?page=stock_entries"
                           class="btn btn-sm btn-secondary ms-2" title="Clear all filters">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list-check"></i> Stock Entries List
        </div>
        <div class="card-body p-0">
            <?php if (empty($entries)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> 
                <?php if ($searchQuery !== ''): ?>
                    No stock entries found matching "<?php echo htmlspecialchars($searchQuery); ?>".
                <?php elseif ($statusFilter !== ''): ?>
                    No stock entries found with status "<?php echo ucfirst(str_replace('_', ' ', $statusFilter)); ?>".
                <?php else: ?>
                    No stock entries found.
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Coil Code</th>
                            <th>Coil Name</th>
                            <th>Quantity</th>
                            <th>Weight (KG)</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($entries as $entry): ?>
                        <?php
                        $displayStatus = $entry['status'] ?? 'available';
                        $isKzincEntry = ($entry['unit_type'] ?? 'meters') !== 'meters';

                        if ($isKzincEntry) {
                            if ((int)($entry['pieces_remaining'] ?? 0) <= 0) {
                                $displayStatus = 'sold';
                            }
                        } else {
                            if ($entry['meters_remaining'] <= 0) {
                                $displayStatus = 'sold';
                            }
                        }

                        $statusBadge = 'bg-info';
                        $statusText = 'Available';

                        if ($displayStatus === 'sold') {
                            $statusBadge = 'bg-danger';
                            $statusText = 'Sold Out';
                        } elseif ($displayStatus === 'factory_use') {
                            $statusBadge = 'bg-warning';
                            $statusText = 'Factory Use';
                        }

                        $checkLedgerSql = 'SELECT COUNT(*) as count FROM stock_ledger WHERE stock_entry_id = ?';
                        $checkStmt = $db->prepare($checkLedgerSql);
                        $checkStmt->execute([$entry['id']]);
                        $ledgerCheck = $checkStmt->fetch();
                        $hasLedger = $ledgerCheck['count'] > 0;
                        ?>
                        <tr>
                            <td>#<?php echo $entry['id']; ?></td>
                            <td><strong><?php echo !empty($entry['coil_code'])
                                ? htmlspecialchars($entry['coil_code'])
                                : 'N/A'; ?></strong></td>
                            <td><?php echo !empty($entry['coil_name'])
                                ? htmlspecialchars($entry['coil_name'])
                                : 'N/A'; ?></td>
                            <td>
                                <?php if ($isKzincEntry): ?>
                                    <?php echo number_format($entry['quantity'] ?? 0, 2); ?> <?php echo htmlspecialchars($entry['unit_type']); ?>
                                    <small class="text-muted d-block">(<?php echo (int)($entry['pieces_total'] ?? 0); ?> pcs)</small>
                                <?php else: ?>
                                    <?php echo number_format($entry['meters'], 2); ?>m
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($isKzincEntry): ?>
                                    <span class="text-muted">N/A</span>
                                <?php elseif (!empty($entry['weight_kg']) && $entry['weight_kg'] > 0): ?>
                                    <span class="text-primary fw-bold">
                                        <?php echo number_format($entry['weight_kg'], 2); ?> kg
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($isKzincEntry): ?>
                                    <span class="badge <?php echo (int)($entry['pieces_remaining'] ?? 0) > 0 ? 'bg-success' : 'bg-secondary'; ?>">
                                        <?php echo (int)($entry['pieces_remaining'] ?? 0); ?> pcs
                                    </span>
                                <?php else: ?>
                                    <span class="badge <?php echo $entry['meters_remaining'] > 0
                                        ? 'bg-success'
                                        : 'bg-secondary'; ?>">
                                        <?php echo number_format($entry['meters_remaining'], 2); ?>m
                                    </span>
                                <?php endif; ?>
                            </td>
                            
                            <td>
                                <span class="badge <?php echo $statusBadge; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                                
                                <?php if ($hasLedger && hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW)): ?>
                                <a href="/new-stock-system/index.php?page=stock_ledger&entry_id=<?php echo $entry['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary ms-1" 
                                   title="View Stock Card for Entry #<?php echo $entry['id']; ?>">
                                    <i class="bi bi-journal-text"></i> Stock Card
                                </a>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($entry['created_by_name']); ?></td>
                            <td>
                                <?php if (!$isKzincEntry && hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT) && $entry['meters_remaining'] > 0): ?>
                                <form method="POST" action="/new-stock-system/controllers/stock_entries/toggle_status/index.php" style="display: inline;">
                                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                    <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                                    <input type="hidden" name="current_status" value="<?php echo $entry['status'] ?? 'available'; ?>">
                                    <button type="submit" class="btn btn-sm btn-<?php echo ($entry['status'] ?? 'available') === 'available' ? 'warning' : 'info'; ?>" 
                                            title="<?php echo ($entry['status'] ?? 'available') === 'available' ? 'Move to Factory Use' : 'Move to Available'; ?>">
                                        <i class="bi bi-arrow-<?php echo ($entry['status'] ?? 'available') === 'available' ? 'right' : 'left'; ?>-circle"></i>
                                        <?php echo ($entry['status'] ?? 'available') === 'available' ? 'To Factory' : 'To Available'; ?>
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
        <?php if (!empty($entries) && $totalEntries > RECORDS_PER_PAGE): ?>
        <div class="card-footer">
            <?php
            $queryParams = $_GET;
            include __DIR__ . '/../../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>