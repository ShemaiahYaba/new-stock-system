<?php
/**
 * K-Zinc Coil Detail View
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'K-Zinc Coil Details - ' . APP_NAME;

$coilId = (int)($_GET['id'] ?? 0);

if ($coilId <= 0) {
    setFlashMessage('error', 'Invalid coil ID.');
    header('Location: /new-stock-system/index.php?page=kzinc_coils');
    exit();
}

$coilModel      = new Coil();
$colorModel     = new Color();
$stockEntryModel = new StockEntry();

$coil = $coilModel->findById($coilId);

if (!$coil || $coil['category'] !== STOCK_CATEGORY_KZINC) {
    setFlashMessage('error', 'K-Zinc coil not found.');
    header('Location: /new-stock-system/index.php?page=kzinc_coils');
    exit();
}

$color = !empty($coil['color_id']) ? $colorModel->findById($coil['color_id']) : null;

// All entries for this coil
$allEntries      = $stockEntryModel->getByCoilId($coilId, 100, 0);
// Available entries (pieces_remaining > 0)
$activeEntries   = $stockEntryModel->getAvailableKzincEntries($coilId);
$totalPieces     = array_sum(array_column($activeEntries, 'pieces_remaining'));

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">K-Zinc Coil</h1>
                <p class="text-muted"><?php echo htmlspecialchars($coil['code']); ?> — <?php echo htmlspecialchars($coil['name']); ?></p>
            </div>
            <a href="/new-stock-system/index.php?page=kzinc_coils" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left: summary card + actions -->
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-layers text-primary" style="font-size:72px;"></i>
                    <h4 class="mt-2"><?php echo htmlspecialchars($coil['code']); ?></h4>
                    <p class="text-muted mb-1"><?php echo htmlspecialchars($coil['name']); ?></p>
                    <span class="badge <?php echo getStatusBadgeClass($coil['status']); ?>">
                        <?php echo STOCK_STATUSES[$coil['status']] ?? $coil['status']; ?>
                    </span>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><i class="bi bi-info-circle"></i> Details</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tr>
                            <th class="ps-3">Color</th>
                            <td>
                                <?php if ($color): ?>
                                    <?php if (!empty($color['hex_code'])): ?>
                                    <span style="display:inline-block;width:14px;height:14px;background:<?php echo htmlspecialchars($color['hex_code']); ?>;border:1px solid #ddd;border-radius:3px;margin-right:4px;vertical-align:middle;"></span>
                                    <?php endif; ?>
                                    <?php echo htmlspecialchars($color['name']); ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-3">Net Weight</th>
                            <td><?php echo number_format($coil['net_weight'], 2); ?> kg</td>
                        </tr>
                        <tr>
                            <th class="ps-3">Pallet Size</th>
                            <td>
                                <?php if (!empty($coil['pallet_size'])): ?>
                                    <strong><?php echo (int)$coil['pallet_size']; ?></strong> bdl/pallet
                                <?php else: ?>
                                    <span class="text-warning">Not set</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-3">Pieces/Bundle</th>
                            <td><?php echo KZINC_PIECES_PER_BUNDLE; ?></td>
                        </tr>
                        <tr>
                            <th class="ps-3">Available Pcs</th>
                            <td>
                                <span class="badge <?php echo $totalPieces > 0 ? 'bg-success' : 'bg-secondary'; ?> fs-6">
                                    <?php echo number_format($totalPieces); ?> pcs
                                </span>
                            </td>
                        </tr>
                        <?php if (!empty($coil['gauge'])): ?>
                        <tr>
                            <th class="ps-3">Gauge</th>
                            <td><?php echo htmlspecialchars($coil['gauge']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th class="ps-3">Created</th>
                            <td><?php echo formatDate($coil['created_at']); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header"><i class="bi bi-gear"></i> Actions</div>
                <div class="card-body d-grid gap-2">
                    <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_EDIT)): ?>
                    <a href="/new-stock-system/index.php?page=kzinc_coils_edit&id=<?php echo $coil['id']; ?>"
                       class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Coil
                    </a>
                    <?php endif; ?>
                    <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_CREATE)): ?>
                    <a href="/new-stock-system/index.php?page=kzinc_stock_create&coil_id=<?php echo $coil['id']; ?>"
                       class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Stock Entry
                    </a>
                    <a href="/new-stock-system/index.php?page=kzinc_sales_create&coil_id=<?php echo $coil['id']; ?>"
                       class="btn btn-primary">
                        <i class="bi bi-cart-plus"></i> New Sale
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Right: stock entries -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-list-check"></i> Stock Entries</span>
                    <?php if (hasPermission(MODULE_KZINC_MANAGEMENT, ACTION_CREATE)): ?>
                    <a href="/new-stock-system/index.php?page=kzinc_stock_create&coil_id=<?php echo $coil['id']; ?>"
                       class="btn btn-sm btn-success">
                        <i class="bi bi-plus-circle"></i> Add Entry
                    </a>
                    <?php endif; ?>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($allEntries)): ?>
                    <div class="alert alert-info m-3">
                        <i class="bi bi-info-circle"></i> No stock entries yet.
                        <a href="/new-stock-system/index.php?page=kzinc_stock_create&coil_id=<?php echo $coil['id']; ?>">Add one now.</a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Quantity</th>
                                    <th>Total Pcs</th>
                                    <th>Remaining Pcs</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($allEntries as $entry): ?>
                                <tr>
                                    <td><small class="text-muted">#<?php echo $entry['id']; ?></small></td>
                                    <td>
                                        <?php echo number_format($entry['quantity'] ?? 0, 0); ?>
                                        <?php echo htmlspecialchars($entry['unit_type'] ?? ''); ?>
                                    </td>
                                    <td><?php echo number_format($entry['pieces_total'] ?? 0); ?> pcs</td>
                                    <td>
                                        <?php $rem = (int)($entry['pieces_remaining'] ?? 0); ?>
                                        <span class="badge <?php echo $rem > 0 ? 'bg-success' : 'bg-secondary'; ?>">
                                            <?php echo number_format($rem); ?> pcs
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo getStatusBadgeClass($entry['status'] ?? 'available'); ?>">
                                            <?php echo STOCK_STATUSES[$entry['status']] ?? ucfirst($entry['status'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td><small><?php echo formatDate($entry['created_at']); ?></small></td>
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
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
