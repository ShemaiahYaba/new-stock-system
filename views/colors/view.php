<?php
/**
 * Color View Details
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/color.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'View Color - ' . APP_NAME;

$colorId = (int)($_GET['id'] ?? 0);

if ($colorId <= 0) {
    setFlashMessage('error', 'Invalid color ID.');
    header('Location: /new-stock-system/index.php?page=colors');
    exit();
}

$colorModel = new Color();
$color = $colorModel->findById($colorId);

if (!$color) {
    setFlashMessage('error', 'Color not found.');
    header('Location: /new-stock-system/index.php?page=colors');
    exit();
}

// Get coils using this color
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("
    SELECT c.*, u.name as created_by_name 
    FROM coils c
    LEFT JOIN users u ON c.created_by = u.id
    WHERE c.color_id = :color_id AND c.deleted_at IS NULL
    ORDER BY c.created_at DESC
    LIMIT 10
");
$stmt->execute([':color_id' => $color['id']]);
$coils = $stmt->fetchAll();

$stmtCount = $db->prepare("SELECT COUNT(*) as count FROM coils WHERE color_id = :color_id AND deleted_at IS NULL");
$stmtCount->execute([':color_id' => $color['id']]);
$coilCount = $stmtCount->fetch()['count'];

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Color Details</h1>
                <p class="text-muted">View color information and usage</p>
            </div>
            <a href="/new-stock-system/index.php?page=colors" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Colors
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <?php if (!empty($color['hex_code'])): ?>
                        <div class="mb-3 d-flex justify-content-center">
                            <div style="width: 120px; height: 120px; background-color: <?php echo htmlspecialchars($color['hex_code']); ?>; border: 3px solid #ddd; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>
                        </div>
                    <?php else: ?>
                        <i class="bi bi-palette text-primary" style="font-size: 80px;"></i>
                    <?php endif; ?>
                    <h4 class="mt-3"><?php echo htmlspecialchars($color['name']); ?></h4>
                    <p class="text-muted"><code><?php echo htmlspecialchars($color['code']); ?></code></p>
                    <?php if ($color['is_active']): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Color Information
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th><i class="bi bi-tag"></i> Code:</th>
                            <td><code><?php echo htmlspecialchars($color['code']); ?></code></td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-type"></i> Name:</th>
                            <td><?php echo htmlspecialchars($color['name']); ?></td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-palette"></i> Hex Code:</th>
                            <td>
                                <?php if (!empty($color['hex_code'])): ?>
                                    <code><?php echo htmlspecialchars($color['hex_code']); ?></code>
                                <?php else: ?>
                                    <span class="text-muted">Not set</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-toggle-on"></i> Status:</th>
                            <td>
                                <?php if ($color['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Record Info
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Color ID:</th>
                            <td>#<?php echo $color['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDate($color['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?php echo $color['updated_at'] ? formatDate($color['updated_at']) : 'Never'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php if (hasPermission(MODULE_COLOR_MANAGEMENT, ACTION_EDIT)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="/new-stock-system/index.php?page=colors_edit&id=<?php echo $color['id']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Color
                    </a>
                    <?php if ($coilCount == 0 && hasPermission(MODULE_COLOR_MANAGEMENT, ACTION_DELETE)): ?>
                    <form method="POST" action="/new-stock-system/controllers/colors/delete/index.php" 
                          onsubmit="return confirmDelete('Are you sure you want to delete this color?');">
                        <input type="hidden" name="id" value="<?php echo $color['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Delete Color
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-graph-up"></i> Usage Statistics
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 text-primary"><?php echo $coilCount; ?></h3>
                                <small class="text-muted">Total Coils</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 text-success">
                                    <?php if ($color['is_active']): ?>
                                        <i class="bi bi-check-circle"></i>
                                    <?php else: ?>
                                        <i class="bi bi-x-circle text-secondary"></i>
                                    <?php endif; ?>
                                </h3>
                                <small class="text-muted">Status</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 text-info">
                                    <?php echo !empty($color['hex_code']) ? '<i class="bi bi-palette-fill"></i>' : '<i class="bi bi-palette text-muted"></i>'; ?>
                                </h3>
                                <small class="text-muted">Visual Preview</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-box-seam"></i> Coils Using This Color</span>
                        <?php if ($coilCount > 10): ?>
                        <span class="badge bg-primary"><?php echo $coilCount; ?> total</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($coils)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No coils are currently using this color.
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coils as $coil): ?>
                                <tr>
                                    <td><code><?php echo htmlspecialchars($coil['code']); ?></code></td>
                                    <td><?php echo htmlspecialchars($coil['name']); ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo STOCK_CATEGORIES[$coil['category']] ?? $coil['category']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'available' => 'success',
                                            'factory_use' => 'warning',
                                            'sold' => 'info',
                                            'out_of_stock' => 'danger'
                                        ][$coil['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $statusClass; ?>">
                                            <?php echo STOCK_STATUSES[$coil['status']] ?? $coil['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo formatDate($coil['created_at']); ?></td>
                                    <td>
                                        <?php if (hasPermission(MODULE_STOCK_MANAGEMENT, ACTION_VIEW)): ?>
                                        <a href="/new-stock-system/index.php?page=coils_view&id=<?php echo $coil['id']; ?>" 
                                           class="btn btn-sm btn-info" title="View Coil">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <?php if ($coilCount > 10): ?>
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="bi bi-info-circle"></i> 
                        Showing 10 of <?php echo $coilCount; ?> coils. 
                        <a href="/new-stock-system/index.php?page=coils&color_id=<?php echo $color['id']; ?>">
                            View all coils with this color
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($coilCount > 0): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-exclamation-triangle"></i> Deletion Warning
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-shield-exclamation"></i> 
                        <strong>Cannot Delete:</strong> This color cannot be deleted because it is currently being used by <?php echo $coilCount; ?> coil(s). 
                        To delete this color, you must first update or remove all coils using it.
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>