<?php
/**
 * Edit Sale View
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/coil.php';
require_once __DIR__ . '/../../models/stock_entry.php';
require_once __DIR__ . '/../../utils/helpers.php';

$saleId = $_GET['id'] ?? null;

if (!$saleId) {
    setFlashMessage('error', 'Sale ID is required');
    redirect('/new-stock-system/index.php?page=sales');
}

$saleModel = new Sale();
$sale = $saleModel->findById($saleId);

if (!$sale) {
    setFlashMessage('error', 'Sale not found');
    redirect('/new-stock-system/index.php?page=sales');
}

// Get related data
$customerModel = new Customer();
$coilModel = new Coil();
$stockEntryModel = new StockEntry();

$customers = $customerModel->getAll();
$coils = $coilModel->getAll();
$stockEntries = $stockEntryModel->getByCoilId($sale['coil_id']);

$pageTitle = 'Edit Sale - ' . APP_NAME;
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <h1>Edit Sale</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/new-stock-system">Home</a></li>
                <li class="breadcrumb-item"><a href="/new-stock-system/index.php?page=sales">Sales</a></li>
                <li class="breadcrumb-item active">Edit Sale</li>
            </ol>
        </nav>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (hasFlashMessage('error')): ?>
                <div class="alert alert-danger">
                    <?php echo getFlashMessage('error'); ?>
                </div>
            <?php endif; ?>

            <form action="/new-stock-system/controllers/sales/update/index.php" method="POST">
                <input type="hidden" name="id" value="<?php echo $sale['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                <div class="mb-3">
                    <label for="customer_id" class="form-label">Customer</label>
                    <select class="form-select" id="customer_id" name="customer_id" required>
                        <option value="">Select Customer</option>
                        <?php foreach ($customers as $customer): ?>
                            <option value="<?php echo $customer['id']; ?>" 
                                <?php echo $customer['id'] == $sale['customer_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($customer['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="coil_id" class="form-label">Coil</label>
                    <select class="form-select" id="coil_id" name="coil_id" required>
                        <option value="">Select Coil</option>
                        <?php foreach ($coils as $coil): ?>
                            <option value="<?php echo $coil['id']; ?>" 
                                data-stock-entries='<?php echo json_encode($stockEntryModel->getByCoilId($coil['id'])); ?>'
                                <?php echo $coil['id'] == $sale['coil_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($coil['code'] . ' - ' . $coil['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="stock_entry_id" class="form-label">Stock Entry</label>
                    <select class="form-select" id="stock_entry_id" name="stock_entry_id" required>
                        <option value="">Select Stock Entry</option>
                        <?php foreach ($stockEntries as $entry): ?>
                            <option value="<?php echo $entry['id']; ?>" 
                                data-available-meters="<?php echo $entry['meters_remaining']; ?>"
                                <?php echo $entry['id'] == $sale['stock_entry_id'] ? 'selected' : ''; ?>>
                                <?php echo 'ID: ' . $entry['id'] . ' - ' . $entry['meters_remaining'] . 'm remaining'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="sale_type" class="form-label">Sale Type</label>
                    <select class="form-select" id="sale_type" name="sale_type" required>
                        <option value="retail" <?php echo $sale['sale_type'] === 'retail' ? 'selected' : ''; ?>>Retail</option>
                        <option value="wholesale" <?php echo $sale['sale_type'] === 'wholesale' ? 'selected' : ''; ?>>Wholesale</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="meters" class="form-label">Meters</label>
                    <input type="number" step="0.01" class="form-control" id="meters" name="meters" 
                           value="<?php echo htmlspecialchars($sale['meters']); ?>" required>
                    <small class="form-text text-muted" id="meters_available"></small>
                </div>

                <div class="mb-3">
                    <label for="price_per_meter" class="form-label">Price per Meter</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="price_per_meter" 
                               name="price_per_meter" value="<?php echo htmlspecialchars($sale['price_per_meter']); ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="total_amount" class="form-label">Total Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" step="0.01" class="form-control" id="total_amount" 
                               name="total_amount" value="<?php echo htmlspecialchars($sale['total_amount']); ?>" readonly>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="pending" <?php echo $sale['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="completed" <?php echo $sale['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $sale['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Sale</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const coilSelect = document.getElementById('coil_id');
    const stockEntrySelect = document.getElementById('stock_entry_id');
    const metersInput = document.getElementById('meters');
    const pricePerMeterInput = document.getElementById('price_per_meter');
    const totalAmountInput = document.getElementById('total_amount');
    const metersAvailableSpan = document.getElementById('meters_available');

    // Update stock entries when coil changes
    coilSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stockEntries = JSON.parse(selectedOption.getAttribute('data-stock-entries') || '[]');
        
        // Clear and repopulate stock entries
        stockEntrySelect.innerHTML = '<option value="">Select Stock Entry</option>';
        stockEntries.forEach(entry => {
            const option = document.createElement('option');
            option.value = entry.id;
            option.textContent = `ID: ${entry.id} - ${entry.meters_remaining}m remaining`;
            option.setAttribute('data-available-meters', entry.meters_remaining);
            stockEntrySelect.appendChild(option);
        });
        
        updateMetersAvailable();
    });

    // Update available meters when stock entry changes
    stockEntrySelect.addEventListener('change', updateMetersAvailable);

    // Update total amount when meters or price changes
    [metersInput, pricePerMeterInput].forEach(input => {
        input.addEventListener('input', updateTotalAmount);
    });

    function updateMetersAvailable() {
        const selectedOption = stockEntrySelect.options[stockEntrySelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const availableMeters = parseFloat(selectedOption.getAttribute('data-available-meters') || '0');
            metersAvailableSpan.textContent = `Available: ${availableMeters.toFixed(2)} meters`;
            metersInput.max = availableMeters;
        } else {
            metersAvailableSpan.textContent = '';
            metersInput.removeAttribute('max');
        }
        updateTotalAmount();
    }

    function updateTotalAmount() {
        const meters = parseFloat(metersInput.value) || 0;
        const pricePerMeter = parseFloat(pricePerMeterInput.value) || 0;
        totalAmountInput.value = (meters * pricePerMeter).toFixed(2);
    }

    // Initialize
    updateMetersAvailable();
    updateTotalAmount();
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
