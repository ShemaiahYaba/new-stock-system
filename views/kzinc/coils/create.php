<?php
/**
 * Create K-Zinc Coil
 */

require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Add K-Zinc Coil - ' . APP_NAME;

$colorModel = new Color();
$colors = $colorModel->getActive();

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Add K-Zinc Coil</h1>
                <p class="text-muted">Register a new K-Zinc coil</p>
            </div>
            <a href="/new-stock-system/index.php?page=kzinc_coils" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to K-Zinc Coils
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-layers"></i> Coil Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/coils/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <!-- Category locked to KZinc -->
                        <input type="hidden" name="category" value="<?php echo STOCK_CATEGORY_KZINC; ?>">
                        <!-- Redirect back to KZinc module after save -->
                        <input type="hidden" name="redirect_to" value="kzinc_coils">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Coil Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code"
                                       placeholder="e.g., KZ-001" required>
                                <div class="invalid-feedback">Please provide a coil code.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Coil Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="e.g., Premium KZinc 0.45" required>
                                <div class="invalid-feedback">Please provide a name.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="color_id" class="form-label">Base Color <span class="text-danger">*</span></label>
                                <select class="form-select" id="color_id" name="color_id" required>
                                    <option value="">Select color</option>
                                    <?php foreach ($colors as $color): ?>
                                    <option value="<?php echo $color['id']; ?>">
                                        <?php echo htmlspecialchars($color['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Please select a color.</div>
                                <?php if (empty($colors)): ?>
                                <small class="text-warning">
                                    <i class="bi bi-exclamation-triangle"></i> No colors found.
                                    <a href="/new-stock-system/index.php?page=colors_create">Add colors first</a>
                                </small>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="net_weight" class="form-label">Net Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="net_weight" name="net_weight"
                                       step="0.01" min="0" placeholder="e.g., 1500.00" required>
                                <div class="invalid-feedback">Please provide net weight.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pallet_size" class="form-label">
                                Pallet Size (bundles per pallet) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="pallet_size" name="pallet_size"
                                   min="1" step="1" placeholder="e.g., 85" required>
                            <small class="text-muted">
                                How many bundles fit on one pallet. Each bundle = <?php echo KZINC_PIECES_PER_BUNDLE; ?> pieces.
                            </small>
                            <div class="invalid-feedback">Pallet size is required for K-Zinc coils.</div>
                        </div>

                        <div class="mb-3">
                            <label for="gauge" class="form-label">Gauge</label>
                            <input type="text" class="form-control" id="gauge" name="gauge"
                                   placeholder="e.g., 0.45mm">
                            <small class="text-muted">Material thickness (optional)</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=kzinc_coils" class="btn btn-secondary">
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
                <div class="card-header"><i class="bi bi-info-circle"></i> K-Zinc Units</div>
                <div class="card-body">
                    <ul class="list-unstyled small text-muted">
                        <li class="mb-1"><strong>Pallet</strong> → bundles → pieces</li>
                        <li class="mb-1">Each bundle = <strong><?php echo KZINC_PIECES_PER_BUNDLE; ?> pieces</strong></li>
                        <li class="mb-1">Pallet size varies per coil — set it here</li>
                    </ul>
                    <hr>
                    <p class="small text-muted mb-0">
                        Stock entries, sales, and production papers for this coil will all be
                        managed through the K-Zinc module.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
