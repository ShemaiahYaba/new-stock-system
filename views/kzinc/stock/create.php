<?php
/**
 * KZinc Stock Entry Create
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Add K-Zinc Stock Entry - ' . APP_NAME;

// Pre-select a coil if passed via query string
$preselectedCoilId = isset($_GET['coil_id']) ? (int)$_GET['coil_id'] : null;

$coilModel  = new Coil();
// Only pallet/sheet coils belong in the K-Zinc stock module
$kzincCoils = array_values(array_filter(
    $coilModel->getAll(STOCK_CATEGORY_KZINC, 1000, 0),
    fn($c) => ($c['kzinc_track_mode'] ?? null) === 'pallets'
));

$selectedCoil = null;
if ($preselectedCoilId) {
    foreach ($kzincCoils as $c) {
        if ($c['id'] === $preselectedCoilId) {
            $selectedCoil = $c;
            break;
        }
    }
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Add K-Zinc Stock Entry</h1>
                <p class="text-muted">Record incoming K-Zinc stock in pallets, bundles, or pieces</p>
            </div>
            <a href="/new-stock-system/index.php?page=kzinc_stock" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to K-Zinc Stock
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header"><i class="bi bi-plus-circle"></i> Stock Entry</div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/stock_entries/create/index.php"
                          method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="redirect_to" value="kzinc_stock">

                        <div class="mb-3">
                            <label for="coil_id" class="form-label">K-Zinc Coil <span class="text-danger">*</span></label>
                            <?php if ($selectedCoil): ?>
                            <input type="hidden" name="coil_id" value="<?php echo $selectedCoil['id']; ?>">
                            <div class="form-control bg-light" style="pointer-events:none;">
                                <strong><?php echo htmlspecialchars($selectedCoil['code']); ?></strong>
                                — <?php echo htmlspecialchars($selectedCoil['name']); ?>
                                <span class="badge bg-secondary ms-2">
                                    <?php echo (int)($selectedCoil['pallet_size'] ?? 0); ?> bdl/pallet
                                </span>
                            </div>
                            <small>
                                <a href="/new-stock-system/index.php?page=kzinc_stock_create">Choose a different coil</a>
                            </small>
                            <?php else: ?>
                            <select class="form-select" id="coil_id" name="coil_id" required>
                                <option value="">— Select a K-Zinc coil —</option>
                                <?php foreach ($kzincCoils as $c): ?>
                                <option value="<?php echo $c['id']; ?>"
                                        data-pallet-size="<?php echo (int)($c['pallet_size'] ?? 0); ?>">
                                    <?php echo htmlspecialchars($c['code']); ?>
                                    — <?php echo htmlspecialchars($c['name']); ?>
                                    (<?php echo (int)($c['pallet_size'] ?? 0); ?> bdl/pallet)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a coil.</div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="unit_type" class="form-label">Unit Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="unit_type" name="unit_type" required>
                                <option value="">— Select unit —</option>
                                <?php foreach (KZINC_UNITS as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a unit type.</div>
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="quantity" name="quantity"
                                   step="0.01" min="0.01" placeholder="e.g., 2" required>
                            <div class="invalid-feedback">Quantity must be greater than 0.</div>
                        </div>

                        <!-- Breakdown preview -->
                        <div id="kzinc_breakdown" class="alert alert-secondary alert-permanent" style="display:none;">
                            <i class="bi bi-calculator"></i>
                            <span id="kzinc_breakdown_text"></span>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="/new-stock-system/index.php?page=kzinc_stock" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><i class="bi bi-info-circle"></i> Unit Hierarchy</div>
                <div class="card-body">
                    <ul class="list-unstyled small text-muted mb-0">
                        <li class="mb-2">
                            <strong>Pallet</strong> → bundles → pieces<br>
                            <span class="text-muted">Pallet size is set on the coil</span>
                        </li>
                        <li class="mb-2">
                            <strong>Bundle</strong> → pieces<br>
                            <span class="text-muted">1 bundle = <?php echo KZINC_PIECES_PER_BUNDLE; ?> pieces (fixed)</span>
                        </li>
                        <li>
                            <strong>Pieces</strong> — enter exact count
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const PIECES_PER_BUNDLE = <?php echo KZINC_PIECES_PER_BUNDLE; ?>;

    const coilSelect     = document.getElementById('coil_id');
    const unitTypeSelect = document.getElementById('unit_type');
    const quantityInput  = document.getElementById('quantity');
    const breakdownBox   = document.getElementById('kzinc_breakdown');
    const breakdownText  = document.getElementById('kzinc_breakdown_text');

    // Pre-selected coil pallet size (when dropdown is hidden)
    let currentPalletSize = <?php echo $selectedCoil ? (int)($selectedCoil['pallet_size'] ?? 0) : 0; ?>;

    if (coilSelect) {
        coilSelect.addEventListener('change', function () {
            const opt = coilSelect.options[coilSelect.selectedIndex];
            currentPalletSize = parseInt(opt.getAttribute('data-pallet-size') || '0', 10);
            updateBreakdown();
        });
    }

    function updateBreakdown() {
        const qty      = parseFloat(quantityInput.value) || 0;
        const unitType = unitTypeSelect.value;

        if (!unitType || qty <= 0) {
            breakdownBox.style.display = 'none';
            return;
        }

        if (unitType === 'pallets' && currentPalletSize <= 0) {
            breakdownText.textContent = 'Pallet size not configured on this coil.';
            breakdownBox.style.display = '';
            return;
        }

        let text = '';

        if (unitType === 'pallets') {
            const bundles = qty * currentPalletSize;
            const pieces  = bundles * PIECES_PER_BUNDLE;
            text = `${qty} pallet(s) → ${bundles} bundles → ${pieces} pieces`;
        } else if (unitType === 'bundles') {
            const pieces = qty * PIECES_PER_BUNDLE;
            text = `${qty} bundle(s) → ${pieces} pieces`;
        } else if (unitType === 'pieces') {
            text = `${qty} piece(s)`;
        }

        breakdownText.textContent = text;
        breakdownBox.style.display = '';
    }

    unitTypeSelect.addEventListener('change', updateBreakdown);
    quantityInput.addEventListener('input', updateBreakdown);
})();
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
