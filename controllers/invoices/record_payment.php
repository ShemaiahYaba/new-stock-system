<?php
/**
 * Record Payment Controller
 * File: controllers/invoices/record_payment.php
 * Handles payment recording for invoices
 */

session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/invoice.php';
require_once __DIR__ . '/../../models/receipt.php';
require_once __DIR__ . '/../../utils/auth_middleware.php';

// Ensure user is authenticated
checkAuth();

// Set JSON header
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    // Get POST data
    $invoiceId = (int) ($_POST['invoice_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);
    $paymentMethod = trim($_POST['payment_method'] ?? 'cash');
    $reference = trim($_POST['reference'] ?? '');
    $userId = $_SESSION['user_id'] ?? 0;

    // Validate inputs
    if ($invoiceId <= 0) {
        throw new Exception('Invalid invoice ID');
    }

    if ($amount <= 0) {
        throw new Exception('Payment amount must be greater than zero');
    }

    // Initialize models
    $invoiceModel = new Invoice();
    $receiptModel = new Receipt();

    // Get invoice details
    $invoice = $invoiceModel->findById($invoiceId);
    if (!$invoice) {
        throw new Exception('Invoice not found');
    }

    // Calculate balance
    $totalAmount = (float) $invoice['total'];
    $currentPaid = (float) $invoice['paid_amount'];
    $balance = $totalAmount - $currentPaid;

    // Validate payment amount doesn't exceed balance
    if ($amount > $balance) {
        throw new Exception(
            'Payment amount exceeds invoice balance of ₦' . number_format($balance, 2),
        );
    }

    // Start transaction
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();

    try {
        // Create receipt record
        $receiptId = $receiptModel->create([
            'invoice_id' => $invoiceId,
            'amount_paid' => $amount,
            'payment_method' => $paymentMethod,
            'reference' => $reference,
            'created_by' => $userId,
        ]);

        if (!$receiptId) {
            throw new Exception('Failed to create receipt record');
        }

        // Update invoice paid amount and status
        $success = $invoiceModel->updatePaidAmount($invoiceId, $amount);

        if (!$success) {
            throw new Exception('Failed to update invoice payment');
        }

        // Commit transaction
        $db->commit();

        // Get updated invoice
        $updatedInvoice = $invoiceModel->findById($invoiceId);
        $newBalance = (float) $updatedInvoice['total'] - (float) $updatedInvoice['paid_amount'];

        // Return success response
        echo json_encode([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'data' => [
                'receipt_id' => $receiptId,
                'invoice_id' => $invoiceId,
                'amount_paid' => $amount,
                'total_paid' => (float) $updatedInvoice['paid_amount'],
                'balance' => $newBalance,
                'status' => $updatedInvoice['status'],
            ],
        ]);
    } catch (Exception $e) {
        // Rollback transaction on error
        $db->rollBack();
        throw $e;
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
