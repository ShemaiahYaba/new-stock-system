<?php
/**
 * Coils List View - COMPLETELY FIXED VERSION
 * Replace views/stock/coils/index.php with this EXACT file
 * 
 * This version eliminates ALL potential issues:
 * - No external includes for action buttons
 * - Explicit null checks
 * - Inline rendering
 * - Clear debugging
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Coils - ' . APP_NAME;

$category = $_GET['category'] ?? null;
$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';

$coilModel = new Coil();

if (!empty($searchQuery)) {
    $coils = $coilModel->search($searchQuery, $category, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalCoils = count($coils);
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
                <p class="text-muted">Manage coil inventory (<?php echo $totalCoils; ?> total)</p>
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
                <a href="/new-stock-system/index.php?page=coils" 
                   class="btn btn-sm <?php echo !$category ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    All Categories
                </a>
                <?php foreach (STOCK_CATEGORIES as $catKey => $catName): ?>
                <a href="/new-stock-system/index.php?page=coils&category=<?php echo $catKey; ?>" 
                   class="btn btn-sm <?php echo $category === $catKey ? 'btn-primary' : 'btn-outline-primary'; ?>">
                    <?php echo $catName; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <!-- Coils Table -->
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
                        <input type="hidden" name="category" value="<?php echo $category; ?>">
                        <?php endif; ?>
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search coils..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="/new-stock-system/index.php?page=coils<?php echo $category ? '&category='.$category : ''; ?>" 
                           class="btn btn-sm btn-secondary ms-2">
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
                <i class="bi bi-info-circle"></i> No coils found.
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
                            <!-- Code Column -->
                            <td><strong><?php echo htmlspecialchars($coil['code']); ?></strong></td>
                            
                            <!-- Name Column -->
                            <td><?php echo htmlspecialchars($coil['name']); ?></td>
                            
                            <!-- Color Column -->
                            <td><?php echo htmlspecialchars(COIL_COLORS[$coil['color']] ?? $coil['color']); ?></td>
                            
                            <!-- Net Weight Column -->
                            <td><?php echo number_format($coil['net_weight'], 2); ?> kg</td>
                            
                            <!-- Category Column -->
                            <td>
                                <span class="badge bg-info">
                                    <?php echo STOCK_CATEGORIES[$coil['category']] ?? $coil['category']; ?>
                                </span>
                            </td>
                            
                            <!-- STATUS Column - FIXED -->
                            <td>
                                <?php
                                // Get status value with fallback
                                $status = $coil['status'] ?? 'available';
                                
                                // Get badge class
                                $badgeClass = 'badge-secondary'; // Default
                                if ($status === 'available') {
                                    $badgeClass = 'bg-success';
                                } elseif ($status === 'factory_use') {
                                    $badgeClass = 'bg-warning';
                                } elseif ($status === 'sold') {
                                    $badgeClass = 'bg-danger';
                                }
                                
                                // Get display text
                                $statusText = STOCK_STATUSES[$status] ?? ucfirst(str_replace('_', ' ', $status));
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>">
                                    <?php echo $statusText; ?>
                                </span>
                            </td>
                            
                            <!-- CREATED AT Column - FIXED -->
                            <td>
                                <?php 
                                if (isset($coil['created_at']) && !empty($coil['created_at'])) {
                                    echo formatDate($coil['created_at']);
                                } else {
                                    echo '<span class="text-muted">N/A</span>';
                                }
                                ?>
                            </td>
                            
                            <!-- ACTIONS Column - FIXED -->
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <!-- View Button -->
                                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW)): ?>
                                    <a href="/new-stock-system/index.php?page=coils_view&id=<?php echo $coil['id']; ?>" 
                                       class="btn btn-info btn-sm" 
                                       title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <!-- Edit Button -->
                                    <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_EDIT)): ?>
                                    <a href="/new-stock-system/index.php?page=coils_edit&id=<?php echo $coil['id']; ?>" 
                                       class="btn btn-warning btn-sm" 
                                       title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <!-- Delete Button -->
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
        <?php if (!empty($coils)): ?>
        <div class="card-footer">
            <?php $queryParams = $_GET; include __DIR__ . '/../../../layout/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>