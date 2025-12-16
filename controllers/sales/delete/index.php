<?php
/**
 * Delete Sale Controller
 * File: controllers/sales/delete/index.php
 * Handles soft deletion of sales records
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/sale.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', 'Invalid request method');
    redirect('/new-stock-system/index.php?page=sales');
}

// Check permissions
if (!hasPermission(MODULE_SALES, ACTION_DELETE)) {
    setFlashMessage('error', 'You do not have permission to delete sales');
    redirect('/new-stock-system/index.php?page=sales');
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('error', 'Invalid security token. Please try again.');
    redirect('/new-stock-system/index.php?page=sales');
}

// Get sale ID
$saleId = (int) ($_POST['id'] ?? 0);

if ($saleId <= 0) {
    setFlashMessage('error', 'Invalid sale ID');
    redirect('/new-stock-system/index.php?page=sales');
}

try {
    $saleModel = new Sale();
    
    // Check if sale exists
    $sale = $saleModel->findById($saleId);
    
    if (!$sale) {
        throw new Exception('Sale not found');
    }
    
    // Check if sale has an invoice
    $hasInvoice = $saleModel->hasInvoice($saleId);
    
    if ($hasInvoice) {
        // Get invoice details
        $invoice = $saleModel->getInvoice($saleId);
        
        // Don't allow deletion if invoice has been paid (partially or fully)
        if ($invoice && $invoice['paid_amount'] > 0) {
            throw new Exception('Cannot delete sale with paid invoice. Please void the payments first.');
        }
    }
    
    // Start transaction
    $db = Database::getInstance()->getConnection();
    $db->beginTransaction();
    
    try {
        // Soft delete the sale
        $deleted = $saleModel->delete($saleId);
        
        if (!$deleted) {
            throw new Exception('Failed to delete sale record');
        }
        
        // If sale has an invoice, soft delete it too
        if ($hasInvoice && $invoice) {
            $invoiceDeleteSql = "UPDATE invoices SET deleted_at = NOW() WHERE id = ?";
            $stmt = $db->prepare($invoiceDeleteSql);
            $invoiceDeleted = $stmt->execute([$invoice['id']]);
            
            if (!$invoiceDeleted) {
                throw new Exception('Failed to delete associated invoice');
            }
        }
        
        // Commit transaction
        $db->commit();
        
        // Log activity
        logActivity(
            'Sale Deleted',
            "Sale #$saleId deleted by " . getCurrentUser()['name']
        );
        
        setFlashMessage('success', 'Sale deleted successfully');
        redirect('/new-stock-system/index.php?page=sales');
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log('Error deleting sale: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    
    setFlashMessage('error', 'Failed to delete sale: ' . $e->getMessage());
    redirect('/new-stock-system/index.php?page=sales_view&id=' . $saleId);
}