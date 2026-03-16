<?php
/**
 * KZinc New Sale — Multi-Color Production Workflow
 *
 * One KZinc coil can cover multiple colour variations in a single
 * production order. Each row is: colour label + bundles + price/bundle.
 * Pieces are computed automatically (bundles × KZINC_PIECES_PER_BUNDLE).
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/customer.php';
require_once __DIR__ . '/../../../models/warehouse.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'New K-Zinc Sale - ' . APP_NAME;

$customerModel   = new Customer();
$warehouseModel  = new Warehouse();
$coilModel       = new Coil();
$stockEntryModel = new StockEntry();

$customers  = $customerModel->getAll(1000, 0);
$warehouses = $warehouseModel->getActive();
$kzincCoils = $coilModel->getAll(STOCK_CATEGORY_KZINC, 1000, 0);

// Build two coil datasets:
//   $coilsData    — live sales: only coils with pieces remaining
//   $allCoilsData — historical entry: all KZinc coils regardless of stock
$coilsData    = [];
$allCoilsData = [];
foreach ($kzincCoils as $c) {
    $entries         = $stockEntryModel->getAvailableKzincEntries($c['id']);
    $availablePieces = (int)array_sum(array_column($entries, 'pieces_remaining'));
    $coilRow = [
        'id'               => (int)$c['id'],
        'code'             => $c['code'],
        'name'             => $c['name'],
        'pallet_size'      => (int)($c['pallet_size'] ?? 0),
        'available_pieces' => $availablePieces,
        'status'           => $c['status'],
    ];
    $allCoilsData[] = $coilRow;
    if ($availablePieces > 0) {
        $coilsData[] = $coilRow;
    }
}

// Colors for the colour-variant picker
$db     = Database::getInstance()->getConnection();
$colors = $db->query("SELECT id, name, hex_code FROM colors WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<style>
.kz-row {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 14px 16px;
    margin-bottom: 10px;
    position: relative;
}
.kz-row .remove-row {
    position: absolute;
    top: 10px;
    right: 10px;
}
.kz-total-box {
    background: #fff8e1;
    border: 2px solid #ffc107;
    border-radius: 8px;
    padding: 18px 22px;
}
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">New K-Zinc Sale</h1>
                <p class="text-muted">Create a production order for a K-Zinc coil</p>
            </div>
            <a href="/new-stock-system/index.php?page=kzinc_sales" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to K-Zinc Sales
            </a>
        </div>
    </div>

    <!-- Historical sale banner (shown when checkbox is checked) -->
    <div id="historicalBanner" class="alert alert-warning alert-permanent py-2 mb-3" style="display:none;">
        <i class="bi bi-clock-history"></i>
        <strong>Historical Entry Mode</strong> — This sale will be recorded for bookkeeping only.
        Stock levels will <strong>not</strong> be deducted.
    </div>

    <form id="kzincSaleForm">
        <input type="hidden" id="csrf_token" value="<?php echo generateCsrfToken(); ?>">
        <input type="hidden" id="is_historical" value="0">

        <!-- Historical sale toggle -->
        <div class="card mb-3 border-warning">
            <div class="card-body py-2 px-3">
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="historicalToggle" role="switch">
                    <label class="form-check-label" for="historicalToggle">
                        <strong>Record historical sale</strong>
                        <span class="text-muted small ms-2">— Sale already happened. Log for records only, do not deduct from stock.</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="row g-3">

            <!-- ── LEFT COLUMN ── -->
            <div class="col-lg-8">

                <!-- Section 1: Order Info -->
                <div class="card mb-3">
                    <div class="card-header fw-bold">
                        <i class="bi bi-info-circle"></i> 1. Order Information
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer_id" required>
                                    <option value="">— Select customer —</option>
                                    <?php foreach ($customers as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($c['name']); ?>"
                                            data-phone="<?php echo htmlspecialchars($c['phone'] ?? ''); ?>"
                                            data-email="<?php echo htmlspecialchars($c['email'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select class="form-select" id="warehouse_id" required>
                                    <option value="">— Select warehouse —</option>
                                    <?php foreach ($warehouses as $w): ?>
                                    <option value="<?php echo $w['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($w['name']); ?>">
                                        <?php echo htmlspecialchars($w['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">K-Zinc Coil <span class="text-danger">*</span></label>
                                <select class="form-select" id="coil_id" required
                                        <?php echo empty($coilsData) ? 'disabled' : ''; ?>>
                                    <option value="">
                                        <?php echo empty($coilsData) ? '— No coils with available stock —' : '— Select coil —'; ?>
                                    </option>
                                    <?php foreach ($coilsData as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"
                                            data-available="<?php echo $c['available_pieces']; ?>"
                                            data-pallet="<?php echo $c['pallet_size']; ?>"
                                            data-code="<?php echo htmlspecialchars($c['code']); ?>"
                                            data-name="<?php echo htmlspecialchars($c['name']); ?>">
                                        <?php echo htmlspecialchars($c['code']); ?> — <?php echo htmlspecialchars($c['name']); ?>
                                        (<?php echo number_format($c['available_pieces']); ?> pcs available)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div id="coil_stock_info" class="mt-1 small text-muted" style="display:none;"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sale Date</label>
                                <input type="date" class="form-control" id="sale_date"
                                       value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Colour Variations -->
                <div class="card mb-3" id="colourVariationsCard">
                    <div class="card-header d-flex justify-content-between align-items-center fw-bold">
                        <span><i class="bi bi-palette"></i> 2. Colour Variations</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="addRowBtn">
                            <i class="bi bi-plus-circle"></i> Add Row
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive d-none d-md-block mb-2">
                            <table class="table table-sm table-borderless mb-0">
                                <thead class="text-muted small">
                                    <tr>
                                        <th style="width:30%">Colour / Label</th>
                                        <th style="width:18%">Bundles</th>
                                        <th style="width:15%">Pieces</th>
                                        <th style="width:20%">Price / Bundle (₦)</th>
                                        <th style="width:17%">Subtotal (₦)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>

                        <div id="colourRows"></div>

                        <div id="noRowsMsg" class="text-muted small text-center py-3">
                            Click <strong>Add Row</strong> to add a colour variation.
                        </div>
                    </div>
                </div>
                <!-- Section 3: Add-Ons & Additional Charges -->
                <div class="card mb-3" id="addonsCard" style="display:none;">
                    <div class="card-header bg-info text-white fw-bold">
                        <i class="bi bi-plus-square"></i> 3. Add-Ons &amp; Additional Charges
                    </div>
                    <div class="card-body">
                        <p class="text-muted small mb-3">Select optional services and charges to include in the invoice.</p>
                        <div id="addonsContainer">
                            <div class="text-muted small"><i class="bi bi-hourglass-split"></i> Loading add-ons…</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ── RIGHT COLUMN ── -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header fw-bold"><i class="bi bi-receipt"></i> 3. Summary</div>
                    <div class="card-body">
                        <table class="table table-sm mb-0">
                            <tr>
                                <td class="text-muted">Total Bundles</td>
                                <td class="text-end fw-bold" id="summTotalBundles">0</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Total Pieces</td>
                                <td class="text-end fw-bold" id="summTotalPieces">0</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Available Pieces</td>
                                <td class="text-end" id="summAvailable">—</td>
                            </tr>
                            <tr class="table-warning" id="summOverRow" style="display:none;">
                                <td colspan="2" class="text-danger fw-bold text-center">
                                    <i class="bi bi-exclamation-triangle"></i> Exceeds available stock!
                                </td>
                            </tr>
                            <tr class="border-top">
                                <td class="text-muted">Production Subtotal</td>
                                <td class="text-end fw-bold" id="summSubtotal">₦0.00</td>
                            </tr>
                            <tr id="summAddonsRow" style="display:none;">
                                <td class="text-muted">Add-Ons</td>
                                <td class="text-end" id="summAddons">₦0.00</td>
                            </tr>
                            <tr class="border-top table-light">
                                <td class="fw-bold fs-6">Grand Total</td>
                                <td class="text-end fw-bold fs-5" id="summTotal">₦0.00</td>
                            </tr>
                        </table>

                        <div class="mt-3">
                            <label class="form-label small text-muted">Notes (optional)</label>
                            <textarea class="form-control form-control-sm" id="order_notes" rows="2"
                                      placeholder="Any special instructions..."></textarea>
                        </div>

                        <button type="button" class="btn btn-primary w-100 mt-3" id="submitBtn" disabled>
                            <i class="bi bi-check-circle"></i> Confirm & Create Sale
                        </button>
                        <div id="submitError" class="alert alert-danger mt-2 small" style="display:none;"></div>
                        <div id="submitSpinner" class="text-center mt-2" style="display:none;">
                            <div class="spinner-border spinner-border-sm text-primary"></div>
                            <small class="ms-1">Creating sale…</small>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.row -->
    </form>
</div>

<!-- Colour Row Template -->
<template id="rowTemplate">
    <div class="kz-row" data-row-id="">
        <button type="button" class="btn btn-sm btn-outline-danger remove-row" title="Remove row">
            <i class="bi bi-trash"></i>
        </button>
        <div class="row g-2 align-items-center">
            <div class="col-md-3 col-12">
                <label class="form-label small mb-1">Colour / Label</label>
                <select class="form-select form-select-sm colour-select">
                    <option value="">— Pick colour —</option>
                    <?php foreach ($colors as $col): ?>
                    <option value="<?php echo htmlspecialchars($col['name']); ?>"
                            data-hex="<?php echo htmlspecialchars($col['hex_code'] ?? ''); ?>">
                        <?php echo htmlspecialchars($col['name']); ?>
                    </option>
                    <?php endforeach; ?>
                    <option value="__custom__">Other (type below)</option>
                </select>
                <input type="text" class="form-control form-control-sm mt-1 custom-label"
                       placeholder="Custom colour/label" style="display:none;">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small mb-1">Bundles</label>
                <input type="number" class="form-control form-control-sm bundle-input"
                       min="1" step="1" placeholder="0">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small mb-1">Pieces</label>
                <div class="form-control form-control-sm bg-light pieces-display text-center fw-bold">0</div>
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small mb-1">Price / Bundle (₦)</label>
                <input type="number" class="form-control form-control-sm price-input"
                       min="0" step="0.01" placeholder="0.00">
            </div>
            <div class="col-md-2 col-6">
                <label class="form-label small mb-1">Subtotal</label>
                <div class="form-control form-control-sm bg-light subtotal-display fw-bold">₦0.00</div>
            </div>
        </div>
    </div>
</template>

<script>
(function () {
    const PIECES_PER_BUNDLE  = <?php echo KZINC_PIECES_PER_BUNDLE; ?>;
    const KZINC_CATEGORY     = '<?php echo STOCK_CATEGORY_KZINC; ?>';

    // Coil datasets — live (with stock) vs all (for historical entry)
    const liveCoilsData = <?php echo json_encode(array_values($coilsData)); ?>;
    const allCoilsData  = <?php echo json_encode(array_values($allCoilsData)); ?>;

    // ── State ──────────────────────────────────────
    let rowCounter      = 0;
    let currentCoilData = null;
    let availableAddons = [];        // fetched from server
    let selectedAddons  = new Map(); // addonId → { addon, amount }
    let isHistorical    = false;

    // ── DOM refs ───────────────────────────────────
    const coilSelect        = document.getElementById('coil_id');
    const coilStockInfo     = document.getElementById('coil_stock_info');
    const historicalToggle  = document.getElementById('historicalToggle');
    const historicalBanner  = document.getElementById('historicalBanner');
    const isHistoricalInput = document.getElementById('is_historical');
    const addRowBtn       = document.getElementById('addRowBtn');
    const colourRowsEl    = document.getElementById('colourRows');
    const noRowsMsg       = document.getElementById('noRowsMsg');
    const addonsCard      = document.getElementById('addonsCard');
    const addonsContainer = document.getElementById('addonsContainer');

    const summBundles     = document.getElementById('summTotalBundles');
    const summPieces      = document.getElementById('summTotalPieces');
    const summAvailable   = document.getElementById('summAvailable');
    const summOverRow     = document.getElementById('summOverRow');
    const summSubtotal    = document.getElementById('summSubtotal');
    const summAddonsRow   = document.getElementById('summAddonsRow');
    const summAddons      = document.getElementById('summAddons');
    const summTotal       = document.getElementById('summTotal');

    const submitBtn       = document.getElementById('submitBtn');
    const submitError     = document.getElementById('submitError');
    const submitSpinner   = document.getElementById('submitSpinner');
    const rowTemplate     = document.getElementById('rowTemplate');

    // ── Format helpers ─────────────────────────────
    function fmt(n) {
        return '₦' + parseFloat(n || 0).toLocaleString('en', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // ── Historical toggle ──────────────────────────
    function rebuildCoilDropdown(dataset) {
        const currentVal = coilSelect.value;
        coilSelect.innerHTML = '';
        const blank = document.createElement('option');
        blank.value = '';
        blank.textContent = dataset.length === 0 ? '— No coils available —' : '— Select coil —';
        coilSelect.appendChild(blank);
        dataset.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.dataset.available = c.available_pieces;
            opt.dataset.pallet    = c.pallet_size;
            opt.dataset.code      = c.code;
            opt.dataset.name      = c.name;
            opt.textContent = `${c.code} — ${c.name} (${c.available_pieces.toLocaleString()} pcs available)`;
            if (String(c.id) === currentVal) opt.selected = true;
            coilSelect.appendChild(opt);
        });
        coilSelect.disabled = dataset.length === 0;
        // Reset coil state if previous selection no longer in dataset
        const stillValid = dataset.some(c => String(c.id) === currentVal);
        if (!stillValid) {
            currentCoilData = null;
            coilStockInfo.style.display = 'none';
        }
    }

    historicalToggle.addEventListener('change', function () {
        isHistorical = this.checked;
        isHistoricalInput.value = isHistorical ? '1' : '0';
        historicalBanner.style.display = isHistorical ? '' : 'none';
        rebuildCoilDropdown(isHistorical ? allCoilsData : liveCoilsData);
        recalcSummary();
    });

    // ── Coil selection ─────────────────────────────
    coilSelect.addEventListener('change', function () {
        const opt = this.options[this.selectedIndex];
        if (!opt || !opt.value) {
            currentCoilData = null;
            coilStockInfo.style.display = 'none';
            recalcSummary();
            return;
        }
        currentCoilData = {
            id:        parseInt(opt.value),
            code:      opt.dataset.code,
            name:      opt.dataset.name,
            palletSize: parseInt(opt.dataset.pallet || '0'),
            available:  parseInt(opt.dataset.available || '0'),
        };
        coilStockInfo.textContent = `${currentCoilData.available} pieces available`;
        coilStockInfo.style.display = '';
        summAvailable.textContent = currentCoilData.available.toLocaleString() + ' pcs';
        recalcSummary();
    });

    // ── Add-ons loading ────────────────────────────
    async function loadAddons() {
        try {
            const resp = await fetch(
                `/new-stock-system/controllers/production_properties/get_by_category.php?category=${KZINC_CATEGORY}&include_addons=1`
            );
            const data = await resp.json();
            if (data.success) {
                availableAddons = Array.isArray(data.addons) ? data.addons
                    : (data.properties || []).filter(p => p.is_addon == 1);
            }
        } catch (e) {
            availableAddons = [];
        }
        renderAddons();
    }

    function renderAddons() {
        if (availableAddons.length === 0) {
            addonsCard.style.display = 'none';
            return;
        }
        addonsCard.style.display = '';

        const charges     = availableAddons.filter(a => a.is_refundable != 1);
        const adjustments = availableAddons.filter(a => a.is_refundable == 1);

        let html = '';
        if (charges.length > 0) {
            html += renderAddonGroup('Add-On Charges', 'success', 'bi-plus-circle', charges);
        }
        if (adjustments.length > 0) {
            html += renderAddonGroup('Adjustments & Refunds', 'warning', 'bi-dash-circle', adjustments);
        }
        addonsContainer.innerHTML = html;

        // Wire up checkboxes and amount inputs
        addonsContainer.querySelectorAll('.addon-check').forEach(cb => {
            cb.addEventListener('change', onAddonToggle);
        });
        addonsContainer.querySelectorAll('.addon-amount').forEach(inp => {
            inp.addEventListener('input', onAddonAmountChange);
        });
    }

    function renderAddonGroup(title, color, icon, addons) {
        let html = `<h6 class="mb-2 mt-3 text-${color}"><i class="bi ${icon}"></i> ${title}</h6><div class="row">`;
        addons.forEach(addon => {
            const defaultAmt = parseFloat(addon.default_price) || 0;
            const isRefund   = addon.is_refundable == 1;
            const methodLabel = addon.calculation_method === 'percentage'
                ? `${defaultAmt}%` : fmt(defaultAmt);
            html += `
            <div class="col-md-6 col-lg-4 mb-2">
                <div class="card h-100 p-2 border addon-card-item" data-addon-id="${addon.id}">
                    <div class="form-check">
                        <input class="form-check-input addon-check" type="checkbox"
                               id="addon_${addon.id}" value="${addon.id}"
                               data-method="${addon.calculation_method || 'fixed'}"
                               data-default="${defaultAmt}"
                               data-refund="${isRefund ? '1' : '0'}">
                        <label class="form-check-label fw-bold small" for="addon_${addon.id}">
                            ${escHtml(addon.name)}
                        </label>
                    </div>
                    <div class="d-flex align-items-center gap-1 mt-1">
                        <span class="badge bg-secondary text-capitalize" style="font-size:0.7rem;">
                            ${addon.calculation_method || 'fixed'}
                        </span>
                        <small class="text-muted">${methodLabel}</small>
                    </div>
                    <div class="addon-amount-wrapper mt-2" style="display:none;">
                        <label class="form-label small mb-1">Amount (₦)</label>
                        <input type="number" class="form-control form-control-sm addon-amount"
                               data-addon-id="${addon.id}"
                               min="0" step="0.01" placeholder="${defaultAmt}">
                    </div>
                </div>
            </div>`;
        });
        html += '</div>';
        return html;
    }

    function escHtml(str) {
        return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function onAddonToggle(e) {
        const addonId = parseInt(e.target.value);
        const card    = e.target.closest('.addon-card-item');
        const wrapper = card.querySelector('.addon-amount-wrapper');
        const amtInp  = card.querySelector('.addon-amount');

        if (e.target.checked) {
            wrapper.style.display = '';
            const addon      = availableAddons.find(a => a.id == addonId);
            const calcMethod = e.target.dataset.method;
            const defaultAmt = parseFloat(e.target.dataset.default) || 0;
            let amount = calcMethod === 'percentage'
                ? (getProductionSubtotal() * defaultAmt / 100)
                : defaultAmt;
            if (e.target.dataset.refund === '1' && amount > 0) amount = -amount;
            amtInp.value = amount.toFixed(2);
            selectedAddons.set(addonId, { addon, amount });
        } else {
            wrapper.style.display = 'none';
            amtInp.value = '';
            selectedAddons.delete(addonId);
        }
        recalcSummary();
    }

    function onAddonAmountChange(e) {
        const addonId = parseInt(e.target.dataset.addonId);
        const addon   = availableAddons.find(a => a.id == addonId);
        const isRefund = addon?.is_refundable == 1;
        let amount    = parseFloat(e.target.value) || 0;
        if (isRefund && amount > 0) amount = -amount;
        selectedAddons.set(addonId, { addon, amount });
        recalcSummary();
    }

    function getProductionSubtotal() {
        let total = 0;
        colourRowsEl.querySelectorAll('.kz-row').forEach(rowEl => {
            const bundles = parseFloat(rowEl.querySelector('.bundle-input').value) || 0;
            const price   = parseFloat(rowEl.querySelector('.price-input').value)  || 0;
            total += bundles * price;
        });
        return total;
    }

    function getAddonsTotal() {
        let total = 0;
        selectedAddons.forEach(({ amount }) => { total += amount; });
        return total;
    }

    // ── Add row ────────────────────────────────────
    addRowBtn.addEventListener('click', addRow);

    function addRow() {
        rowCounter++;
        const tpl   = rowTemplate.content.cloneNode(true);
        const rowEl = tpl.querySelector('.kz-row');
        rowEl.dataset.rowId = rowCounter;

        rowEl.querySelector('.remove-row').addEventListener('click', () => {
            rowEl.remove();
            refreshNoRowsMsg();
            recalcSummary();
        });

        const colourSel   = rowEl.querySelector('.colour-select');
        const customInput = rowEl.querySelector('.custom-label');
        colourSel.addEventListener('change', () => {
            customInput.style.display = colourSel.value === '__custom__' ? '' : 'none';
        });

        rowEl.querySelector('.bundle-input').addEventListener('input', () => updateRow(rowEl));
        rowEl.querySelector('.price-input').addEventListener('input',  () => updateRow(rowEl));

        colourRowsEl.appendChild(tpl);
        refreshNoRowsMsg();
        recalcSummary();
    }

    function updateRow(rowEl) {
        const bundles  = parseFloat(rowEl.querySelector('.bundle-input').value) || 0;
        const price    = parseFloat(rowEl.querySelector('.price-input').value)  || 0;
        const pieces   = Math.round(bundles * PIECES_PER_BUNDLE);
        const subtotal = bundles * price;

        rowEl.querySelector('.pieces-display').textContent   = pieces.toLocaleString();
        rowEl.querySelector('.subtotal-display').textContent = fmt(subtotal);
        recalcSummary();
    }

    function recalcSummary() {
        let totalBundles = 0, totalPieces = 0;
        const productionSubtotal = getProductionSubtotal();

        colourRowsEl.querySelectorAll('.kz-row').forEach(rowEl => {
            const bundles = parseFloat(rowEl.querySelector('.bundle-input').value) || 0;
            totalBundles += bundles;
            totalPieces  += Math.round(bundles * PIECES_PER_BUNDLE);
        });

        const addonsTotal = getAddonsTotal();
        const grandTotal  = productionSubtotal + addonsTotal;

        summBundles.textContent  = totalBundles.toLocaleString();
        summPieces.textContent   = totalPieces.toLocaleString();
        summSubtotal.textContent = fmt(productionSubtotal);

        if (addonsTotal !== 0) {
            summAddons.textContent     = fmt(addonsTotal);
            summAddonsRow.style.display = '';
        } else {
            summAddonsRow.style.display = 'none';
        }

        summTotal.textContent = fmt(grandTotal);

        const available = currentCoilData ? currentCoilData.available : null;
        // Historical mode: never show over-stock warning — stock is not being deducted
        const over = !isHistorical && available !== null && totalPieces > available;
        summOverRow.style.display = over ? '' : 'none';

        const hasCoil = !!coilSelect.value;
        const hasRows = colourRowsEl.querySelectorAll('.kz-row').length > 0 && totalPieces > 0;
        submitBtn.disabled = !(hasCoil && hasRows && !over);

        // Re-calc any percentage add-ons when production total changes
        addonsContainer.querySelectorAll('.addon-check:checked').forEach(cb => {
            if (cb.dataset.method === 'percentage') {
                const addonId  = parseInt(cb.value);
                const pct      = parseFloat(cb.dataset.default) || 0;
                const isRefund = cb.dataset.refund === '1';
                let amount     = productionSubtotal * pct / 100;
                if (isRefund && amount > 0) amount = -amount;
                const amtInp = addonsContainer.querySelector(`.addon-amount[data-addon-id="${addonId}"]`);
                if (amtInp) amtInp.value = amount.toFixed(2);
                const entry = selectedAddons.get(addonId);
                if (entry) entry.amount = amount;
            }
        });
    }

    function refreshNoRowsMsg() {
        noRowsMsg.style.display = colourRowsEl.querySelectorAll('.kz-row').length > 0 ? 'none' : '';
    }

    // ── Submit ─────────────────────────────────────
    submitBtn.addEventListener('click', submitSale);

    async function submitSale() {
        submitError.style.display = 'none';

        const customerId  = document.getElementById('customer_id').value;
        const warehouseId = document.getElementById('warehouse_id').value;
        const coilId      = coilSelect.value;
        const saleDate    = document.getElementById('sale_date').value;
        const notes       = document.getElementById('order_notes').value;

        if (!customerId || !warehouseId || !coilId) {
            showError('Please fill in customer, warehouse, and coil.');
            return;
        }

        // Build colour properties
        const rows = colourRowsEl.querySelectorAll('.kz-row');
        const properties = [];
        let totalPieces = 0;
        const productionSubtotal = getProductionSubtotal();

        for (const rowEl of rows) {
            const colourSel   = rowEl.querySelector('.colour-select');
            const customInput = rowEl.querySelector('.custom-label');
            const bundles     = parseFloat(rowEl.querySelector('.bundle-input').value) || 0;
            const price       = parseFloat(rowEl.querySelector('.price-input').value)  || 0;
            const pieces      = Math.round(bundles * PIECES_PER_BUNDLE);
            const subtotal    = bundles * price;

            if (bundles <= 0) continue;

            const label = colourSel.value === '__custom__'
                ? (customInput.value.trim() || 'Custom')
                : (colourSel.value || 'KZinc');

            properties.push({
                propertyType: 'bundles',
                label,
                quantity:   bundles,
                pieces,
                unitPrice:  price,
                subtotal,
                meters:     0,
                sheetQty:   bundles,
                sheetMeter: 0,
            });
            totalPieces += pieces;
        }

        if (properties.length === 0) {
            showError('Add at least one colour row with a bundle quantity.');
            return;
        }

        // Build add-ons arrays
        const addonsArray = [];
        const addonInvoiceItems = [];
        let totalCharges = 0, totalAdjustments = 0;

        selectedAddons.forEach(({ addon, amount }, addonId) => {
            addonsArray.push({
                addon_id:           addon.id,
                code:               addon.code || '',
                name:               addon.name,
                amount:             amount,
                calculation_method: addon.calculation_method || 'fixed',
                display_section:    addon.is_refundable == 1 ? 'adjustment' : 'addon',
            });
            addonInvoiceItems.push({
                description: addon.name,
                quantity:    1,
                unit:        'charge',
                unit_price:  amount,
                subtotal:    amount,
            });
            if (amount >= 0) totalCharges     += amount;
            else             totalAdjustments += amount;
        });

        const addonsTotal = getAddonsTotal();
        const grandTotal  = productionSubtotal + addonsTotal;

        // Display names
        const custOpt  = document.getElementById('customer_id').options;
        const custName = custOpt[custOpt.selectedIndex]?.dataset.name  || '';
        const custPhone= custOpt[custOpt.selectedIndex]?.dataset.phone || '';
        const whOpt    = document.getElementById('warehouse_id').options;
        const whName   = whOpt[whOpt.selectedIndex]?.dataset.name || '';

        const productionData = {
            customer_id:  parseInt(customerId),
            warehouse_id: parseInt(warehouseId),
            coil_id:      parseInt(coilId),
            sale_date:    saleDate,
            is_historical: isHistorical,
            production_paper: {
                customer:  { name: custName, phone: custPhone, email: '' },
                warehouse: { name: whName },
                coil:      { code: currentCoilData?.code, name: currentCoilData?.name },
                properties,
                addons: addonsArray,
                summary: {
                    totalMeters: 0,
                    totalAmount: productionSubtotal,
                },
                addonSummary: { totalCharges, totalAdjustments },
                grandTotal,
            },
        };

        const coilCode = currentCoilData?.code ?? '';
        const invoiceData = {
            customer: { name: custName, phone: custPhone, email: '' },
            items: properties.map(p => ({
                product_code: `${coilCode} - ${p.quantity} Bundle${p.quantity !== 1 ? 's' : ''} - ${p.label}`,
                description:  `${p.pieces} pcs`,
                quantity:    p.quantity,
                unit:        'bundles',
                unit_price:  p.unitPrice,
                subtotal:    p.subtotal,
            })),
            addon_items:   addonInvoiceItems,
            addon_summary: { total_charges: totalCharges, total_adjustments: totalAdjustments },
            tax:      { type: 'fixed', value: 0, amount: 0 },
            discount: { type: 'fixed', value: 0, amount: 0 },
            shipping: 0,
            grandTotal,
            notes,
        };

        const formData = new FormData();
        formData.append('csrf_token',      document.getElementById('csrf_token').value);
        formData.append('production_data', JSON.stringify(productionData));
        formData.append('invoice_data',    JSON.stringify(invoiceData));

        submitBtn.disabled = true;
        submitSpinner.style.display = '';

        try {
            const resp = await fetch('/new-stock-system/controllers/sales/create_workflow/index.php', {
                method: 'POST',
                body:   formData,
            });
            const data = await resp.json();

            if (data.success) {
                window.location.href = `/new-stock-system/index.php?page=invoice_view&id=${data.invoice_id}`;
            } else {
                showError(data.message || 'Failed to create sale. Please try again.');
                submitBtn.disabled = false;
            }
        } catch (e) {
            showError('Network error. Please try again.');
            submitBtn.disabled = false;
        } finally {
            submitSpinner.style.display = 'none';
        }
    }

    function showError(msg) {
        submitError.textContent = msg;
        submitError.style.display = '';
    }

    // ── Init ───────────────────────────────────────
    refreshNoRowsMsg();
    addRow();        // start with one empty row
    loadAddons();    // fetch add-ons in background
})();
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
