<?php
/**
 * Edit Production Property Form
 * File: views/production_properties/edit.php
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/production_property.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Edit Production Property - ' . APP_NAME;

$propertyId = (int)($_GET['id'] ?? 0);

if ($propertyId <= 0) {
    setFlashMessage('error', 'Invalid property ID.');
    header('Location: /new-stock-system/index.php?page=production_properties');
    exit();
}

$propertyModel = new ProductionProperty();
$property = $propertyModel->findById($propertyId);

if (!$property) {
    setFlashMessage('error', 'Production property not found.');
    header('Location: /new-stock-system/index.php?page=production_properties');
    exit();
}

// Parse metadata if exists
$metadata = !empty($property['metadata']) ? json_decode($property['metadata'], true) : [];
$piecesPerBundle = $metadata['pieces_per_bundle'] ?? '';

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Edit Production Property</h1>
                <p class="text-muted">Update property information</p>
            </div>
            <a href="/new-stock-system/index.php?page=production_properties" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Properties
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-pencil"></i> Property Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/production_properties/update/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <input type="hidden" name="id" value="<?php echo $property['id']; ?>">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Property Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       value="<?php echo htmlspecialchars($property['code']); ?>" required>
                                <div class="invalid-feedback">Please provide a property code.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($property['name']); ?>" required>
                                <div class="invalid-feedback">Please provide a display name.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="alusteel" <?php echo $property['category'] === 'alusteel' ? 'selected' : ''; ?>>Alusteel</option>
                                    <option value="aluminum" <?php echo $property['category'] === 'aluminum' ? 'selected' : ''; ?>>Aluminum</option>
                                    <option value="kzinc" <?php echo $property['category'] === 'kzinc' ? 'selected' : ''; ?>>K-Zinc</option>
                                </select>
                                <div class="invalid-feedback">Please select a category.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="property_type" class="form-label">Property Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="property_type" name="property_type" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="<?php echo PROPERTY_TYPE_METER_BASED; ?>" 
                                            <?php echo $property['property_type'] === PROPERTY_TYPE_METER_BASED ? 'selected' : ''; ?>>
                                        Meter Based
                                    </option>
                                    <option value="<?php echo PROPERTY_TYPE_UNIT_BASED; ?>" 
                                            <?php echo $property['property_type'] === PROPERTY_TYPE_UNIT_BASED ? 'selected' : ''; ?>>
                                        Unit Based
                                    </option>
                                    <option value="<?php echo PROPERTY_TYPE_BUNDLE_BASED; ?>" 
                                            <?php echo $property['property_type'] === PROPERTY_TYPE_BUNDLE_BASED ? 'selected' : ''; ?>>
                                        Bundle Based
                                    </option>
                                </select>
                                <div class="invalid-feedback">Please select a property type.</div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="default_price" class="form-label">Default Price (₦)</label>
                                <input type="number" class="form-control" id="default_price" name="default_price" 
                                       min="0" step="0.01" value="<?php echo $property['default_price'] ?? 0; ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                       min="0" step="1" value="<?php echo $property['sort_order'] ?? 0; ?>">
                            </div>
                            
                            <div class="col-md-4 mb-3" id="bundle_config" 
                                 style="display: <?php echo $property['property_type'] === PROPERTY_TYPE_BUNDLE_BASED ? 'block' : 'none'; ?>;">
                                <label for="pieces_per_bundle" class="form-label">Pieces per Bundle</label>
                                <input type="number" class="form-control" id="pieces_per_bundle" name="pieces_per_bundle" 
                                       min="1" step="1" value="<?php echo $piecesPerBundle; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                       <?php echo $property['is_active'] ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active Property
                                </label>
                            </div>
                        </div>
                        
                        <?php if ($propertyModel->isUsedInProductions($property['id'])): ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            <strong>Warning:</strong> This property is currently being used in production records. 
                            Changes to calculation rules may affect reporting consistency.
                        </div>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=production_properties" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Property
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Property Details
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th>Property ID:</th>
                            <td>#<?php echo $property['id']; ?></td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td><?php echo formatDate($property['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th>Updated:</th>
                            <td><?php echo $property['updated_at'] ? formatDate($property['updated_at']) : 'Never'; ?></td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                <?php if ($property['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('property_type').addEventListener('change', function() {
    const bundleConfig = document.getElementById('bundle_config');
    if (this.value === '<?php echo PROPERTY_TYPE_BUNDLE_BASED; ?>') {
        bundleConfig.style.display = 'block';
        document.getElementById('pieces_per_bundle').required = true;
    } else {
        bundleConfig.style.display = 'none';
        document.getElementById('pieces_per_bundle').required = false;
    }
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>