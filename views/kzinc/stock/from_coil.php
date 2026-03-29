<?php
/**
 * KZinc: Stock from Coil
 * Convert KZinc meter stock into KZinc pallet/bundle/piece stock entries.
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Stock from Coil - K-Zinc - ' . APP_NAME;

$coilModel       = new Coil();
$stockEntryModel = new StockEntry();

// All KZinc coils that have meter-based stock entries with remaining meters
$db = Database::getInstance()->getConnection();
$meterCoils = $db->query(
    "SELECT c.id, c.code, c.name, c.pallet_size,
            COALESCE(SUM(se.meters_remaining), 0) AS available_meters
     FROM coils c
     JOIN stock_entries se ON se.coil_id = c.id
     WHERE c.category = '" . STOCK_CATEGORY_KZINC . "'
       AND c.deleted_at IS NULL
       AND se.deleted_at IS NULL
       AND (se.unit_type = 'meters' OR se.unit_type IS NULL)
       AND se.meters_remaining > 0
     GROUP BY c.id, c.code, c.name, c.pallet_size
     HAVING available_meters > 0
     ORDER BY c.code ASC"
)->fetchAll();

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Stock from Coil</h1>
                <p class="text-muted">Convert K-Zinc coil meter stock into pallets, bundles, or pieces</p>
            </div>
            <a href="/new-stock-system/index.php?page=kzinc_stock" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to K-Zinc Stock
            </a>
        </div>
    </div>

    <?php if (empty($meterCoils)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i>
        No K-Zinc coil meter stock available. Add meter-based stock entries through
        <a href="/new-stock-system/index.php?page=stock_entries">Stock Management</a> first.
    </div>
    <?php else: ?>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header fw-bold">
                    <i class="bi bi-arrow-left-right"></i> Convert Coil Meters to Pallet Stock
                </div>
                <div class="card-body">
                    <form method="POST"
                          action="/new-stock-system/controllers/kzinc/stock/from_coil/index.php"
                          onsubmit="return confirm('Convert meter stock into pallet stock? This will deduct meters from the selected coil entry.');">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Source K-Zinc Coil <span class="text-danger">*</span></label>
                            <select class="form-select" name="coil_id" id="sourceCoilId" required
                                    onchange="loadCoilEntries(this.value)">
                                <option value="">— Select a K-Zinc coil —</option>
                                <?php foreach ($meterCoils as $c): ?>
                                <option value="<?php echo $c['id']; ?>"
                                        data-pallet="<?php echo $c['pallet_size']; ?>">
                                    <?php echo htmlspecialchars($c['code']); ?> — <?php echo htmlspecialchars($c['name']); ?>
                                    (<?php echo number_format($c['available_meters'], 2); ?> m available)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3" id="entrySelectWrap" style="display:none;">
                            <label class="form-label fw-bold">Source Stock Entry <span class="text-danger">*</span></label>
                            <select class="form-select" name="source_entry_id" id="sourceEntryId" required>
                                <option value="">— Select stock entry —</option>
                            </select>
                            <div class="form-text" id="entryBalanceInfo"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Meters to Consume <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="meters_to_consume"
                                   id="metersToConsume" min="0.01" step="0.01" placeholder="e.g. 25.5" required>
                            <div class="form-text">Meters deducted from the coil entry as factory use.</div>
                        </div>

                        <hr>
                        <p class="text-muted small fw-bold mb-2">Resulting Pallet Stock Entry</p>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Unit Type <span class="text-danger">*</span></label>
                                <select class="form-select" name="unit_type" id="unitType" required
                                        onchange="updatePalletHint()">
                                    <option value="<?php echo STOCK_UNIT_PALLETS; ?>">Pallets</option>
                                    <option value="<?php echo STOCK_UNIT_BUNDLES; ?>">Bundles</option>
                                    <option value="<?php echo STOCK_UNIT_PIECES; ?>">Pieces</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" name="quantity"
                                       id="resultQty" min="1" step="1" placeholder="e.g. 4" required
                                       oninput="updatePalletHint()">
                            </div>
                        </div>

                        <div class="alert alert-info mt-3 small" id="palletHint" style="display:none;"></div>

                        <button type="submit" class="btn btn-primary w-100 mt-3">
                            <i class="bi bi-arrow-left-right"></i> Convert & Add Pallet Stock
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><i class="bi bi-info-circle"></i> How This Works</div>
                <div class="card-body small text-muted">
                    <ol class="ps-3">
                        <li class="mb-2">Select the source KZinc coil that has meter stock.</li>
                        <li class="mb-2">Choose which stock entry to cut from.</li>
                        <li class="mb-2">Enter how many meters will be consumed.</li>
                        <li class="mb-2">Enter the resulting pallet/bundle/piece quantity.</li>
                        <li class="mb-2">On submit:
                            <ul>
                                <li>Meters are deducted from the coil stock (factory use).</li>
                                <li>A new K-Zinc pallet stock entry is created.</li>
                                <li>Both movements are logged in the stock ledger.</li>
                            </ul>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<script>
const coilEntries = <?php
    $entriesMap = [];
    foreach ($meterCoils as $mc) {
        $stmt = $db->prepare(
            "SELECT id, meters_remaining, created_at FROM stock_entries
             WHERE coil_id = ? AND deleted_at IS NULL
               AND (unit_type = 'meters' OR unit_type IS NULL)
               AND meters_remaining > 0
             ORDER BY created_at ASC"
        );
        $stmt->execute([$mc['id']]);
        $entriesMap[$mc['id']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($entriesMap);
?>;

function loadCoilEntries(coilId) {
    const wrap    = document.getElementById('entrySelectWrap');
    const sel     = document.getElementById('sourceEntryId');
    const info    = document.getElementById('entryBalanceInfo');
    sel.innerHTML = '<option value="">— Select stock entry —</option>';

    const entries = coilEntries[coilId] || [];
    if (!coilId || entries.length === 0) { wrap.style.display = 'none'; return; }

    entries.forEach(e => {
        const opt = document.createElement('option');
        opt.value = e.id;
        opt.textContent = `Entry #${e.id} — ${parseFloat(e.meters_remaining).toFixed(2)} m remaining (${e.created_at.substring(0, 10)})`;
        opt.dataset.balance = e.meters_remaining;
        sel.appendChild(opt);
    });
    wrap.style.display = '';
    sel.addEventListener('change', () => {
        const bal = sel.options[sel.selectedIndex]?.dataset.balance;
        info.textContent = bal ? `Available: ${parseFloat(bal).toFixed(2)} m` : '';
    });
    updatePalletHint();
}

function updatePalletHint() {
    const coilOpt    = document.getElementById('sourceCoilId');
    const palletSize = parseInt(coilOpt.options[coilOpt.selectedIndex]?.dataset.pallet || '0');
    const unitType   = document.getElementById('unitType').value;
    const qty        = parseInt(document.getElementById('resultQty').value) || 0;
    const hint       = document.getElementById('palletHint');
    const pcsPerBundle = <?php echo KZINC_PIECES_PER_BUNDLE; ?>;

    if (unitType === 'pallets' && palletSize > 0 && qty > 0) {
        const bundles = qty * palletSize;
        const pieces  = bundles * pcsPerBundle;
        hint.textContent = `${qty} pallet(s) = ${bundles} bundles = ${pieces} pcs (pallet size: ${palletSize} bundles/pallet)`;
        hint.style.display = '';
    } else if (unitType === 'bundles' && qty > 0) {
        hint.textContent = `${qty} bundle(s) = ${qty * pcsPerBundle} pcs`;
        hint.style.display = '';
    } else {
        hint.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
