<?php
/**
 * Create Color Form
 */

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Create Color - ' . APP_NAME;

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create New Color</h1>
                <p class="text-muted">Add a new coil color to the system</p>
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
                    <i class="bi bi-palette"></i> Color Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/colors/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Color Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       placeholder="e.g., IBeige, PGreen" required>
                                <div class="invalid-feedback">Please provide a color code.</div>
                                <small class="form-text text-muted">Short unique identifier (e.g., IBeige, SBlue)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g., I/Beige, P/Green" required>
                                <div class="invalid-feedback">Please provide a display name.</div>
                                <small class="form-text text-muted">Full name shown to users</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="hex_code" class="form-label">Hex Color Code</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="hex_code" name="hex_code" 
                                       placeholder="#FF5733" pattern="^#[0-9A-Fa-f]{6}$"
                                       maxlength="7">
                                <input type="color" class="form-control form-control-color" id="color_picker" 
                                       title="Choose color" value="#563d7c">
                            </div>
                            <small class="form-text text-muted">Optional - Used for visual preview in the UI (format: #RRGGBB)</small>
                            <div class="invalid-feedback">Invalid hex code format. Use #RRGGBB format.</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Active Color
                                </label>
                                <small class="form-text text-muted d-block">Only active colors can be used when creating coils.</small>
                            </div>
                        </div>
                        
                        <div id="color_preview" class="alert alert-secondary d-none">
                            <strong>Preview:</strong>
                            <div class="mt-2 d-flex align-items-center">
                                <div id="preview_box" style="width: 50px; height: 50px; border: 2px solid #ddd; border-radius: 4px; background-color: #563d7c;"></div>
                                <div class="ms-3">
                                    <div><strong id="preview_name">Color Name</strong></div>
                                    <div><code id="preview_code">CODE</code></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> Color codes must be unique. This color will be available for selection when creating or editing coils.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=colors" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Color
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Field Information
                </div>
                <div class="card-body">
                    <h6>Required Fields</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> Color Code
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success"></i> Display Name
                        </li>
                    </ul>
                    <hr>
                    <h6>Optional Fields</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-circle text-muted"></i> Hex Color Code
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-circle text-muted"></i> Active Status
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-lightbulb"></i> Tips
                </div>
                <div class="card-body">
                    <ul class="small">
                        <li class="mb-2">Use short, memorable codes (e.g., IBeige, TCRed)</li>
                        <li class="mb-2">Display names should match your actual color naming convention</li>
                        <li class="mb-2">Hex codes help visually identify colors in dropdowns</li>
                        <li class="mb-2">Inactive colors won't appear in coil creation forms</li>
                    </ul>
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
    
    if (code || name || hex) {
        document.getElementById('color_preview').classList.remove('d-none');
        document.getElementById('preview_code').textContent = code || 'CODE';
        document.getElementById('preview_name').textContent = name || 'Color Name';
        
        if (/^#[0-9A-F]{6}$/i.test(hex)) {
            document.getElementById('preview_box').style.backgroundColor = hex;
        }
    } else {
        document.getElementById('color_preview').classList.add('d-none');
    }
}
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>