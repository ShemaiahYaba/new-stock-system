<?php
/**
 * Export Invoice as PDF
 * Simple HTML to PDF conversion without external libraries
 */

session_start();

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/auth_middleware.php';

requirePermission(MODULE_SALES_MANAGEMENT, ACTION_VIEW);

$saleId = (int)($_GET['id'] ?? 0);

if ($saleId <= 0) {
    die('Invalid sale ID.');
}

$saleModel = new Sale();
$sale = $saleModel->findById($saleId);

if (!$sale) {
    die('Sale not found.');
}

// Generate invoice number
$invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($sale['id'], 6, '0', STR_PAD_LEFT);
$invoiceDate = date('F d, Y', strtotime($sale['created_at']));

// Set headers for PDF download
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $invoiceNumber . '.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// For now, we'll generate HTML that can be saved as PDF
// In production, use a library like TCPDF or mPDF
// This is a simple HTML version that browsers can "Print to PDF"

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice <?php echo $invoiceNumber; ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, sans-serif; 
            padding: 40px;
            font-size: 12px;
            line-height: 1.6;
        }
        .invoice-header {
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .row { display: flex; justify-content: space-between; margin-bottom: 20px; }
        .col-left { width: 48%; }
        .col-right { width: 48%; text-align: right; }
        h1 { color: #007bff; font-size: 24px; margin-bottom: 5px; }
        h2 { font-size: 20px; margin-bottom: 10px; }
        h3 { font-size: 16px; margin-bottom: 5px; }
        h5 { font-size: 14px; margin-bottom: 10px; color: #007bff; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: 600; }
        .text-end { text-align: right; }
        .text-center { text-align: center; }
        .text-muted { color: #6c757d; }
        .text-primary { color: #007bff; }
        .total-row { background: #e7f3ff; font-weight: bold; }
        .invoice-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
    </style>
</head>
<body>
    <!-- Invoice Header -->
    <div class="invoice-header">
        <div class="row">
            <div class="col-left">
                <h1><?php echo APP_NAME; ?></h1>
                <p class="text-muted">Stock Management System</p>
            </div>
            <div class="col-right">
                <h2>INVOICE</h2>
                <p><strong><?php echo $invoiceNumber; ?></strong></p>
                <p class="text-muted"><?php echo $invoiceDate; ?></p>
            </div>
        </div>
    </div>
    
    <!-- Customer & Sale Details -->
    <div class="row">
        <div class="col-left">
            <h5>Bill To:</h5>
            <p><strong><?php echo htmlspecialchars($sale['customer_name']); ?></strong></p>
            <?php if ($sale['customer_phone']): ?>
            <p>Phone: <?php echo htmlspecialchars($sale['customer_phone']); ?></p>
            <?php endif; ?>
        </div>
        <div class="col-right">
            <h5>Sale Details:</h5>
            <p><strong>Type:</strong> <?php echo SALE_TYPES[$sale['sale_type']]; ?></p>
            <p><strong>Date:</strong> <?php echo $invoiceDate; ?></p>
            <p><strong>Processed By:</strong> <?php echo htmlspecialchars($sale['created_by_name']); ?></p>
        </div>
    </div>
    
    <!-- Items Table -->
    <table>
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
            <tr class="total-row">
                <td colspan="4" class="text-end"><strong>Total Amount:</strong></td>
                <td class="text-end"><strong>₦<?php echo number_format($sale['total_amount'], 2); ?></strong></td>
            </tr>
        </tfoot>
    </table>
    
    <!-- Payment Info -->
    <div class="row">
        <div class="col-left">
            <h5>Payment Information:</h5>
            <p><strong>Status:</strong> 
                <span class="badge badge-<?php echo $sale['status'] === 'completed' ? 'success' : 'warning'; ?>">
                    <?php echo ucfirst($sale['status']); ?>
                </span>
            </p>
            <p><strong>Method:</strong> Cash/Bank Transfer</p>
        </div>
        <div class="col-right">
            <h5>Notes:</h5>
            <p class="text-muted">Thank you for your business!</p>
            <p class="text-muted">For any queries, please contact us.</p>
        </div>
    </div>
    
    <!-- Footer -->
    <div class="invoice-footer">
        <p>This is a computer-generated invoice and does not require a signature.</p>
        <p>Invoice generated on <?php echo date('F d, Y \a\t h:i A'); ?></p>
        <p><strong><?php echo APP_NAME; ?></strong> | Stock Management System</p>
    </div>
    
    <script>
        // Auto-trigger print dialog
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
<?php
exit();
?>
