<?php
/**
 * Colors List View - FIXED
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/color.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Color Management - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';

$colorModel = new Color();

// ✅ FIXED: Properly count search results
if (!empty($searchQuery)) {
    $colors = $colorModel->search($searchQuery, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalColors = $colorModel->countSearch($searchQuery); // Use the new countSearch method
} else {
    $colors = $colorModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalColors = $colorModel->count();
}

$paginationData = getPaginationData($totalColors, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Color Management</h1>
                <p class="text-muted">Manage coil colors for the system</p>
            </div>
            <?php if (hasPermission(MODULE_COLOR_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=colors_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Color
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-palette"></i> Colors List (<?= $totalColors ?> total<?= !empty($searchQuery) ? ' - filtered' : '' ?>)
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="colors">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search colors..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="/new-stock-system/index.php?page=colors" class="btn btn-sm btn-secondary ms-2">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($colors)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> 
                <?php if (!empty($searchQuery)): ?>
                    No colors found matching "<?php echo htmlspecialchars($searchQuery); ?>".
                <?php else: ?>
                    No colors found.
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
                            <th>Preview</th>
                            <th>Hex Code</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($colors as $color): ?>
                        <tr>
                            <td><?php echo $color['id']; ?></td>
                            <td><code><?php echo htmlspecialchars($color['code']); ?></code></td>
                            <td><strong><?php echo htmlspecialchars($color['name']); ?></strong></td>
                            <td>
                                <?php if (!empty($color['hex_code'])): ?>
                                    <div style="width: 30px; height: 30px; background-color: <?php echo htmlspecialchars($color['hex_code']); ?>; border: 1px solid #ddd; border-radius: 4px;"></div>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($color['hex_code'])): ?>
                                    <code><?php echo htmlspecialchars($color['hex_code']); ?></code>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($color['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($color['created_by_name'] ?? '-'); ?></td>
                            <td><?php echo formatDate($color['created_at']); ?></td>
                            <td>
                                <?php
                                $id = $color['id'];
                                $module = 'colors';
                                $canView = hasPermission(MODULE_COLOR_MANAGEMENT, ACTION_VIEW);
                                $canEdit = hasPermission(MODULE_COLOR_MANAGEMENT, ACTION_EDIT);
                                $canDelete = hasPermission(MODULE_COLOR_MANAGEMENT, ACTION_DELETE);
                                include __DIR__ . '/../../layout/quick_action_buttons.php';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($colors)): ?>
        <div class="card-footer">
            <?php $queryParams = $_GET; include __DIR__ . '/../../layout/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>