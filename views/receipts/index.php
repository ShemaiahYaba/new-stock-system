<?php
/**
 * Enhanced Receipts List with Fixed Filtering (No Autocomplete)
 * File: views/receipts/index.php
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/receipt.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Payment Receipts - ' . APP_NAME;

// Get filter parameters
$currentPage = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
$filters = [
    'invoice_id' => $_GET['invoice_id'] ?? '',
    'payment_method' => $_GET['payment_method'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'customer_search' => $_GET['customer_search'] ?? '',
    'min_amount' => $_GET['min_amount'] ?? '',
    'max_amount' => $_GET['max_amount'] ?? '',
    'status' => $_GET['status'] ?? ''
];

$receiptModel = new Receipt();
$limit = RECORDS_PER_PAGE;
$offset = ($currentPage - 1) * $limit;

// Get receipts with pagination and filters
$receipts = $receiptModel->getAllWithFilters($limit, $offset, $filters);
$totalReceipts = $receiptModel->countWithFilters($filters);

// Calculate pagination data
$totalPages = ceil($totalReceipts / $limit);
$paginationData = [
    'currentPage' => $currentPage,
    'totalPages' => $totalPages,
    'hasPrevious' => $currentPage > 1,
    'hasNext' => $currentPage < $totalPages,
    'previousPage' => $currentPage > 1 ? $currentPage - 1 : 1,
    'nextPage' => $currentPage < $totalPages ? $currentPage + 1 : $totalPages
];

// Get filter options
$filterOptions = $receiptModel->getFilterOptions();

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
    .filter-card {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
    }
    .filter-badge {
        background: #0d6efd;
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0.25rem;
    }
    .filter-badge .remove-filter {
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .filter-badge .remove-filter:hover {
        opacity: 1;
    }
    .advanced-filters {
        display: none;
    }
    .advanced-filters.show {
        display: block;
    }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-receipt"></i> Payment Receipts
                </h1>
                <p class="text-muted">View and manage payment receipts (<?= $totalReceipts ?> total)</p>
            </div>
        </div>
    </div>
    
    <!-- Enhanced Filters -->
    <div class="card filter-card mb-3">
        <div class="card-body">
            <form method="GET" action="/new-stock-system/index.php" id="filterForm">
                <input type="hidden" name="page" value="receipts">
                
                <!-- Quick Filters -->
                <div class="row g-3 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-search"></i> Search
                        </label>
                        <input type="text" 
                               name="customer_search" 
                               class="form-control" 
                               placeholder="Customer, phone, or invoice #..."
                               value="<?= htmlspecialchars($filters['customer_search']) ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-calendar-range"></i> Date From
                        </label>
                        <input type="date" 
                               name="date_from" 
                               class="form-control"
                               value="<?= htmlspecialchars($filters['date_from']) ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-calendar-check"></i> Date To
                        </label>
                        <input type="date" 
                               name="date_to" 
                               class="form-control"
                               value="<?= htmlspecialchars($filters['date_to']) ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">
                            <i class="bi bi-credit-card"></i> Payment Method
                        </label>
                        <select name="payment_method" class="form-select">
                            <option value="">All Methods</option>
                            <?php foreach ($filterOptions['payment_methods'] as $method): ?>
                            <option value="<?= htmlspecialchars($method) ?>" 
                                <?= $filters['payment_method'] === $method ? 'selected' : '' ?>>
                                <?= ucwords(str_replace('_', ' ', $method)) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- Toggle Advanced Filters -->
                <div class="mb-3">
                    <button type="button" 
                            class="btn btn-sm btn-outline-secondary" 
                            id="toggleAdvanced">
                        <i class="bi bi-chevron-down"></i> Advanced Filters
                    </button>
                </div>
                
                <!-- Advanced Filters (Hidden by default) -->
                <div id="advancedFilters" class="advanced-filters">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-file-text"></i> Invoice
                            </label>
                            <select name="invoice_id" class="form-select">
                                <option value="">All Invoices</option>
                                <?php foreach ($filterOptions['invoices'] as $invoice): ?>
                                <option value="<?= $invoice['id'] ?>" 
                                    <?= $filters['invoice_id'] == $invoice['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($invoice['invoice_number']) ?> - 
                                    <?= htmlspecialchars($invoice['customer_name'] ?? 'N/A') ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label">
                                <i class="bi bi-tag"></i> Invoice Status
                            </label>
                            <select name="status" class="form-select">
                                <option value="">All Statuses</option>
                                <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="partial" <?= $filters['status'] === 'partial' ? 'selected' : '' ?>>Partial</option>
                                <option value="unpaid" <?= $filters['status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="bi bi-cash"></i> Min Amount
                            </label>
                            <input type="number" 
                                   name="min_amount" 
                                   class="form-control" 
                                   placeholder="0.00"
                                   step="0.01"
                                   value="<?= htmlspecialchars($filters['min_amount']) ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <label class="form-label">
                                <i class="bi bi-cash-stack"></i> Max Amount
                            </label>
                            <input type="number" 
                                   name="max_amount" 
                                   class="form-control" 
                                   placeholder="0.00"
                                   step="0.01"
                                   value="<?= htmlspecialchars($filters['max_amount']) ?>">
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel"></i> Apply Filters
                    </button>
                    <a href="/new-stock-system/index.php?page=receipts" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-counterclockwise"></i> Clear All
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Active Filters Display -->
    <?php 
    $activeFilters = array_filter($filters);
    if (!empty($activeFilters)): 
    ?>
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <strong class="me-2">Active Filters:</strong>
                <?php foreach ($activeFilters as $key => $value): ?>
                    <span class="filter-badge">
                        <?php 
                        $label = ucwords(str_replace('_', ' ', $key));
                        $displayValue = $value;
                        
                        if ($key === 'payment_method') {
                            $displayValue = ucwords(str_replace('_', ' ', $value));
                        } elseif ($key === 'invoice_id') {
                            $invoice = array_filter($filterOptions['invoices'], fn($inv) => $inv['id'] == $value);
                            $displayValue = !empty($invoice) ? reset($invoice)['invoice_number'] : $value;
                        }
                        
                        echo htmlspecialchars($label) . ': ' . htmlspecialchars($displayValue);
                        ?>
                        <span class="remove-filter" onclick="removeFilter('<?= $key ?>')">
                            <i class="bi bi-x"></i>
                        </span>
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Receipts List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-list"></i> Payment Receipts 
                    <span class="badge bg-secondary"><?= $totalReceipts ?></span>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($receipts)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No payment receipts found matching your filters.
            </div>
            <?php else: 
                // Group receipts by invoice
                $groupedReceipts = [];
                $invoiceModel = new Invoice();
                $invoiceIds = array_unique(array_column($receipts, 'invoice_id'));
                $invoicesData = [];
                
                foreach ($invoiceIds as $invoiceId) {
                    $invoice = $invoiceModel->findById($invoiceId);
                    if ($invoice) {
                        $invoicesData[$invoiceId] = $invoice;
                    }
                }
                
                foreach ($receipts as $receipt) {
                    $invoiceId = $receipt['invoice_id'];
                    
                    if (!isset($groupedReceipts[$invoiceId])) {
                        $invoiceData = $invoicesData[$invoiceId] ?? null;
                        $invoiceShapeRaw = $invoiceData['invoice_shape'] ?? null;
                        if (is_array($invoiceShapeRaw)) {
                            $invoiceShape = $invoiceShapeRaw;
                        } elseif (is_string($invoiceShapeRaw) && $invoiceShapeRaw !== '') {
                            $decoded = json_decode($invoiceShapeRaw, true);
                            $invoiceShape = is_array($decoded) ? $decoded : [];
                        } else {
                            $invoiceShape = [];
                        }
                        
                        $customerName = $invoiceShape['customer']['name'] ?? 'Customer';
                        
                        $groupedReceipts[$invoiceId] = [
                            'invoice' => [
                                'id' => $invoiceId,
                                'number' => $invoiceData['invoice_number'] ?? $receipt['invoice_number'],
                                'customer' => $customerName,
                                'customer_name' => $customerName,
                                'total' => $invoiceData['total'] ?? $receipt['invoice_total'] ?? 0,
                                'paid' => 0,
                                'status' => $invoiceData['status'] ?? $receipt['invoice_status'] ?? 'unpaid',
                            ],
                            'receipts' => [],
                        ];
                    }
                    $groupedReceipts[$invoiceId]['receipts'][] = $receipt;
                    $groupedReceipts[$invoiceId]['invoice']['paid'] += $receipt['amount_paid'];
                }
                
                foreach ($groupedReceipts as $invoiceId => $invoiceData):
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
                    <div class="invoice-group-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1">
                                    <a href="/new-stock-system/index.php?page=invoice_view&id=<?= $invoice['id'] ?>">
                                        <?= htmlspecialchars($invoice['number']) ?>
                                    </a>
                                </h5>
                                <p class="mb-0 text-muted">
                                    Customer: <?= htmlspecialchars($invoice['customer_name']) ?>
                                </p>
                            </div>
                            <div class="text-end">
                                <div>
                                    <span class="status-badge <?= $statusClass ?>">
                                        <?= strtoupper($invoice['status']) ?>
                                    </span>
                                </div>
                                <div class="mt-2">Total: <span class="fw-bold">₦<?= number_format($invoice['total'], 2) ?></span></div>
                                <div>Paid: <span class="text-success">₦<?= number_format($invoice['paid'], 2) ?></span></div>
                                <div>Balance: 
                                    <span class="fw-bold <?= $balance > 0 ? 'text-danger' : 'text-success' ?>">
                                        ₦<?= number_format($balance, 2) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                        <strong>RCPT-<?= str_pad($receipt['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                                    </td>
                                    <td><?= date('M d, Y h:i A', strtotime($receipt['created_at'])) ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= ucwords(str_replace('_', ' ', $receipt['payment_method'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <?= !empty($receipt['reference']) ? htmlspecialchars($receipt['reference']) : '<span class="text-muted small">(No reference)</span>' ?>
                                    </td>
                                    <td class="text-end receipt-amount text-success">
                                        ₦<?= number_format($receipt['amount_paid'], 2) ?>
                                    </td>
                                    <td><?= htmlspecialchars($receipt['created_by_name'] ?? 'System') ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="/new-stock-system/index.php?page=invoice_view&id=<?= $invoice['id'] ?>" 
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
                                    <td class="text-end"><strong>₦<?= number_format($invoice['paid'], 2) ?></strong></td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php if ($paginationData['totalPages'] > 1): 
                    $filterQuery = http_build_query(array_filter($filters));
                ?>
                <div class="p-3 border-top">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($paginationData['currentPage'] > 1): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=receipts&page_num=<?= $paginationData['previousPage'] ?>&<?= $filterQuery ?>" 
                                   aria-label="Previous">
                                    <span aria-hidden="true">&laquo;</span>
                                </a>
                            </li>
                            <?php endif; ?>
                            
                            <?php
                            $range = 2;
                            $start = max(1, $paginationData['currentPage'] - $range);
                            $end = min($paginationData['totalPages'], $paginationData['currentPage'] + $range);

                            if ($start > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=receipts&page_num=1&<?= $filterQuery ?>">1</a>
                            </li>
                            <?php if ($start > 2): ?>
                            <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php endif; endif; ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
                            <li class="page-item <?= $i === $paginationData['currentPage'] ? 'active' : '' ?>">
                                <a class="page-link" href="?page=receipts&page_num=<?= $i ?>&<?= $filterQuery ?>">
                                    <?= $i ?>
                                </a>
                            </li>
                            <?php endfor; ?>

                            <?php if ($end < $paginationData['totalPages']): 
                                if ($end < $paginationData['totalPages'] - 1): ?>
                            <li class="page-item disabled"><span class="page-link">&hellip;</span></li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=receipts&page_num=<?= $paginationData['totalPages'] ?>&<?= $filterQuery ?>"><?= $paginationData['totalPages'] ?></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if ($paginationData['currentPage'] < $paginationData['totalPages']): ?>
                            <li class="page-item">
                                <a class="page-link" 
                                   href="?page=receipts&page_num=<?= $paginationData['nextPage'] ?>&<?= $filterQuery ?>" 
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
// Toggle advanced filters
document.getElementById('toggleAdvanced').addEventListener('click', function() {
    const advancedFilters = document.getElementById('advancedFilters');
    const icon = this.querySelector('i');
    
    advancedFilters.classList.toggle('show');
    
    if (advancedFilters.classList.contains('show')) {
        icon.classList.remove('bi-chevron-down');
        icon.classList.add('bi-chevron-up');
        this.innerHTML = '<i class="bi bi-chevron-up"></i> Hide Advanced Filters';
    } else {
        icon.classList.remove('bi-chevron-up');
        icon.classList.add('bi-chevron-down');
        this.innerHTML = '<i class="bi bi-chevron-down"></i> Advanced Filters';
    }
});

// Remove individual filter
function removeFilter(filterKey) {
    const form = document.getElementById('filterForm');
    const input = form.querySelector(`[name="${filterKey}"]`);
    if (input) {
        if (input.type === 'select-one') {
            input.selectedIndex = 0;
        } else {
            input.value = '';
        }
    }
    form.submit();
}

// Print receipt
function printReceipt(receiptId) {
    window.open(
        '/new-stock-system/views/receipts/print.php?id=' + receiptId, 
        'PrintReceipt',
        'width=800,height=600'
    );
}
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>