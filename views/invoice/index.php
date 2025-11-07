<?php
/**
 * Invoices List View
 * File: views/invoices/index.php
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Invoices - ' . APP_NAME;

// Pagination setup
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$statusFilter = $_GET['status'] ?? '';

$invoiceModel = new Invoice();
$limit = RECORDS_PER_PAGE;
$offset = ($currentPage - 1) * $limit;

// Get invoices with pagination and status filter
$invoices = $invoiceModel->getAll($limit, $offset, $statusFilter);
$totalInvoices = $invoiceModel->count($statusFilter);

$paginationData = getPaginationData($totalInvoices, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<style>
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .payment-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
    }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-receipt"></i> Invoices
                </h1>
                <p class="text-muted">Manage and track all invoices</p>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="/new-stock-system/index.php" class="row g-3">
                <input type="hidden" name="page" value="invoices">
                
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="paid" <?= $statusFilter === 'paid'
                            ? 'selected'
                            : '' ?>>Paid</option>
                        <option value="partial" <?= $statusFilter === 'partial'
                            ? 'selected'
                            : '' ?>>Partial Payment</option>
                        <option value="unpaid" <?= $statusFilter === 'unpaid'
                            ? 'selected'
                            : '' ?>>Unpaid</option>
                        <option value="overdue" <?= $statusFilter === 'overdue'
                            ? 'selected'
                            : '' ?>>Overdue</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Invoices List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-list"></i> Invoice Records (<?= $totalInvoices ?> total)
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($invoices)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No invoices found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice):

                            $invoiceData = json_decode($invoice['invoice_shape'], true);
                            $balance = $invoice['total_amount'] - $invoice['paid_amount'];
                            $isPaid = $balance <= 0;
                            $isPartial = !$isPaid && $invoice['paid_amount'] > 0;
                            $statusClass = $isPaid
                                ? 'success'
                                : ($isPartial
                                    ? 'warning'
                                    : 'danger');
                            ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($invoice['invoice_number']) ?></strong>
                                <?php if ($invoice['production_id']): ?>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-gear"></i> PR-<?= str_pad(
                                        $invoice['production_id'],
                                        4,
                                        '0',
                                        STR_PAD_LEFT,
                                    ) ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars(
                                $invoiceData['customer']['name'] ?? 'N/A',
                            ) ?></td>
                            <td><?= date('M d, Y', strtotime($invoice['created_at'])) ?></td>
                            <td>₦<?= number_format($invoice['total_amount'], 2) ?></td>
                            <td>₦<?= number_format($invoice['paid_amount'], 2) ?></td>
                            <td>₦<?= number_format($balance, 2) ?></td>
                            <td>
                                <span class="badge bg-<?= $statusClass ?> payment-status">
                                    <?= $isPaid ? 'Paid' : ($isPartial ? 'Partial' : 'Unpaid') ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/new-stock-system/index.php?page=invoice_view&id=<?= $invoice[
                                        'id'
                                    ] ?>" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="View Invoice">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (!$isPaid): ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-success trigger-payment" 
                                            data-invoice-id="<?= $invoice['id'] ?>"
                                            title="Record Payment">
                                        <i class="bi bi-cash-coin"></i>
                                    </button>
                                    <?php endif; ?>
                                    <a href="/new-stock-system/index.php?page=invoice_print&id=<?= $invoice[
                                        'id'
                                    ] ?>" 
                                       class="btn btn-sm btn-outline-secondary" 
                                       target="_blank"
                                       title="Print Invoice">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($paginationData['totalPages'] > 1): ?>
            <div class="p-3 border-top">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <a class="page-link" href="?page=invoices&page_num=<?= $paginationData[
                            'currentPage'
                        ] -
                            1 .
                            ($statusFilter
                                ? '&status=' . urlencode($statusFilter)
                                : '') ?>" aria-label="Previous">
    <span aria-hidden="true">&laquo;</span>
</a>
                        <?php for ($i = 1; $i <= $paginationData['totalPages']; $i++): ?>
<li class="page-item <?= $i === $paginationData['currentPage'] ? 'active' : '' ?>">
    <a class="page-link" 
       href="?page=invoices&page_num=<?= $i .
           ($statusFilter ? '&status=' . urlencode($statusFilter) : '') ?>">
        <?= $i ?>
    </a>
</li>
<?php endfor; ?>
                            <?php if (
                                $paginationData['currentPage'] < $paginationData['totalPages']
                            ): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=invoices&page_num=<?= $paginationData[
                                    'currentPage'
                                ] +
                                    1 .
                                    ($statusFilter
                                        ? '&status=' . urlencode($statusFilter)
                                        : '') ?>" aria-label="Next">
                                    <span aria-hidden="true">&raquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                       
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
            <?php endif; ?>
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
                <div class="modal-body">
                    <input type="hidden" name="invoice_id" id="invoice_id">
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Amount (₦)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="amount" name="amount" required>
                        <div class="form-text">Enter payment amount</div>
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
                        <input type="text" class="form-control" id="reference" name="reference" placeholder="e.g. Bank ref, cheque #, etc.">
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
    
    document.querySelectorAll('.trigger-payment').forEach(button => {
        button.addEventListener('click', function() {
            const invoiceId = this.getAttribute('data-invoice-id');
            document.getElementById('invoice_id').value = invoiceId;
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
                // Reload page to update the list
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
