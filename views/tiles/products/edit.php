<?php
/**
 * ============================================
 * FILE: views/tiles/products/edit.php
 * Edit an existing tile product
 * ============================================
 */
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/tile_product.php';
require_once __DIR__ . '/../../../models/design.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Edit Tile Product - ' . APP_NAME;

$productId = (int)($_GET['id'] ?? 0);

if ($productId <= 0) {
    setFlashMessage('error', 'Invalid product ID.');
    header('Location: /new-stock-system/index.php?page=tile_products');
    exit();
}

$productModel = new TileProduct();
$designModel  = new Design();
$colorModel   = new Color();

$product = $productModel->findById($productId);
if (!$product) {
    setFlashMessage('error', 'Product not found.');
    header('Location: /new-stock-system/index.php?page=tile_products');
    exit();
}

$designs = $designModel->getActive();
$colors  = $colorModel->getActive();

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Tile Product</h1>
                <p class="text-muted"><code><?= htmlspecialchars($product['code']) ?></code></p>
            </div>
            <a href="/new-stock-system/index.php?page=tile_products_view&id=<?= $product['id'] ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Product
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Product Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/tiles/products/update/index.php"
                          method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                        <input type="hidden" name="id" value="<?= $product['id'] ?>">

                        <div class="mb-3">
                            <label for="design_id" class="form-label">Design <span class="text-danger">*</span></label>
                            <select class="form-select" id="design_id" name="design_id" required>
                                <option value="">-- Select Design --</option>
                                <?php foreach ($designs as $design): ?>
                                <option value="<?= $design['id'] ?>"
                                        data-code="<?= htmlspecialchars($design['code']) ?>"
                                        <?= $product['design_id'] == $design['id'] ? 'selected' : '' ?>>
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
                                        data-hex="<?= htmlspecialchars($color['hex_code'] ?? '') ?>"
                                        <?= $product['color_id'] == $color['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($color['name']) ?> (<?= htmlspecialchars($color['code']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a color.</div>
                            <div id="colorPreview" class="mt-2" <?= empty($product['color_hex']) ? 'style="display:none;"' : '' ?>>
                                <div style="width:100%;height:40px;border:1px solid #dee2e6;border-radius:4px;background:<?= htmlspecialchars($product['color_hex'] ?? '#ccc') ?>;"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="gauge" class="form-label">Gauge <span class="text-danger">*</span></label>
                            <select class="form-select" id="gauge" name="gauge" required>
                                <option value="">-- Select Gauge --</option>
                                <?php foreach (TILE_GAUGES as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $product['gauge'] === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a gauge.</div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <?php foreach (TILE_STOCK_STATUS as $key => $label): ?>
                                <option value="<?= $key ?>" <?= $product['status'] === $key ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Code preview -->
                        <div id="codePreview" class="alert alert-secondary">
                            <strong>Product Code:</strong>
                            <code id="generatedCode" class="fs-5"><?= htmlspecialchars($product['code']) ?></code>
                            <small class="text-muted d-block mt-1">Auto-generated from Design + Color + Gauge</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=tile_products_view&id=<?= $product['id'] ?>"
                               class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header"><i class="bi bi-info-circle"></i> Note</div>
                <div class="card-body small text-muted">
                    <p>Changing the <strong>design, color, or gauge</strong> will regenerate the product code.</p>
                    <p class="mb-0">Stock history and existing sales linked to this product are not affected.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const designSel = document.getElementById('design_id');
    const colorSel  = document.getElementById('color_id');
    const gaugeSel  = document.getElementById('gauge');
    const codeEl    = document.getElementById('generatedCode');
    const preview   = document.getElementById('colorPreview');

    function updateCode() {
        const dc = designSel.selectedOptions[0]?.dataset.code || '';
        const cc = colorSel.selectedOptions[0]?.dataset.code  || '';
        const g  = gaugeSel.value.toUpperCase();
        codeEl.textContent = (dc && cc && g) ? `${dc}-${cc}-${g}` : '—';
    }

    function updateColorPreview() {
        const hex = colorSel.selectedOptions[0]?.dataset.hex || '';
        if (hex) {
            preview.querySelector('div').style.background = hex;
            preview.style.display = '';
        } else {
            preview.style.display = 'none';
        }
    }

    designSel.addEventListener('change', updateCode);
    gaugeSel.addEventListener('change', updateCode);
    colorSel.addEventListener('change', () => { updateCode(); updateColorPreview(); });
})();
</script>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
