<?php
/**
 * EXTENDED Create Production Property Form - WITH ADD-ON SUPPORT
 * File: views/production_properties/create.php
 * REPLACE the existing create.php with this version
 */

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Create Production Property - ' . APP_NAME;

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Create New Production Property</h1>
                <p class="text-muted">Add a new property or add-on charge</p>
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
                    <i class="bi bi-gear"></i> Property Information
                </div>
                <div class="card-body">
                    <form action="/new-stock-system/controllers/production_properties/create/index.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                        
                        <!-- Property Type Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Property Type <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check card p-3 mb-2" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" name="is_addon" id="type_production" value="0" checked>
                                        <label class="form-check-label w-100" for="type_production" style="cursor: pointer;">
                                            <strong><i class="bi bi-box-seam text-primary"></i> Production Property</strong>
                                            <p class="small mb-0 text-muted">Quantity/meter-based properties (Mainsheet, Scraps, etc.)</p>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check card p-3 mb-2" style="cursor: pointer;">
                                        <input class="form-check-input" type="radio" name="is_addon" id="type_addon" value="1">
                                        <label class="form-check-label w-100" for="type_addon" style="cursor: pointer;">
                                            <strong><i class="bi bi-plus-circle text-success"></i> Add-On Charge</strong>
                                            <p class="small mb-0 text-muted">Service charges (Bending, Freight, Installation, etc.)</p>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Property Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="code" name="code" 
                                       placeholder="e.g., mainsheet, bending" required>
                                <small class="form-text text-muted">Unique identifier (lowercase, no spaces)</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       placeholder="e.g., Mainsheet, Bending Service" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="alusteel">Alusteel</option>
                                    <option value="aluminum">Aluminum</option>
                                    <option value="kzinc">K-Zinc</option>
                                </select>
                            </div>
                            
                            <!-- Production Property Type -->
                            <div class="col-md-6 mb-3" id="production_type_field">
                                <label for="property_type" class="form-label">Property Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="property_type" name="property_type" required>
                                    <option value="">-- Select Type --</option>
                                    <option value="<?php echo PROPERTY_TYPE_METER_BASED; ?>">Meter Based</option>
                                    <option value="<?php echo PROPERTY_TYPE_UNIT_BASED; ?>">Unit Based</option>
                                    <option value="<?php echo PROPERTY_TYPE_BUNDLE_BASED; ?>">Bundle Based</option>
                                </select>
                            </div>
                            
                            <!-- Add-On Calculation Method -->
                            <div class="col-md-6 mb-3 d-none" id="calculation_method_field">
                                <label for="calculation_method" class="form-label">Calculation Method <span class="text-danger">*</span></label>
                                <select class="form-select" id="calculation_method" name="calculation_method">
                                    <option value="<?php echo CALC_METHOD_FIXED; ?>">Fixed Amount (₦)</option>
                                    <option value="<?php echo CALC_METHOD_PERCENTAGE; ?>">Percentage (%)</option>
                                </select>
                                <small class="form-text text-muted">How is this charge calculated?</small>
                            </div>
                        </div>
                        
                        <!-- Add-On Specific Fields -->
                        <div class="row d-none" id="addon_fields">
                            <div class="col-md-6 mb-3">
                                <label for="applies_to" class="form-label">Applies To</label>
                                <select class="form-select" id="applies_to" name="applies_to">
                                    <option value="<?php echo APPLIES_TO_TOTAL; ?>">Total (After Tax)</option>
                                    <option value="<?php echo APPLIES_TO_SUBTOTAL; ?>">Subtotal (Before Tax)</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="display_section" class="form-label">Display Section</label>
                                <select class="form-select" id="display_section" name="display_section">
                                    <option value="<?php echo DISPLAY_SECTION_ADDON; ?>">Add-On Charges</option>
                                    <option value="<?php echo DISPLAY_SECTION_ADJUSTMENT; ?>">Adjustments & Refunds</option>
                                </select>
                            </div>
                            
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_refundable" name="is_refundable">
                                    <label class="form-check-label" for="is_refundable">
                                        This is a refund/credit (negative amount)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="default_price" class="form-label">Default Price/Amount (₦)</label>
                                <input type="number" class="form-control" id="default_price" name="default_price" 
                                       min="0" step="0.01" value="0" placeholder="0.00">
                                <small class="form-text text-muted" id="price_help">Default price per unit/meter</small>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                       min="0" step="1" value="0">
                            </div>
                            
                            <div class="col-md-4 mb-3" id="bundle_config" style="display: none;">
                                <label for="pieces_per_bundle" class="form-label">Pieces per Bundle</label>
                                <input type="number" class="form-control" id="pieces_per_bundle" name="pieces_per_bundle" 
                                       min="1" step="1" placeholder="15">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Active Property
                                </label>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            <strong>Note:</strong> Property codes must be unique. 
                            <span id="property_type_note">This property will be available in the production workflow.</span>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="/new-stock-system/index.php?page=production_properties" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Create Property
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle"></i> Property Types
                </div>
                <div class="card-body" id="help_production">
                    <h6><i class="bi bi-box-seam text-primary"></i> Production Properties</h6>
                    <ul class="small mb-0">
                        <li><strong>Meter Based:</strong> Sheet Qty × Meter/Sheet</li>
                        <li><strong>Unit Based:</strong> Quantity × Unit Price</li>
                        <li><strong>Bundle Based:</strong> Bundles with piece conversion</li>
                    </ul>
                </div>
                <div class="card-body d-none" id="help_addon">
                    <h6><i class="bi bi-plus-circle text-success"></i> Add-On Charges</h6>
                    <ul class="small mb-0">
                        <li><strong>Bending:</strong> Labour service charges</li>
                        <li><strong>Loading:</strong> Loading cost</li>
                        <li><strong>Freight:</strong> Shipping charges</li>
                        <li><strong>Accessories:</strong> Nails, washers, etc.</li>
                        <li><strong>Installation:</strong> Installation service</li>
                        <li><strong>Refund:</strong> Credits/refunds (negative)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle between production and add-on
