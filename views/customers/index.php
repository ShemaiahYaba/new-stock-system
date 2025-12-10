<?php
/**
 * Customers List View
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Customers - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';

$customerModel = new Customer();

if (!empty($searchQuery)) {
    $customers = $customerModel->search($searchQuery, RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalCustomers = $customerModel->countSearch($searchQuery);
} else {
    $customers = $customerModel->getAll(RECORDS_PER_PAGE, ($currentPage - 1) * RECORDS_PER_PAGE);
    $totalCustomers = $customerModel->count();
}

$paginationData = getPaginationData($totalCustomers, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Customer Management</h1>
                <p class="text-muted">Manage customer information</p>
            </div>
            <?php if (hasPermission(MODULE_CUSTOMER_MANAGEMENT, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=customers_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Customer
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <i class="bi bi-person-badge"></i> Customers List
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="customers">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search customers..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="/new-stock-system/index.php?page=customers" class="btn btn-sm btn-secondary ms-2">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($customers)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No customers found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Company</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo $customer['id']; ?></td>
                            <td><?php echo htmlspecialchars($customer['name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($customer['phone']); ?></td>
                            <td><?php echo htmlspecialchars($customer['company'] ?? '-'); ?></td>
                            <td><?php echo formatDate($customer['created_at']); ?></td>
                            <td>
                                <?php
                                $id = $customer['id'];
                                $module = 'customers';
                                $canView = hasPermission(MODULE_CUSTOMER_MANAGEMENT, ACTION_VIEW);
                                $canEdit = hasPermission(MODULE_CUSTOMER_MANAGEMENT, ACTION_EDIT);
                                $canDelete = hasPermission(MODULE_CUSTOMER_MANAGEMENT, ACTION_DELETE);
                                include __DIR__ . '/../../layout/quick_action_buttons.php';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($customers)): ?>
        <div class="card-footer">
            <?php $queryParams = $_GET; include __DIR__ . '/../../layout/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
