<?php
/**
 * Edit K-Zinc Coil
 */

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';

$pageTitle = 'Edit K-Zinc Coil - ' . APP_NAME;

$coilId = (int)($_GET['id'] ?? 0);

if ($coilId <= 0) {
    setFlashMessage('error', 'Invalid coil ID.');
    header('Location: /new-stock-system/index.php?page=kzinc_coils');
    exit();
}

$coilModel = new Coil();
$colorModel = new Color();
$coil = $coilModel->findById($coilId);
$colors = $colorModel->getActive();

if (!$coil || $coil['category'] !== STOCK_CATEGORY_KZINC) {
    setFlashMessage('error', 'K-Zinc coil not found.');
    header('Location: /new-stock-system/index.php?page=kzinc_coils');
    exit();
}

require_once __DIR__ . '/../../../layout/header.php';
require_once __DIR__ . '/../../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit K-Zinc Coil</h1>
                <p class="text-muted"><?php echo htmlspecialchars($coil['code']); ?> — <?php echo htmlspecialchars($coil['name']); ?></p>
            </div>
            <a href="/new-stock-system/index.php?page=kzinc_coils" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to K-Zinc Coils
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><i class="bi bi-pencil"></i> Coil Information</div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/coils/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $coil['id']; ?>">
                        <input type="hidden" name="category" value="<?php echo STOCK_CATEGORY_KZINC; ?>">
                        <input type="hidden" name="redirect_to" value="kzinc_coils">

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
                                <label for="color_id" class="form-label">Base Color <span class="text-danger">*</span></label>
                                <select class="form-select" id="color_id" name="color_id" required>
                                    <option value="">Select color</option>
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
                                <label for="net_weight" class="form-label">Net Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="net_weight" name="net_weight"
                                       step="0.01" min="0" value="<?php echo $coil['net_weight']; ?>" required>
                                <div class="invalid-feedback">Please provide net weight.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="pallet_size" class="form-label">
                                Pallet Size (bundles per pallet) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="pallet_size" name="pallet_size"
                                   min="1" step="1"
                                   value="<?php echo htmlspecialchars($coil['pallet_size'] ?? ''); ?>" required>
                            <small class="text-muted">
                                How many bundles fit on one pallet. Each bundle = <?php echo KZINC_PIECES_PER_BUNDLE; ?> pieces.
                            </small>
                            <div class="invalid-feedback">Pallet size is required.</div>
                        </div>

                        <div class="mb-3">
                            <label for="gauge" class="form-label">Gauge</label>
                            <input type="text" class="form-control" id="gauge" name="gauge"
                                   placeholder="e.g., 0.45mm"
                                   value="<?php echo htmlspecialchars($coil['gauge'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <?php foreach (STOCK_STATUSES as $statusKey => $statusName): ?>
                                <option value="<?php echo $statusKey; ?>"
                                    <?php echo $coil['status'] === $statusKey ? 'selected' : ''; ?>>
                                    <?php echo $statusName; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            Changing pallet size only affects <em>new</em> stock entries — existing entries are unaffected.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=kzinc_coils" class="btn btn-secondary">
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

<?php require_once __DIR__ . '/../../../layout/footer.php'; ?>