const typeProduction = document.getElementById('type_production');
const typeAddon = document.getElementById('type_addon');
const productionTypeField = document.getElementById('production_type_field');
const calculationMethodField = document.getElementById('calculation_method_field');
const addonFields = document.getElementById('addon_fields');
const bundleConfig = document.getElementById('bundle_config');
const priceHelp = document.getElementById('price_help');
const propertyTypeNote = document.getElementById('property_type_note');
const helpProduction = document.getElementById('help_production');
const helpAddon = document.getElementById('help_addon');

typeProduction.addEventListener('change', function() {
    if (this.checked) {
        productionTypeField.classList.remove('d-none');
        calculationMethodField.classList.add('d-none');
        addonFields.classList.add('d-none');
        document.getElementById('property_type').required = true;
        document.getElementById('calculation_method').required = false;
        priceHelp.textContent = 'Default price per unit/meter';
        propertyTypeNote.textContent = 'This property will be available in the production workflow.';
        helpProduction.classList.remove('d-none');
        helpAddon.classList.add('d-none');
    }
});

typeAddon.addEventListener('change', function() {
    if (this.checked) {
        productionTypeField.classList.add('d-none');
        calculationMethodField.classList.remove('d-none');
        addonFields.classList.remove('d-none');
        document.getElementById('property_type').required = false;
        document.getElementById('calculation_method').required = true;
        // Set property_type to unit_based for add-ons
        document.getElementById('property_type').value = '<?php echo PROPERTY_TYPE_UNIT_BASED; ?>';
        priceHelp.textContent = 'Fixed amount or percentage value';
        propertyTypeNote.textContent = 'This add-on will be available during invoice generation.';
        helpProduction.classList.add('d-none');
        helpAddon.classList.remove('d-none');
    }
});

// Show/hide bundle configuration
document.getElementById('property_type').addEventListener('change', function() {
    if (this.value === '<?php echo PROPERTY_TYPE_BUNDLE_BASED; ?>') {
        bundleConfig.style.display = 'block';
        document.getElementById('pieces_per_bundle').required = true;
    } else {
        bundleConfig.style.display = 'none';
        document.getElementById('pieces_per_bundle').required = false;
    }
});

// Auto-set display section based on refundable
document.getElementById('is_refundable').addEventListener('change', function() {
    const displaySection = document.getElementById('display_section');
    if (this.checked) {
        displaySection.value = '<?php echo DISPLAY_SECTION_ADJUSTMENT; ?>';
    } else {
        displaySection.value = '<?php echo DISPLAY_SECTION_ADDON; ?>';
    }
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>