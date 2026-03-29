<?php
/**
 * Coils List View — Alusteel, Aluminum & KZinc meter coils
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Coils - ' . APP_NAME;

// Filter parameters
$category    = isset($_GET['category']) ? $_GET['category'] : null;
$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$statusFilter = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;
$gaugeFilter  = isset($_GET['gauge'])  && $_GET['gauge']  !== '' ? trim($_GET['gauge']) : null;
$colorFilter  = isset($_GET['color_id']) && $_GET['color_id'] !== '' ? (int)$_GET['color_id'] : null;

$coilModel  = new Coil();
$colorModel = new Color();

// Categories shown on this page — excludes Tile only; KZinc coils are managed here (meter-based)
$nonKzincCategories = array_keys(array_filter(STOCK_CATEGORIES, function ($k) {
    return $k !== STOCK_CATEGORY_TILE;
}, ARRAY_FILTER_USE_KEY));

// Gauge & color options for filter dropdowns
// Scope to current category if one is selected, otherwise show all non-KZinc gauges
$gaugeOptions = $coilModel->getDistinctGauges($category);
$colorOptions = $colorModel->getAll(1000, 0);

// ---- Data fetching -------------------------------------------------------
if ($searchQuery !== '') {
    $coils = $coilModel->search(
        $searchQuery, $category, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE,
        $statusFilter, $gaugeFilter, $colorFilter
    );
    $allSearchResults = $coilModel->search($searchQuery, $category, 10000, 0, $statusFilter, $gaugeFilter, $colorFilter);

    if (!$category) {
        $coils            = array_values(array_filter($coils, fn($c) => in_array($c['category'], $nonKzincCategories)));
        $allSearchResults = array_values(array_filter($allSearchResults, fn($c) => in_array($c['category'], $nonKzincCategories)));
    }
    $totalCoils = count($allSearchResults);
} else {
    $coils = $coilModel->getAll(
        $category, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE,
        $statusFilter, $gaugeFilter, $colorFilter
    );
    if (!$category) {
        $coils = array_values(array_filter($coils, fn($c) => in_array($c['category'], $nonKzincCategories)));
        $totalCoils = array_sum(array_map(
            fn($cat) => $coilModel->count($cat, $statusFilter, $gaugeFilter, $colorFilter),
            $nonKzincCategories
        ));
    } else {
        $totalCoils = $coilModel->count($category, $statusFilter, $gaugeFilter, $colorFilter);
    }
}

$paginationData = getPaginationData($totalCoils, $currentPage);

// Are any advanced filters active?
$hasActiveFilters = $statusFilter !== null || $gaugeFilter !== null || $colorFilter !== null;

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <?php echo $category ? STOCK_CATEGORIES[$category] . ' ' : ''; ?>Coils
                </h1>
                <p class="text-muted">
                    Manage coil inventory
                    <?php if ($searchQuery !== ''): ?>
                        <span class="badge bg-info">Search: "<?php echo htmlspecialchars($searchQuery); ?>"</span>
                    <?php endif; ?>
                    <?php if ($hasActiveFilters): ?>
                        <span class="badge bg-warning text-dark">Filtered</span>
                    <?php endif; ?>
                    (<?php echo $totalCoils; ?> <?php echo $searchQuery !== '' ? 'results' : 'total'; ?>)
                </p>
            </div>
            <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=coils_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Coil
            </a>
            <?php endif; ?>
        </div>
    </div>

    <?php if (hasPermission(MODULE_KZINC_MANAGEMENT)): ?>
    <div class="alert alert-info alert-permanent py-2 mb-3">
        <i class="bi bi-layers"></i>
        K-Zinc coil meter stock is managed here. For pallet / bundle / piece operations, use the
        <a href="/new-stock-system/index.php?page=kzinc_coils" class="alert-link">K-Zinc module</a>.
    </div>
    <?php endif; ?>

    <!-- Category Tabs (Alusteel & Aluminum only) -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <div class="btn-group" role="group">
                <?php
                $catBase = '/new-stock-system/index.php?page=coils';
                $catExtra = '';
                if ($searchQuery !== '') $catExtra .= '&search=' . urlencode($searchQuery);
                if ($statusFilter)       $catExtra .= '&status=' . urlencode($statusFilter);
                if ($gaugeFilter)        $catExtra .= '&gauge='  . urlencode($gaugeFilter);
                if ($colorFilter)        $catExtra .= '&color_id=' . $colorFilter;
                ?>
                <a href="<?php echo $catBase . $catExtra; ?>"
                   class="btn btn-sm <?php echo !$category ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    All
                </a>
                <?php foreach (STOCK_CATEGORIES as $catKey => $catName):
                    if ($catKey === STOCK_CATEGORY_TILE) continue; ?>
                <a href="<?php echo $catBase . '&category=' . $catKey . $catExtra; ?>"
                   class="btn btn-sm <?php echo $category === $catKey ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <?php echo $catName; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Filters Panel -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="/new-stock-system/index.php" class="row g-2 align-items-end">
                <input type="hidden" name="page" value="coils">
                <?php if ($category): ?>
                <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                <?php endif; ?>

                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label small mb-1">Search</label>
                    <input type="text"
                           name="search"
                           class="form-control form-control-sm"
                           placeholder="Code or name…"
                           value="<?php echo htmlspecialchars($searchQuery); ?>"
                           autocomplete="off">
                </div>

                <!-- Status -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All statuses</option>
                        <?php foreach (STOCK_STATUSES as $sKey => $sName): ?>
                        <option value="<?php echo $sKey; ?>" <?php echo $statusFilter === $sKey ? 'selected' : ''; ?>>
                            <?php echo $sName; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Gauge -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Gauge</label>
                    <select name="gauge" class="form-select form-select-sm">
                        <option value="">All gauges</option>
                        <?php foreach ($gaugeOptions as $g): ?>
                        <option value="<?php echo htmlspecialchars($g); ?>"
                                <?php echo $gaugeFilter === $g ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($g); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Color -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Color</label>
                    <select name="color_id" class="form-select form-select-sm">
                        <option value="">All colors</option>
                        <?php foreach ($colorOptions as $col): ?>
                        <option value="<?php echo $col['id']; ?>"
                                <?php echo $colorFilter === (int)$col['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($col['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <?php if ($searchQuery !== '' || $hasActiveFilters): ?>
                    <a href="/new-stock-system/index.php?page=coils<?php echo $category ? '&category=' . $category : ''; ?>"
                       class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x"></i> Reset
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Coils Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-box-seam"></i> Coils List (<?php echo $totalCoils; ?> total)
        </div>
        <div class="card-body p-0">
            <?php if (empty($coils)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i>
                <?php if ($searchQuery !== '' || $hasActiveFilters): ?>
                    No coils match the current filters.
                    <a href="/new-stock-system/index.php?page=coils<?php echo $category ? '&category=' . $category : ''; ?>">
                        Clear filters
                    </a>
                <?php else: ?>
                    No coils found.
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
                            <th>Weight (kg)</th>
                            <th>Meters / Pallet Size</th>
                            <th>Gauge</th>
                            <th>Category</th>
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
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($coil['net_weight'], 2); ?></td>
                            <td>
                                <?php if ($coil['category'] === STOCK_CATEGORY_KZINC): ?>
                                    <?php if (!empty($coil['pallet_size'])): ?>
                                        <span class="badge bg-secondary"><?php echo (int)$coil['pallet_size']; ?> bdl/pallet</span>
                                    <?php else: ?>
                                        <span class="text-muted">No pallet size</span>
                                    <?php endif; ?>
                                <?php elseif (!empty($coil['meters']) && $coil['meters'] > 0): ?>
                                    <?php echo number_format($coil['meters'], 2); ?>m
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($coil['gauge'])): ?>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($coil['gauge']); ?></span>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo STOCK_CATEGORIES[$coil['category']] ?? $coil['category']; ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $status = $coil['status'] ?? 'available';
                                $statusText = STOCK_STATUSES[$status] ?? ucfirst(str_replace('_', ' ', $status));
                                ?>
                                <span class="badge <?php echo getStatusBadgeClass($status); ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW)): ?>
                                    <a href="/new-stock-system/index.php?page=coils_view&id=<?php echo $coil['id']; ?>"
                                       class="btn btn-info btn-sm" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT)): ?>
                                    <a href="/new-stock-system/index.php?page=coils_edit&id=<?php echo $coil['id']; ?>"
                                       class="btn btn-warning btn-sm" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>

                                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_DELETE)): ?>
                                    <form method="POST"
                                          action="/new-stock-system/controllers/coils/delete/index.php"
                                          style="display:inline-block;"
                                          onsubmit="return confirm('Are you sure you want to delete this coil?');">
                                        <input type="hidden" name="id" value="<?php echo $coil['id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
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
        <?php if ($totalCoils > RECORDS_PER_PAGE): ?>
        <div class="card-footer">
            <?php
            $queryParams = ['page' => 'coils'];
            if ($category)      $queryParams['category'] = $category;
            if ($searchQuery !== '') $queryParams['search'] = $searchQuery;
            if ($statusFilter)  $queryParams['status']   = $statusFilter;
            if ($gaugeFilter)   $queryParams['gauge']    = $gaugeFilter;
            if ($colorFilter)   $queryParams['color_id'] = $colorFilter;
            include __DIR__ . '/../../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
