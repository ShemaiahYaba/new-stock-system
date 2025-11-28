<?php
/**
 * ============================================
 * FILE: views/tiles/sales/view.php
 * UPDATED: Now shows invoice and payment info
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_sale.php';
require_once __DIR__ . '/../../../models/invoice.php';
require_once __DIR__ . '/../../../models/receipt.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Sale Details - ' . APP_NAME;

$saleId = (int)($_GET['id'] ?? 0);

if ($saleId <= 0) {
    setFlashMessage('error', 'Invalid sale ID.');
    header('Location: /new-stock-system/index.php?page=tile_sales');
    exit();
}

$saleModel = new TileSale();
$sale = $saleModel->findById($saleId);

if (!$sale) {
    setFlashMessage('error', 'Sale not found.');
    header('Location: /new-stock-system/index.php?page=tile_sales');
    exit();
}

// Get invoice information
$invoiceModel = new Invoice();
$invoice = $invoiceModel->findByTileSale($saleId);

$hasInvoice = !empty($invoice);
if ($hasInvoice) {
    $totalAmount = $invoice['total'] ?? 0;
    $paidAmount = $invoice['paid_amount'] ?? 0;
    $balance = $totalAmount - $paidAmount;
    $isPaid = $balance <= 0;
    $isPartial = !$isPaid && $paidAmount > 0;
    
    // Get payment history
    $receiptModel = new Receipt();
    $payments = $receiptModel->findByInvoiceId($invoice['id']);
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<style>
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-block;
}
.status-paid { background-color: #d1e7dd; color: #0f5132; }
.status-partial { background-color: #fff3cd; color: #856404; }
.status-unpaid { background-color: #f8d7da; color: #842029; }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Sale Details</h1>
                <p class="text-muted">Sale #<?= $sale['id'] ?></p>
            </div>
            <a href="/new-stock-system/index.php?page=tile_sales" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Sales
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <!-- Sale Information -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-receipt"></i> Sale Information
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Customer</h6>
                            <p class="mb-1"><strong><?= htmlspecialchars($sale['customer_name']) ?></strong></p>
                            <?php if ($sale['customer_phone']): ?>
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-phone"></i> <?= htmlspecialchars($sale['customer_phone']) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Product</h6>
                            <p class="mb-1"><code><?= htmlspecialchars($sale['product_code']) ?></code></p>
                            <p class="mb-0 small text-muted">
                                <?= htmlspecialchars($sale['design_name']) ?> / 
                                <?= htmlspecialchars($sale['color_name']) ?> / 
                                <?= htmlspecialchars(TILE_GAUGES[$sale['gauge']]) ?>
                            </p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">Quantity</h6>
                            <h4><?= number_format($sale['quantity'], 2) ?> <small>pieces</small></h4>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">Unit Price</h6>
                            <h4>₦<?= number_format($sale['unit_price'], 2) ?></h4>
                        </div>
                        <div class="col-md-4 mb-3">
                            <h6 class="text-muted">Total Amount</h6>
                            <h3 class="text-success">₦<?= number_format($sale['total_amount'], 2) ?></h3>
                        </div>
                    </div>
                    
                    <?php if ($sale['notes']): ?>
                    <hr>
                    <h6 class="text-muted">Notes</h6>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($sale['notes'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Invoice & Payment Information -->
            <?php if ($hasInvoice): ?>
            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-file-earmark-text"></i> Invoice & Payment Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 40%;">Invoice Number:</th>
                                    <td>
                                        <a href="/new-stock-system/index.php?page=invoice_view&id=<?= $invoice['id'] ?>">
                                            <strong><?= htmlspecialchars($invoice['invoice_number']) ?></strong>
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Invoice Date:</th>
                                    <td><?= date('M d, Y', strtotime($invoice['created_at'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="status-badge status-<?= $isPaid ? 'paid' : ($isPartial ? 'partial' : 'unpaid') ?>">
                                            <?= $isPaid ? 'PAID' : ($isPartial ? 'PARTIAL PAYMENT' : 'UNPAID') ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th style="width: 40%;">Total Amount:</th>
                                    <td><strong>₦<?= number_format($totalAmount, 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Amount Paid:</th>
                                    <td class="text-success"><strong>₦<?= number_format($paidAmount, 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Balance Due:</th>
                                    <td class="<?= $balance > 0 ? 'text-danger' : 'text-success' ?>">
                                        <strong>₦<?= number_format($balance, 2) ?></strong>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <?php if (!$isPaid): ?>
                        <button type="button" 
                                class="btn btn-success trigger-payment" 
                                data-invoice-id="<?= $invoice['id'] ?>"
                                data-invoice-balance="<?= $balance ?>">
                            <i class="bi bi-cash-coin"></i> Record Payment
                        </button>
                        <?php endif; ?>
                        <a href="/new-stock-system/index.php?page=invoice_view&id=<?= $invoice['id'] ?>" 
                           class="btn btn-primary">
                            <i class="bi bi-eye"></i> View Full Invoice
                        </a>
                        <a href="/new-stock-system/views/invoices/print_view.php?id=<?= $invoice['id'] ?>" 
                           class="btn btn-outline-secondary" target="_blank">
                            <i class="bi bi-printer"></i> Print Invoice
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Payment History -->
            <?php if (!empty($payments)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Payment History
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Reference</th>
                                    <th>Method</th>
                                    <th class="text-end">Amount</th>
                                    <th>Processed By</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?= date('M d, Y h:i A', strtotime($payment['created_at'])) ?></td>
                                    <td><?= !empty($payment['reference']) ? htmlspecialchars($payment['reference']) : '-' ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= ucwords(str_replace('_', ' ', $payment['payment_method'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-end text-success">
                                        <strong>₦<?= number_format($payment['amount_paid'], 2) ?></strong>
                                    </td>
                                    <td><?= htmlspecialchars($payment['created_by_name'] ?? 'System') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i> 
                <strong>Legacy Sale:</strong> This sale was created before invoice integration was implemented.
            </div>
            <?php endif; ?>
            
            <!-- Amount Breakdown -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-calculator"></i> Amount Breakdown
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>Quantity:</td>
                            <td class="text-end"><?= number_format($sale['quantity'], 2) ?> pieces</td>
                        </tr>
                        <tr>
                            <td>Unit Price:</td>
                            <td class="text-end">₦<?= number_format($sale['unit_price'], 2) ?></td>
                        </tr>
                        <tr>
                            <td>Calculation:</td>
                            <td class="text-end">
                                <?= number_format($sale['quantity'], 2) ?> × 
                                ₦<?= number_format($sale['unit_price'], 2) ?>
                            </td>
                        </tr>
                        <tr class="table-success">
                            <th>Total Amount:</th>
                            <th class="text-end fs-5">₦<?= number_format($sale['total_amount'], 2) ?></th>
                        </tr>
                    </table>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <strong>In Words:</strong> 
                        <?= numberToWords($sale['total_amount']) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Sale Status
                </div>
                <div class="card-body text-center">
                    <?php
                    $statusClass = 'secondary';
                    if ($sale['status'] === 'completed') $statusClass = 'success';
                    elseif ($sale['status'] === 'pending') $statusClass = 'warning';
                    ?>
                    <span class="badge bg-<?= $statusClass ?> fs-5 px-4 py-2">
                        <?= htmlspecialchars(TILE_SALE_STATUS[$sale['status']]) ?>
                    </span>
                </div>
            </div>
            
            <!-- Transaction Info -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Transaction Info
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                        <th>Sale ID:</th>
                            <td>#<?= $sale['id'] ?></td>
                        </tr>
                        <tr>
                            <th>Date:</th>
                            <td><?= formatDate($sale['created_at']) ?></td>
                        </tr>
                        <tr>
                            <th>Created By:</th>
                            <td><?= htmlspecialchars($sale['created_by_name']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <?php if ($hasInvoice): ?>
                    <a href="/new-stock-system/views/invoices/print_view.php?id=<?= $invoice['id'] ?>" 
                       class="btn btn-outline-primary" target="_blank">
                        <i class="bi bi-printer"></i> Print Invoice
                    </a>
                    <?php endif; ?>
                    <a href="/new-stock-system/index.php?page=tile_products_view&id=<?= $sale['tile_product_id'] ?>" 
                       class="btn btn-outline-secondary">
                        <i class="bi bi-box"></i> View Product
                    </a>
                    <a href="/new-stock-system/index.php?page=customers_view&id=<?= $sale['customer_id'] ?>" 
                       class="btn btn-outline-info">
                        <i class="bi bi-person"></i> View Customer
                    </a>
                    <a href="/new-stock-system/index.php?page=tile_stock_card&product_id=<?= $sale['tile_product_id'] ?>" 
                       class="btn btn-outline-success">
                        <i class="bi bi-journal-text"></i> Stock Card
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<?php if ($hasInvoice && !$isPaid): ?>
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="paymentForm" method="POST" action="/new-stock-system/controllers/invoices/record_payment.php">
                <input type="hidden" name="invoice_id" id="invoice_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (₦)</label>
                        <input type="number" step="0.01" min="0" class="form-control" 
                               id="amount" name="amount" required>
                        <div class="form-text">Maximum: ₦<span id="maxAmount">0.00</span></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="pos">POS</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reference" class="form-label">Reference/Note</label>
                        <input type="text" class="form-control" id="reference" 
                               name="reference" placeholder="e.g. Bank ref, cheque #, etc.">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Record Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    
    document.querySelectorAll('.trigger-payment').forEach(button => {
        button.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            const balance = parseFloat(this.getAttribute('data-invoice-balance'));
            
            document.getElementById('invoice_id').value = invoiceId;
            document.getElementById('amount').max = balance;
            document.getElementById('maxAmount').textContent = balance.toFixed(2);
            document.getElementById('amount').value = balance.toFixed(2);
            
            paymentModal.show();
        });
    });
    
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing...';
        
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment recorded successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to record payment'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>