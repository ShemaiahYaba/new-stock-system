<?php
/**
 * Receipts List
 * File: views/receipts/index.php
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/receipt.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Payment Receipts - ' . APP_NAME;

// Pagination setup
$currentPage = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
$invoiceFilter = $_GET['invoice_id'] ?? '';

$receiptModel = new Receipt();
$limit = RECORDS_PER_PAGE;
$offset = ($currentPage - 1) * $limit;

// Get receipts with pagination and filters
$receipts = $receiptModel->getAll($limit, $offset, '', $invoiceFilter);
$totalReceipts = $receiptModel->count('', $invoiceFilter);
$paginationData = getPaginationData($totalReceipts, $currentPage);

// Get unique invoices for filter dropdown
$invoices = $receiptModel->getInvoicesForFilter();

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
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
    .receipt-amount { font-weight: 600; }
    .invoice-group {
        border-left: 3px solid #0d6efd;
        margin-bottom: 2rem;
    }
    .invoice-group-header {
        background-color: #f8f9fa;
        padding: 1rem;
        margin-bottom: 1rem;
        border-radius: 0.25rem;
    }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-receipt"></i> Payment Receipts
                </h1>
                <p class="text-muted">View and manage payment receipts</p>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="/new-stock-system/index.php" class="row g-3">
                <input type="hidden" name="page" value="receipts">
                
                <div class="col-md-6">
                    <label class="form-label">Filter by Invoice</label>
                    <select name="invoice_id" class="form-select" onchange="this.form.submit()">
                        <option value="">All Invoices</option>
                        <?php foreach ($invoices as $invoice): ?>
                        <option value="<?= $invoice['id'] ?>" 
                            <?= $invoiceFilter == $invoice['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($invoice['invoice_number']) ?> - 
                            <?= htmlspecialchars($invoice['customer_name'] ?? 'N/A') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <a href="/new-stock-system/index.php?page=receipts" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Receipts List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-list"></i> Payment Receipts (<?= $totalReceipts ?> total)
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($receipts)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No payment receipts found.
            </div>
            <?php
                // Group receipts by invoice

                // Determine status badge class
                // Group receipts by invoice
                // Determine status badge class
                else: ?>
                <?php
                $groupedReceipts = [];
                foreach ($receipts as $receipt) {
                    $invoiceId = $receipt['invoice_id'];
                    if (!isset($groupedReceipts[$invoiceId])) {
                        $groupedReceipts[$invoiceId] = [
                            'invoice' => [
                                'id' => $receipt['invoice_id'],
                                'number' => $receipt['invoice_number'],
                                'customer' => $receipt['customer_name'] ?? 'N/A',
                                'total' => $receipt['invoice_total'] ?? 0,
                                'paid' => 0,
                                'status' => $receipt['invoice_status'] ?? 'unpaid',
                            ],
                            'receipts' => [],
                        ];
                    }
                    $groupedReceipts[$invoiceId]['receipts'][] = $receipt;
                    $groupedReceipts[$invoiceId]['invoice']['paid'] += $receipt['amount_paid'];
                }
                ?>

                <?php foreach ($groupedReceipts as $invoiceId => $invoiceData):

                    $invoice = $invoiceData['invoice'];
                    $balance = $invoice['total'] - $invoice['paid'];

                    $statusClass = 'status-unpaid';
                    if ($invoice['status'] === 'paid') {
                        $statusClass = 'status-paid';
                    } elseif ($invoice['status'] === 'partial') {
                        $statusClass = 'status-partial';
                    }
                    ?>
                <div class="invoice-group mb-4">
                    <!-- Invoice Header -->
                    <div class="invoice-group-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">
                                    <a href="/new-stock-system/index.php?page=invoice_view&id=<?= $invoice[
                                        'id'
                                    ] ?>">
                                        <?= htmlspecialchars($invoice['number']) ?>
                                    </a>
                                </h5>
                                <p class="mb-0 text-muted">
                                    Customer: <?= htmlspecialchars($invoice['customer']) ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <div>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= strtoupper($invoice['status']) ?>
                                    </span>
                                </div>
                                <div class="mt-2">Total: <span class="fw-bold">₦<?= number_format(
                                    $invoice['total'],
                                    2,
                                ) ?></span></div>
                                <div>Paid: <span class="text-success">₦<?= number_format(
                                    $invoice['paid'],
                                    2,
                                ) ?></span></div>
                                <div>Balance: 
                                    <span class="fw-bold <?= $balance > 0
                                        ? 'text-danger'
                                        : 'text-success' ?>">
                                        ₦<?= number_format($balance, 2) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Receipts Table -->
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Receipt #</th>
                                    <th>Date</th>
                                    <th>Payment Method</th>
                                    <th>Reference</th>
                                    <th class="text-end">Amount</th>
                                    <th>Received By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoiceData['receipts'] as $receipt): ?>
                                <tr>
                                    <td>
                                        <strong>RCPT-<?= str_pad(
                                            $receipt['id'],
                                            5,
                                            '0',
                                            STR_PAD_LEFT,
                                        ) ?></strong>
                                    </td>
                                    <td><?= date(
                                        'M d, Y h:i A',
                                        strtotime($receipt['created_at']),
                                    ) ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= ucwords(
                                                str_replace('_', ' ', $receipt['payment_method']),
                                            ) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($receipt['reference'])): ?>
                                            <small class="text-muted"><?= htmlspecialchars(
                                                $receipt['reference'],
                                            ) ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end receipt-amount text-success">
                                        ₦<?= number_format($receipt['amount_paid'], 2) ?>
                                    </td>
                                    <td><?= htmlspecialchars(
                                        $receipt['created_by_name'] ?? 'System',
                                    ) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/new-stock-system/index.php?page=invoice_view&id=<?= $invoice[
                                                'id'
                                            ] ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Invoice">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-secondary" 
                                                    title="Print Receipt"
                                                    onclick="printReceipt(<?= $receipt['id'] ?>)">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total Payments:</strong></td>
                                    <td class="text-end"><strong>₦<?= number_format(
                                        $invoice['paid'],
                                        2,
                                    ) ?></strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php
                endforeach; ?>

                <!-- Pagination -->
                <?php if ($paginationData['total_pages'] > 1): ?>
                <div class="p-3 border-top">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($paginationData['currentPage'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=receipts&page_num=<?= $paginationData[
                                       'currentPage'
                                   ] -
                                       1 .
                                       ($invoiceFilter
                                           ? '&invoice_id=' . urlencode($invoiceFilter)
                                           : '') ?>" 
                                   aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $paginationData['totalPages']; $i++): ?>
                            <li class="page-item <?= $i === $paginationData['currentPage']
                                ? 'active'
                                : '' ?>">
                                <a class="page-link" 
                                   href="?page=receipts&page_num=<?= $i .
                                       ($invoiceFilter
                                           ? '&invoice_id=' . urlencode($invoiceFilter)
                                           : '') ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>
                            
                            <?php if (
                                $paginationData['currentPage'] < $paginationData['totalPages']
                            ): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=receipts&page_num=<?= $paginationData[
                                       'currentPage'
                                   ] +
                                       1 .
                                       ($invoiceFilter
                                           ? '&invoice_id=' . urlencode($invoiceFilter)
                                           : '') ?>" 
                                   aria-label="Next">
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

<script>
function printReceipt(receiptId) {
    // Open receipt in new window for printing
    window.open(
        '/new-stock-system/views/receipts/print.php?id=' + receiptId, 
        'PrintReceipt',
        'width=800,height=600'
    );
}
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
