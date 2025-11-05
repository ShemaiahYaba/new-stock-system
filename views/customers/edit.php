<?php
/**
 * Edit Customer Form
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Edit Customer - ' . APP_NAME;

$customerId = (int)($_GET['id'] ?? 0);

if ($customerId <= 0) {
    setFlashMessage('error', 'Invalid customer ID.');
    header('Location: /new-stock-system/index.php?page=customers');
    exit();
}

$customerModel = new Customer();
$customer = $customerModel->findById($customerId);

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
                <h1 class="page-title">Edit Customer</h1>
                <p class="text-muted">Update customer information</p>
            </div>
            <a href="/new-stock-system/index.php?page=customers" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Customers
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Customer Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/customers/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                            <div class="invalid-feedback">Please provide customer name.</div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($customer['phone']); ?>" required>
                                <div class="invalid-feedback">Please provide phone number.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="company" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="company" name="company" 
                                   value="<?php echo htmlspecialchars($customer['company'] ?? ''); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=customers" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Customer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
