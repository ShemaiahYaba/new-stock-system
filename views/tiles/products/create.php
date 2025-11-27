<?php
/**
 * ============================================
 * FILE: views/tiles/products/create.php
 * Create new tile product
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/design.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Create Tile Product - ' . APP_NAME;

$designModel = new Design();
$colorModel = new Color();

$designs = $designModel->getActive();
$colors = $colorModel->getActive();

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create Tile Product</h1>
                <p class="text-muted">Define a new product variant</p>
            </div>
            <a href="/new-stock-system/index.php?page=tile_products" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Products
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-grid-3x3"></i> Product Configuration
                </div>
                <div class="card-body">
                    <?php if (empty($designs) || empty($colors)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> 
                        <strong>Prerequisites Missing:</strong>
                        <?php if (empty($designs)): ?>
                        <p class="mb-0">No designs available. <a href="/new-stock-system/index.php?page=designs_create">Create a design first</a>.</p>
                        <?php endif; ?>
                        <?php if (empty($colors)): ?>
                        <p class="mb-0">No colors available. <a href="/new-stock-system/index.php?page=colors_create">Create colors first</a>.</p>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    
                    <form action="/new-stock-system/controllers/tiles/products/create/index.php" 
                          method="POST" class="needs-validation" novalidate id="productForm">
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        
                        <div class="mb-3">
                            <label for="design_id" class="form-label">Design <span class="text-danger">*</span></label>
                            <select class="form-select" id="design_id" name="design_id" required>
                                <option value="">-- Select Design --</option>
                                <?php foreach ($designs as $design): ?>
                                <option value="<?= $design['id'] ?>" data-code="<?= htmlspecialchars($design['code']) ?>">
                                    <?= htmlspecialchars($design['name']) ?> (<?= htmlspecialchars($design['code']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a design.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="color_id" class="form-label">Color <span class="text-danger">*</span></label>
                            <select class="form-select" id="color_id" name="color_id" required>
                                <option value="">-- Select Color --</option>
                                <?php foreach ($colors as $color): ?>
                                <option value="<?= $color['id'] ?>" 
                                        data-code="<?= htmlspecialchars($color['code']) ?>"
                                        data-hex="<?= htmlspecialchars($color['hex_code'] ?? '') ?>">
                                    <?= htmlspecialchars($color['name']) ?> (<?= htmlspecialchars($color['code']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a color.</div>
                            
                            <div id="colorPreview" class="mt-2" style="display: none;">
                                <div style="width: 100%; height: 40px; border: 1px solid #dee2e6; border-radius: 4px;"></div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="gauge" class="form-label">Gauge <span class="text-danger">*</span></label>
                            <select class="form-select" id="gauge" name="gauge" required>
                                <option value="">-- Select Gauge --</option>
                                <?php foreach (TILE_GAUGES as $key => $label): ?>
                                <option value="<?= $key ?>"><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a gauge.</div>
                        </div>
                        
                        <!-- Product code preview -->
                        <div id="codePreview" class="alert alert-secondary" style="display: none;">
                            <strong>Generated Product Code:</strong> 
                            <code id="generatedCode" class="fs-5">-</code>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Product code will be automatically generated as: <strong>DESIGN-COLOR-GAUGE</strong>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=tile_products" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Product
                            </button>
                        </div>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Product Info
                </div>
                <div class="card-body">
                    <h6>Product Structure</h6>
                    <p class="small text-muted">
                        Each product is a unique combination of:
                    </p>
                    <ul class="small">
                        <li><strong>Design:</strong> Tile pattern (Milano, Shingle, etc.)</li>
                        <li><strong>Color:</strong> Color variant</li>
                        <li><strong>Gauge:</strong> Thickness (Thick, Normal, Light)</li>
                    </ul>
                    
                    <h6 class="mt-3">Example Products</h6>
                    <ul class="small">
                        <li><code>MILANO-RED-THICK</code></li>
                        <li><code>SHINGLE-BLUE-NORMAL</code></li>
                        <li><code>CORONA-BLACK-LIGHT</code></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update product code preview
function updateCodePreview() {
    const designSelect = document.getElementById('design_id');
    const colorSelect = document.getElementById('color_id');
    const gaugeSelect = document.getElementById('gauge');
    const preview = document.getElementById('codePreview');
    const codeDisplay = document.getElementById('generatedCode');
    
    const designCode = designSelect.selectedOptions[0]?.getAttribute('data-code') || '';
    const colorCode = colorSelect.selectedOptions[0]?.getAttribute('data-code') || '';
    const gauge = gaugeSelect.value.toUpperCase();
    
    if (designCode && colorCode && gauge) {
        const code = `${designCode}-${colorCode}-${gauge}`;
        codeDisplay.textContent = code;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

// Show color preview
function updateColorPreview() {
    const colorSelect = document.getElementById('color_id');
    const preview = document.getElementById('colorPreview');
    const hex = colorSelect.selectedOptions[0]?.getAttribute('data-hex') || '';
    
    if (hex) {
        preview.querySelector('div').style.backgroundColor = hex;
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

document.getElementById('design_id').addEventListener('change', updateCodePreview);
document.getElementById('color_id').addEventListener('change', function() {
    updateCodePreview();
    updateColorPreview();
});
document.getElementById('gauge').addEventListener('change', updateCodePreview);
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>