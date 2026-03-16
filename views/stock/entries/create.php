<?php
/**
 * Create Stock Entry Form
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Create Stock Entry - ' . APP_NAME;

$coilId = isset($_GET['coil_id']) ? (int)$_GET['coil_id'] : null;

// Get available coils — exclude KZinc (managed via the K-Zinc module)
$coilModel = new Coil();
$coils = array_filter(
    $coilModel->getAll(null, 1000, 0),
    fn($c) => $c['category'] !== STOCK_CATEGORY_KZINC
);

// If coil_id is provided, get that specific coil
$selectedCoil = null;
if ($coilId) {
    $selectedCoil = $coilModel->findById($coilId);
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create Stock Entry</h1>
                <p class="text-muted">Add stock to a coil</p>
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
                    <i class="bi bi-plus-circle"></i> Stock Entry Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/stock_entries/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="coil_id" class="form-label">Select Coil <span class="text-danger">*</span></label>
                            <select class="form-select" id="coil_id" name="coil_id" required <?php echo $selectedCoil ? 'disabled' : ''; ?>>
                                <option value="">Select a coil</option>
                                <?php foreach ($coils as $coil): ?>
                                <option value="<?php echo $coil['id']; ?>"
                                        data-category="<?php echo htmlspecialchars($coil['category']); ?>"
                                        data-pallet-size="<?php echo (int)($coil['pallet_size'] ?? 0); ?>"
                                        <?php echo ($selectedCoil && $selectedCoil['id'] == $coil['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($coil['code']); ?> -
                                    <?php echo htmlspecialchars($coil['name']); ?>
                                    (<?php echo STOCK_CATEGORIES[$coil['category']]; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($selectedCoil): ?>
                            <input type="hidden" name="coil_id" value="<?php echo $selectedCoil['id']; ?>">
                            <?php endif; ?>
                            <div class="invalid-feedback">Please select a coil.</div>
                        </div>
                        
                        <?php if ($selectedCoil): ?>
                        <div class="alert alert-info">
                            <strong>Selected Coil:</strong> <?php echo htmlspecialchars($selectedCoil['code']); ?> - 
                            <?php echo htmlspecialchars($selectedCoil['name']); ?><br>
                            <strong>Status:</strong> <span class="badge <?php echo getStatusBadgeClass($selectedCoil['status']); ?>">
                                <?php echo STOCK_STATUSES[$selectedCoil['status']]; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Non-KZinc fields (meters + weight) -->
                        <div id="meters_section">
                            <div class="mb-3">
                                <label for="meters" class="form-label">Meters <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="meters" name="meters"
                                       step="0.01" min="0.01" placeholder="e.g., 500.50">
                                <small class="form-text text-muted">Total meters for this stock entry</small>
                            </div>
                            <div class="mb-3">
                                <label for="weight_kg" class="form-label">Weight (KG)</label>
                                <input type="number" class="form-control" id="weight_kg" name="weight_kg"
                                       step="0.01" min="0" placeholder="e.g., 2850.00">
                                <small class="form-text text-muted">Weight (kg)</small>
                            </div>
                        </div>

                        <!-- KZinc fields (unit type + quantity) -->
                        <div id="kzinc_section" style="display:none;">
                            <div class="mb-3">
                                <label for="unit_type" class="form-label">Unit Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="unit_type" name="unit_type">
                                    <option value="">-- Select Unit --</option>
                                    <?php foreach (KZINC_UNITS as $key => $label): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity"
                                       step="0.01" min="0.01" placeholder="e.g., 2">
                                <small class="form-text text-muted">Amount in the selected unit</small>
                            </div>
                            <div id="kzinc_breakdown" class="alert alert-secondary alert-permanent" style="display:none;">
                                <i class="bi bi-calculator"></i>
                                <span id="kzinc_breakdown_text"></span>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i>
                            <strong>Note:</strong> Stock entries track available stock per coil.
                            The remaining quantity will be deducted as sales are made.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=stock_entries" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Stock Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Stock Entry Info
                </div>
                <div class="card-body">
                    <h6>What is a Stock Entry?</h6>
                    <p class="small text-muted">
                        A stock entry records available stock for a coil.
                        For aluminum/alusteel, stock is tracked in meters.
                        For KZinc, stock is tracked in pallets, bundles, or pieces.
                    </p>

                    <h6 class="mt-3">KZinc Units</h6>
                    <ul class="small text-muted">
                        <li><strong>Pallets</strong> → bundles → pieces</li>
                        <li>Each bundle = <?php echo KZINC_PIECES_PER_BUNDLE; ?> pieces</li>
                        <li>Pallet size varies per coil</li>
                    </ul>

                    <h6 class="mt-3">Workflow</h6>
                    <ol class="small text-muted">
                        <li>Create a coil</li>
                        <li>Add a stock entry</li>
                        <li>Sell from available stock</li>
                        <li>Track remaining quantity</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const KZINC_CATEGORY     = '<?php echo STOCK_CATEGORY_KZINC; ?>';
    const PIECES_PER_BUNDLE  = <?php echo KZINC_PIECES_PER_BUNDLE; ?>;

    const coilSelect      = document.getElementById('coil_id');
    const metersSection   = document.getElementById('meters_section');
    const kzincSection    = document.getElementById('kzinc_section');
    const metersInput     = document.getElementById('meters');
    const unitTypeSelect  = document.getElementById('unit_type');
    const quantityInput   = document.getElementById('quantity');
    const breakdownBox    = document.getElementById('kzinc_breakdown');
    const breakdownText   = document.getElementById('kzinc_breakdown_text');

    let currentPalletSize = 0;

    function updateBreakdown() {
        const qty      = parseFloat(quantityInput.value) || 0;
        const unitType = unitTypeSelect.value;

        if (!unitType) {
            breakdownBox.style.display = 'none';
            return;
        }

        if (unitType === 'pallets' && currentPalletSize <= 0) {
            breakdownBox.style.display = 'none';
            return;
        }

        let pieces = 0, bundles = 0, pallets = 0, text = '';

        if (unitType === 'pallets') {
            pallets = qty;
            bundles = qty * currentPalletSize;
            pieces  = bundles * PIECES_PER_BUNDLE;
            text    = `${pallets} pallet(s) → ${bundles} bundles → ${pieces} pieces`;
        } else if (unitType === 'bundles') {
            bundles = qty;
            pieces  = bundles * PIECES_PER_BUNDLE;
            text    = `${bundles} bundle(s) → ${pieces} pieces`;
        } else if (unitType === 'pieces') {
            pieces = qty;
            text   = `${pieces} piece(s)`;
        }

        breakdownText.textContent = text;
        breakdownBox.style.display = '';
    }

    function onCoilChange() {
        const selected = coilSelect.options[coilSelect.selectedIndex];
        const category   = selected ? selected.getAttribute('data-category') : '';
        currentPalletSize = selected ? parseInt(selected.getAttribute('data-pallet-size') || '0', 10) : 0;
        const isKzinc = category === KZINC_CATEGORY;

        metersSection.style.display = isKzinc ? 'none' : '';
        kzincSection.style.display  = isKzinc ? '' : 'none';

        metersInput.required    = !isKzinc;
        unitTypeSelect.required = isKzinc;
        quantityInput.required  = isKzinc;

        breakdownBox.style.display = 'none';
        updateBreakdown();
    }

    coilSelect.addEventListener('change', onCoilChange);
    unitTypeSelect.addEventListener('change', updateBreakdown);
    quantityInput.addEventListener('input', updateBreakdown);

    // Run on page load in case a coil is pre-selected
    onCoilChange();
})();
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
