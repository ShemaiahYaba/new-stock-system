<?php
/**
 * Edit Color Form
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/color.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Edit Color - ' . APP_NAME;

$colorId = (int)($_GET['id'] ?? 0);

if ($colorId <= 0) {
    setFlashMessage('error', 'Invalid color ID.');
    header('Location: /new-stock-system/index.php?page=colors');
    exit();
}

$colorModel = new Color();
$color = $colorModel->findById($colorId);

if (!$color) {
    setFlashMessage('error', 'Color not found.');
    header('Location: /new-stock-system/index.php?page=colors');
    exit();
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Color</h1>
                <p class="text-muted">Update color information</p>
            </div>
            <a href="/new-stock-system/index.php?page=colors" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Colors
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Color Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/colors/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $color['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Color Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="<?php echo htmlspecialchars($color['code']); ?>" required>
                                <div class="invalid-feedback">Please provide a color code.</div>
                                <small class="form-text text-muted">Short unique identifier</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($color['name']); ?>" required>
                                <div class="invalid-feedback">Please provide a display name.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="hex_code" class="form-label">Hex Color Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="hex_code" name="hex_code" 
                                       placeholder="#FF5733" pattern="^#[0-9A-Fa-f]{6}$"
                                       value="<?php echo htmlspecialchars($color['hex_code'] ?? ''); ?>"
                                       maxlength="7">
                                <input type="color" class="form-control form-control-color" id="color_picker" 
                                       title="Choose color" value="<?php echo htmlspecialchars($color['hex_code'] ?? '#563d7c'); ?>">
                            </div>
                            <small class="form-text text-muted">Optional - Used for visual preview in the UI</small>
                            <div class="invalid-feedback">Invalid hex code format. Use #RRGGBB format.</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?php echo $color['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active Color
                                </label>
                                <small class="form-text text-muted d-block">Only active colors can be used when creating coils.</small>
                            </div>
                        </div>
                        
                        <div id="color_preview" class="alert alert-secondary">
                            <strong>Preview:</strong>
                            <div class="mt-2 d-flex align-items-center">
                                <div id="preview_box" style="width: 50px; height: 50px; border: 2px solid #ddd; border-radius: 4px; background-color: <?php echo htmlspecialchars($color['hex_code'] ?? '#563d7c'); ?>;"></div>
                                <div class="ms-3">
                                    <div><strong id="preview_name"><?php echo htmlspecialchars($color['name']); ?></strong></div>
                                    <div><code id="preview_code"><?php echo htmlspecialchars($color['code']); ?></code></div>
                                </div>
                            </div>
                        </div>
                        
                        <?php
                        // Check if color is being used
                        $db = Database::getInstance()->getConnection();
                        $stmt = $db->prepare("SELECT COUNT(*) as count FROM coils WHERE color_id = :color_id AND deleted_at IS NULL");
                        $stmt->execute([':color_id' => $color['id']]);
                        $usage = $stmt->fetch();
                        
                        if ($usage['count'] > 0):
                        ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Warning:</strong> This color is currently being used by <?php echo $usage['count']; ?> coil(s). 
                            Changes will affect how those coils are displayed.
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=colors" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Color
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Color Details
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Color ID:</th>
                            <td>#<?php echo $color['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDate($color['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?php echo $color['updated_at'] ? formatDate($color['updated_at']) : 'Never'; ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if ($color['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-box-seam"></i> Usage Statistics
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <i class="bi bi-info-circle text-primary"></i>
                        This color is used by <strong><?php echo $usage['count']; ?></strong> coil(s).
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Sync color picker with hex input
document.getElementById('color_picker').addEventListener('input', function(e) {
    document.getElementById('hex_code').value = e.target.value.toUpperCase();
    updatePreview();
});

// Sync hex input with color picker
document.getElementById('hex_code').addEventListener('input', function(e) {
    const hex = e.target.value;
    if (/^#[0-9A-F]{6}$/i.test(hex)) {
        document.getElementById('color_picker').value = hex;
    }
    updatePreview();
});

// Update preview when name or code changes
document.getElementById('code').addEventListener('input', updatePreview);
document.getElementById('name').addEventListener('input', updatePreview);

function updatePreview() {
    const code = document.getElementById('code').value;
    const name = document.getElementById('name').value;
    const hex = document.getElementById('hex_code').value;
    
    document.getElementById('preview_code').textContent = code;
    document.getElementById('preview_name').textContent = name;
    
    if (/^#[0-9A-F]{6}$/i.test(hex)) {
        document.getElementById('preview_box').style.backgroundColor = hex;
    }
}
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>