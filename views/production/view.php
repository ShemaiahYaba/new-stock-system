<?php
/**
 * Production Paper View — supports both meter-based (Alusteel/Aluminum) and
 * KZinc (bundles/pieces) production records.
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/production.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'View Production - ' . APP_NAME;

$productionId = (int)($_GET['id'] ?? 0);

if ($productionId <= 0) {
    setFlashMessage('error', 'Invalid production ID.');
    header('Location: /new-stock-system/index.php?page=production');
    exit();
}

$productionModel = new Production();
$production = $productionModel->findById($productionId);

if (!$production) {
    setFlashMessage('error', 'Production record not found.');
    header('Location: /new-stock-system/index.php?page=production');
    exit();
}

$prodPaper = [];
if (isset($production['production_paper'])) {
    $prodPaper = is_array($production['production_paper'])
        ? $production['production_paper']
        : (json_decode($production['production_paper'], true) ?: []);
}

// Detect KZinc: any property with pieces > 0 and meters == 0 is a KZinc row
$isKzinc = false;
foreach ($prodPaper['properties'] ?? [] as $prop) {
    if (($prop['pieces'] ?? 0) > 0 && ($prop['meters'] ?? 0) == 0) {
        $isKzinc = true;
        break;
    }
}

// Sale reference — KZinc sales go to kzinc_sales_view, regular go to sales_view
$saleViewPage = $isKzinc ? 'kzinc_sales_view' : 'sales_view';

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<style>
/* ── Screen styles ────────────────────────────── */
.production-paper {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.section-header {
    background: #f8f9fa;
    padding: 10px 15px;
    border-left: 4px solid #007bff;
    margin: 20px 0 15px 0;
    font-weight: 600;
}
.immutable-banner {
    background: #fff3cd;
    border: 2px solid #ffc107;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.property-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
}
.kzinc-badge { background: #0d6efd20; border: 1px solid #0d6efd40; border-radius: 4px; }
.hash-display {
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    background: #e9ecef;
    padding: 8px;
    border-radius: 4px;
    word-break: break-all;
}
.addon-row { background: #fffbeb; }

/* ── Print styles ────────────────────────────── */
@media print {
    /* Hide all chrome */
    .sidebar,
    nav.navbar,
    .page-header,
    .immutable-banner,
    .no-print,
    .btn,
    .card-footer,
    .alert-permanent,
    footer {
        display: none !important;
    }

    /* Remove the content-wrapper indent caused by sidebar */
    body, .content-wrapper, .main-content, .wrapper {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
    }

    .production-paper {
        box-shadow: none !important;
        border-radius: 0 !important;
        padding: 10px 20px !important;
    }

    .section-header {
        background: #f0f0f0 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    /* Force table borders to print */
    table, th, td {
        border-color: #999 !important;
    }

    /* Page breaks */
    .property-card { page-break-inside: avoid; }
    .section-header { page-break-after: avoid; }

    /* Ensure summary cards print with colour */
    .card.bg-light { background: #f8f8f8 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .text-success { color: #198754 !important; }
    .text-primary { color: #0d6efd !important; }

    /* Print header */
    .print-only { display: block !important; }
}

/* Hide print-only elements on screen */
.print-only { display: none; }
</style>

<div class="content-wrapper">
    <div class="page-header no-print">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-file-earmark-text"></i> Production Paper
                    <?php if ($isKzinc): ?>
                    <span class="badge bg-info ms-2">K-Zinc</span>
                    <?php endif; ?>
                </h1>
                <p class="text-muted">
                    Production #<?php echo str_pad($production['id'], 4, '0', STR_PAD_LEFT); ?> —
                    <?php echo htmlspecialchars($prodPaper['production_reference'] ?? 'N/A'); ?>
                </p>
            </div>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary no-print">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="/new-stock-system/index.php?page=<?php echo $saleViewPage; ?>&id=<?php echo $production['sale_id']; ?>"
                   class="btn btn-info no-print">
                    <i class="bi bi-eye"></i> View Related Sale
                </a>
                <a href="/new-stock-system/index.php?page=production" class="btn btn-secondary no-print">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Shown only when printing -->
    <div class="print-only mb-3">
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
            <div>
                <h4 class="mb-0"><?php echo COMPANY_NAME; ?></h4>
                <small class="text-muted">Production Paper</small>
            </div>
            <div class="text-end">
                <strong><?php echo htmlspecialchars($prodPaper['production_reference'] ?? ('PR-' . str_pad($production['id'], 4, '0', STR_PAD_LEFT))); ?></strong><br>
                <small><?php echo date('d M Y', strtotime($production['created_at'])); ?></small>
            </div>
        </div>
    </div>

    <!-- Immutability Warning (screen only) -->
    <div class="immutable-banner no-print">
        <div class="d-flex align-items-center">
            <i class="bi bi-lock fs-3 me-3 text-warning"></i>
            <div>
                <h6 class="mb-1"><strong>Immutable Record</strong></h6>
                <p class="mb-0 small">This production record is permanently locked and cannot be modified.</p>
            </div>
        </div>
    </div>

    <!-- Production Paper -->
    <div class="production-paper">

        <!-- ── Header: Production Info + Warehouse/Customer ── -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h5 class="text-primary">Production Information</h5>
                <table class="table table-sm table-bordered">
                    <tr>
                        <th width="40%">Production ID</th>
                        <td><strong>PR-<?php echo str_pad($production['id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Reference</th>
                        <td><?php echo htmlspecialchars($prodPaper['production_reference'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Sale Reference</th>
                        <td>
                            <span class="no-print">
                                <a href="/new-stock-system/index.php?page=<?php echo $saleViewPage; ?>&id=<?php echo $production['sale_id']; ?>">
                                    #SO-<?php echo str_pad($production['sale_id'], 6, '0', STR_PAD_LEFT); ?>
                                </a>
                            </span>
                            <span class="print-only">#SO-<?php echo str_pad($production['sale_id'], 6, '0', STR_PAD_LEFT); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $statusColors = [
                                PRODUCTION_STATUS_PENDING      => 'warning',
                                PRODUCTION_STATUS_IN_PROGRESS  => 'info',
                                PRODUCTION_STATUS_COMPLETED    => 'success',
                                PRODUCTION_STATUS_CANCELLED    => 'danger',
                            ];
                            $color = $statusColors[$production['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $color; ?>">
                                <?php echo PRODUCTION_STATUSES[$production['status']] ?? $production['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td><?php echo date('d M Y H:i', strtotime($production['created_at'])); ?></td>
                    </tr>
                    <tr>
                        <th>Created By</th>
                        <td><?php echo htmlspecialchars($production['created_by_name']); ?></td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h5 class="text-primary">Warehouse &amp; Customer</h5>
                <table class="table table-sm table-bordered">
                    <tr>
                        <th width="40%">Warehouse</th>
                        <td><strong><?php echo htmlspecialchars($prodPaper['warehouse']['name'] ?? 'N/A'); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Customer</th>
                        <td><strong><?php echo htmlspecialchars($prodPaper['customer']['name'] ?? 'N/A'); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?php echo htmlspecialchars($prodPaper['customer']['phone'] ?? 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?php echo htmlspecialchars($prodPaper['customer']['email'] ?? 'N/A'); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- ── Coil Information ── -->
        <div class="section-header">
            <i class="bi bi-box-seam"></i> Coil Information
            <?php if ($isKzinc): ?>
            <span class="badge bg-info ms-2">K-Zinc</span>
            <?php endif; ?>
        </div>
        <table class="table table-bordered mb-4">
            <tr>
                <th width="20%">Coil Code</th>
                <td><?php echo htmlspecialchars($prodPaper['coil']['code'] ?? 'N/A'); ?></td>
                <th width="20%">Coil Name</th>
                <td><?php echo htmlspecialchars($prodPaper['coil']['name'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Category</th>
                <td><?php echo htmlspecialchars($prodPaper['coil']['category'] ?? ($isKzinc ? 'K-Zinc' : 'N/A')); ?></td>
                <th>Weight</th>
                <td><?php echo number_format($prodPaper['coil']['weight'] ?? 0, 2); ?> kg</td>
            </tr>
        </table>

        <!-- ── Production Properties ── -->
        <div class="section-header">
            <i class="bi bi-list-check"></i>
            <?php echo $isKzinc ? 'Colour Breakdown' : 'Production Properties'; ?>
        </div>

        <?php if (!empty($prodPaper['properties'])): ?>

            <?php if ($isKzinc): ?>
            <!-- KZinc: table layout (bundles / pieces / price per bundle) -->
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Colour / Label</th>
                        <th class="text-center">Bundles</th>
                        <th class="text-center">Pieces</th>
                        <th class="text-end">Price / Bundle</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prodPaper['properties'] as $i => $prop): ?>
                    <tr>
                        <td><?php echo $i + 1; ?></td>
                        <td><strong><?php echo htmlspecialchars($prop['label'] ?? $prop['property_id'] ?? '—'); ?></strong></td>
                        <td class="text-center"><?php echo number_format($prop['sheet_qty'] ?? $prop['quantity'] ?? 0, 0); ?></td>
                        <td class="text-center"><?php echo number_format($prop['pieces'] ?? 0); ?></td>
                        <td class="text-end">₦<?php echo number_format($prop['unit_price'] ?? 0, 2); ?></td>
                        <td class="text-end"><strong>₦<?php echo number_format($prop['row_subtotal'] ?? $prop['subtotal'] ?? 0, 2); ?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php
                $productionSubtotal = array_sum(array_column($prodPaper['properties'], 'row_subtotal'))
                    ?: array_sum(array_column($prodPaper['properties'], 'subtotal'));
                $hasAddons = !empty($prodPaper['addons']);
                $grandTotal = $prodPaper['grand_total'] ?? $prodPaper['total_amount'] ?? $productionSubtotal;
                ?>
                <?php if ($hasAddons): ?>
                <tfoot>
                    <tr class="table-light">
                        <td colspan="5" class="text-end text-muted">Production Subtotal</td>
                        <td class="text-end text-muted">₦<?php echo number_format($productionSubtotal, 2); ?></td>
                    </tr>
                    <?php foreach ($prodPaper['addons'] as $addon): ?>
                    <tr class="addon-row">
                        <td colspan="4">
                            <i class="bi bi-plus-square text-muted"></i>
                            <em><?php echo htmlspecialchars($addon['name'] ?? $addon['code'] ?? ''); ?></em>
                            <span class="badge bg-secondary ms-1" style="font-size:0.7rem;"><?php echo htmlspecialchars($addon['calculation_method'] ?? 'fixed'); ?></span>
                        </td>
                        <td colspan="2" class="text-end <?php echo ($addon['amount'] ?? 0) < 0 ? 'text-danger' : ''; ?>">
                            ₦<?php echo number_format($addon['amount'] ?? 0, 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="table-primary fw-bold">
                        <td colspan="5" class="text-end">Grand Total</td>
                        <td class="text-end fs-5">₦<?php echo number_format($grandTotal, 2); ?></td>
                    </tr>
                </tfoot>
                <?php else: ?>
                <tfoot>
                    <tr class="table-primary fw-bold">
                        <td colspan="5" class="text-end">Total</td>
                        <td class="text-end fs-5">₦<?php echo number_format($grandTotal, 2); ?></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>

            <?php else: ?>
            <!-- Meter-based: existing card layout -->
            <?php foreach ($prodPaper['properties'] as $index => $prop): ?>
            <div class="property-card">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="text-primary mb-2">
                        Property <?php echo $index + 1; ?>:
                        <strong><?php echo ucfirst($prop['property_id'] ?? $prop['label'] ?? ''); ?></strong>
                    </h6>
                    <span class="badge bg-info">₦<?php echo number_format($prop['row_subtotal'] ?? 0, 2); ?></span>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted">Sheet Quantity</small>
                        <p class="mb-0"><strong><?php echo $prop['sheet_qty'] ?? 0; ?> sheets</strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Meter per Sheet</small>
                        <p class="mb-0"><strong><?php echo number_format($prop['sheet_meter'] ?? 0, 2); ?>m</strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Total Meters</small>
                        <p class="mb-0"><strong><?php echo number_format($prop['meters'] ?? 0, 2); ?>m</strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Unit Price</small>
                        <p class="mb-0"><strong>₦<?php echo number_format($prop['unit_price'] ?? 0, 2); ?>/m</strong></p>
                    </div>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">
                        Calculation: <?php echo $prop['sheet_qty'] ?? 0; ?> × <?php echo number_format($prop['sheet_meter'] ?? 0, 2); ?>m × ₦<?php echo number_format($prop['unit_price'] ?? 0, 2); ?>
                    </small>
                    <small class="text-muted">
                        Subtotal: <strong>₦<?php echo number_format($prop['row_subtotal'] ?? 0, 2); ?></strong>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (!empty($prodPaper['addons'])): ?>
            <div class="section-header mt-3">
                <i class="bi bi-plus-square"></i> Add-Ons &amp; Charges
            </div>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Add-On</th><th>Method</th><th class="text-end">Amount</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($prodPaper['addons'] as $addon): ?>
                    <tr class="addon-row">
                        <td><?php echo htmlspecialchars($addon['name'] ?? ''); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($addon['calculation_method'] ?? 'fixed'); ?></span></td>
                        <td class="text-end <?php echo ($addon['amount'] ?? 0) < 0 ? 'text-danger' : ''; ?>">
                            ₦<?php echo number_format($addon['amount'] ?? 0, 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
            <?php endif; ?>

        <?php else: ?>
        <div class="alert alert-warning">No properties defined for this production record.</div>
        <?php endif; ?>

        <!-- ── Summary ── -->
        <div class="section-header mt-4">
            <i class="bi bi-calculator"></i> Production Summary
        </div>

        <?php if ($isKzinc): ?>
        <?php
        $totalBundles = array_sum(array_map(fn($p) => $p['sheet_qty'] ?? $p['quantity'] ?? 0, $prodPaper['properties'] ?? []));
        $totalPieces  = array_sum(array_column($prodPaper['properties'] ?? [], 'pieces'));
        $grandTotal   = $prodPaper['grand_total'] ?? $prodPaper['total_amount'] ?? 0;
        $addonTotal   = array_sum(array_column($prodPaper['addons'] ?? [], 'amount'));
        $prodSubtotal = $grandTotal - $addonTotal;
        ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-light text-center p-3">
                    <h6 class="text-muted mb-1">Total Bundles</h6>
                    <h3 class="mb-0 text-primary"><?php echo number_format($totalBundles, 0); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light text-center p-3">
                    <h6 class="text-muted mb-1">Total Pieces</h6>
                    <h3 class="mb-0 text-info"><?php echo number_format($totalPieces, 0); ?></h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-light text-center p-3">
                    <h6 class="text-muted mb-1"><?php echo $addonTotal != 0 ? 'Grand Total' : 'Total Amount'; ?></h6>
                    <h3 class="mb-0 text-success">₦<?php echo number_format($grandTotal, 2); ?></h3>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Meters</h6>
                        <h3 class="mb-0 text-primary"><?php echo number_format($prodPaper['total_meters'] ?? 0, 2); ?>m</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-2"><?php echo !empty($prodPaper['addons']) ? 'Grand Total' : 'Total Amount'; ?></h6>
                        <h3 class="mb-0 text-success">
                            ₦<?php echo number_format($prodPaper['grand_total'] ?? $prodPaper['total_amount'] ?? 0, 2); ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Signatures (print-friendly) ── -->
        <div class="row mt-5 print-only">
            <div class="col-6 text-center">
                <div style="border-top: 1px solid #000; width:70%; margin: 0 auto; padding-top: 4px;">
                    <small>Customer Signature</small>
                </div>
            </div>
            <div class="col-6 text-center">
                <div style="border-top: 1px solid #000; width:70%; margin: 0 auto; padding-top: 4px;">
                    <small>For <?php echo COMPANY_NAME; ?></small>
                </div>
            </div>
        </div>

        <!-- ── Immutable Hash (screen only) ── -->
        <div class="section-header mt-4 no-print">
            <i class="bi bi-shield-check"></i> Immutability Verification
        </div>
        <div class="alert alert-secondary no-print">
            <p class="mb-2"><strong>Immutable Hash (SHA256):</strong></p>
            <div class="hash-display"><?php echo htmlspecialchars($production['immutable_hash']); ?></div>
            <small class="text-muted mt-2 d-block">This hash ensures data integrity. Any modification to this record will be detected.</small>
        </div>

        <!-- ── Quick Actions (screen only) ── -->
        <div class="mt-4 d-flex justify-content-between no-print">
            <a href="/new-stock-system/index.php?page=production" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            <div class="d-flex gap-2">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="/new-stock-system/index.php?page=<?php echo $saleViewPage; ?>&id=<?php echo $production['sale_id']; ?>"
                   class="btn btn-info">
                    <i class="bi bi-eye"></i> View Related Sale
                </a>
            </div>
        </div>

    </div><!-- /.production-paper -->
</div><!-- /.content-wrapper -->

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
