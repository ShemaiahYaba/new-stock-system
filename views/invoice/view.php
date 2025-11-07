<?php
/**
 * Receipt Detail View
 * File: views/receipts/view.php
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/receipt.php';
require_once __DIR__ . '/../../utils/helpers.php';

// Check if receipt ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    setFlashMessage('error', 'Invalid receipt ID');
    header('Location: ?page=receipts');
    exit();
}

$receiptId = (int) $_GET['id'];
$receiptModel = new Receipt();
$receipt = $receiptModel->findById($receiptId);

if (!$receipt) {
    setFlashMessage('error', 'Receipt not found');
    header('Location: ?page=receipts');
    exit();
}

$pageTitle = 'Receipt #' . $receipt['id'] . ' - ' . APP_NAME;
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-receipt"></i> Receipt Details
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="?page=dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="?page=receipts">Receipts</a></li>
                        <li class="breadcrumb-item active" aria-current="page">#<?= $receipt[
                            'id'
                        ] ?></li>
                    </ol>
                </nav>
            </div>
            <div class="btn-group">
                <a href="?page=receipts" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Receipt #<?= $receipt['id'] ?></h5>
                        <span class="badge bg-<?= getStatusBadgeClass($receipt['status']) ?>">
                            <?= ucfirst($receipt['status']) ?>
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Receipt Header -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>From</h6>
                            <address>
                                <strong><?= APP_NAME ?></strong><br>
                                Plot E18-E19, Saburi, Dei-Dei<br>
                                FCT, Abuja, Nigeria<br>
                                Phone: +2348065336645<br>
                                Email: info@example.com
                            </address>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6>Receipt Details</h6>
                            <p class="mb-1">
                                <strong>Receipt #:</strong> <?= $receipt['id'] ?><br>
                                <strong>Date:</strong> <?= formatDate($receipt['created_at']) ?><br>
                                <strong>Payment Method:</strong> <?= ucfirst(
                                    $receipt['payment_method'],
                                ) ?><br>
                                <?php if (!empty($receipt['reference'])): ?>
                                <strong>Reference:</strong> <?= htmlspecialchars(
                                    $receipt['reference'],
                                ) ?><br>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>

                    <!-- Invoice Info -->
                    <div class="border-top border-bottom py-3 mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Invoice Information</h6>
                                <p class="mb-1">
                                    <strong>Invoice #:</strong> 
                                    <a href="?page=invoices&action=view&id=<?= $receipt[
                                        'invoice_id'
                                    ] ?>">
                                        <?= htmlspecialchars($receipt['invoice_number']) ?>
                                    </a><br>
                                    <strong>Customer:</strong> 
                                    <?= htmlspecialchars($receipt['customer_name'] ?? 'N/A') ?><br>
                                    <strong>Phone:</strong> 
                                    <?= htmlspecialchars($receipt['customer_phone'] ?? 'N/A') ?>
                                </p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h6>Payment Details</h6>
                                <p class="mb-1">
                                    <strong>Amount Paid:</strong> 
                                    <?= formatCurrency($receipt['amount_paid']) ?><br>
                                    <strong>Payment Date:</strong> 
                                    <?= formatDate($receipt['created_at']) ?><br>
                                    <strong>Processed By:</strong> 
                                    <?= htmlspecialchars($receipt['created_by_name'] ?? 'System') ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="mb-4">
                        <h6>Payment Details</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-end">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Payment for Invoice #<?= htmlspecialchars(
                                            $receipt['invoice_number'],
                                        ) ?></td>
                                        <td class="text-end"><?= formatCurrency(
                                            $receipt['amount_paid'],
                                        ) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-end fw-bold">Total:</td>
                                        <td class="text-end fw-bold"><?= formatCurrency(
                                            $receipt['amount_paid'],
                                        ) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    <?php if (!empty($receipt['notes'])): ?>
                    <div class="alert alert-light">
                        <h6>Notes:</h6>
                        <p class="mb-0"><?= nl2br(htmlspecialchars($receipt['notes'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Receipt Footer -->
                    <div class="text-center mt-4 pt-4 border-top">
                        <p class="text-muted mb-0">
                            Thank you for your business. This receipt serves as confirmation of your payment.
                        </p>
                        <p class="text-muted">
                            For any inquiries, please contact our support team.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include any receipt-specific JavaScript here -->
<script>
// Add any client-side functionality as needed
document.addEventListener('DOMContentLoaded', function() {
    // Print receipt when print button is clicked
    document.querySelector('.btn-print').addEventListener('click', function() {
        window.print();
    });
});
</script>