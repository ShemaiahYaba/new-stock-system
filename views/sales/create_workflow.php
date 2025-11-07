<?php
/**
 * New Sale - Production Workflow (3 Tabs)
 * File: views/sales/create_workflow.php
 *
 * Tab 1: Production - Define production requirements
 * Tab 2: Invoice - Financial breakdown
 * Tab 3: Confirm Order - Review and submit (creates immutable records)
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/warehouse.php';
require_once __DIR__ . '/../../models/coil.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'New Sale - Production Workflow - ' . APP_NAME;

// Get required data
$customerModel = new Customer();
$warehouseModel = new Warehouse();
$coilModel = new Coil();

$customers = $customerModel->getAll(1000, 0);
$warehouses = $warehouseModel->getActive();

// Get only Factory Use coils for production workflow
$factoryCoils = $coilModel->getByStatus(STOCK_STATUS_FACTORY_USE);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<style>
.workflow-tabs {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.workflow-tabs .nav-tabs {
    background: #f8f9fa;
    border-bottom: 3px solid #dee2e6;
}

.workflow-tabs .nav-tabs .nav-link {
    color: #6c757d;
    font-weight: 600;
    padding: 15px 30px;
    border: none;
    position: relative;
}

.workflow-tabs .nav-tabs .nav-link.active {
    color: #007bff;
    background: white;
    border-bottom: 3px solid #007bff;
    margin-bottom: -3px;
}

.workflow-tabs .nav-tabs .nav-link.completed {
    color: #28a745;
}

.workflow-tabs .nav-tabs .nav-link.completed::after {
    content: "✓";
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #28a745;
    font-weight: bold;
}

.workflow-tabs .nav-tabs .nav-link:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.property-row {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
}

.property-row-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.computed-value {
    background: #e7f3ff;
    border: 1px solid #b3d9ff;
    padding: 8px 12px;
    border-radius: 4px;
    font-weight: 600;
    color: #004085;
}

.summary-box {
    background: #fff3cd;
    border: 2px solid #ffc107;
    border-radius: 8px;
    padding: 20px;
    margin-top: 20px;
}

.summary-box h4 {
    color: #856404;
    margin-bottom: 15px;
}

.invoice-preview {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
}

.confirm-section {
    background: white;
    border-radius: 8px;
    padding: 20px;
}

.production-paper-preview,
.invoice-shape-preview {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    max-height: 600px;
    overflow-y: auto;
}
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">New Sale - Production Workflow</h1>
                <p class="text-muted">Complete all three tabs to create an immutable sale record</p>
            </div>
            <a href="/new-stock-system/index.php?page=sales" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Cancel
            </a>
        </div>
    </div>

    <div class="workflow-tabs">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs" id="workflowTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="production-tab" data-bs-toggle="tab" 
                        data-bs-target="#production-content" type="button" role="tab">
                    <i class="bi bi-gear"></i> 1. Production
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="invoice-tab" data-bs-toggle="tab" 
                        data-bs-target="#invoice-content" type="button" role="tab" disabled>
                    <i class="bi bi-receipt"></i> 2. Invoice
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="confirm-tab" data-bs-toggle="tab" 
                        data-bs-target="#confirm-content" type="button" role="tab" disabled>
                    <i class="bi bi-check-circle"></i> 3. Confirm Order
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content p-4" id="workflowTabContent">
            
            <!-- ===================== TAB 1: PRODUCTION ===================== -->
            <div class="tab-pane fade show active" id="production-content" role="tabpanel">
                <h4 class="mb-4"><i class="bi bi-gear"></i> Production Requirements</h4>
                
                <!-- Step 1: Customer & Warehouse Selection -->
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Step 1:</strong> Customer & Warehouse Selection
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                                <select class="form-select" id="customer_id" name="customer_id" required>
                                    <option value="">Select customer</option>
                                    <?php foreach ($customers as $customer): ?>
                                    <option value="<?php echo $customer['id']; ?>" 
                                            data-name="<?php echo htmlspecialchars(
                                                $customer['name'],
                                            ); ?>"
                                            data-phone="<?php echo htmlspecialchars(
                                                $customer['phone'],
                                            ); ?>"
                                            data-address="<?php echo htmlspecialchars(
                                                $customer['address'] ?? '',
                                            ); ?>"
                                            data-company="<?php echo htmlspecialchars(
                                                $customer['company'] ?? '',
                                            ); ?>">
                                        <?php echo htmlspecialchars(
                                            $customer['name'],
                                        ); ?> - <?php echo htmlspecialchars($customer['phone']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">
                                    <a href="/new-stock-system/index.php?page=customers_create" target="_blank">Create new customer</a>
                                </small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="warehouse_id" class="form-label">Warehouse <span class="text-danger">*</span></label>
                                <select class="form-select" id="warehouse_id" name="warehouse_id" required>
                                    <option value="">Select warehouse</option>
                                    <?php foreach ($warehouses as $warehouse): ?>
                                    <option value="<?php echo $warehouse['id']; ?>"
                                            data-name="<?php echo htmlspecialchars(
                                                $warehouse['name'],
                                            ); ?>"
                                            data-location="<?php echo htmlspecialchars(
                                                $warehouse['location'] ?? '',
                                            ); ?>">
                                        <?php echo htmlspecialchars($warehouse['name']); ?>
                                        <?php if ($warehouse['location']): ?>
                                        - <?php echo htmlspecialchars($warehouse['location']); ?>
                                        <?php endif; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div id="selection_summary" class="alert alert-info d-none">
                            <strong>Selected:</strong> <span id="selection_text"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2: Stock Selection -->
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Step 2:</strong> Stock Selection
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="coil_id" class="form-label">Select Coil (Factory Use Only) <span class="text-danger">*</span></label>
                            <select class="form-select" id="coil_id" name="coil_id" required>
                                <option value="">Select coil</option>
                                <?php foreach ($factoryCoils as $coil): ?>
                                <option value="<?php echo $coil['id']; ?>"
                                        data-code="<?php echo htmlspecialchars($coil['code']); ?>"
                                        data-name="<?php echo htmlspecialchars($coil['name']); ?>"
                                        data-category="<?php echo $coil['category']; ?>"
                                        data-color="<?php echo $coil['color']; ?>"
                                        data-weight="<?php echo $coil['net_weight']; ?>">
                                    <?php echo htmlspecialchars($coil['code']); ?> - 
                                    <?php echo htmlspecialchars($coil['name']); ?>
                                    (<?php echo STOCK_CATEGORIES[$coil['category']]; ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div id="coil_details" class="alert alert-success d-none mb-3">
                            <strong>Coil Details:</strong><br>
                            <span id="coil_info"></span>
                        </div>
                        
                        <!-- Property Selection (Dynamic - Shows when Alusteel is selected) -->
                        <div id="property_selection" class="d-none">
                            <hr>
                            <h5 class="mb-3">Property Configuration</h5>
                            <p class="text-muted">Define the properties for this stock</p>
                            
                            <div id="properties_container">
                                <!-- Property rows will be dynamically added here -->
                            </div>
                            
                            <button type="button" class="btn btn-success btn-sm" id="add_property_btn">
                                <i class="bi bi-plus-circle"></i> Add Another Property
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Production Summary -->
                <div id="production_summary" class="summary-box d-none">
                    <h4><i class="bi bi-calculator"></i> Production Summary</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Total Meters:</strong> <span id="total_meters_display" class="computed-value">0.00m</span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Total Amount:</strong> <span id="total_amount_display" class="computed-value">₦0.00</span></p>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-end">
                    <button type="button" class="btn btn-primary btn-lg" id="proceed_to_invoice_btn" disabled>
                        Proceed to Invoice <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- ===================== TAB 2: INVOICE ===================== -->
            <div class="tab-pane fade" id="invoice-content" role="tabpanel">
                <h4 class="mb-4"><i class="bi bi-receipt"></i> Invoice Details</h4>
                
                <div class="invoice-preview">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Company Information</h5>
                            <p class="mb-1"><strong><?php echo COMPANY_NAME; ?></strong></p>
                            <p class="mb-1 small"><?php echo COMPANY_ADDRESS; ?></p>
                            <p class="mb-1 small">Phone: <?php echo COMPANY_PHONE; ?></p>
                            <p class="mb-1 small">Email: <?php echo COMPANY_EMAIL; ?></p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h5>Customer Information</h5>
                            <div id="invoice_customer_info">
                                <p class="text-muted">Select customer in Production tab</p>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5 class="mb-3">Invoice Items</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Code</th>
                                    <th>Description</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody id="invoice_items_body">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Complete production tab to see items
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Order Tax:</strong></td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm text-end" 
                                               id="invoice_tax" value="0.00" step="0.01" min="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Discount:</strong></td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm text-end" 
                                               id="invoice_discount" value="0.00" step="0.01" min="0">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                                    <td class="text-end">
                                        <input type="number" class="form-control form-control-sm text-end" 
                                               id="invoice_shipping" value="0.00" step="0.01" min="0">
                                    </td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="4" class="text-end"><h5 class="mb-0">Grand Total:</h5></td>
                                    <td class="text-end"><h5 class="mb-0" id="invoice_grand_total">₦0.00</h5></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="mt-3">
                        <h5>Notes</h5>
                        <textarea class="form-control" id="invoice_notes" rows="3" 
                                  placeholder="Receipt statement, refund policy, etc."></textarea>
                    </div>
                </div>
                
                <div class="mt-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary" id="back_to_production_btn">
                        <i class="bi bi-arrow-left"></i> Back to Production
                    </button>
                    <button type="button" class="btn btn-primary btn-lg" id="proceed_to_confirm_btn">
                        Proceed to Confirm <i class="bi bi-arrow-right"></i>
                    </button>
                </div>
            </div>
            
            <!-- ===================== TAB 3: CONFIRM ORDER ===================== -->
            <div class="tab-pane fade" id="confirm-content" role="tabpanel">
                <h4 class="mb-4"><i class="bi bi-check-circle"></i> Confirm Order</h4>
                
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Important:</strong> Once confirmed, this record will be <strong>immutable</strong>. 
                    Changes will require Super Admin approval and will be logged in the audit trail.
                </div>
                
                <div class="row">
                    <!-- Left Column: Production Paper -->
                    <div class="col-md-6">
                        <div class="confirm-section">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-file-earmark-text"></i> Production Paper
                            </h5>
                            <div class="production-paper-preview" id="production_paper_preview">
                                <p class="text-muted">Complete previous tabs to see production details</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Invoice -->
                    <div class="col-md-6">
                        <div class="confirm-section">
                            <h5 class="text-primary mb-3">
                                <i class="bi bi-receipt"></i> Invoice Summary
                            </h5>
                            <div class="invoice-shape-preview" id="invoice_shape_preview">
                                <p class="text-muted">Complete previous tabs to see invoice details</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form id="confirm_order_form" class="mt-4">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <input type="hidden" name="production_data" id="production_data_input">
                    <input type="hidden" name="invoice_data" id="invoice_data_input">
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirm_immutable" required>
                        <label class="form-check-label" for="confirm_immutable">
                            I understand this record will be immutable and can only be modified by Super Admin
                        </label>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary" id="back_to_invoice_btn">
                            <i class="bi bi-arrow-left"></i> Back to Invoice
                        </button>
                        <button type="submit" class="btn btn-success btn-lg" id="submit_order_btn" disabled>
                            <i class="bi bi-check-circle"></i> Confirm & Create Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// ============================================================
// GLOBAL STATE MANAGEMENT
// ============================================================
const workflowState = {
    customer: null,
    warehouse: null,
    coil: null,
    properties: [],
    productionSummary: {
        totalMeters: 0,
        totalAmount: 0
    },
    invoiceData: {
        tax: 0,
        discount: 0,
        shipping: 0,
        grandTotal: 0,
        notes: ''
    }
};

// Property counter for unique IDs
let propertyCounter = 0;

// ============================================================
// TAB 1: PRODUCTION - EVENT HANDLERS
// ============================================================

// Customer Selection
document.getElementById('customer_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (this.value) {
        workflowState.customer = {
            id: this.value,
            name: option.dataset.name,
            phone: option.dataset.phone,
            address: option.dataset.address,
            company: option.dataset.company
        };
        updateSelectionSummary();
    } else {
        workflowState.customer = null;
    }
    validateProductionTab();
});

// Warehouse Selection
document.getElementById('warehouse_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (this.value) {
        workflowState.warehouse = {
            id: this.value,
            name: option.dataset.name,
            location: option.dataset.location
        };
        updateSelectionSummary();
    } else {
        workflowState.warehouse = null;
    }
    validateProductionTab();
});

// Coil Selection
document.getElementById('coil_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (this.value) {
        workflowState.coil = {
            id: this.value,
            code: option.dataset.code,
            name: option.dataset.name,
            category: option.dataset.category,
            color: option.dataset.color,
            weight: option.dataset.weight
        };
        
        // Show coil details
        const coilInfo = `
            <strong>Code:</strong> ${workflowState.coil.code} | 
            <strong>Name:</strong> ${workflowState.coil.name}<br>
            <strong>Category:</strong> ${workflowState.coil.category} | 
            <strong>Color:</strong> ${workflowState.coil.color} | 
            <strong>Weight:</strong> ${workflowState.coil.weight}kg
        `;
        document.getElementById('coil_info').innerHTML = coilInfo;
        document.getElementById('coil_details').classList.remove('d-none');
        
        // Show property selection for Alusteel
        if (workflowState.coil.category === 'alusteel') {
            document.getElementById('property_selection').classList.remove('d-none');
            // Add first property row
            if (workflowState.properties.length === 0) {
                addPropertyRow();
            }
        } else {
            document.getElementById('property_selection').classList.add('d-none');
            workflowState.properties = [];
        }
    } else {
        workflowState.coil = null;
        document.getElementById('coil_details').classList.add('d-none');
        document.getElementById('property_selection').classList.add('d-none');
    }
    validateProductionTab();
});

// Add Property Row
document.getElementById('add_property_btn').addEventListener('click', addPropertyRow);

function addPropertyRow() {
    const rowId = propertyCounter++;
    const container = document.getElementById('properties_container');
    
    const rowHtml = `
        <div class="property-row" id="property_row_${rowId}">
            <div class="property-row-header">
                <h6 class="mb-0">Property ${rowId + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removePropertyRow(${rowId})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label class="form-label">Property Type <span class="text-danger">*</span></label>
                    <select class="form-select property-type" data-row-id="${rowId}" required>
                        <option value="">Select property</option>
                        <option value="mainsheet">Mainsheet</option>
                        <option value="flatsheet">Flatsheet</option>
                        <option value="cladding">Cladding</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-2">
                    <label class="form-label">Sheet Qty <span class="text-danger">*</span></label>
                    <input type="number" class="form-control sheet-qty" data-row-id="${rowId}" 
                           min="1" step="1" placeholder="e.g., 24" required>
                </div>
                
                <div class="col-md-2 mb-2">
                    <label class="form-label">Meter/Sheet <span class="text-danger">*</span></label>
                    <input type="number" class="form-control sheet-meter" data-row-id="${rowId}" 
                           min="0.01" step="0.01" placeholder="e.g., 8.20" required>
                </div>
                
                <div class="col-md-2 mb-2">
                    <label class="form-label">Total Meters</label>
                    <input type="text" class="form-control computed-meters" id="meters_${rowId}" readonly>
                </div>
                
                <div class="col-md-2 mb-2">
                    <label class="form-label">Unit Price (₦)</label>
                    <input type="number" class="form-control unit-price" data-row-id="${rowId}" 
                           min="0" step="0.01" placeholder="10300.00">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Row Subtotal:</small>
                        <span class="computed-value" id="subtotal_${rowId}">₦0.00</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', rowHtml);
    attachPropertyRowListeners(rowId);
}

function attachPropertyRowListeners(rowId) {
    const row = document.getElementById(`property_row_${rowId}`);
    const inputs = row.querySelectorAll('.sheet-qty, .sheet-meter, .unit-price');
    
    inputs.forEach(input => {
        input.addEventListener('input', () => calculatePropertyRow(rowId));
    });
}

function calculatePropertyRow(rowId) {
    const row = document.getElementById(`property_row_${rowId}`);
    const sheetQty = parseFloat(row.querySelector('.sheet-qty').value) || 0;
    const sheetMeter = parseFloat(row.querySelector('.sheet-meter').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
    
    const meters = sheetQty * sheetMeter;
    const subtotal = meters * unitPrice;
    
    // Update display
    document.getElementById(`meters_${rowId}`).value = meters.toFixed(2) + 'm';
    document.getElementById(`subtotal_${rowId}`).textContent = '₦' + subtotal.toLocaleString('en-US', {minimumFractionDigits: 2});
    
    // Update state
    updatePropertyState(rowId, {
        propertyType: row.querySelector('.property-type').value,
        sheetQty: sheetQty,
        sheetMeter: sheetMeter,
        meters: meters,
        unitPrice: unitPrice,
        subtotal: subtotal
    });
    
    // Recalculate totals
    calculateProductionTotals();
}

function updatePropertyState(rowId, data) {
    const existingIndex = workflowState.properties.findIndex(p => p.rowId === rowId);
    
    if (existingIndex >= 0) {
        workflowState.properties[existingIndex] = { rowId, ...data };
    } else {
        workflowState.properties.push({ rowId, ...data });
    }
}

function removePropertyRow(rowId) {
    if (confirm('Remove this property row?')) {
        document.getElementById(`property_row_${rowId}`).remove();
        workflowState.properties = workflowState.properties.filter(p => p.rowId !== rowId);
        calculateProductionTotals();
        validateProductionTab();
    }
}

function calculateProductionTotals() {
    let totalMeters = 0;
    let totalAmount = 0;
    
    workflowState.properties.forEach(prop => {
        totalMeters += prop.meters;
        totalAmount += prop.subtotal;
    });
    
    workflowState.productionSummary = { totalMeters, totalAmount };
    
    // Update display
    document.getElementById('total_meters_display').textContent = totalMeters.toFixed(2) + 'm';
    document.getElementById('total_amount_display').textContent = '₦' + totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2});
    
    // Show summary box
    if (workflowState.properties.length > 0) {
        document.getElementById('production_summary').classList.remove('d-none');
    } else {
        document.getElementById('production_summary').classList.add('d-none');
    }
    
    validateProductionTab();
}

function updateSelectionSummary() {
    if (workflowState.customer && workflowState.warehouse) {
        const text = `Customer: ${workflowState.customer.name} | Warehouse: ${workflowState.warehouse.name}`;
        document.getElementById('selection_text').textContent = text;
        document.getElementById('selection_summary').classList.remove('d-none');
    }
}

function validateProductionTab() {
    const isValid = workflowState.customer && 
                    workflowState.warehouse && 
                    workflowState.coil && 
                    workflowState.properties.length > 0 &&
                    workflowState.productionSummary.totalMeters > 0;
    
    document.getElementById('proceed_to_invoice_btn').disabled = !isValid;
}

// Proceed to Invoice
document.getElementById('proceed_to_invoice_btn').addEventListener('click', function() {
    // Mark production tab as completed
    document.getElementById('production-tab').classList.add('completed');
    
    // Enable invoice tab
    document.getElementById('invoice-tab').disabled = false;
    
    // Populate invoice tab
    populateInvoiceTab();
    
    // Switch to invoice tab
    const invoiceTab = new bootstrap.Tab(document.getElementById('invoice-tab'));
    invoiceTab.show();
});

// ============================================================
// TAB 2: INVOICE - POPULATION & HANDLERS
// ============================================================

function populateInvoiceTab() {
    // Populate customer info
    if (workflowState.customer) {
        const customerHtml = `
            <p class="mb-1"><strong>${workflowState.customer.name}</strong></p>
            ${workflowState.customer.company ? `<p class="mb-1 small">${workflowState.customer.company}</p>` : ''}
            <p class="mb-1 small">Phone: ${workflowState.customer.phone}</p>
            ${workflowState.customer.address ? `<p class="mb-1 small">${workflowState.customer.address}</p>` : ''}
        `;
        document.getElementById('invoice_customer_info').innerHTML = customerHtml;
    }
    
    // Populate invoice items
    const tbody = document.getElementById('invoice_items_body');
    tbody.innerHTML = '';
    
    workflowState.properties.forEach((prop, index) => {
        const row = `
            <tr>
                <td>${workflowState.coil.code}</td>
                <td>
                    <strong>${workflowState.coil.name}</strong><br>
                    <small class="text-muted">${prop.propertyType} (${prop.sheetQty} sheets × ${prop.sheetMeter}m)</small>
                </td>
                <td class="text-end">₦${prop.unitPrice.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
                <td class="text-center">${prop.meters.toFixed(2)}m</td>
                <td class="text-end">₦${prop.subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
    
    // Calculate initial grand total
    calculateInvoiceGrandTotal();
}

// Invoice calculation listeners
['invoice_tax', 'invoice_discount', 'invoice_shipping'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateInvoiceGrandTotal);
});

function calculateInvoiceGrandTotal() {
    const subtotal = workflowState.productionSummary.totalAmount;
    const tax = parseFloat(document.getElementById('invoice_tax').value) || 0;
    const discount = parseFloat(document.getElementById('invoice_discount').value) || 0;
    const shipping = parseFloat(document.getElementById('invoice_shipping').value) || 0;
    
    const grandTotal = subtotal + tax - discount + shipping;
    
    workflowState.invoiceData = {
        tax,
        discount,
        shipping,
        grandTotal,
        notes: document.getElementById('invoice_notes').value
    };
    
    document.getElementById('invoice_grand_total').textContent = '₦' + grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2});
}

// Back to Production
document.getElementById('back_to_production_btn').addEventListener('click', function() {
    const productionTab = new bootstrap.Tab(document.getElementById('production-tab'));
    productionTab.show();
});

// Proceed to Confirm
document.getElementById('proceed_to_confirm_btn').addEventListener('click', function() {
    // Mark invoice tab as completed
    document.getElementById('invoice-tab').classList.add('completed');
    
    // Enable confirm tab
    document.getElementById('confirm-tab').disabled = false;
    
    // Populate confirm tab
    populateConfirmTab();
    
    // Switch to confirm tab
    const confirmTab = new bootstrap.Tab(document.getElementById('confirm-tab'));
    confirmTab.show();
});

// ============================================================
// TAB 3: CONFIRM ORDER - POPULATION & SUBMISSION
// ============================================================

function populateConfirmTab() {
    // Populate Production Paper Preview
    const productionPaperHtml = `
        <h6 class="text-primary">Customer & Warehouse</h6>
        <p class="mb-1"><strong>Customer:</strong> ${workflowState.customer.name} (${workflowState.customer.phone})</p>
        <p class="mb-2"><strong>Warehouse:</strong> ${workflowState.warehouse.name}</p>
        
        <h6 class="text-primary mt-3">Coil Information</h6>
        <p class="mb-1"><strong>Code:</strong> ${workflowState.coil.code}</p>
        <p class="mb-1"><strong>Name:</strong> ${workflowState.coil.name}</p>
        <p class="mb-2"><strong>Category:</strong> ${workflowState.coil.category}</p>
        
        <h6 class="text-primary mt-3">Properties</h6>
        ${workflowState.properties.map((prop, index) => `
            <div class="mb-2 p-2 bg-light border rounded">
                <strong>${index + 1}. ${prop.propertyType}</strong><br>
                <small>
                    ${prop.sheetQty} sheets × ${prop.sheetMeter}m = ${prop.meters.toFixed(2)}m<br>
                    Unit Price: ₦${prop.unitPrice.toLocaleString()} | Subtotal: ₦${prop.subtotal.toLocaleString('en-US', {minimumFractionDigits: 2})}
                </small>
            </div>
        `).join('')}
        
        <h6 class="text-primary mt-3">Summary</h6>
        <p class="mb-1"><strong>Total Meters:</strong> ${workflowState.productionSummary.totalMeters.toFixed(2)}m</p>
        <p class="mb-0"><strong>Total Amount:</strong> ₦${workflowState.productionSummary.totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</p>
    `;
    document.getElementById('production_paper_preview').innerHTML = productionPaperHtml;
    
    // Populate Invoice Preview
    const invoicePreviewHtml = `
        <h6 class="text-primary">Invoice Summary</h6>
        <table class="table table-sm">
            <tr>
                <td>Subtotal:</td>
                <td class="text-end">₦${workflowState.productionSummary.totalAmount.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
            <tr>
                <td>Tax:</td>
                <td class="text-end">₦${workflowState.invoiceData.tax.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
            <tr>
                <td>Discount:</td>
                <td class="text-end">-₦${workflowState.invoiceData.discount.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
            <tr>
                <td>Shipping:</td>
                <td class="text-end">₦${workflowState.invoiceData.shipping.toLocaleString('en-US', {minimumFractionDigits: 2})}</td>
            </tr>
            <tr class="table-primary">
                <td><strong>Grand Total:</strong></td>
                <td class="text-end"><strong>₦${workflowState.invoiceData.grandTotal.toLocaleString('en-US', {minimumFractionDigits: 2})}</strong></td>
            </tr>
        </table>
        
        ${workflowState.invoiceData.notes ? `
            <h6 class="text-primary mt-3">Notes</h6>
            <p class="small">${workflowState.invoiceData.notes}</p>
        ` : ''}
    `;
    document.getElementById('invoice_shape_preview').innerHTML = invoicePreviewHtml;
    
    // Prepare hidden form data
    prepareFormData();
}

function prepareFormData() {
    const productionData = {
        customer_id: workflowState.customer.id,
        warehouse_id: workflowState.warehouse.id,
        coil_id: workflowState.coil.id,
        production_paper: {
            customer: workflowState.customer,
            warehouse: workflowState.warehouse,
            coil: workflowState.coil,
            properties: workflowState.properties,
            summary: workflowState.productionSummary
        }
    };
    
    const invoiceData = {
        customer: workflowState.customer,
        items: workflowState.properties.map(prop => ({
            product_code: workflowState.coil.code,
            description: `${workflowState.coil.name} - ${prop.propertyType}`,
            unit_price: prop.unitPrice,
            quantity: prop.meters,
            subtotal: prop.subtotal
        })),
        tax: workflowState.invoiceData.tax,
        discount: workflowState.invoiceData.discount,
        shipping: workflowState.invoiceData.shipping,
        grandTotal: workflowState.invoiceData.grandTotal,
        notes: workflowState.invoiceData.notes
    };
    
    document.getElementById('production_data_input').value = JSON.stringify(productionData);
    document.getElementById('invoice_data_input').value = JSON.stringify(invoiceData);
}

// Back to Invoice
document.getElementById('back_to_invoice_btn').addEventListener('click', function() {
    const invoiceTab = new bootstrap.Tab(document.getElementById('invoice-tab'));
    invoiceTab.show();
});

// Enable submit button when checkbox is checked
document.getElementById('confirm_immutable').addEventListener('change', function() {
    document.getElementById('submit_order_btn').disabled = !this.checked;
});

// Form Submission
document.getElementById('confirm_order_form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!confirm('Are you sure you want to create this order? This action cannot be undone.')) {
        return;
    }
    
    // Disable submit button to prevent double submission
    const submitBtn = document.getElementById('submit_order_btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    
    // Submit form
    const formData = new FormData(this);
    
    fetch('/new-stock-system/controllers/sales/create_workflow/index.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Order created successfully!');
            window.location.href = '/new-stock-system/index.php?page=sales_view&id=' + data.sale_id;
        } else {
            alert('Error: ' + (data.message || 'Failed to create order'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Confirm & Create Order';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while creating the order.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Confirm & Create Order';
    });
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
