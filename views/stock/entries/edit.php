<?php
/**
 * Edit Stock Entry Form
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Edit Stock Entry - ' . APP_NAME;

$entryId = (int)($_GET['id'] ?? 0);

if ($entryId <= 0) {
    setFlashMessage('error', 'Invalid stock entry ID.');
    header('Location: /new-stock-system/index.php?page=stock_entries');
    exit();
}

$stockEntryModel = new StockEntry();
$entry = $stockEntryModel->findById($entryId);

if (!$entry) {
    setFlashMessage('error', 'Stock entry not found.');
    header('Location: /new-stock-system/index.php?page=stock_entries');
    exit();
}

$isKzincEntry = ($entry['unit_type'] ?? 'meters') !== 'meters';

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Stock Entry</h1>
                <p class="text-muted">Update stock entry</p>
            </div>
            <a href="/new-stock-system/index.php?page=stock_entries" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Stock Entries
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Stock Entry Information
                </div>
                <div class="card-body">
                    <!-- Summary info bar -->
                    <div class="alert alert-info mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Coil:</strong> <?php echo htmlspecialchars($entry['coil_code']); ?> - <?php echo htmlspecialchars($entry['coil_name']); ?>
                            </div>
                            <?php if ($isKzincEntry): ?>
                            <div class="col-md-3">
                                <strong>Total:</strong> <?php echo number_format($entry['quantity'] ?? 0, 2); ?> <?php echo htmlspecialchars($entry['unit_type']); ?>
                            </div>
                            <div class="col-md-3">
                                <strong>Pieces Remaining:</strong> <?php echo (int)($entry['pieces_remaining'] ?? 0); ?> pcs
                            </div>
                            <?php else: ?>
                            <div class="col-md-3">
                                <strong>Current Meters:</strong> <?php echo number_format($entry['meters'], 2); ?>m
                            </div>
                            <div class="col-md-3">
                                <strong>Remaining:</strong> <?php echo number_format($entry['meters_remaining'], 2); ?>m
                            </div>
                            <?php if (array_key_exists('weight_kg', $entry)): ?>
                            <div class="col-md-3 mt-2">
                                <strong>Weight (KG):</strong>
                                <?php echo isset($entry['weight_kg']) ? number_format($entry['weight_kg'], 2) . ' kg' : 'Not set'; ?>
                            </div>
                            <div class="col-md-3 mt-2">
                                <strong>Remaining Weight:</strong>
                                <?php
                                if (isset($entry['weight_kg_remaining'])) {
                                    echo number_format($entry['weight_kg_remaining'], 2) . ' kg';
                                } elseif (isset($entry['weight_kg'])) {
                                    echo number_format($entry['weight_kg'], 2) . ' kg';
                                } else {
                                    echo 'Not set';
                                }
                                ?>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <form action="/new-stock-system/controllers/stock_entries/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                        <input type="hidden" name="unit_type" value="<?php echo htmlspecialchars($entry['unit_type'] ?? 'meters'); ?>">

                        <?php if ($isKzincEntry): ?>
                        <!-- KZinc fields -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Unit Type</label>
                                <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars(ucfirst($entry['unit_type'])); ?>" readonly>
                                <small class="text-muted">Unit type cannot be changed after creation</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                       step="0.01" min="0.01"
                                       value="<?php echo $entry['quantity'] ?? ''; ?>" required>
                                <small class="form-text text-muted">
                                    Amount in <?php echo htmlspecialchars($entry['unit_type']); ?>
                                    (<?php echo (int)($entry['pieces_total'] ?? 0); ?> pieces total)
                                </small>
                            </div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Note:</strong> Changing the quantity recalculates the total pieces.
                            Pieces already deducted by sales are preserved — you cannot set a quantity that
                            results in fewer total pieces than those already sold.
                        </div>

                        <?php else: ?>
                        <!-- Non-KZinc (meter-based) fields -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meters" class="form-label">Total Meters <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="meters" name="meters"
                                       step="0.01" min="<?php echo $entry['meters'] - $entry['meters_remaining']; ?>"
                                       value="<?php echo $entry['meters']; ?>" required>
                                <div class="invalid-feedback">Please provide meters.</div>
                                <small class="form-text text-muted">
                                    Minimum: <?php echo number_format($entry['meters'] - $entry['meters_remaining'], 2); ?>m
                                    (cannot be less than meters already used)
                                </small>
                            </div>
                            <?php if (array_key_exists('weight_kg', $entry)): ?>
                            <div class="col-md-6 mb-3">
                                <label for="weight_kg" class="form-label">Total Weight (KG)</label>
                                <input type="number" class="form-control" id="weight_kg" name="weight_kg"
                                       step="0.01" min="0"
                                       value="<?php echo $entry['weight_kg'] ?? ''; ?>">
                                <small class="form-text text-muted">
                                    <?php if (isset($entry['weight_kg'])): ?>
                                    Current weight: <?php echo number_format($entry['weight_kg'], 2); ?> kg
                                    <?php else: ?>
                                    Enter weight in kilograms (optional)
                                    <?php endif; ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Note:</strong> Changing the total meters will adjust the remaining meters accordingly.
                            You cannot set it below the amount already used.
                        </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=stock_entries" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Stock Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
