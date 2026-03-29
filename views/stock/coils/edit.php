<?php
/**
 * Edit Coil Form - Updated with Meters and Gauge fields
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Edit Coil - ' . APP_NAME;

$coilId = (int)($_GET['id'] ?? 0);

if ($coilId <= 0) {
    setFlashMessage('error', 'Invalid coil ID.');
    header('Location: /new-stock-system/index.php?page=coils');
    exit();
}

$coilModel = new Coil();
$colorModel = new Color();
$coil = $coilModel->findById($coilId);
$colors = $colorModel->getActive();

if (!$coil) {
    setFlashMessage('error', 'Coil not found.');
    header('Location: /new-stock-system/index.php?page=coils');
    exit();
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Coil</h1>
                <p class="text-muted">Update coil information</p>
            </div>
            <a href="/new-stock-system/index.php?page=coils" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Coils
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Coil Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/coils/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $coil['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Coil Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="<?php echo htmlspecialchars($coil['code']); ?>" required>
                                <div class="invalid-feedback">Please provide a coil code.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Coil Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($coil['name']); ?>" required>
                                <div class="invalid-feedback">Please provide a name.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color_id" class="form-label">Color <span class="text-danger">*</span></label>
                                <select class="form-select" id="color_id" name="color_id" required>
                                    <option value="">-- Select Color --</option>
                                    <?php foreach ($colors as $color): ?>
                                    <option value="<?php echo $color['id']; ?>" 
                                        <?php echo $coil['color_id'] == $color['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($color['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a color.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <?php foreach (STOCK_CATEGORIES as $catKey => $catName): ?>
                                    <option value="<?php echo $catKey; ?>" <?php echo $coil['category'] === $catKey ? 'selected' : ''; ?>>
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
                                       step="0.01" min="0" value="<?php echo $coil['net_weight']; ?>" required>
                                <div class="invalid-feedback">Please provide net weight.</div>
                            </div>

                            <div class="col-md-4 mb-3" id="meters_col">
                                <label for="meters" class="form-label">Meters</label>
                                <input type="number" class="form-control" id="meters" name="meters"
                                       step="0.01" min="0" placeholder="e.g., 500.00"
                                       value="<?php echo isset($coil['meters']) ? $coil['meters'] : ''; ?>">
                                <small class="text-muted">Approximate meters per coil</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="gauge" class="form-label">Gauge</label>
                                <input type="text" class="form-control" id="gauge" name="gauge"
                                       placeholder="e.g., 0.45mm"
                                       value="<?php echo isset($coil['gauge']) ? htmlspecialchars($coil['gauge']) : ''; ?>">
                                <small class="text-muted">Material thickness</small>
                            </div>
                        </div>

                        <!-- KZinc: tracking mode -->
                        <?php $currentTrackMode = $coil['kzinc_track_mode'] ?? null; ?>
                        <div id="kzinc_track_mode_row" style="display:none;">
                            <div class="alert alert-warning py-2 mb-3">
                                <strong><i class="bi bi-layers"></i> K-Zinc Tracking Mode</strong>
                                <small class="d-block text-muted mt-1">Changing this after stock entries exist may cause inconsistencies.</small>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check form-check-card border rounded p-3">
                                        <input class="form-check-input" type="radio" name="kzinc_track_mode"
                                               id="track_meters" value="meters"
                                               <?php echo $currentTrackMode === 'meters' ? 'checked' : ''; ?>>
                                        <label class="form-check-label w-100" for="track_meters">
                                            <strong>Meter coil</strong> — raw roll<br>
                                            <small class="text-muted">Tracked in meters/kg via Stock Management.</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-check-card border rounded p-3">
                                        <input class="form-check-input" type="radio" name="kzinc_track_mode"
                                               id="track_pallets" value="pallets"
                                               <?php echo $currentTrackMode === 'pallets' ? 'checked' : ''; ?>>
                                        <label class="form-check-label w-100" for="track_pallets">
                                            <strong>Pallet/sheet coil</strong> — pre-cut<br>
                                            <small class="text-muted">Tracked in pallets, bundles &amp; pieces via K-Zinc module.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="pallet_size_row" style="display:none;">
                            <label for="pallet_size" class="form-label">Pallet Size (bundles per pallet) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="pallet_size" name="pallet_size"
                                   min="1" step="1" placeholder="e.g. 85, 92, 112"
                                   value="<?php echo htmlspecialchars($coil['pallet_size'] ?? ''); ?>">
                            <small class="text-muted">How many bundles fit on one pallet. Changing this only affects new stock entries.</small>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <?php foreach (STOCK_STATUSES as $statusKey => $statusName): ?>
                                <option value="<?php echo $statusKey; ?>" <?php echo $coil['status'] === $statusKey ? 'selected' : ''; ?>>
                                    <?php echo $statusName; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a status.</div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Note:</strong> Changing the status may affect sales and stock entries.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=coils" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Coil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const KZINC = '<?php echo STOCK_CATEGORY_KZINC; ?>';
    const categorySelect = document.getElementById('category');
    const trackModeRow   = document.getElementById('kzinc_track_mode_row');
    const palletRow      = document.getElementById('pallet_size_row');
    const palletInput    = document.getElementById('pallet_size');
    const metersCol      = document.getElementById('meters_col');
    const metersInput    = document.getElementById('meters');
    const trackRadios    = document.querySelectorAll('input[name="kzinc_track_mode"]');

    function onTrackModeChange() {
        const selected = document.querySelector('input[name="kzinc_track_mode"]:checked');
        if (!selected) {
            palletRow.style.display = 'none';
            palletInput.required    = false;
            metersCol.style.display = '';
            return;
        }
        const isPallets = selected.value === 'pallets';
        palletRow.style.display  = isPallets ? '' : 'none';
        palletInput.required     = isPallets;
        metersCol.style.display  = isPallets ? 'none' : '';
        if (isPallets) metersInput.value = '';
    }

    function onCategoryChange() {
        const isKzinc = categorySelect.value === KZINC;
        trackModeRow.style.display = isKzinc ? '' : 'none';
        if (!isKzinc) {
            trackRadios.forEach(r => r.checked = false);
            palletRow.style.display = 'none';
            palletInput.required    = false;
            metersCol.style.display = '';
        } else {
            onTrackModeChange();
        }
    }

    categorySelect.addEventListener('change', onCategoryChange);
    trackRadios.forEach(r => r.addEventListener('change', onTrackModeChange));
    onCategoryChange();
})();
</script>
<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>