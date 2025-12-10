<?php
/**
 * Customer View Details
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'View Customer - ' . APP_NAME;

$customerId = (int)($_GET['id'] ?? 0);

if ($customerId <= 0) {
    setFlashMessage('error', 'Invalid customer ID.');
    header('Location: /new-stock-system/index.php?page=customers');
    exit();
}

$customerModel = new Customer();
$saleModel = new Sale();

$customer = $customerModel->findById($customerId);
$purchaseHistory = $saleModel->getByCustomer($customerId);

if (!$customer) {
    setFlashMessage('error', 'Customer not found.');
    header('Location: /new-stock-system/index.php?page=customers');
    exit();
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Customer Details</h1>
                <p class="text-muted">View customer information</p>
            </div>
            <a href="/new-stock-system/index.php?page=customers" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Customers
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-person-badge text-primary" style="font-size: 80px;"></i>
                    <h4 class="mt-3"><?php echo htmlspecialchars($customer['name']); ?></h4>
                    <?php if ($customer['company']): ?>
                    <p class="text-muted"><?php echo htmlspecialchars($customer['company']); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Contact Information
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th><i class="bi bi-telephone"></i> Phone:</th>
                            <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                        </tr>
                        <?php if ($customer['email']): ?>
                        <tr>
                            <th><i class="bi bi-envelope"></i> Email:</th>
                            <td><?php echo htmlspecialchars($customer['email']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($customer['address']): ?>
                        <tr>
                            <th><i class="bi bi-geo-alt"></i> Address:</th>
                            <td><?php echo nl2br(htmlspecialchars($customer['address'])); ?></td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Account Info
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Customer ID:</th>
                            <td>#<?php echo $customer['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDate($customer['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?php echo $customer['updated_at'] ? formatDate($customer['updated_at']) : 'Never'; ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <?php if (hasPermission(MODULE_CUSTOMER_MANAGEMENT, ACTION_EDIT)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="/new-stock-system/index.php?page=customers_edit&id=<?php echo $customer['id']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Customer
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-cart"></i> Purchase History
                </div>
                <div class="card-body p-0">
                    <?php if (empty($purchaseHistory)): ?>
                        <div class="alert alert-info m-3">
                            <i class="bi bi-info-circle"></i> No purchase history found for this customer.
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sale ID</th>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Total Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($purchaseHistory as $sale): ?>
                                        <tr>
                                            <td>#<?php echo $sale['id']; ?></td>
                                            <td><?php echo formatDate($sale['created_at']); ?></td>
                                            <td>
                                                <?php 
                                                    if (!empty($sale['coil_name'])) {
                                                        echo htmlspecialchars($sale['coil_name']);
                                                        if (!empty($sale['coil_code'])) {
                                                            echo ' (' . htmlspecialchars($sale['coil_code']) . ')';
                                                        }
                                                    } else {
                                                        echo 'N/A';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                    if ($sale['sale_type'] === 'meter') {
                                                        echo number_format($sale['meters'], 2) . ' meters';
                                                    } else {
                                                        echo number_format($sale['weight_kg'], 2) . ' kg';
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo number_format($sale['total_amount'], 2); ?></td>
                                            <td>
                                                <?php if ($sale['status'] === 'completed'): ?>
                                                    <span class="badge bg-success">Completed</span>
                                                <?php elseif ($sale['status'] === 'pending'): ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo ucfirst($sale['status']); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
