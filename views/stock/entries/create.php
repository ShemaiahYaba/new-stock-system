<?php
/**
 * Create Stock Entry Form
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Create Stock Entry - ' . APP_NAME;

$coilId = isset($_GET['coil_id']) ? (int)$_GET['coil_id'] : null;

// Get available coils
$coilModel = new Coil();
$coils = $coilModel->getAll(null, 1000, 0);

// If coil_id is provided, get that specific coil
$selectedCoil = null;
if ($coilId) {
    $selectedCoil = $coilModel->findById($coilId);
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create Stock Entry</h1>
                <p class="text-muted">Add meter specification to a coil</p>
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
                    <i class="bi bi-plus-circle"></i> Stock Entry Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/stock_entries/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="mb-3">
                            <label for="coil_id" class="form-label">Select Coil <span class="text-danger">*</span></label>
                            <select class="form-select" id="coil_id" name="coil_id" required <?php echo $selectedCoil ? 'disabled' : ''; ?>>
                                <option value="">Select a coil</option>
                                <?php foreach ($coils as $coil): ?>
                                <option value="<?php echo $coil['id']; ?>" 
                                        <?php echo ($selectedCoil && $selectedCoil['id'] == $coil['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($coil['code']); ?> - 
                                    <?php echo htmlspecialchars($coil['name']); ?> 
                                    (<?php echo STOCK_CATEGORIES[$coil['category']]; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ($selectedCoil): ?>
                            <input type="hidden" name="coil_id" value="<?php echo $selectedCoil['id']; ?>">
                            <?php endif; ?>
                            <div class="invalid-feedback">Please select a coil.</div>
                        </div>
                        
                        <?php if ($selectedCoil): ?>
                        <div class="alert alert-info">
                            <strong>Selected Coil:</strong> <?php echo htmlspecialchars($selectedCoil['code']); ?> - 
                            <?php echo htmlspecialchars($selectedCoil['name']); ?><br>
                            <strong>Status:</strong> <span class="badge <?php echo getStatusBadgeClass($selectedCoil['status']); ?>">
                                <?php echo STOCK_STATUSES[$selectedCoil['status']]; ?>
                            </span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="mb-3">
    <label for="meters" class="form-label">Meters <span class="text-danger">*</span></label>
    <input type="number" class="form-control" id="meters" name="meters" 
           step="0.01" min="0.01" placeholder="e.g., 500.50" required>
    <small class="form-text text-muted">Total meters for this stock entry</small>
</div>

<!-- NEW FIELD -->
<div class="mb-3">
    <label for="weight_kg" class="form-label">Weight (KG) </label>
    <input type="number" class="form-control" id="weight_kg" name="weight_kg" 
           step="0.01" min="0" placeholder="e.g., 2850.00">
    <small class="form-text text-muted">Weight(kg)</small>
</div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> Stock entries define the meter specifications for coils. 
                            The remaining meters will be tracked as sales are made.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=stock_entries" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Stock Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Stock Entry Info
                </div>
                <div class="card-body">
                    <h6>What is a Stock Entry?</h6>
                    <p class="small text-muted">
                        A stock entry records the meter specification for a coil. 
                        This allows you to track how much material is available.
                    </p>
                    
                    <h6 class="mt-3">Workflow</h6>
                    <ol class="small text-muted">
                        <li>Create a coil</li>
                        <li>Add stock entry with meters</li>
                        <li>Sell from available meters</li>
                        <li>Track remaining meters</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
