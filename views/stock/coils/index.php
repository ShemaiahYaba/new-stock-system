<?php
/**
 * Coils List View - FINAL WORKING VERSION
 * Replace views/stock/coils/index.php with this file
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Coils - ' . APP_NAME;

// Get filter parameters - SIMPLIFIED
$category = isset($_GET['category']) ? $_GET['category'] : null;
$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$coilModel = new Coil();

// Perform search or regular listing
if ($searchQuery !== '') {
    $coils = $coilModel->search($searchQuery, $category, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    // For search results, count them properly
    $allSearchResults = $coilModel->search($searchQuery, $category, 10000, 0);
    $totalCoils = count($allSearchResults);
} else {
    $coils = $coilModel->getAll($category, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalCoils = $coilModel->count($category);
}

$paginationData = getPaginationData($totalCoils, $currentPage);

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
                        (<?php echo $totalCoils; ?> results)
                    <?php else: ?>
                        (<?php echo $totalCoils; ?> total)
                    <?php endif; ?>
                </p>
            </div>
            <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=coils_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Coil
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Category Filter -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="/new-stock-system/index.php?page=coils<?php echo $searchQuery !== '' ? '&search=' . urlencode($searchQuery) : ''; ?>" 
                   class="btn btn-sm <?php echo !$category ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    All Categories
                </a>
                <?php foreach (STOCK_CATEGORIES as $catKey => $catName): ?>
                <a href="/new-stock-system/index.php?page=coils&category=<?php echo $catKey; ?><?php echo $searchQuery !== '' ? '&search=' . urlencode($searchQuery) : ''; ?>" 
                   class="btn btn-sm <?php echo $category === $catKey ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <?php echo $catName; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Search and Coils Table -->
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-box-seam"></i> Coils List
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="coils">
                        <?php if ($category): ?>
                        <input type="hidden" name="category" value="<?php echo htmlspecialchars($category); ?>">
                        <?php endif; ?>
                        <input type="text" 
                               name="search" 
                               class="form-control form-control-sm me-2" 
                               placeholder="Search by code or name..." 
                               value="<?php echo htmlspecialchars($searchQuery); ?>"
                               autocomplete="off">
                        <button type="submit" class="btn btn-sm btn-primary" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($searchQuery !== ''): ?>
                        <a href="/new-stock-system/index.php?page=coils<?php echo $category ? '&category='.$category : ''; ?>" 
                           class="btn btn-sm btn-secondary ms-2" title="Clear search">
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
                    No coils found matching "<?php echo htmlspecialchars($searchQuery); ?>".
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
                            <th>Net Weight</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Created At</th>
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
                                    <span style="display: inline-block; width: 15px; height: 15px; background-color: <?php echo htmlspecialchars($coil['color_hex'] ?? '#cccccc'); ?>; border: 1px solid #ddd; border-radius: 3px; margin-right: 5px; vertical-align: middle;"></span>
                                    <?php echo htmlspecialchars($coil['color_name']); ?>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo number_format($coil['net_weight'], 2); ?> kg</td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo STOCK_CATEGORIES[$coil['category']] ?? $coil['category']; ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $status = $coil['status'] ?? 'available';
                                $badgeClass = 'badge-secondary';
                                if ($status === 'available') {
                                    $badgeClass = 'bg-success';
                                } elseif ($status === 'factory_use') {
                                    $badgeClass = 'bg-warning';
                                } elseif ($status === 'sold') {
                                    $badgeClass = 'bg-danger';
                                }
                                $statusText = STOCK_STATUSES[$status] ?? ucfirst(str_replace('_', ' ', $status));
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            <td>
                                <?php 
                                if (isset($coil['created_at']) && !empty($coil['created_at'])) {
                                    echo formatDate($coil['created_at']);
                                } else {
                                    echo '<span class="text-muted">N/A</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW)): ?>
                                    <a href="/new-stock-system/index.php?page=coils_view&id=<?php echo $coil['id']; ?>" 
                                       class="btn btn-info btn-sm" 
                                       title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT)): ?>
                                    <a href="/new-stock-system/index.php?page=coils_edit&id=<?php echo $coil['id']; ?>" 
                                       class="btn btn-warning btn-sm" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_DELETE)): ?>
                                    <form method="POST" 
                                          action="/new-stock-system/controllers/coils/delete/index.php" 
                                          style="display: inline-block;" 
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
        <?php if (!empty($coils) && $totalCoils > RECORDS_PER_PAGE): ?>
        <div class="card-footer">
            <?php 
            // Build query params for pagination
            $queryParams = [];
            $queryParams['page'] = 'coils';
            if ($category) $queryParams['category'] = $category;
            if ($searchQuery !== '') $queryParams['search'] = $searchQuery;
            include __DIR__ . '/../../../layout/pagination.php'; 
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>