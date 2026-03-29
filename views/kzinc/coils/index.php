<?php
/**
 * KZinc Coils List
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'K-Zinc Coils - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$coilModel = new Coil();

// Only pallet/sheet KZinc coils belong in this module
$palletOnly = fn($c) => ($c['kzinc_track_mode'] ?? null) === 'pallets';

if ($searchQuery !== '') {
    $allResults = $coilModel->search($searchQuery, STOCK_CATEGORY_KZINC, 10000, 0);
    $allResults = array_values(array_filter($allResults, $palletOnly));
    $totalCoils = count($allResults);
    $coils      = array_slice($allResults, ($currentPage - 1) * RECORDS_PER_PAGE, RECORDS_PER_PAGE);
} else {
    $allKzinc   = $coilModel->getAll(STOCK_CATEGORY_KZINC, 10000, 0);
    $allKzinc   = array_values(array_filter($allKzinc, $palletOnly));
    $totalCoils = count($allKzinc);
    $coils      = array_slice($allKzinc, ($currentPage - 1) * RECORDS_PER_PAGE, RECORDS_PER_PAGE);
}

// Load piece totals for each coil
$stockEntryModel = new StockEntry();
$pieceTotals = [];
foreach ($coils as $coil) {
    $entries = $stockEntryModel->getAvailableKzincEntries($coil['id']);
    $pieceTotals[$coil['id']] = array_sum(array_column($entries, 'pieces_remaining'));
}

$paginationData = getPaginationData($totalCoils, $currentPage);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">K-Zinc Coils</h1>
                <p class="text-muted">
                    Manage K-Zinc coil inventory
                    <?php if ($searchQuery !== ''): ?>
                        <span class="badge bg-info">Search: "<?php echo htmlspecialchars($searchQuery); ?>"</span>
                    <?php endif; ?>
                    (<?php echo $totalCoils; ?> total)
                </p>
            </div>
            <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=kzinc_coils_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add K-Zinc Coil
            </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-layers"></i> K-Zinc Coils
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="kzinc_coils">
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                               placeholder="Search by code or name..."
                               value="<?php echo htmlspecialchars($searchQuery); ?>" autocomplete="off">
                        <button type="submit" class="btn btn-sm btn-primary" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($searchQuery !== ''): ?>
                        <a href="/new-stock-system/index.php?page=kzinc_coils"
                           class="btn btn-sm btn-secondary ms-2" title="Clear">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($coils)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i>
                <?php if ($searchQuery !== ''): ?>
                    No K-Zinc coils found matching "<?php echo htmlspecialchars($searchQuery); ?>".
                <?php else: ?>
                    No K-Zinc coils found. <a href="/new-stock-system/index.php?page=kzinc_coils_create">Add one now.</a>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Color</th>
                            <th>Net Weight</th>
                            <th>Pallet Size</th>
                            <th>Pieces Available</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coils as $coil): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($coil['code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($coil['name']); ?></td>
                            <td>
                                <?php if (!empty($coil['color_name'])): ?>
                                    <span style="display:inline-block;width:14px;height:14px;background:<?php echo htmlspecialchars($coil['color_hex'] ?? '#ccc'); ?>;border:1px solid #ddd;border-radius:3px;margin-right:4px;vertical-align:middle;"></span>
                                    <?php echo htmlspecialchars($coil['color_name']); ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($coil['net_weight'], 2); ?> kg</td>
                            <td>
                                <?php if (!empty($coil['pallet_size'])): ?>
                                    <span class="badge bg-secondary"><?php echo (int)$coil['pallet_size']; ?> bdl/pallet</span>
                                <?php else: ?>
                                    <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Not set</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $pcs = $pieceTotals[$coil['id']] ?? 0; ?>
                                <span class="badge <?php echo $pcs > 0 ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo number_format($pcs); ?> pcs
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo getStatusBadgeClass($coil['status']); ?>">
                                    <?php echo STOCK_STATUSES[$coil['status']] ?? $coil['status']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/new-stock-system/index.php?page=kzinc_coils_view&id=<?php echo $coil['id']; ?>"
                                       class="btn btn-info" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_EDIT)): ?>
                                    <a href="/new-stock-system/index.php?page=kzinc_coils_edit&id=<?php echo $coil['id']; ?>"
                                       class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    <a href="/new-stock-system/index.php?page=kzinc_stock_create&coil_id=<?php echo $coil['id']; ?>"
                                       class="btn btn-success" title="Add Stock">
                                        <i class="bi bi-plus-circle"></i>
                                    </a>
                                    <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_DELETE)): ?>
                                    <form method="POST"
                                          action="/new-stock-system/controllers/kzinc/coils/delete/index.php"
                                          style="display:inline-block;"
                                          onsubmit="return confirm('Are you sure you want to delete this K-Zinc coil?');">
                                        <input type="hidden" name="id" value="<?php echo $coil['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <button type="submit" class="btn btn-danger" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($coils) && $totalCoils > RECORDS_PER_PAGE): ?>
        <div class="card-footer">
            <?php
            $queryParams = ['page' => 'kzinc_coils'];
            if ($searchQuery !== '') $queryParams['search'] = $searchQuery;
            include __DIR__ . '/../../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
