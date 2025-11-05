<?php
/**
 * Sales Invoice View
 * Display and export invoice
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Invoice - ' . APP_NAME;

$saleId = (int)($_GET['id'] ?? 0);

if ($saleId <= 0) {
    setFlashMessage('error', 'Invalid sale ID.');
    header('Location: /new-stock-system/index.php?page=sales');
    exit();
}

$saleModel = new Sale();
$sale = $saleModel->findById($saleId);

if (!$sale) {
    setFlashMessage('error', 'Sale not found.');
    header('Location: /new-stock-system/index.php?page=sales');
    exit();
}

// Generate invoice number
$invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($sale['id'], 6, '0', STR_PAD_LEFT);
$invoiceDate = date('F d, Y', strtotime($sale['created_at']));

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<style>
@media print {
    .no-print { display: none !important; }
    .content-wrapper { margin: 0 !important; padding: 0 !important; }
    .invoice-container { box-shadow: none !important; }
}

.invoice-container {
    background: white;
    padding: 40px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.invoice-header {
    border-bottom: 3px solid #007bff;
    padding-bottom: 20px;
    margin-bottom: 30px;
}

.invoice-details {
    margin-bottom: 30px;
}

.invoice-table th {
    background: #f8f9fa;
    font-weight: 600;
}

.invoice-footer {
    margin-top: 40px;
    padding-top: 20px;
    border-top: 2px solid #dee2e6;
}
</style>

<div class="content-wrapper">
    <div class="page-header no-print">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Invoice</h1>
                <p class="text-muted">Invoice #<?php echo $invoiceNumber; ?></p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print Invoice
                </button>
                <a href="/new-stock-system/controllers/sales/export_invoice.php?id=<?php echo $sale['id']; ?>" 
                   class="btn btn-success" target="_blank">
                    <i class="bi bi-file-pdf"></i> Export PDF
                </a>
                <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Sales
                </a>
            </div>
        </div>
    </div>
    
    <div class="invoice-container">
        <!-- Invoice Header -->
        <div class="invoice-header">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="text-primary mb-0"><?php echo APP_NAME; ?></h2>
                    <p class="text-muted mb-0">Stock Management System</p>
                </div>
                <div class="col-md-6 text-end">
                    <h3 class="mb-0">INVOICE</h3>
                    <p class="mb-0"><strong><?php echo $invoiceNumber; ?></strong></p>
                    <p class="text-muted mb-0"><?php echo $invoiceDate; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Customer & Sale Details -->
        <div class="invoice-details">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary">Bill To:</h5>
                    <p class="mb-1"><strong><?php echo htmlspecialchars($sale['customer_name']); ?></strong></p>
                    <?php if ($sale['customer_phone']): ?>
                    <p class="mb-1">Phone: <?php echo htmlspecialchars($sale['customer_phone']); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="text-primary">Sale Details:</h5>
                    <p class="mb-1"><strong>Type:</strong> <?php echo SALE_TYPES[$sale['sale_type']]; ?></p>
                    <p class="mb-1"><strong>Date:</strong> <?php echo $invoiceDate; ?></p>
                    <p class="mb-1"><strong>Processed By:</strong> <?php echo htmlspecialchars($sale['created_by_name']); ?></p>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="table invoice-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-center">Coil Code</th>
                    <th class="text-end">Meters</th>
                    <th class="text-end">Price/Meter</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($sale['coil_name']); ?></strong><br>
                        <small class="text-muted">Status: <?php echo STOCK_STATUSES[$sale['coil_status']] ?? $sale['coil_status']; ?></small>
                    </td>
                    <td class="text-center">
                        <strong><?php echo htmlspecialchars($sale['coil_code']); ?></strong>
                    </td>
                    <td class="text-end"><?php echo number_format($sale['meters'], 2); ?>m</td>
                    <td class="text-end">₦<?php echo number_format($sale['price_per_meter'], 2); ?></td>
                    <td class="text-end"><strong>₦<?php echo number_format($sale['total_amount'], 2); ?></strong></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                    <td class="text-end"><strong>₦<?php echo number_format($sale['total_amount'], 2); ?></strong></td>
                </tr>
                <tr>
                    <td colspan="4" class="text-end"><strong>Tax (0%):</strong></td>
                    <td class="text-end"><strong>₦0.00</strong></td>
                </tr>
                <tr class="table-primary">
                    <td colspan="4" class="text-end"><h5 class="mb-0">Total Amount:</h5></td>
                    <td class="text-end"><h5 class="mb-0">₦<?php echo number_format($sale['total_amount'], 2); ?></h5></td>
                </tr>
            </tfoot>
        </table>
        
        <!-- Payment Info -->
        <div class="row mt-4">
            <div class="col-md-6">
                <h6 class="text-primary">Payment Information:</h6>
                <p class="mb-1"><strong>Status:</strong> <span class="badge bg-<?php echo $sale['status'] === 'completed' ? 'success' : 'warning'; ?>"><?php echo ucfirst($sale['status']); ?></span></p>
                <p class="mb-1"><strong>Method:</strong> Cash/Bank Transfer</p>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Notes:</h6>
                <p class="text-muted small mb-0">Thank you for your business!</p>
                <p class="text-muted small mb-0">For any queries, please contact us.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="invoice-footer text-center">
            <p class="text-muted small mb-1">This is a computer-generated invoice and does not require a signature.</p>
            <p class="text-muted small mb-0">Invoice generated on <?php echo date('F d, Y \a\t h:i A'); ?></p>
        </div>
    </div>
    
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-lg btn-primary">
            <i class="bi bi-printer"></i> Print Invoice
        </button>
        <a href="/new-stock-system/controllers/sales/export_invoice.php?id=<?php echo $sale['id']; ?>" 
           class="btn btn-lg btn-success" target="_blank">
            <i class="bi bi-file-pdf"></i> Download PDF
        </a>
        <a href="/new-stock-system/index.php?page=sales" class="btn btn-lg btn-secondary">
            <i class="bi bi-list"></i> Back to Sales List
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
