<?php
/**
 * Production Property View Details
 * File: views/production_properties/view.php
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/production_property.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'View Production Property - ' . APP_NAME;

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

// Check if property is being used
$isUsed = $propertyModel->isUsedInProductions($propertyId);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Production Property Details</h1>
                <p class="text-muted">View property information and usage statistics</p>
            </div>
            <a href="/new-stock-system/index.php?page=production_properties" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Properties
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <?php
                    $typeIcons = [
                        'unit_based' => 'bi-box text-success',
                        'meter_based' => 'bi-rulers text-primary',
                        'bundle_based' => 'bi-boxes text-warning'
                    ];
                    $icon = $typeIcons[$property['property_type']] ?? 'bi-question-circle text-secondary';
                    ?>
                    <i class="bi <?php echo $icon; ?>" style="font-size: 80px;"></i>
                    <h4 class="mt-3"><?php echo htmlspecialchars($property['name']); ?></h4>
                    <p class="text-muted"><code><?php echo htmlspecialchars($property['code']); ?></code></p>
                    <?php if ($property['is_active']): ?>
                        <span class="badge bg-success">Active</span>
                    <?php else: ?>
                        <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Property Information
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th><i class="bi bi-tag"></i> Code:</th>
                            <td><code><?php echo htmlspecialchars($property['code']); ?></code></td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-type"></i> Name:</th>
                            <td><?php echo htmlspecialchars($property['name']); ?></td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-folder"></i> Category:</th>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo STOCK_CATEGORIES[$property['category']] ?? ucfirst($property['category']); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-gear"></i> Type:</th>
                            <td><?php echo PROPERTY_TYPES[$property['property_type']] ?? $property['property_type']; ?></td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-currency-dollar"></i> Price:</th>
                            <td><strong><?php echo formatCurrency($property['default_price'] ?? 0); ?></strong></td>
                        </tr>
                        <tr>
                            <th><i class="bi bi-sort-numeric-down"></i> Sort Order:</th>
                            <td><?php echo $property['sort_order'] ?? 0; ?></td>
                        </tr>
                        <?php if ($property['property_type'] === PROPERTY_TYPE_BUNDLE_BASED && !empty($metadata['pieces_per_bundle'])): ?>
                        <tr>
                            <th><i class="bi bi-boxes"></i> Pieces/Bundle:</th>
                            <td><?php echo $metadata['pieces_per_bundle']; ?> pieces</td>
                        </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-clock-history"></i> Record Info
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
                            <th>Created By:</th>
                            <td><?php echo htmlspecialchars($property['created_by_name'] ?? 'Unknown'); ?></td>
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
            
            <?php if (hasPermission(MODULE_PRODUCTION_PROPERTIES, ACTION_EDIT)): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-gear"></i> Actions
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="/new-stock-system/index.php?page=production_properties_edit&id=<?php echo $property['id']; ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Edit Property
                    </a>
                    <?php if (!$isUsed && hasPermission(MODULE_PRODUCTION_PROPERTIES, ACTION_DELETE)): ?>
                    <form method="POST" action="/new-stock-system/controllers/production_properties/delete/index.php" 
                          onsubmit="return confirmDelete('Are you sure you want to delete this property?');">
                        <input type="hidden" name="id" value="<?php echo $property['id']; ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Delete Property
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-graph-up"></i> Usage Statistics
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 text-primary">
                                    <?php echo $isUsed ? '<i class="bi bi-check-circle"></i>' : '<i class="bi bi-x-circle text-secondary"></i>'; ?>
                                </h3>
                                <small class="text-muted">Usage Status</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 text-success">
                                    <?php echo $property['is_active'] ? '<i class="bi bi-toggle-on"></i>' : '<i class="bi bi-toggle-off text-secondary"></i>'; ?>
                                </h3>
                                <small class="text-muted">Active Status</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 bg-light rounded">
                                <h3 class="mb-0 text-info"><?php echo formatCurrency($property['default_price'] ?? 0); ?></h3>
                                <small class="text-muted">Default Price</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-calculator"></i> Calculation Details
                </div>
                <div class="card-body">
                    <?php if ($property['property_type'] === PROPERTY_TYPE_METER_BASED): ?>
                    <div class="alert alert-info">
                        <h6><i class="bi bi-rulers"></i> Meter-Based Calculation</h6>
                        <p class="mb-2">This property calculates totals based on sheet quantity and meters per sheet:</p>
                        <div class="bg-white p-3 rounded border">
                            <code>Total Meters = Sheet Quantity × Meters per Sheet</code><br>
                            <code>Subtotal = Total Meters × Unit Price</code>
                        </div>
                        <p class="mt-3 mb-0"><strong>Used for:</strong> Mainsheet, Flatsheet, Cladding, etc.</p>
                    </div>
                    <?php elseif ($property['property_type'] === PROPERTY_TYPE_UNIT_BASED): ?>
                    <div class="alert alert-success">
                        <h6><i class="bi bi-box"></i> Unit-Based Calculation</h6>
                        <p class="mb-2">This property calculates totals based on simple unit count:</p>
                        <div class="bg-white p-3 rounded border">
                            <code>Subtotal = Quantity × Unit Price</code>
                        </div>
                        <p class="mt-3 mb-0"><strong>Used for:</strong> Scraps, Individual Pieces, etc.</p>
                    </div>
                    <?php elseif ($property['property_type'] === PROPERTY_TYPE_BUNDLE_BASED): ?>
                    <div class="alert alert-warning">
                        <h6><i class="bi bi-boxes"></i> Bundle-Based Calculation</h6>
                        <p class="mb-2">This property calculates totals with automatic piece conversion:</p>
                        <div class="bg-white p-3 rounded border">
                            <code>Total Pieces = Bundles × <?php echo $metadata['pieces_per_bundle'] ?? 15; ?> pieces/bundle</code><br>
                            <code>Subtotal = Bundles × Unit Price</code>
                        </div>
                        <p class="mt-3 mb-0"><strong>Used for:</strong> Bundle packages containing multiple units</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($isUsed): ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-exclamation-triangle text-warning"></i> Usage Information
                </div>
                <div class="card-body">
                    <div class="alert alert-warning mb-0">
                        <i class="bi bi-shield-exclamation"></i> 
                        <strong>Property In Use:</strong> This property is currently being used in production records. 
                        It cannot be deleted to maintain data integrity. Changes to pricing or calculation rules may affect future reports.
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Usage Information
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Not Yet Used:</strong> This property has not been used in any production records yet. 
                        You can safely edit or delete it without affecting existing data.
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="card mt-3">
                <div class="card-header">
                    <i class="bi bi-lightbulb"></i> Property Features
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>✅ Advantages</h6>
                            <ul class="small">
                                <li>Dynamic calculation based on type</li>
                                <li>Consistent pricing across sales</li>
                                <li>Easy to update without code changes</li>
                                <li>Category-specific organization</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>🎯 Best Practices</h6>
                            <ul class="small">
                                <li>Set realistic default prices</li>
                                <li>Use clear, descriptive names</li>
                                <li>Keep sort order logical</li>
                                <li>Disable instead of deleting when possible</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(message) {
    return confirm(message);
}
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>