<?php
/**
 * View Sale Details - UPDATED FOR CASCADE DELETE
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/coil.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../utils/helpers.php';

$saleId = $_GET['id'] ?? null;

if (!$saleId) {
    setFlashMessage('error', 'Sale ID is required');
    redirect('/new-stock-system/index.php?page=sales');
}

$saleModel = new Sale();
$sale = $saleModel->findById($saleId);

if (!$sale) {
    setFlashMessage('error', 'Sale not found');
    redirect('/new-stock-system/index.php?page=sales');
}

// Get related data
$customerModel = new Customer();
$coilModel = new Coil();
$stockEntryModel = new StockEntry();

$customer = $customerModel->findById($sale['customer_id']);
$coil = $coilModel->findById($sale['coil_id']);
$stockEntry = $sale['stock_entry_id'] ? $stockEntryModel->findById($sale['stock_entry_id']) : null;

// Get invoice information
$invoice = $saleModel->getInvoice($sale['id']);

// Get payment receipts if invoice exists
$db = Database::getInstance()->getConnection();
$receipts = [];
$totalPaid = 0;
if ($invoice) {
    $receiptsSql = "SELECT * FROM receipts WHERE invoice_id = ? ORDER BY created_at DESC";
    $receiptsStmt = $db->prepare($receiptsSql);
    $receiptsStmt->execute([$invoice['id']]);
    $receipts = $receiptsStmt->fetchAll();
    $totalPaid = array_sum(array_column($receipts, 'amount_paid'));
}

$pageTitle = 'View Sale - ' . APP_NAME;
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <h1>Sale Details</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/new-stock-system">Home</a></li>
                <li class="breadcrumb-item"><a href="/new-stock-system/index.php?page=sales">Sales</a></li>
                <li class="breadcrumb-item active">View Sale</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <h4>Sale Information</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%;">Sale ID:</th>
                            <td>#<?php echo $sale['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Date:</th>
                            <td><?php echo formatDate($sale['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <span class="badge <?php echo $sale['status'] === 'completed'
                                    ? 'bg-success'
                                    : ($sale['status'] === 'cancelled'
                                        ? 'bg-danger'
                                        : 'bg-warning'); ?>">
                                    <?php echo ucfirst($sale['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Sale Type:</th>
                            <td><?php echo ucfirst(str_replace('_', ' ', $sale['sale_type'])); ?></td>
                        </tr>
                        <?php if ($invoice): ?>
                        <tr>
                            <th>Invoice:</th>
                            <td>
                                <a href="/new-stock-system/index.php?page=invoice_view&id=<?php echo $invoice['id']; ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    View Invoice #<?php echo $invoice['invoice_number']; ?>
                                </a>
                                <span class="ms-2 text-muted">
                                    (<?php echo ucfirst($invoice['status']); ?>)
                                </span>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <h4>Customer Information</h4>
                    <?php if ($customer): ?>
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 40%;">Name:</th>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                            </tr>
                            <tr>
                                <th>Company:</th>
                                <td><?php echo htmlspecialchars($customer['company'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?></td>
                            </tr>
                            <tr>
                                <th>Phone:</th>
                                <td><?php echo htmlspecialchars($customer['phone'] ?? 'N/A'); ?></td>
                            </tr>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-warning">Customer not found</div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-12">
                    <h4>Product Details</h4>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Coil Code</th>
                                    <th>Coil Name</th>
                                    <th>Stock Entry</th>
                                    <th>Quantity Sold</th>
                                    <th>Unit Price</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($coil['code'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($coil['name'] ?? 'N/A'); ?></td>
                                    <td><?php echo $stockEntry ? '#' . $stockEntry['id'] : 'N/A'; ?></td>
                                    <td>
                                        <?php 
                                        if ($sale['weight_kg'] !== null && $sale['weight_kg'] > 0) {
                                            echo number_format($sale['weight_kg'], 2) . ' kg';
                                        } else {
                                            echo number_format($sale['meters'], 2) . ' m';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if ($sale['price_per_kg'] !== null && $sale['price_per_kg'] > 0) {
                                            echo '₦' . number_format($sale['price_per_kg'], 2) . '/kg';
                                        } else {
                                            echo '₦' . number_format($sale['price_per_meter'], 2) . '/m';
                                        }
                                        ?>
                                    </td>
                                    <td>₦<?php echo number_format($sale['total_amount'], 2); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <?php if ($invoice): ?>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Invoice Information</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%;">Invoice Number:</th>
                                            <td><?php echo $invoice['invoice_number']; ?></td>
                                        </tr>
                                        <tr>
                                            <th>Date:</th>
                                            <td><?php echo formatDate($invoice['created_at']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Status:</th>
                                            <td>
                                                <span class="badge <?php echo $invoice['status'] === 'paid'
                                                    ? 'bg-success'
                                                    : ($invoice['status'] === 'cancelled'
                                                        ? 'bg-danger'
                                                        : 'bg-warning'); ?>">
                                                    <?php echo ucfirst($invoice['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%;">Total Amount:</th>
                                            <td><?php echo formatCurrency($invoice['total']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Paid Amount:</th>
                                            <td><?php echo formatCurrency($invoice['paid_amount']); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Balance Due:</th>
                                            <td><?php echo formatCurrency($invoice['total'] - $invoice['paid_amount']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            <?php if (!empty($receipts)): ?>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Payment History</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Amount</th>
                                                    <th>Method</th>
                                                    <th>Reference</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($receipts as $receipt): ?>
                                                <tr>
                                                    <td><?php echo formatDate($receipt['created_at']); ?></td>
                                                    <td>₦<?php echo number_format($receipt['amount_paid'], 2); ?></td>
                                                    <td><?php echo ucfirst($receipt['payment_method']); ?></td>
                                                    <td><?php echo htmlspecialchars($receipt['reference'] ?: 'N/A'); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-light">
                                                    <th>Total Paid:</th>
                                                    <th>₦<?php echo number_format($totalPaid, 2); ?></th>
                                                    <th colspan="2"></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Back to Sales
                        </a>
                        <div>
                            <?php if (hasPermission(MODULE_SALES, ACTION_EDIT) && $sale['status'] !== 'cancelled'): ?>
                                <a href="/new-stock-system/index.php?page=sales_edit&id=<?php echo $sale['id']; ?>" 
                                   class="btn btn-primary">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($invoice): ?>
                            <a href="/new-stock-system/index.php?page=invoice_view&id=<?php echo $invoice['id']; ?>"
                               class="btn btn-success">
                                <i class="bi bi-file-pdf"></i> View Invoice
                            </a>
                            <?php endif; ?>
                            
                            <?php if (hasPermission(MODULE_SALES, ACTION_DELETE)): ?>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" 
                                        data-bs-target="#deleteModal">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle"></i> Confirm Permanent Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>DANGER:</strong> This will PERMANENTLY delete this sale and ALL related records!
                </div>
                
                <p><strong>The following will be deleted:</strong></p>
                <ul>
                    <li><i class="bi bi-check2"></i> Sale record #<?php echo $sale['id']; ?></li>
                    <?php if ($invoice): ?>
                    <li><i class="bi bi-check2"></i> Invoice <?php echo $invoice['invoice_number']; ?></li>
                    <?php endif; ?>
                    <?php if (!empty($receipts)): ?>
                    <li><i class="bi bi-check2"></i> <?php echo count($receipts); ?> payment record(s) totaling ₦<?php echo number_format($totalPaid, 2); ?></li>
                    <?php endif; ?>
                    <li><i class="bi bi-check2"></i> Production records (if any)</li>
                    <li><i class="bi bi-check2"></i> Stock ledger entries</li>
                </ul>
                
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i>
                    <strong>Note:</strong> Stock meters will be restored to the stock entry.
                </div>
                
                <hr>
                
                <p class="mb-0"><strong>Sale Details:</strong></p>
                <ul class="mb-0">
                    <li>Customer: <strong><?php echo htmlspecialchars($customer['name'] ?? 'N/A'); ?></strong></li>
                    <li>Amount: <strong>₦<?php echo number_format($sale['total_amount'], 2); ?></strong></li>
                    <li>Date: <strong><?php echo formatDate($sale['created_at']); ?></strong></li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>
                <form action="/new-stock-system/controllers/sales/delete/index.php" method="POST" style="display: inline;">
                    <input type="hidden" name="id" value="<?php echo $sale['id']; ?>">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Yes, Delete Everything
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>