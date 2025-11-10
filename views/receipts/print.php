<?php
/**
 * Receipt Print View
 * File: views/receipts/print.php
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/receipt.php';
require_once __DIR__ . '/../../utils/helpers.php';
require_once __DIR__ . '/../../utils/print_helpers.php';

$receiptId = (int) ($_GET['id'] ?? 0);

if ($receiptId <= 0) {
    die('Invalid receipt ID');
}

$receiptModel = new Receipt();
$receipt = $receiptModel->findById($receiptId);

if (!$receipt) {
    die('Receipt not found');
}

// Calculate balance
$balance = $receipt['invoice_total'] - $receipt['invoice_paid'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt RCPT-<?= str_pad($receipt['id'], 5, '0', STR_PAD_LEFT) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
            padding: 20px;
            background: #fff;
            font-size: 14px;
        }
        
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border: 2px solid #000;
        }
        
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000;
        }
        
        .receipt-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        
        .receipt-number {
            font-size: 18px;
            color: #666;
        }
        
        .company-info, .customer-info {
            margin-bottom: 20px;
        }
        
        .info-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .payment-details {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #dee2e6;
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .amount-paid {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            text-align: center;
            padding: 20px;
            background-color: #d4edda;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .invoice-summary {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #000;
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
            margin: 40px auto 10px;
            width: 80%;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
            .receipt-container { border: none; padding: 20px; }
        }
    </style>
</head>
<body>
    <!-- Print Button -->
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print(); return false;" style="padding: 10px 20px; font-size: 16px; cursor: pointer;">
            🖨️ Print Receipt
        </button>
        <button onclick="window.close(); return false;" style="padding: 10px 20px; font-size: 16px; cursor: pointer; margin-left: 10px;">
            ✖️ Close
        </button>
    </div>

    <div class="receipt-container">
        <!-- Header -->
        <div class="receipt-header">
            <div class="receipt-title">Payment Receipt</div>
            <div class="receipt-number">RCPT-<?= str_pad(
                $receipt['id'],
                5,
                '0',
                STR_PAD_LEFT,
            ) ?></div>
            <div style="margin-top: 10px; font-size: 14px;">
                Date: <?= date('F d, Y h:i A', strtotime($receipt['created_at'])) ?>
            </div>
        </div>

        <!-- Company Info -->
        <div class="company-info">
            <div class="info-label">From:</div>
            <div><strong><?= defined('COMPANY_NAME')
                ? COMPANY_NAME
                : 'Obumek Alluminium Company Ltd.' ?></strong></div>
            <div>Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja</div>
            <div>Phone: +2348065336645</div>
            <div>Email: info@obumekalluminium.com</div>
        </div>

        <!-- Customer Info -->
        <div class="customer-info">
            <div class="info-label">Received From:</div>
            <div><strong><?= htmlspecialchars($receipt['customer_name']) ?></strong></div>
            <?php if (!empty($receipt['customer_phone'])): ?>
            <div>Phone: <?= htmlspecialchars($receipt['customer_phone']) ?></div>
            <?php endif; ?>
        </div>

        <!-- Amount Paid (Highlighted) -->
        <div class="amount-paid">
            AMOUNT PAID: ₦<?= number_format($receipt['amount_paid'], 2) ?>
        </div>

        <!-- Payment Details -->
        <div class="payment-details">
            <div class="detail-row">
                <span>Payment Method:</span>
                <strong><?= ucwords(str_replace('_', ' ', $receipt['payment_method'])) ?></strong>
            </div>
            <?php if (!empty($receipt['reference'])): ?>
            <div class="detail-row">
                <span>Reference:</span>
                <strong><?= htmlspecialchars($receipt['reference']) ?></strong>
            </div>
            <?php endif; ?>
            <div class="detail-row">
                <span>Received By:</span>
                <strong><?= htmlspecialchars($receipt['created_by_name'] ?? 'System') ?></strong>
            </div>
        </div>

        <!-- Invoice Summary -->
        <div class="invoice-summary">
            <div class="info-label">Invoice Details:</div>
            <div class="payment-details">
                <div class="detail-row">
                    <span>Invoice Number:</span>
                    <strong><?= htmlspecialchars($receipt['invoice_number']) ?></strong>
                </div>
                <div class="detail-row">
                    <span>Invoice Total:</span>
                    <strong>₦<?= number_format($receipt['invoice_total'], 2) ?></strong>
                </div>
                <div class="detail-row">
                    <span>Total Paid:</span>
                    <strong class="text-success">₦<?= number_format(
                        $receipt['invoice_paid'],
                        2,
                    ) ?></strong>
                </div>
                <div class="detail-row">
                    <span>Balance Due:</span>
                    <strong style="color: <?= $balance > 0 ? '#dc3545' : '#28a745' ?>">
                        ₦<?= number_format($balance, 2) ?>
                    </strong>
                </div>
                <div class="detail-row">
                    <span>Invoice Status:</span>
                    <strong><?= strtoupper($receipt['invoice_status']) ?></strong>
                </div>
            </div>
        </div>

        <!-- Amount in Words -->
        <div style="margin-top: 20px; padding: 10px; background-color: #f8f9fa; border-radius: 4px;">
            <strong>Amount in words:</strong><br>
            <?= numberToWords($receipt['amount_paid']) ?>
        </div>

        <!-- Signatures -->
        <div class="signature-area">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Company Signature</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div>Customer Signature</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This is a computer-generated receipt.</p>
            <p>Generated on <?= date('F j, Y \a\t g:i A') ?></p>
            <p>Thank you for your payment!</p>
        </div>
    </div>

    <script>
        // Auto-print when page loads
        window.onload = function() {
            // Uncomment the line below to auto-print
            // window.print();
        };
    </script>
</body>
</html>