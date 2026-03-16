<?php
/**
 * Invoices List View
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Invoices - ' . APP_NAME;

$currentPage  = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
$statusFilter = trim($_GET['status'] ?? '');
$searchQuery  = trim($_GET['search'] ?? '');

$invoiceModel = new Invoice();
$db           = Database::getInstance()->getConnection();

$limit  = RECORDS_PER_PAGE;
$offset = ($currentPage - 1) * $limit;

// ── Summary stats ────────────────────────────────────────────────────────────
$stats = $db->query(
    "SELECT
        COUNT(*) AS total,
        COALESCE(SUM(total), 0) AS total_value,
        COALESCE(SUM(paid_amount), 0) AS total_paid,
        COALESCE(SUM(total - paid_amount), 0) AS total_due,
        SUM(CASE WHEN paid_amount = 0 THEN 1 ELSE 0 END) AS unpaid_count,
        SUM(CASE WHEN paid_amount > 0 AND paid_amount < total THEN 1 ELSE 0 END) AS partial_count,
        SUM(CASE WHEN paid_amount >= total THEN 1 ELSE 0 END) AS paid_count
     FROM invoices"
)->fetch();

// ── Filtered query ───────────────────────────────────────────────────────────
$where  = ['1=1'];
$params = [];

if ($statusFilter !== '') {
    $where[]  = 'i.status = :status';
    $params[':status'] = $statusFilter;
}

if ($searchQuery !== '') {
    // Search in stored customer name (inside invoice_shape JSON) and invoice_number
    $where[]  = "(i.invoice_number LIKE :q OR JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name')) LIKE :q2)";
    $params[':q']  = "%$searchQuery%";
    $params[':q2'] = "%$searchQuery%";
}

$whereStr = implode(' AND ', $where);

$countStmt = $db->prepare("SELECT COUNT(*) FROM invoices i WHERE $whereStr");
$countStmt->execute($params);
$totalInvoices = (int)$countStmt->fetchColumn();

$listStmt = $db->prepare(
    "SELECT i.*,
            JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.name'))  AS customer_name,
            JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.customer.phone')) AS customer_phone,
            JSON_UNQUOTE(JSON_EXTRACT(i.invoice_shape, '$.meta.ref'))       AS sale_ref
     FROM invoices i
     WHERE $whereStr
     ORDER BY i.id DESC
     LIMIT :limit OFFSET :offset"
);
foreach ($params as $k => $v) {
    $listStmt->bindValue($k, $v);
}
$listStmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
$listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$listStmt->execute();
$invoices = $listStmt->fetchAll();

$paginationData = getPaginationData($totalInvoices, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title"><i class="bi bi-receipt"></i> Invoices</h1>
                <p class="text-muted">
                    All sales invoices
                    <?php if ($searchQuery !== ''): ?>
                        <span class="badge bg-info">Search: "<?php echo htmlspecialchars($searchQuery); ?>"</span>
                    <?php endif; ?>
                    (<?php echo $totalInvoices; ?> total)
                </p>
            </div>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center h-100">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">Total Invoiced</div>
                    <div class="fw-bold fs-5">₦<?php echo number_format($stats['total_value'], 2); ?></div>
                    <div class="text-muted small"><?php echo $stats['total']; ?> invoices</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-success">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">Total Collected</div>
                    <div class="fw-bold fs-5 text-success">₦<?php echo number_format($stats['total_paid'], 2); ?></div>
                    <div class="text-muted small"><?php echo $stats['paid_count']; ?> fully paid</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-danger">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">Outstanding</div>
                    <div class="fw-bold fs-5 text-danger">₦<?php echo number_format($stats['total_due'], 2); ?></div>
                    <div class="text-muted small"><?php echo $stats['unpaid_count']; ?> unpaid</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center h-100 border-warning">
                <div class="card-body py-3">
                    <div class="text-muted small mb-1">Part-Paid</div>
                    <div class="fw-bold fs-5 text-warning"><?php echo $stats['partial_count']; ?></div>
                    <div class="text-muted small">invoices</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <!-- Status buttons -->
                <div class="col-md-7">
                    <label class="form-label small text-muted">Filter by Status</label>
                    <div class="btn-group w-100" role="group">
                        <?php
                        $baseUrl = '/new-stock-system/index.php?page=invoices' .
                            ($searchQuery !== '' ? '&search=' . urlencode($searchQuery) : '');
                        ?>
                        <a href="<?php echo $baseUrl; ?>"
                           class="btn btn-sm <?php echo $statusFilter === '' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            All <span class="badge bg-white text-dark ms-1"><?php echo $stats['total']; ?></span>
                        </a>
                        <a href="<?php echo $baseUrl; ?>&status=unpaid"
                           class="btn btn-sm <?php echo $statusFilter === 'unpaid' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                            Unpaid <span class="badge bg-white text-dark ms-1"><?php echo $stats['unpaid_count']; ?></span>
                        </a>
                        <a href="<?php echo $baseUrl; ?>&status=partial"
                           class="btn btn-sm <?php echo $statusFilter === 'partial' ? 'btn-warning' : 'btn-outline-warning'; ?>">
                            Partial <span class="badge bg-white text-dark ms-1"><?php echo $stats['partial_count']; ?></span>
                        </a>
                        <a href="<?php echo $baseUrl; ?>&status=paid"
                           class="btn btn-sm <?php echo $statusFilter === 'paid' ? 'btn-success' : 'btn-outline-success'; ?>">
                            Paid <span class="badge bg-white text-dark ms-1"><?php echo $stats['paid_count']; ?></span>
                        </a>
                    </div>
                </div>
                <!-- Search -->
                <div class="col-md-5">
                    <label class="form-label small text-muted">Search</label>
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="invoices">
                        <?php if ($statusFilter !== ''): ?>
                        <input type="hidden" name="status" value="<?php echo htmlspecialchars($statusFilter); ?>">
                        <?php endif; ?>
                        <input type="text" name="search" class="form-control form-control-sm me-2"
                               placeholder="Customer name or invoice #…"
                               value="<?php echo htmlspecialchars($searchQuery); ?>" autocomplete="off">
                        <button type="submit" class="btn btn-sm btn-primary" title="Search">
                            <i class="bi bi-search"></i>
                        </button>
                        <?php if ($searchQuery !== '' || $statusFilter !== ''): ?>
                        <a href="/new-stock-system/index.php?page=invoices"
                           class="btn btn-sm btn-secondary ms-2" title="Clear all filters">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoices table -->
    <div class="card">
        <div class="card-header"><i class="bi bi-list"></i> Invoice Records</div>
        <div class="card-body p-0">
            <?php if (empty($invoices)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i>
                <?php if ($searchQuery !== ''): ?>
                    No invoices found matching "<?php echo htmlspecialchars($searchQuery); ?>".
                <?php elseif ($statusFilter !== ''): ?>
                    No <?php echo htmlspecialchars($statusFilter); ?> invoices found.
                <?php else: ?>
                    No invoices found.
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Reference</th>
                            <th>Date</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Balance</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $inv):
                            $total   = (float)($inv['total'] ?? 0);
                            $paid    = (float)($inv['paid_amount'] ?? 0);
                            $balance = $total - $paid;
                            $isPaid     = $balance <= 0;
                            $isPartial  = !$isPaid && $paid > 0;
                            $statusClass = $isPaid ? 'success' : ($isPartial ? 'warning' : 'danger');
                            $statusLabel = $isPaid ? 'Paid' : ($isPartial ? 'Partial' : 'Unpaid');

                            // Sale type label
                            $typeLabel = '';
                            $typeIcon  = '';
                            if ($inv['production_id']) {
                                $typeLabel = 'Production';
                                $typeIcon  = 'bi-gear';
                            } elseif ($inv['sale_type'] === 'tile_sale') {
                                $typeLabel = 'Tile Sale';
                                $typeIcon  = 'bi-grid-3x3';
                            } elseif ($inv['sale_type'] === 'coil_sale') {
                                $typeLabel = 'Coil Sale';
                                $typeIcon  = 'bi-disc';
                            }
                        ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($inv['invoice_number']); ?></strong>
                                <?php if ($typeLabel): ?>
                                <small class="d-block text-muted">
                                    <i class="bi <?php echo $typeIcon; ?>"></i> <?php echo $typeLabel; ?>
                                </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($inv['customer_name'] ?? 'N/A'); ?>
                                <?php if (!empty($inv['customer_phone'])): ?>
                                <small class="d-block text-muted"><?php echo htmlspecialchars($inv['customer_phone']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?php echo htmlspecialchars($inv['sale_ref'] ?? '—'); ?></small>
                            </td>
                            <td><small><?php echo date('M d, Y', strtotime($inv['created_at'])); ?></small></td>
                            <td class="text-end">₦<?php echo number_format($total, 2); ?></td>
                            <td class="text-end text-success">₦<?php echo number_format($paid, 2); ?></td>
                            <td class="text-end <?php echo $balance > 0 ? 'text-danger fw-bold' : ''; ?>">
                                ₦<?php echo number_format(max(0, $balance), 2); ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $statusClass; ?>">
                                    <?php echo $statusLabel; ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="/new-stock-system/index.php?page=invoice_view&id=<?php echo $inv['id']; ?>"
                                       class="btn btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if (!$isPaid): ?>
                                    <button type="button" class="btn btn-outline-success trigger-payment"
                                            data-invoice-id="<?php echo $inv['id']; ?>"
                                            data-customer="<?php echo htmlspecialchars($inv['customer_name'] ?? ''); ?>"
                                            data-balance="<?php echo number_format($balance, 2); ?>"
                                            title="Record Payment">
                                        <i class="bi bi-cash-coin"></i>
                                    </button>
                                    <?php endif; ?>
                                    <a href="/new-stock-system/views/invoices/print_view.php?id=<?php echo $inv['id']; ?>"
                                       class="btn btn-outline-secondary" target="_blank" title="Print">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if ($totalInvoices > RECORDS_PER_PAGE): ?>
        <div class="card-footer">
            <?php
            $queryParams = ['page' => 'invoices'];
            if ($statusFilter !== '') $queryParams['status'] = $statusFilter;
            if ($searchQuery !== '')  $queryParams['search'] = $searchQuery;
            include __DIR__ . '/../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cash-coin"></i> Record Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="paymentForm" method="POST"
                  action="/new-stock-system/controllers/invoices/record_payment.php">
                <div class="modal-body">
                    <input type="hidden" name="invoice_id" id="modal_invoice_id">

                    <div class="alert alert-info py-2 mb-3" id="modal_invoice_info"></div>

                    <div class="mb-3">
                        <label class="form-label">Amount (₦)</label>
                        <input type="text" class="form-control" id="amount" name="amount" required
                               oninput="formatPaymentNumber(this)" autocomplete="off">
                        <input type="hidden" id="amount_raw" name="amount_raw">
                        <div class="form-text">Balance due shown above</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="pos">POS</option>
                            <option value="cheque">Cheque</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reference / Note</label>
                        <input type="text" class="form-control" name="reference"
                               placeholder="Bank ref, cheque #, etc.">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="paymentSubmitBtn">
                        <i class="bi bi-check-circle"></i> Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:1100">
    <div id="appToast" class="toast align-items-center border-0" role="alert" aria-live="assertive">
        <div class="d-flex">
            <div class="toast-body" id="appToastMsg"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<script>
function formatPaymentNumber(input) {
    let value = input.value.replace(/[^\d.]/g, '');
    const parts = value.split('.');
    if (parts.length > 2) value = parts[0] + '.' + parts.slice(1).join('');
    const p = value.split('.');
    p[0] = p[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    input.value = p.length > 1 ? p[0] + '.' + p[1] : p[0];
    document.getElementById('amount_raw').value = value.replace(/,/g, '');
}

function showToast(message, type) {
    const toast    = document.getElementById('appToast');
    const toastMsg = document.getElementById('appToastMsg');
    toast.className = 'toast align-items-center border-0 text-white bg-' +
                      (type === 'success' ? 'success' : 'danger');
    toastMsg.textContent = message;
    bootstrap.Toast.getOrCreateInstance(toast, { delay: 3000 }).show();
}

document.addEventListener('DOMContentLoaded', function () {
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));

    document.querySelectorAll('.trigger-payment').forEach(btn => {
        btn.addEventListener('click', function () {
            const id       = this.dataset.invoiceId;
            const customer = this.dataset.customer;
            const balance  = this.dataset.balance;
            document.getElementById('modal_invoice_id').value = id;
            document.getElementById('modal_invoice_info').textContent =
                customer + ' — Balance due: ₦' + balance;
            document.getElementById('amount').value = '';
            document.getElementById('amount_raw').value = '';
            paymentModal.show();
        });
    });

    document.getElementById('paymentForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('paymentSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Processing…';

        fetch(this.action, { method: 'POST', body: new FormData(this) })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    paymentModal.hide();
                    showToast('Payment recorded successfully', 'success');
                    setTimeout(() => window.location.reload(), 1200);
                } else {
                    showToast(data.message || 'Error processing payment', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Record Payment';
                }
            })
            .catch(() => {
                showToast('Network error. Please try again.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Record Payment';
            });
    });
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
