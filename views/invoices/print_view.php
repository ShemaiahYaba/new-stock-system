<?php
/**
 * Modern Invoice Print View
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/print_helpers.php';

$invoiceId = (int) ($_GET['id'] ?? 0);

if ($invoiceId <= 0) {
    die('Invalid invoice ID');
}

$invoiceModel = new Invoice();
$invoice = $invoiceModel->findById($invoiceId);

if (!$invoice) {
    die('Invoice not found');
}

// Handle invoice shape data
$invoiceData = is_array($invoice['invoice_shape'])
    ? $invoice['invoice_shape']
    : (is_string($invoice['invoice_shape'])
        ? json_decode($invoice['invoice_shape'], true)
        : []);

if (!is_array($invoiceData)) {
    $invoiceData = [];
}

// Calculate amounts
$totalAmount = $invoice['total'] ?? 0;
$paidAmount = $invoice['paid_amount'] ?? 0;
$balance = $totalAmount - $paidAmount;
$isPaid = $balance <= 0;

// Set the content type to HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= htmlspecialchars($invoice['invoice_number'] ?? '') ?> - <?= defined(
     'APP_NAME',
 )
     ? APP_NAME
     : 'Stock System' ?></title>
    <style>
        /* Basic reset */
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            background: #fff;
            font-size: 14px;
        }
        
        /* Modern Invoice Styles */
        .text-uppercase { text-transform: uppercase; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .font-weight-bold { font-weight: 600; }
        .mb-0 { margin-bottom: 0; }
        .mb-1 { margin-bottom: 0.5rem; }
        .mb-2 { margin-bottom: 1rem; }
        .mb-3 { margin-bottom: 1.5rem; }
        .mt-2 { margin-top: 1rem; }
        .mt-3 { margin-top: 1.5rem; }
        .p-3 { padding: 1.5rem; }
        .border-bottom { border-bottom: 1px solid #dee2e6; }
        .bg-light { background-color: #f8f9fa; }
        
        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .company-logo {
            max-width: 180px;
            margin-bottom: 15px;
        }
        
        .invoice-title {
            color: #2c3e50;
            margin-bottom: 5px;
            font-size: 24px;
        }
        
        .invoice-info {
            text-align: right;
        }
        
        .invoice-info p {
            margin: 5px 0;
            color: #6c757d;
        }
        
        .customer-info {
            margin-bottom: 30px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .items-table th {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border: 1px solid #dee2e6;
        }
        
        .items-table td {
            padding: 12px 10px;
            border: 1px solid #dee2e6;
            vertical-align: top;
        }
        
        .totals-table {
            width: 300px;
            margin-left: auto;
            margin-top: 20px;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 10px;
            border: 1px solid #dee2e6;
        }
        
        .totals-table tr:last-child td {
            font-weight: 600;
            font-size: 1.1em;
            background-color: #f8f9fa;
        }
        
        .signature-area {
            margin-top: 50px;
            display: flex;
            justify-content: space-between;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin: 10px 0;
            padding-top: 10px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-paid { background-color: #d4edda; color: #155724; }
        .status-partial { background-color: #fff3cd; color: #856404; }
        .status-unpaid { background-color: #f8d7da; color: #721c24; }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: #fff !important; }
            .invoice-container { box-shadow: none; padding: 0; }
            .page-break { page-break-before: always; }
        }
        
        /* Layout */
        .container-fluid { 
            max-width: 1000px; 
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Typography */
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-start { text-align: left; }
        
        /* Spacing */
        .mb-4 { margin-bottom: 1.5rem; }
        .me-2 { margin-right: 0.5rem; }
        .mt-5 { margin-top: 3rem; }
        
        /* Buttons */
        .btn { 
            display: inline-block; 
            padding: 0.375rem 0.75rem; 
            border: 1px solid transparent; 
            border-radius: 0.25rem; 
            text-decoration: none; 
            cursor: pointer;
        }
        .btn-primary { 
            color: #fff; 
            background-color: #0d6efd; 
            border-color: #0d6efd; 
        }
        .btn-outline-secondary { 
            color: #6c757d; 
            border-color: #6c757d; 
            background-color: transparent; 
        }
        
        /* Layout Utilities */
        .d-flex { display: flex; }
        .justify-content-between { justify-content: space-between; }
        .align-items-start { align-items: flex-start; }
        .flex-col { display: flex; flex-direction: column; }
        
        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }
        
        /* Company Info */
        .company-logo { 
            margin-bottom: 1rem; 
        }
        .company-logo img { 
            max-height: 50px; 
            width: auto; 
        }
        .company-info h2 { 
            color: #2c3e50;
            margin: 0 0 0.5rem 0; 
            font-size: 1.5rem; 
        }
        
        /* Invoice Meta */
        .invoice-title { 
            font-size: 1.75rem; 
            margin: 0 0 1rem 0; 
            color: #3498db; 
        }
        .invoice-meta { 
            text-align: right; 
        }
        .invoice-meta p { 
            margin: 0.25rem 0; 
            color: #7f8c8d;
        }
        
        /* Tables */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 1.5rem 0; 
        }
        th, td { 
            padding: 0.75rem; 
            text-align: left; 
            border-bottom: 1px solid #dee2e6; 
        }
        th { 
            background-color: #f8f9fa; 
            font-weight: 600; 
        }
        
        /* Status Badges */
        .status-badge { 
            display: inline-block; 
            padding: 0.25rem 0.5rem; 
            border-radius: 0.25rem; 
            font-size: 0.875rem; 
            font-weight: 600; 
        }
        .status-paid { 
            background-color: #d1e7dd; 
            color: #0f5132; 
        }
        .status-partial { 
            background-color: #fff3cd; 
            color: #664d03; 
        }
        .status-unpaid { 
            background-color: #f8d7da; 
            color: #842029; 
        }
        
        /* Print Styles */
        @media print {
            .no-print { 
                display: none !important; 
            }
            body { 
                padding: 0;
                background: #fff !important;
            }
            .container-fluid { 
                max-width: 100%; 
                padding: 20px;
                box-shadow: none;
            }
            table { 
                page-break-inside: auto; 
            }
            tr { 
                page-break-inside: avoid; 
                page-break-after: auto; 
            }
            .company-logo img {
                max-height: 60px;
            }
        }
        .customer-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .customer-info h4 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
        }
        .items-table th {
            background-color: #3498db;
            color: white;
            padding: 12px;
            text-align: left;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals-table {
            width: 300px;
            margin-left: auto;
            margin-top: 30px;
        }
        .totals-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .totals-table tr:last-child td {
            font-weight: bold;
            font-size: 1.1em;
            border-bottom: none;
        }
        .notes {
            margin-top: 40px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .signature-area {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            text-align: center;
            width: 45%;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 40px auto 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            margin-left: 10px;
        }
        .status-paid { background-color: #d1e7dd; color: #0f5132; }
        .status-partial { background-color: #fff3cd; color: #664d03; }
        .status-unpaid { background-color: #f8d7da; color: #842029; }
        
        @media print {
            .no-print {
                display: none !important;
            }
            .invoice-container {
                box-shadow: none;
                padding: 0;
            }
            body {
                padding: 20px;
                background: #fff !important;
            }
        }
    </style>
</head>
<body>
    <!-- Print Controls (Hidden when printing) -->
    <div class="no-print text-center mb-4">
        <button onclick="window.print(); return false;" class="btn btn-primary me-2 print-button">
            <i class="bi bi-printer me-1"></i> Print Invoice
        </button>
        <button onclick="window.close(); return false;" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> Close
        </button>
    </div>

    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div>
                <img src="/new-stock-system/assets/logo.png" alt="Company Logo" class="company-logo">
                <h2 class="company-name mb-0"><?= htmlspecialchars(
                    $invoiceData['company']['name'] ?? 'Stock System',
                ) ?></h2>
                <p class="text-muted mb-0"><?= nl2br(
                    htmlspecialchars($invoiceData['company']['address'] ?? ''),
                ) ?></p>
                <p class="text-muted mb-0">Phone: <?= htmlspecialchars(
                    $invoiceData['company']['phone'] ?? '',
                ) ?></p>
                <p class="text-muted">Email: <?= htmlspecialchars(
                    $invoiceData['company']['email'] ?? '',
                ) ?></p>
            </div>
            <div class="invoice-info">
                <h1 class="invoice-title">SALES INVOICE</h1>
                <p><strong>Invoice #:</strong> <?= htmlspecialchars(
                    $invoice['invoice_number'],
                ) ?></p>
                <p><strong>Date:</strong> <?= date(
                    'F d, Y',
                    strtotime($invoice['created_at']),
                ) ?></p>
                <p><strong>Status:</strong> 
                    <span class="status-badge status-<?= $isPaid
                        ? 'paid'
                        : ($paidAmount > 0
                            ? 'partial'
                            : 'unpaid') ?>">
                        <?= $isPaid ? 'Paid' : ($paidAmount > 0 ? 'Partially Paid' : 'Unpaid') ?>
                    </span>
                </p>
                <?php if (!empty($invoiceData['meta']['ref'])): ?>
                    <p><strong>Reference:</strong> <?= htmlspecialchars(
                        $invoiceData['meta']['ref'],
                    ) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Customer & Invoice Info -->
        <div class="row mb-4" style="display: flex; flex-wrap: nowrap; gap: 20px;">
            <div style="flex: 1; min-width: 0;">
                <div class="bg-light p-3" style="height: 100%;">
                    <h4 class="mb-2" style="font-size: 16px; line-height: 1.2;">Bill To:</h4>
                    <p class="mb-1 font-weight-bold" style="line-height: 1.2;"><?= htmlspecialchars(
                        $invoiceData['customer']['name'] ?? '',
                    ) ?></p>
                    <p class="mb-1" style="line-height: 1.2;"><?= nl2br(
                        htmlspecialchars($invoiceData['customer']['address'] ?? ''),
                    ) ?></p>
                    <?php if (!empty($invoiceData['customer']['phone'])): ?>
                        <p class="mb-1" style="line-height: 1.2;">Phone: <?= htmlspecialchars(
                            $invoiceData['customer']['phone'],
                        ) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($invoiceData['customer']['email'])): ?>
                        <p class="mb-0" style="line-height: 1.2;">Email: <?= htmlspecialchars(
                            $invoiceData['customer']['email'],
                        ) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div class="bg-light p-3" style="height: 100%;">
                    <h4 class="mb-2" style="font-size: 16px; line-height: 1.2;">Invoice Details</h4>
                    <p class="mb-1" style="line-height: 1.2;"><strong>Invoice #:</strong> <?= htmlspecialchars($invoice['invoice_number'] ?? '') ?></p>
                    <p class="mb-1" style="line-height: 1.2;"><strong>Invoice Date:</strong> <?= date(
                        'F d, Y',
                        strtotime($invoice['created_at']),
                    ) ?></p>
                    <p class="mb-1" style="line-height: 1.2;"><strong>Due Date:</strong> <?= !empty($invoice['due_date'])
                        ? date('F d, Y', strtotime($invoice['due_date']))
                        : 'On Receipt' ?></p>
                    <p class="mb-0" style="line-height: 1.2;"><strong>Terms:</strong> <?= !empty($invoice['payment_terms'])
                        ? htmlspecialchars($invoice['payment_terms'])
                        : 'Net 30' ?></p>
                </div>
            </div>
        </div>
            <script>
                // Add print functionality
                document.addEventListener('DOMContentLoaded', function() {
                    // Auto-print when the page loads (only in print view)
                    if (window.location.search.indexOf('print=1') > -1) {
                        window.print();
                        // Close the window after printing (or if print is cancelled)
                        window.onafterprint = function() {
                            setTimeout(window.close, 500);
                        };
                    }
                    
                    // Handle print button click
                    document.querySelector('.print-button')?.addEventListener('click', function() {
                        // Open print dialog
                        window.print();
                    });
                });
            </script>

            <!-- Items Table -->
            <div class="table-responsive">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th class="border-bottom" style="width: 5%;">#</th>
                            <th class="border-bottom" style="width: 45%;">DESCRIPTION</th>
                            <th class="text-right border-bottom" style="width: 10%;">QTY</th>
                            <th class="text-right border-bottom" style="width: 20%;">UNIT PRICE</th>
                            <th class="text-right border-bottom" style="width: 20%;">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $subtotal = 0;
                        $itemCount = 1;
                        foreach ($invoiceData['items'] as $item):

                            $amount = $item['quantity'] * $item['unit_price'];
                            $subtotal += $amount;
                            ?>
                        <tr>
                            <td class="border-bottom"><?= $itemCount++ ?></td>
                            <td class="border-bottom">
                                <div class="font-weight-bold"><?= htmlspecialchars(
                                    $item['description'],
                                ) ?></div>
                                <?php if (!empty($item['details'])): ?>
                                    <div class="text-muted small"><?= nl2br(
                                        htmlspecialchars($item['details']),
                                    ) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="text-right border-bottom"><?= number_format(
                                $item['quantity'],
                                2,
                            ) ?></td>
                            <td class="text-right border-bottom">₦<?= number_format(
                                $item['unit_price'],
                                2,
                            ) ?></td>
                            <td class="text-right border-bottom">₦<?= number_format(
                                $amount,
                                2,
                            ) ?></td>
                        </tr>
                        <?php
                        endforeach;
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="row">
                <div class="col-md-6">
                    <?php if (!empty($invoiceData['notes']['terms'])): ?>
                        <div class="p-3 bg-light">
                            <h5 class="mb-2">Terms & Conditions</h5>
                            <p class="mb-0"><?= nl2br(
                                htmlspecialchars($invoiceData['notes']['terms']),
                            ) ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <table class="totals-table">
                        <tr>
                            <td>Subtotal:</td>
                            <td class="text-right">₦<?= number_format($subtotal, 2) ?></td>
                        </tr>
                <?php if (($invoiceData['tax'] ?? 0) > 0): ?>
                <tr>
                    <td>Tax (<?= $invoiceData['tax_type'] === 'percentage'
                        ? $invoiceData['tax_value'] . '%'
                        : '' ?>):</td>
                    <td class="text-right">₦<?= number_format($invoiceData['tax'] ?? 0, 2) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($invoiceData['shipping'] ?? 0) > 0): ?>
                <tr>
                    <td>Shipping:</td>
                    <td class="text-right">₦<?= number_format(
                        $invoiceData['shipping'] ?? 0,
                        2,
                    ) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (($invoiceData['discount'] ?? 0) > 0): ?>
                <tr>
                    <td>Discount (<?= ($invoiceData['discount_type'] ?? 'fixed') === 'percentage'
                        ? ($invoiceData['discount_value'] ?? '0') . '%'
                        : 'Fixed' ?>):</td>
                    <td class="text-right">-₦<?= number_format(
                        $invoiceData['discount'],
                        2,
                    ) ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td><strong>Total:</strong></td>
                    <td class="text-right"><strong>₦<?= number_format(
                        $totalAmount,
                        2,
                    ) ?></strong></td>
                </tr>
                <?php if ($paidAmount > 0): ?>
                <tr>
                    <td>Paid:</td>
                    <td class="text-right">₦<?= number_format($paidAmount, 2) ?></td>
                </tr>
                <tr>
                    <td><strong>Balance Due:</strong></td>
                    <td class="text-right"><strong>₦<?= number_format($balance, 2) ?></strong></td>
                </tr>
                <?php endif; ?>
            </table>

            <!-- Amount in Words -->
            <div class="mt-4 p-3" style="background-color: #f8f9fa; border-radius: 4px; margin-top: 20px;">
                <p class="mb-1"><strong>Amount in words:</strong></p>
                <p class="mb-0"><?= numberToWords($totalAmount) ?></p>
            </div>

            <!-- Notes -->
            <?php if (!empty($invoiceData['notes']['custom_notes'])): ?>
            <div class="notes">
                <h5>Notes</h5>
                <p><?= nl2br(htmlspecialchars($invoiceData['notes']['custom_notes'])) ?></p>
            </div>
            <?php endif; ?>

            <!-- Signatures -->
            <div class="signature-area">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p>Customer's Signature</p>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p>Authorized Signature</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-5 text-muted" style="font-size: 0.9em;">
                <p>Thank you for your business!</p>
                <p>Generated on <?= date('F j, Y \a\t g:i A') ?></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-print when the page loads (for print view)
        document.addEventListener('DOMContentLoaded', function() {
            // Only auto-print if we're in the print view (not in the main view)
            if (window.location.search.includes('print=1')) {
                window.print();
                
                // Close the window after printing (with a delay to ensure print dialog shows)
                window.onafterprint = function() {
                    setTimeout(function() {
                        window.close();
                    }, 500);
                };
            }
        });
    </script>
</body>
</html>
