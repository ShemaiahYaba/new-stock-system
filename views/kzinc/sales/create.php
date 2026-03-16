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

// Build coil data for JS (id, code, name, pallet_size, available_pieces)
$coilsData = [];
foreach ($kzincCoils as $c) {
    $entries = $stockEntryModel->getAvailableKzincEntries($c['id']);
    $coilsData[] = [
        'id'          => (int)$c['id'],
        'code'        => $c['code'],
        'name'        => $c['name'],
        'pallet_size' => (int)($c['pallet_size'] ?? 0),
        'available_pieces' => (int)array_sum(array_column($entries, 'pieces_remaining')),
        'status'      => $c['status'],
    ];
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

    <form id="kzincSaleForm">
        <input type="hidden" id="csrf_token" value="<?php echo generateCsrfToken(); ?>">

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
                                <select class="form-select" id="coil_id" required>
                                    <option value="">— Select coil —</option>
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
                <div class="card mb-3">
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
                                <td class="fw-bold">Total Amount</td>
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
    const PIECES_PER_BUNDLE = <?php echo KZINC_PIECES_PER_BUNDLE; ?>;

    // ── State ──────────────────────────────────────
    let rowCounter       = 0;
    let currentCoilData  = null;

    // ── DOM refs ───────────────────────────────────
    const coilSelect      = document.getElementById('coil_id');
    const coilStockInfo   = document.getElementById('coil_stock_info');
    const addRowBtn       = document.getElementById('addRowBtn');
    const colourRowsEl    = document.getElementById('colourRows');
    const noRowsMsg       = document.getElementById('noRowsMsg');

    const summBundles     = document.getElementById('summTotalBundles');
    const summPieces      = document.getElementById('summTotalPieces');
    const summAvailable   = document.getElementById('summAvailable');
    const summOverRow     = document.getElementById('summOverRow');
    const summTotal       = document.getElementById('summTotal');

    const submitBtn       = document.getElementById('submitBtn');
    const submitError     = document.getElementById('submitError');
    const submitSpinner   = document.getElementById('submitSpinner');

    const rowTemplate     = document.getElementById('rowTemplate');

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
            id:          parseInt(opt.value),
            code:        opt.dataset.code,
            name:        opt.dataset.name,
            palletSize:  parseInt(opt.dataset.pallet || '0'),
            available:   parseInt(opt.dataset.available || '0'),
        };
        coilStockInfo.textContent = `${currentCoilData.available} pieces available`;
        coilStockInfo.style.display = '';
        summAvailable.textContent = currentCoilData.available.toLocaleString() + ' pcs';
        recalcSummary();
    });

    // ── Add row ────────────────────────────────────
    addRowBtn.addEventListener('click', addRow);

    function addRow() {
        rowCounter++;
        const tpl   = rowTemplate.content.cloneNode(true);
        const rowEl = tpl.querySelector('.kz-row');
        rowEl.dataset.rowId = rowCounter;

        // Remove btn
        rowEl.querySelector('.remove-row').addEventListener('click', () => {
            rowEl.remove();
            refreshNoRowsMsg();
            recalcSummary();
        });

        // Colour select → show/hide custom input
        const colourSel   = rowEl.querySelector('.colour-select');
        const customInput = rowEl.querySelector('.custom-label');
        colourSel.addEventListener('change', () => {
            customInput.style.display = colourSel.value === '__custom__' ? '' : 'none';
        });

        // Bundle / price → recalc
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

        rowEl.querySelector('.pieces-display').textContent  = pieces.toLocaleString();
        rowEl.querySelector('.subtotal-display').textContent = '₦' + subtotal.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        recalcSummary();
    }

    function recalcSummary() {
        let totalBundles = 0, totalPieces = 0, totalAmount = 0;

        colourRowsEl.querySelectorAll('.kz-row').forEach(rowEl => {
            const bundles = parseFloat(rowEl.querySelector('.bundle-input').value) || 0;
            const price   = parseFloat(rowEl.querySelector('.price-input').value)  || 0;
            totalBundles += bundles;
            totalPieces  += Math.round(bundles * PIECES_PER_BUNDLE);
            totalAmount  += bundles * price;
        });

        summBundles.textContent = totalBundles.toLocaleString();
        summPieces.textContent  = totalPieces.toLocaleString();
        summTotal.textContent   = '₦' + totalAmount.toLocaleString('en', {minimumFractionDigits: 2, maximumFractionDigits: 2});

        const available = currentCoilData ? currentCoilData.available : null;
        const over = available !== null && totalPieces > available;
        summOverRow.style.display = over ? '' : 'none';

        // Enable submit if: coil selected, at least 1 row, total > 0, not over stock
        const rows       = colourRowsEl.querySelectorAll('.kz-row');
        const hasCoil    = !!coilSelect.value;
        const hasRows    = rows.length > 0 && totalPieces > 0;
        submitBtn.disabled = !(hasCoil && hasRows && !over);
    }

    function refreshNoRowsMsg() {
        const hasRows = colourRowsEl.querySelectorAll('.kz-row').length > 0;
        noRowsMsg.style.display = hasRows ? 'none' : '';
    }

    // ── Submit ─────────────────────────────────────
    document.getElementById('submitBtn').addEventListener('click', submitSale);

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

        // Build properties from rows
        const rows = colourRowsEl.querySelectorAll('.kz-row');
        const properties = [];
        let totalPieces = 0, totalAmount = 0;

        for (const rowEl of rows) {
            const colourSel   = rowEl.querySelector('.colour-select');
            const customInput = rowEl.querySelector('.custom-label');
            const bundles     = parseFloat(rowEl.querySelector('.bundle-input').value) || 0;
            const price       = parseFloat(rowEl.querySelector('.price-input').value)  || 0;
            const pieces      = Math.round(bundles * PIECES_PER_BUNDLE);
            const subtotal    = bundles * price;

            const label = colourSel.value === '__custom__'
                ? (customInput.value.trim() || 'Custom')
                : (colourSel.value || 'KZinc');

            if (bundles <= 0) continue;

            properties.push({
                propertyType: 'bundles',
                label:        label,
                quantity:     bundles,
                pieces:       pieces,
                unitPrice:    price,
                subtotal:     subtotal,
                meters:       0,
                sheetQty:     bundles,
                sheetMeter:   0,
            });

            totalPieces += pieces;
            totalAmount += subtotal;
        }

        if (properties.length === 0) {
            showError('Add at least one colour row with a bundle quantity.');
            return;
        }

        // Get display names for production paper
        const custOpt = document.getElementById('customer_id').options;
        const custName = custOpt[custOpt.selectedIndex]?.dataset.name || '';
        const custPhone = custOpt[custOpt.selectedIndex]?.dataset.phone || '';

        const whOpt  = document.getElementById('warehouse_id').options;
        const whName = whOpt[whOpt.selectedIndex]?.dataset.name || '';

        const productionData = {
            customer_id:  parseInt(customerId),
            warehouse_id: parseInt(warehouseId),
            coil_id:      parseInt(coilId),
            sale_date:    saleDate,
            production_paper: {
                customer:  { name: custName, phone: custPhone, email: '' },
                warehouse: { name: whName },
                coil:      { code: currentCoilData?.code, name: currentCoilData?.name },
                properties: properties,
                addons:     [],
                summary: {
                    totalMeters: 0,
                    totalAmount: totalAmount,
                },
                addonSummary: { totalCharges: 0, totalAdjustments: 0 },
                grandTotal: totalAmount,
            },
        };

        const invoiceData = {
            customer: { name: custName, phone: custPhone, email: '' },
            items: properties.map(p => ({
                description: p.label,
                quantity:    p.quantity,
                unit:        'bundles',
                unit_price:  p.unitPrice,
                subtotal:    p.subtotal,
            })),
            addon_items:   [],
            addon_summary: { total_charges: 0, total_adjustments: 0 },
            tax:      { type: 'fixed', value: 0, amount: 0 },
            discount: { type: 'fixed', value: 0, amount: 0 },
            shipping: 0,
            grandTotal: totalAmount,
            notes: notes,
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
                window.location.href = `/new-stock-system/index.php?page=kzinc_sales_view&id=${data.sale_id}`;
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

    // Init
    refreshNoRowsMsg();
    addRow(); // Start with one empty row
})();
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
