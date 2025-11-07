<?php
/**
 * Invoice Detail View
 * File: views/invoices/view.php
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../utils/helpers.php';

$invoiceId = (int) ($_GET['id'] ?? 0);

if ($invoiceId <= 0) {
    setFlashMessage('error', 'Invalid invoice ID.');
    header('Location: /new-stock-system/index.php?page=invoices');
    exit();
}

$invoiceModel = new Invoice();
$invoice = $invoiceModel->findById($invoiceId);

if (!$invoice) {
    setFlashMessage('error', 'Invoice not found.');
    header('Location: /new-stock-system/index.php?page=invoices');
    exit();
}

// Decode invoice shape
$invoiceData = json_decode($invoice['invoice_shape'], true);
$balance = $invoice['total_amount'] - $invoice['paid_amount'];
$isPaid = $balance <= 0;
$isPartial = !$isPaid && $invoice['paid_amount'] > 0;

$pageTitle = 'Invoice ' . $invoice['invoice_number'] . ' - ' . APP_NAME;

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<style>
    .invoice-header {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .payment-status {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .payment-status.paid {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    .payment-status.partial {
        background-color: #fff3cd;
        color: #664d03;
    }
    .payment-status.unpaid {
        background-color: #f8d7da;
        color: #842029;
    }
    .company-logo {
        max-height: 80px;
        margin-bottom: 20px;
    }
    .signature-area {
        margin-top: 60px;
        border-top: 1px solid #dee2e6;
        padding-top: 15px;
    }
    .signature-line {
        border-top: 1px solid #000;
        width: 200px;
        margin: 40px auto 0;
        text-align: center;
        padding-top: 5px;
    }
    .items-table th {
        background-color: #f8f9fa;
    }
    .amount-in-words {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
        font-style: italic;
    }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-receipt"></i> Invoice Details
                </h1>
                <p class="text-muted">#<?= htmlspecialchars($invoice['invoice_number']) ?></p>
            </div>
            <div class="d-flex gap-2">
                <a href="/new-stock-system/index.php?page=invoice_print&id=<?= $invoiceId ?>" 
                   class="btn btn-outline-secondary" target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>
                <a href="/new-stock-system/index.php?page=invoices" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Invoice Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <?php if (!empty($invoiceData['company']['logo'])): ?>
                        <img src="<?= htmlspecialchars($invoiceData['company']['logo']) ?>" 
                             alt="Company Logo" class="company-logo">
                    <?php endif; ?>
                    <h4><?= htmlspecialchars($invoiceData['company']['banner'] ?? 'INVOICE') ?></h4>
                    <p class="mb-1"><?= nl2br(
                        htmlspecialchars($invoiceData['company']['address'] ?? ''),
                    ) ?></p>
                    <p class="mb-1">Phone: <?= htmlspecialchars(
                        $invoiceData['company']['phone'] ?? '',
                    ) ?></p>
                    <p class="mb-0">Email: <?= htmlspecialchars(
                        $invoiceData['company']['email'] ?? '',
                    ) ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h2>INVOICE</h2>
                    <p class="mb-1"><strong>Invoice #:</strong> <?= htmlspecialchars(
                        $invoice['invoice_number'],
                    ) ?></p>
                    <p class="mb-1"><strong>Date:</strong> <?= date(
                        'F d, Y',
                        strtotime($invoice['created_at']),
                    ) ?></p>
                    <p class="mb-1"><strong>Status:</strong> 
                        <span class="payment-status <?= $isPaid
                            ? 'paid'
                            : ($isPartial
                                ? 'partial'
                                : 'unpaid') ?>">
                            <?= $isPaid ? 'Paid' : ($isPartial ? 'Partially Paid' : 'Unpaid') ?>
                        </span>
                    </p>
                    <?php if (!empty($invoiceData['meta']['ref'])): ?>
                        <p class="mb-0"><strong>Reference:</strong> <?= htmlspecialchars(
                            $invoiceData['meta']['ref'],
                        ) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-light">
                            <strong>Bill To:</strong>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars(
                                $invoiceData['customer']['name'] ?? '',
                            ) ?></h5>
                            <p class="card-text mb-1"><?= nl2br(
                                htmlspecialchars($invoiceData['customer']['address'] ?? ''),
                            ) ?></p>
                            <p class="card-text mb-0">Phone: <?= htmlspecialchars(
                                $invoiceData['customer']['phone'] ?? '',
                            ) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-header bg-light">
                            <strong>Payment Summary</strong>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end">₦<?= number_format(
                                        $invoice['total_amount'] -
                                            ($invoiceData['tax'] ?? 0) -
                                            ($invoiceData['shipping'] ?? 0),
                                        2,
                                    ) ?></td>
                                </tr>
                                <?php if (!empty($invoiceData['tax'])): ?>
                                <tr>
                                    <td>Tax:</td>
                                    <td class="text-end">₦<?= number_format(
                                        $invoiceData['tax'],
                                        2,
                                    ) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if (!empty($invoiceData['shipping'])): ?>
                                <tr>
                                    <td>Shipping:</td>
                                    <td class="text-end">₦<?= number_format(
                                        $invoiceData['shipping'],
                                        2,
                                    ) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="table-active">
                                    <td><strong>Total:</strong></td>
                                    <td class="text-end"><strong>₦<?= number_format(
                                        $invoice['total_amount'],
                                        2,
                                    ) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>Paid:</td>
                                    <td class="text-end">₦<?= number_format(
                                        $invoice['paid_amount'],
                                        2,
                                    ) ?></td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Balance Due:</strong></td>
                                    <td class="text-end"><strong>₦<?= number_format(
                                        $balance,
                                        2,
                                    ) ?></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Items</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Item</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoiceData['items'] as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <div><strong><?= htmlspecialchars(
                                    $item['product_code'] ?? '',
                                ) ?></strong></div>
                                <div class="text-muted"><?= htmlspecialchars(
                                    $item['description'] ?? '',
                                ) ?></div>
                            </td>
                            <td class="text-end">₦<?= number_format(
                                $item['unit_price'] ?? 0,
                                2,
                            ) ?></td>
                            <td class="text-end"><?= htmlspecialchars(
                                $item['qty_text'] ?? '',
                            ) ?></td>
                            <td class="text-end">₦<?= number_format(
                                $item['subtotal'] ?? 0,
                                2,
                            ) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <?php if (!empty($invoiceData['notes']['receipt_statement'])): ?>
                        <tr>
                            <td colspan="5" class="small">
                                <strong>Note:</strong> <?= htmlspecialchars(
                                    $invoiceData['notes']['receipt_statement'],
                                ) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Amount in Words -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="amount-in-words">
                <strong>Amount in words:</strong> 
                <?= !empty($invoiceData['notes']['amount_in_words'])
                    ? htmlspecialchars($invoiceData['notes']['amount_in_words'])
                    : 'N/A' ?>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    <?php if ($invoice['paid_amount'] > 0): ?>
    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong>Payment History</strong>
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
                        </tr>
                    </thead>
                    <tbody>
                        <!-- This would come from the receipts table in a real implementation -->
                        <tr>
                            <td><?= date('M d, Y', strtotime($invoice['updated_at'])) ?></td>
                            <td>Initial Payment</td>
                            <td>N/A</td>
                            <td class="text-end">₦<?= number_format(
                                $invoice['paid_amount'],
                                2,
                            ) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Notes & Signatures -->
    <div class="row">
        <div class="col-md-6">
            <?php if (!empty($invoiceData['notes'])): ?>
            <div class="card">
                <div class="card-header bg-light">
                    <strong>Notes</strong>
                </div>
                <div class="card-body">
                    <?php if (!empty($invoiceData['notes']['refund_policy'])): ?>
                    <p class="mb-2"><strong>Refund Policy:</strong> <?= htmlspecialchars(
                        $invoiceData['notes']['refund_policy'],
                    ) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($invoiceData['notes']['additional_notes'])): ?>
                    <p class="mb-0"><strong>Additional Notes:</strong> <?= nl2br(
                        htmlspecialchars($invoiceData['notes']['additional_notes']),
                    ) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <strong>Signatures</strong>
                </div>
                <div class="card-body text-center">
                    <div class="row">
                        <div class="col-6">
                            <div class="signature-line">Customer</div>
                        </div>
                        <div class="col-6">
                            <div class="signature-line">Authorized Signatory</div>
                        </div>
                    </div>
                    <div class="mt-2 small text-muted">
                        <?= date('F j, Y') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-4 d-flex justify-content-between">
        <a href="/new-stock-system/index.php?page=invoices" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Invoices
        </a>
        <div>
            <?php if (!$isPaid): ?>
            <button type="button" 
                    class="btn btn-success trigger-payment" 
                    data-invoice-id="<?= $invoice['id'] ?>"
                    data-invoice-balance="<?= $balance ?>">
                <i class="bi bi-cash-coin"></i> Record Payment
            </button>
            <?php endif; ?>
            <a href="/new-stock-system/index.php?page=invoice_print&id=<?= $invoiceId ?>" 
               class="btn btn-primary" target="_blank">
                <i class="bi bi-printer"></i> Print Invoice
            </a>
        </div>
    </div>
</div>

<!-- Payment Modal -->
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
    // Handle payment modal
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
    let currentInvoiceId = null;
    let currentBalance = 0;

    // Set up payment buttons
    document.querySelectorAll('.trigger-payment').forEach(button => {
        button.addEventListener('click', function() {
            currentInvoiceId = this.getAttribute('data-invoice-id');
            currentBalance = parseFloat(this.getAttribute('data-invoice-balance'));
            
            document.getElementById('invoice_id').value = currentInvoiceId;
            document.getElementById('amount').max = currentBalance;
            document.getElementById('maxAmount').textContent = currentBalance.toFixed(2);
            document.getElementById('amount').value = currentBalance.toFixed(2);
            
            paymentModal.show();
        });
    });

    // Handle form submission
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Add loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
        
        // Submit form via AJAX
        fetch(this.action, {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('Payment recorded successfully', 'success');
                // Reload page to update the view
                setTimeout(() => window.location.reload(), 1500);
            } else {
                // Show error message
                showToast(data.message || 'Error processing payment', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.', 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    // Helper function to show toast messages
    function showToast(message, type = 'info') {
        // You can implement a toast notification system here
        // For now, using a simple alert
        alert(`${type.toUpperCase()}: ${message}`);
    }
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
