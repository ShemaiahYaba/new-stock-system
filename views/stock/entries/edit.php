<?php
/**
 * Edit Stock Entry Form
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Edit Stock Entry - ' . APP_NAME;

$entryId = (int)($_GET['id'] ?? 0);

if ($entryId <= 0) {
    setFlashMessage('error', 'Invalid stock entry ID.');
    header('Location: /new-stock-system/index.php?page=stock_entries');
    exit();
}

$stockEntryModel = new StockEntry();
$entry = $stockEntryModel->findById($entryId);

if (!$entry) {
    setFlashMessage('error', 'Stock entry not found.');
    header('Location: /new-stock-system/index.php?page=stock_entries');
    exit();
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Stock Entry</h1>
                <p class="text-muted">Update stock entry meters</p>
            </div>
            <a href="/new-stock-system/index.php?page=stock_entries" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Stock Entries
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Stock Entry Information
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3">
                        <strong>Coil:</strong> <?php echo htmlspecialchars($entry['coil_code']); ?> - <?php echo htmlspecialchars($entry['coil_name']); ?><br>
                        <strong>Current Meters:</strong> <?php echo number_format($entry['meters'], 2); ?>m<br>
                        <strong>Remaining:</strong> <?php echo number_format($entry['meters_remaining'], 2); ?>m
                    </div>
                    
                    <form action="/new-stock-system/controllers/stock_entries/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                        
                        <div class="mb-3">
                            <label for="meters" class="form-label">Total Meters <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="meters" name="meters" 
                                   step="0.01" min="<?php echo $entry['meters'] - $entry['meters_remaining']; ?>" 
                                   value="<?php echo $entry['meters']; ?>" required>
                            <div class="invalid-feedback">Please provide meters.</div>
                            <small class="form-text text-muted">
                                Minimum: <?php echo number_format($entry['meters'] - $entry['meters_remaining'], 2); ?>m 
                                (cannot be less than meters already used)
                            </small>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Note:</strong> Changing the total meters will adjust the remaining meters accordingly. 
                            You cannot set it below the amount already used.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=stock_entries" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Stock Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
