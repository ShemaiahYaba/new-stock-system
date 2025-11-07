<?php
/**
 * Step 1: Select Stock for Production
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/stock_entry.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

// Check authentication
checkAuth();

$pageTitle = 'New Sale - Select Stock';

// Get all coils with their stock entries
$coilModel = new Coil();
$coils = $coilModel->getWithStockInfo();

// Prepare data for the view
$coilOptions = [];
$stockEntriesByCoil = [];
$coilMetadata = [];

foreach ($coils as $coil) {
    if (!empty($coil['stock_entries'])) {
        $coilId = $coil['id'];
        $coilOptions[$coilId] = $coil['code'] . ' - ' . $coil['name'];
        $coilMetadata[$coilId] = [
            'code' => $coil['code'],
            'name' => $coil['name'],
            'color' => $coil['color'],
            'category' => $coil['category'],
            'weight' => $coil['net_weight'],
            'status' => $coil['status'],
            'available_meters' => 0,
            'total_entries' => 0
        ];
        
        // Process stock entries for this coil
        $stockEntriesByCoil[$coilId] = [];
        foreach ($coil['stock_entries'] as $entry) {
            if ($entry['meters_remaining'] > 0) {
                $stockEntry = [
                    'id' => $entry['id'],
                    'meters_remaining' => $entry['meters_remaining'],
                    'status' => $entry['status'],
                    'created_at' => $entry['created_at']
                ];
                $stockEntriesByCoil[$coilId][] = $stockEntry;
                
                // Update coil metadata
                $coilMetadata[$coilId]['available_meters'] += $entry['meters_remaining'];
                $coilMetadata[$coilId]['total_entries']++;
            }
        }
    }
}

// Include header and sidebar
require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">New Sale - Select Stock</h1>
                <p class="text-muted">Choose stock for production</p>
            </div>
            <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Sales
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-box-seam"></i> Available Stock
                </div>
                <div class="card-body">
                    <form id="selectStockForm" method="POST" action="?page=sales_production&step=properties">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="coil_id" class="form-label">Select Coil <span class="text-danger">*</span></label>
                                    <select class="form-select" id="coil_id" name="coil_id" required>
                                        <option value="">-- Select Coil --</option>
                                        <?php foreach ($coilOptions as $id => $label): ?>
                                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($label); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_entry_id" class="form-label">Select Stock Entry <span class="text-danger">*</span></label>
                                    <select class="form-select" id="stock_entry_id" name="stock_entry_id" required disabled>
                                        <option value="">-- Select Stock Entry --</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Coil Metadata -->
                        <div id="coilMetadata" class="card mb-4 d-none">
                            <div class="card-header">
                                <i class="bi bi-info-circle"></i> Coil Information
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Code:</strong> <span id="coilCode">-</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Name:</strong> <span id="coilName">-</span></p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>Color:</strong> <span id="coilColor">-</span></p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>Weight:</strong> <span id="coilWeight">-</span> kg</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>Available:</strong> <span id="coilAvailableMeters">0.00</span>m</p>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Category:</strong> <span id="coilCategory">-</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Status:</strong> <span id="coilStatus">-</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Total Entries:</strong> <span id="coilTotalEntries">0</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="noStockMessage" class="alert alert-info d-none">
                            <i class="bi bi-info-circle"></i> No stock entries available for the selected coil.
                        </div>
                        
                        <div class="d-flex justify-content-end mt-3">
                            <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary me-2">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary" id="nextButton" disabled>
                                Next <i class="bi bi-arrow-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('selectStockForm');
    const nextButton = document.getElementById('nextButton');
    const coilSelect = document.getElementById('coil_id');
    const stockEntrySelect = document.getElementById('stock_entry_id');
    const coilMetadata = document.getElementById('coilMetadata');
    const noStockMessage = document.getElementById('noStockMessage');
    
    // Store stock entries data
    const stockEntriesData = <?php echo json_encode($stockEntriesByCoil); ?>;
    const coilMetadataData = <?php echo json_encode($coilMetadata); ?>;
    
    // When coil is selected
    coilSelect.addEventListener('change', function() {
        const coilId = this.value;
        stockEntrySelect.innerHTML = '<option value="">-- Select Stock Entry --</option>';
        stockEntrySelect.disabled = !coilId;
        nextButton.disabled = true;
        
        // Show/hide metadata
        if (coilId) {
            // Update metadata display
            const metadata = coilMetadataData[coilId];
            if (metadata) {
                document.getElementById('coilCode').textContent = metadata.code;
                document.getElementById('coilName').textContent = metadata.name;
                document.getElementById('coilColor').textContent = metadata.color;
                document.getElementById('coilWeight').textContent = metadata.weight;
                document.getElementById('coilCategory').textContent = STOCK_CATEGORIES[metadata.category] || metadata.category;
                document.getElementById('coilStatus').innerHTML = 
                    `<span class="badge bg-${metadata.status === 'available' ? 'success' : 'warning'}">
                        ${metadata.status.charAt(0).toUpperCase() + metadata.status.slice(1)}
                    </span>`;
                document.getElementById('coilAvailableMeters').textContent = metadata.available_meters.toFixed(2);
                document.getElementById('coilTotalEntries').textContent = metadata.total_entries;
                
                coilMetadata.classList.remove('d-none');
            }
            
            // Populate stock entries
            const entries = stockEntriesData[coilId] || [];
            if (entries.length > 0) {
                entries.forEach(entry => {
                    const option = document.createElement('option');
                    option.value = entry.id;
                    option.textContent = `#${entry.id} - ${entry.meters_remaining.toFixed(2)}m (${entry.status})`;
                    option.dataset.status = entry.status;
                    stockEntrySelect.appendChild(option);
                });
                noStockMessage.classList.add('d-none');
            } else {
                noStockMessage.classList.remove('d-none');
            }
        } else {
            coilMetadata.classList.add('d-none');
            noStockMessage.classList.add('d-none');
        }
    });
    
    // When stock entry is selected
    stockEntrySelect.addEventListener('change', function() {
        nextButton.disabled = !this.value;
    });
    
    // Form submission
    form.addEventListener('submit', function(e) {
        if (!stockEntrySelect.value) {
            e.preventDefault();
            alert('Please select both a coil and a stock entry to continue.');
        }
    });
    
    // Define STOCK_CATEGORIES in JavaScript
    const STOCK_CATEGORIES = <?php echo json_encode(STOCK_CATEGORIES); ?>;
});
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php';
?>
