<?php
/**
 * ============================================
 * FILE: views/tiles/designs/index.php
 * List all designs with search and pagination
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/design.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Design Management - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';

$designModel = new Design();

if (!empty($searchQuery)) {
    $designs = $designModel->search($searchQuery, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalDesigns = count($designModel->search($searchQuery, 10000, 0));
} else {
    $designs = $designModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalDesigns = $designModel->count();
}

$paginationData = getPaginationData($totalDesigns, $currentPage);

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Design Management</h1>
                <p class="text-muted">Manage tile designs for products</p>
            </div>
            <?php if (hasPermission(MODULE_DESIGN_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=designs_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Design
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-palette"></i> Designs List (<?= $totalDesigns ?> total)
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="designs">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search designs..." value="<?= htmlspecialchars($searchQuery) ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="/new-stock-system/index.php?page=designs" class="btn btn-sm btn-secondary ms-2">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($designs)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> 
                <?php if (!empty($searchQuery)): ?>
                    No designs found matching "<?= htmlspecialchars($searchQuery) ?>".
                <?php else: ?>
                    No designs found. Create your first design to get started.
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Products</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($designs as $design): ?>
                        <?php
                        $db = Database::getInstance()->getConnection();
                        $stmt = $db->prepare("SELECT COUNT(*) as count FROM tile_products WHERE design_id = ? AND deleted_at IS NULL");
                        $stmt->execute([$design['id']]);
                        $productCount = $stmt->fetch()['count'] ?? 0;
                        ?>
                        <tr>
                            <td>#<?= $design['id'] ?></td>
                            <td><code><?= htmlspecialchars($design['code']) ?></code></td>
                            <td><strong><?= htmlspecialchars($design['name']) ?></strong></td>
                            <td><?= htmlspecialchars(truncateText($design['description'] ?? '-', 50)) ?></td>
                            <td>
                                <?php if ($design['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= $productCount ?> product(s)</span>
                            </td>
                            <td><?= htmlspecialchars($design['created_by_name'] ?? '-') ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <?php if (hasPermission(MODULE_DESIGN_MANAGEMENT, ACTION_EDIT)): ?>
                                    <a href="/new-stock-system/index.php?page=designs_edit&id=<?= $design['id'] ?>" 
                                       class="btn btn-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php endif; ?>
                                    
                                    <?php if (hasPermission(MODULE_DESIGN_MANAGEMENT, ACTION_DELETE) && $productCount == 0): ?>
                                    <form method="POST" action="/new-stock-system/controllers/tiles/designs/delete/index.php" 
                                          style="display: inline;" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="id" value="<?= $design['id'] ?>">
                                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
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
        <?php if (!empty($designs)): ?>
        <div class="card-footer">
            <?php $queryParams = $_GET; include __DIR__ . '/../../../layout/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>


