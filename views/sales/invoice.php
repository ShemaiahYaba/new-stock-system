<?php
/**
 * Sales Invoice View
 * Display and export invoice
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Invoice - ' . APP_NAME;

$saleId = (int) ($_GET['id'] ?? 0);

if ($saleId <= 0) {
    setFlashMessage('error', 'Invalid sale ID.');
    header('Location: /new-stock-system/index.php?page=sales');
    exit();
}

$saleModel = new Sale();
$invoiceModel = new Invoice();

$sale = $saleModel->findById($saleId);

if (!$sale) {
    setFlashMessage('error', 'Sale not found.');
    header('Location: /new-stock-system/index.php?page=sales');
    exit();
}

// Get the invoice for this sale
$invoice = $saleModel->getInvoice($saleId);

if (!$invoice) {
    setFlashMessage('error', 'Invoice not found for this sale.');
    header('Location: /new-stock-system/index.php?page=sales');
    exit();
}

// Decode invoice shape
$invoiceData = $invoice['invoice_shape'];

// Extract data from invoice shape
$companyInfo = $invoiceData['company'] ?? [];
$customerInfo = $invoiceData['customer'] ?? [];
$metaInfo = $invoiceData['meta'] ?? [];
$items = $invoiceData['items'] ?? [];
$subtotal = $invoiceData['subtotal'] ?? 0;
$tax = $invoiceData['order_tax'] ?? 0;
$discount = $invoiceData['discount'] ?? 0;
$shipping = $invoiceData['shipping'] ?? 0;
$grandTotal = $invoiceData['grand_total'] ?? 0;
$paid = $invoiceData['paid'] ?? 0;
$due = $invoiceData['due'] ?? 0;
$notes = $invoiceData['notes'] ?? [];

// Generate invoice display info
$invoiceNumber =
    $invoice['invoice_number'] ??
    'INV-' . date('Y') . '-' . str_pad($sale['id'], 6, '0', STR_PAD_LEFT);
$invoiceDate = date('F d, Y', strtotime($invoice['created_at']));

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
                <p class="text-muted">Invoice #<?php echo htmlspecialchars($invoiceNumber); ?></p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Print Invoice
                </button>
                <a href="/new-stock-system/controllers/sales/export_invoice.php?id=<?php echo $sale[
                    'id'
                ]; ?>" 
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
                    <h2 class="text-primary mb-0"><?php echo htmlspecialchars(
                        $companyInfo['name'] ?? APP_NAME,
                    ); ?></h2>
                    <?php if (!empty($companyInfo['address'])): ?>
                    <p class="text-muted mb-0"><?php echo htmlspecialchars(
                        $companyInfo['address'],
                    ); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($companyInfo['phone'])): ?>
                    <p class="text-muted mb-0">Phone: <?php echo htmlspecialchars(
                        $companyInfo['phone'],
                    ); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($companyInfo['email'])): ?>
                    <p class="text-muted mb-0">Email: <?php echo htmlspecialchars(
                        $companyInfo['email'],
                    ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-end">
                    <h3 class="mb-0">INVOICE</h3>
                    <p class="mb-0"><strong><?php echo htmlspecialchars(
                        $invoiceNumber,
                    ); ?></strong></p>
                    <p class="text-muted mb-0"><?php echo $invoiceDate; ?></p>
                    <?php if (!empty($metaInfo['ref'])): ?>
                    <p class="text-muted mb-0 small">Ref: <?php echo htmlspecialchars(
                        $metaInfo['ref'],
                    ); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Customer & Sale Details -->
        <div class="invoice-details">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-primary">Bill To:</h5>
                    <p class="mb-1"><strong><?php echo htmlspecialchars(
                        $customerInfo['name'] ?? 'N/A',
                    ); ?></strong></p>
                    <?php if (!empty($customerInfo['company'])): ?>
                    <p class="mb-1"><em><?php echo htmlspecialchars(
                        $customerInfo['company'],
                    ); ?></em></p>
                    <?php endif; ?>
                    <?php if (!empty($customerInfo['phone'])): ?>
                    <p class="mb-1">Phone: <?php echo htmlspecialchars(
                        $customerInfo['phone'],
                    ); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($customerInfo['email'])): ?>
                    <p class="mb-1">Email: <?php echo htmlspecialchars(
                        $customerInfo['email'],
                    ); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($customerInfo['address'])): ?>
                    <p class="mb-1">Address: <?php echo htmlspecialchars(
                        $customerInfo['address'],
                    ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="text-primary">Sale Details:</h5>
                    <p class="mb-1"><strong>Type:</strong> <?php echo SALE_TYPES[
                        $sale['sale_type']
                    ] ?? 'Available Stock'; ?></p>
                    <p class="mb-1"><strong>Date:</strong> <?php echo $invoiceDate; ?></p>
                    <p class="mb-1"><strong>Status:</strong> 
                        <span class="badge bg-<?php echo $invoice['status'] === 'paid'
                            ? 'success'
                            : ($invoice['status'] === 'partial'
                                ? 'warning'
                                : 'danger'); ?>">
                            <?php echo strtoupper($invoice['status']); ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        <table class="table invoice-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th>Item Description</th>
                    <th class="text-end">Quantity (m)</th>
                    <th class="text-end">Unit Price (₦)</th>
                    <th class="text-end">Amount (₦)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted">No items found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($items as $index => $item): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars(
                                    $item['description'] ?? 'N/A',
                                ); ?></strong>
                            </td>
                            <td class="text-end"><?php echo number_format(
                                $item['quantity'] ?? 0,
                                2,
                            ); ?></td>
                            <td class="text-end">₦<?php echo number_format(
                                $item['unit_price'] ?? 0,
                                2,
                            ); ?></td>
                            <td class="text-end"><strong>₦<?php echo number_format(
                                $item['total'] ?? 0,
                                2,
                            ); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                    <td class="text-end"><strong>₦<?php echo number_format(
                        $subtotal,
                        2,
                    ); ?></strong></td>
                </tr>
                <?php if ($tax > 0): ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                    <td class="text-end"><strong>₦<?php echo number_format(
                        $tax,
                        2,
                    ); ?></strong></td>
                </tr>
                <?php endif; ?>
                <?php if ($discount > 0): ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                    <td class="text-end"><strong>-₦<?php echo number_format(
                        $discount,
                        2,
                    ); ?></strong></td>
                </tr>
                <?php endif; ?>
                <?php if ($shipping > 0): ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                    <td class="text-end"><strong>₦<?php echo number_format(
                        $shipping,
                        2,
                    ); ?></strong></td>
                </tr>
                <?php endif; ?>
                <tr class="table-primary">
                    <td colspan="4" class="text-end"><h5 class="mb-0">Total Amount:</h5></td>
                    <td class="text-end"><h5 class="mb-0">₦<?php echo number_format(
                        $grandTotal,
                        2,
                    ); ?></h5></td>
                </tr>
                <?php if ($paid > 0): ?>
                <tr>
                    <td colspan="4" class="text-end"><strong>Amount Paid:</strong></td>
                    <td class="text-end"><strong class="text-success">₦<?php echo number_format(
                        $paid,
                        2,
                    ); ?></strong></td>
                </tr>
                <tr class="table-warning">
                    <td colspan="4" class="text-end"><strong>Amount Due:</strong></td>
                    <td class="text-end"><strong class="text-danger">₦<?php echo number_format(
                        $due,
                        2,
                    ); ?></strong></td>
                </tr>
                <?php endif; ?>
            </tfoot>
        </table>
        
        <!-- Payment Info & Notes -->
        <div class="row mt-4">
            <div class="col-md-6">
                <h6 class="text-primary">Payment Information:</h6>
                <p class="mb-1"><strong>Status:</strong> 
                    <span class="badge bg-<?php echo $invoice['status'] === 'paid'
                        ? 'success'
                        : ($invoice['status'] === 'partial'
                            ? 'warning'
                            : 'danger'); ?>">
                        <?php echo strtoupper($invoice['status']); ?>
                    </span>
                </p>
                <p class="mb-1"><strong>Total:</strong> ₦<?php echo number_format(
                    $grandTotal,
                    2,
                ); ?></p>
                <p class="mb-1"><strong>Paid:</strong> ₦<?php echo number_format($paid, 2); ?></p>
                <p class="mb-1"><strong>Balance:</strong> ₦<?php echo number_format($due, 2); ?></p>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Notes:</h6>
                <?php if (!empty($notes['receipt_statement'])): ?>
                <p class="text-muted small mb-1"><?php echo htmlspecialchars(
                    $notes['receipt_statement'],
                ); ?></p>
                <?php endif; ?>
                <?php if (!empty($notes['refund_policy'])): ?>
                <p class="text-muted small mb-1"><?php echo htmlspecialchars(
                    $notes['refund_policy'],
                ); ?></p>
                <?php endif; ?>
                <?php if (!empty($notes['custom_notes'])): ?>
                <p class="text-muted small mb-1"><em><?php echo htmlspecialchars(
                    $notes['custom_notes'],
                ); ?></em></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Signatures -->
        <?php if (!empty($invoiceData['signatures'])): ?>
        <div class="row mt-5">
            <div class="col-md-6">
                <div class="text-center">
                    <div style="border-top: 2px solid #000; display: inline-block; padding-top: 10px; min-width: 200px;">
                        <strong>Customer Signature</strong>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-center">
                    <div style="border-top: 2px solid #000; display: inline-block; padding-top: 10px; min-width: 200px;">
                        <strong>For: <?php echo htmlspecialchars(
                            $invoiceData['signatures']['for_company'] ?? APP_NAME,
                        ); ?></strong>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Footer -->
        <div class="invoice-footer text-center">
            <p class="text-muted small mb-1">This is a computer-generated invoice.</p>
            <p class="text-muted small mb-0">Invoice generated on <?php echo date(
                'F d, Y \a\t h:i A',
            ); ?></p>
        </div>
    </div>
    
    <div class="text-center mt-4 no-print">
        <button onclick="window.print()" class="btn btn-lg btn-primary">
            <i class="bi bi-printer"></i> Print Invoice
        </button>
        <a href="/new-stock-system/controllers/sales/export_invoice.php?id=<?php echo $sale[
            'id'
        ]; ?>" 
           class="btn btn-lg btn-success" target="_blank">
            <i class="bi bi-file-pdf"></i> Download PDF
        </a>
        <a href="/new-stock-system/index.php?page=sales" class="btn btn-lg btn-secondary">
            <i class="bi bi-list"></i> Back to Sales List
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
