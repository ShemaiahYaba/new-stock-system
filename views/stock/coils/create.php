<?php
/**
 * Create Coil Form - Updated with Meters and Gauge fields
 */

require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$fromKzinc = isset($currentPage) && $currentPage === 'kzinc_coils_create';
$pageTitle = ($fromKzinc ? 'Add K-Zinc Coil' : 'Create Coil') . ' - ' . APP_NAME;

// Get active colors from database
$colorModel = new Color();
$colors = $colorModel->getActive();

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title"><?php echo $fromKzinc ? 'Add K-Zinc Coil' : 'Create New Coil'; ?></h1>
                <p class="text-muted"><?php echo $fromKzinc ? 'Register a new K-Zinc coil' : 'Add a new coil to inventory'; ?></p>
            </div>
            <a href="/new-stock-system/index.php?page=<?php echo $fromKzinc ? 'kzinc_coils' : 'coils'; ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> <?php echo $fromKzinc ? 'Back to K-Zinc Coils' : 'Back to Coils'; ?>
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-box-seam"></i> Coil Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/coils/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <?php if ($fromKzinc): ?>
                        <input type="hidden" name="redirect_to" value="kzinc_coils">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Coil Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       placeholder="e.g., COL-001" required>
                                <div class="invalid-feedback">Please provide a coil code.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Coil Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g., Premium Steel Coil" required>
                                <div class="invalid-feedback">Please provide a name.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color_id" class="form-label">Color <span class="text-danger">*</span></label>
                                <select class="form-select" id="color_id" name="color_id" required>
                                    <option value="">Select color</option>
                                    <?php foreach ($colors as $color): ?>
                                    <option value="<?php echo $color['id']; ?>">
                                        <?php echo htmlspecialchars($color['name']); ?>
                                        <?php if (!empty($color['hex_code'])): ?>
                                            <span style="background-color: <?php echo htmlspecialchars($color['hex_code']); ?>; display: inline-block; width: 15px; height: 15px; border-radius: 3px; margin-left: 5px;"></span>
                                        <?php endif; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a color.</div>
                                <?php if (empty($colors)): ?>
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> No colors available. 
                                    <a href="/new-stock-system/index.php?page=colors_create">Add colors first</a>
                                </small>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">Select category</option>
                                    <?php foreach (STOCK_CATEGORIES as $catKey => $catName): ?>
                                    <option value="<?php echo $catKey; ?>"
                                            <?php echo ($fromKzinc && $catKey === STOCK_CATEGORY_KZINC) ? 'selected' : ''; ?>>
                                        <?php echo $catName; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="net_weight" class="form-label">Net Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="net_weight" name="net_weight"
                                       step="0.01" min="0" placeholder="e.g., 1500.50" required>
                                <div class="invalid-feedback">Please provide net weight.</div>
                            </div>

                            <div class="col-md-4 mb-3" id="meters_col">
                                <label for="meters" class="form-label">Meters</label>
                                <input type="number" class="form-control" id="meters" name="meters"
                                       step="0.01" min="0" placeholder="e.g., 500.00">
                                <small class="text-muted">Approximate meters per coil</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="gauge" class="form-label">Gauge</label>
                                <input type="text" class="form-control" id="gauge" name="gauge"
                                       placeholder="e.g., 0.45mm">
                                <small class="text-muted">Material thickness</small>
                            </div>
                        </div>

                        <!-- KZinc: tracking mode choice -->
                        <div id="kzinc_track_mode_row" style="display:none;">
                            <div class="alert alert-warning py-2 mb-3">
                                <strong><i class="bi bi-layers"></i> K-Zinc Tracking Mode</strong> — how will this coil's stock be measured?
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-check-card border rounded p-3">
                                        <input class="form-check-input" type="radio" name="kzinc_track_mode"
                                               id="track_meters" value="meters">
                                        <label class="form-check-label w-100" for="track_meters">
                                            <strong>Meter coil</strong> — raw roll<br>
                                            <small class="text-muted">Tracked in meters/kg via Stock Management. Use for factory input or meter-based sales.</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-card border rounded p-3">
                                        <input class="form-check-input" type="radio" name="kzinc_track_mode"
                                               id="track_pallets" value="pallets">
                                        <label class="form-check-label w-100" for="track_pallets">
                                            <strong>Pallet/sheet coil</strong> — pre-cut<br>
                                            <small class="text-muted">Tracked in pallets, bundles &amp; pieces via the K-Zinc module.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pallet size — only for KZinc pallet coils -->
                        <div class="mb-3" id="pallet_size_row" style="display:none;">
                            <label for="pallet_size" class="form-label">Pallet Size (bundles per pallet) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pallet_size" name="pallet_size"
                                   min="1" step="1" placeholder="e.g. 85, 92, 112">
                            <small class="text-muted">How many bundles fit on one pallet. Each bundle = <?php echo KZINC_PIECES_PER_BUNDLE; ?> pieces.</small>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> The coil will be created with a default status of "Available". 
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=<?php echo $fromKzinc ? 'kzinc_coils' : 'coils'; ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Coil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Stock Categories
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <?php foreach (STOCK_CATEGORIES as $catName): ?>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> <?php echo $catName; ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-rulers"></i> Common Gauges
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">Example gauge values:</p>
                    <ul class="list-unstyled small">
                        <li>• 0.40mm</li>
                        <li>• 0.45mm</li>
                        <li>• 0.50mm</li>
                        <li>• 0.55mm</li>
                        <li>• 0.60mm</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const KZINC = '<?php echo STOCK_CATEGORY_KZINC; ?>';
    const categorySelect   = document.getElementById('category');
    const trackModeRow     = document.getElementById('kzinc_track_mode_row');
    const palletRow        = document.getElementById('pallet_size_row');
    const palletInput      = document.getElementById('pallet_size');
    const metersCol        = document.getElementById('meters_col');
    const metersInput      = document.getElementById('meters');
    const trackRadios      = document.querySelectorAll('input[name="kzinc_track_mode"]');

    function onCategoryChange() {
        const isKzinc = categorySelect.value === KZINC;
        trackModeRow.style.display = isKzinc ? '' : 'none';
        if (!isKzinc) {
            // Reset KZinc-specific state
            trackRadios.forEach(r => r.checked = false);
            palletRow.style.display = 'none';
            palletInput.required = false;
            metersCol.style.display = '';
        } else {
            onTrackModeChange();
        }
    }

    function onTrackModeChange() {
        const selected = document.querySelector('input[name="kzinc_track_mode"]:checked');
        if (!selected) {
            palletRow.style.display = 'none';
            palletInput.required = false;
            metersCol.style.display = '';
            return;
        }
        const isPallets = selected.value === 'pallets';
        palletRow.style.display  = isPallets ? '' : 'none';
        palletInput.required     = isPallets;
        metersCol.style.display  = isPallets ? 'none' : '';
        if (isPallets) metersInput.value = '';
    }

    categorySelect.addEventListener('change', onCategoryChange);
    trackRadios.forEach(r => r.addEventListener('change', onTrackModeChange));

    <?php if ($fromKzinc): ?>
    // Pre-select pallets mode when accessed from K-Zinc module
    document.getElementById('track_pallets').checked = true;
    <?php endif; ?>

    onCategoryChange();
})();
</script>
<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>