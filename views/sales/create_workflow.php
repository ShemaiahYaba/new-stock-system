<?php
/**
 * New Sale - Production Workflow (3 Tabs) - REFACTORED
 * File: views/sales/create_workflow.php
 *
 * UPDATED: Fully modular architecture with separated concerns
 * - JavaScript logic extracted to modules
 * - Add-on support integrated
 * - Clean separation between production properties and add-ons
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/customer.php';
require_once __DIR__ . '/../../models/warehouse.php';
require_once __DIR__ . '/../../models/coil.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'New Sale - Production - ' . APP_NAME;

// Get required data
$customerModel = new Customer();
$warehouseModel = new Warehouse();
$coilModel = new Coil();

$customers = $customerModel->getAll(1000, 0);
$warehouses = $warehouseModel->getActive();
$coils = $coilModel->getForDropdown();

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

/* Add-On Styles */
.addon-card {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    transition: all 0.3s;
    cursor: pointer;
}

.addon-card:hover {
    border-color: #007bff;
    box-shadow: 0 2px 8px rgba(0,123,255,0.1);
}

.addon-card.selected {
    border-color: #007bff;
    background-color: #e7f3ff;
}

.addon-card .form-check-input {
    width: 20px;
    height: 20px;
    margin-top: 0;
}

.addon-type-badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

.addon-inputs {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #dee2e6;
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
                                            data-name="<?php echo htmlspecialchars($customer['name']); ?>"
                                            data-phone="<?php echo htmlspecialchars($customer['phone']); ?>"
                                            data-address="<?php echo htmlspecialchars($customer['address'] ?? ''); ?>"
                                            data-company="<?php echo htmlspecialchars($customer['company'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($customer['name']); ?> - <?php echo htmlspecialchars($customer['phone']); ?>
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
                                            data-name="<?php echo htmlspecialchars($warehouse['name']); ?>"
                                            data-location="<?php echo htmlspecialchars($warehouse['location'] ?? ''); ?>">
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="coil_id" class="form-label">Select Coil <span class="text-danger">*</span></label>
                                    <select class="form-select" id="coil_id" name="coil_id" required>
                                        <option value="">-- Select Coil --</option>
                                        <?php if (empty($coils)): ?>
                                            <option value="">No coils found</option>
                                        <?php else: foreach ($coils as $coil): ?>
                                        <option value="<?php echo $coil['id']; ?>"
                                                data-code="<?php echo htmlspecialchars($coil['code']); ?>"
                                                data-name="<?php echo htmlspecialchars($coil['name']); ?>"
                                                data-category="<?php echo $coil['category']; ?>"
                                                data-status="<?php echo $coil['status']; ?>"
                                                data-color_name="<?php echo htmlspecialchars($coil['color_name'] ?? ''); ?>"
                                                data-weight="<?php echo htmlspecialchars($coil['net_weight'] ?? 0); ?>">
                                            <?php echo htmlspecialchars($coil['code']); ?> - 
                                            <?php echo htmlspecialchars($coil['name']); ?>
                                            (<?php echo STOCK_CATEGORIES[$coil['category']] ?? $coil['category']; ?>)
                                        </option>
                                        <?php endforeach; endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stock_entry_id" class="form-label">Select Stock Entry <span class="text-danger">*</span></label>
                                    <select class="form-select" id="stock_entry_id" name="stock_entry_id" required disabled>
                                        <option value="">-- Select Stock Entry --</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Coil Metadata -->
                        <div id="coil_metadata" class="card mb-3 d-none">
                            <div class="card-header">
                                <i class="bi bi-info-circle"></i> Coil Information
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Code:</strong> <span id="coil_code">-</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Name:</strong> <span id="coil_name">-</span></p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>Color:</strong> <span id="coil_color">-</span></p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>Weight:</strong> <span id="coil_weight">-</span> kg</p>
                                    </div>
                                    <div class="col-md-2">
                                        <p class="mb-1"><strong>Available:</strong> <span id="coil_available">0.00</span>m</p>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Category:</strong> <span id="coil_category">-</span></p>
                                    </div>
                                    <div class="col-md-3">
                                        <p class="mb-1"><strong>Status:</strong> <span id="coil_status">-</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Dynamic Property Container (Managed by PropertyRenderer) -->
                        <div id="properties_container" class="d-none">
                            <hr>
                            <h5 class="mb-3">Property Configuration</h5>
                            <div id="property_rows">
                                <!-- Property rows will be dynamically rendered here -->
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add_property_btn">
                                <i class="bi bi-plus-circle"></i> Add Another Property
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Add-Ons Section -->
                <div id="addons_section" class="card mb-3 d-none">
                    <div class="card-header bg-info text-white">
                        <strong><i class="bi bi-plus-square"></i> Add-Ons & Additional Charges</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">Select optional services and charges to include in the invoice</p>
                        <div id="addons_container">
                            <!-- Add-on cards will be dynamically rendered here -->
                        </div>
                    </div>
                </div>
                
                <!-- Production Summary -->
                <div id="production_summary" class="summary-box d-none">
                    <h4><i class="bi bi-calculator"></i> Production Summary</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Production Items:</strong> <span id="production_count" class="computed-value">0</span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Production Total:</strong> <span id="production_total_display" class="computed-value">₦0.00</span></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Add-Ons Total:</strong> <span id="addons_total_display" class="computed-value">₦0.00</span></p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <h5 class="mb-0">
                                <strong>Grand Total:</strong> 
                                <span id="grand_total_display" class="computed-value fs-4">₦0.00</span>
                            </h5>
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
                    <div class="row mb-4">
                        <!-- Left Column: Customer Details -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h6 class="mb-0">Bill To:</h6>
                                </div>
                                <div class="card-body p-3">
                                    <p class="mb-1 fw-bold" id="invoice_customer">-</p>
                                    <p class="mb-1" id="customer_company" style="display: none;"></p>
                                    <p class="mb-1" id="customer_phone" style="display: none;"></p>
                                    <p class="mb-0" id="customer_address" style="display: none;"></p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column: Company Details -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-body p-3">
                                    <div class="text-end mb-3">
                                        <img src="/new-stock-system/assets/logo.png" alt="Obumek Aluminium" style="max-height: 60px;" class="mb-2">
                                        <h5 class="mb-1">Obumek Aluminium</h5>
                                        <p class="mb-1 small">123 Business Street, Industrial Area</p>
                                        <p class="mb-1 small">Lagos, Nigeria</p>
                                        <p class="mb-1 small">Phone: +234 800 000 0000</p>
                                        <p class="mb-0 small">Email: info@obumekaluminium.com</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Invoice Header -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                                <div>
                                    <h5 class="mb-0">INVOICE #<span id="invoice_number">-</span></h5>
                                    <small class="text-muted">Date: <span id="invoice_date"><?php echo date('Y-m-d'); ?></span></small>
                                </div>
                                <div class="text-end">
                                    <div class="badge bg-light text-dark">
                                        <i class="bi bi-building"></i> <span id="invoice_warehouse">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Invoice Items -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Description</th>
                                            <th class="text-end">Unit Price</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="invoice_items_body">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox"></i> No items added yet
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary (Bottom Right) -->
                    <div class="row justify-content-end mt-4">
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header py-2">
                                    <h6 class="mb-0">Order Summary</h6>
                                </div>
                                <div class="card-body p-0">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td class="border-0">Subtotal:</td>
                                            <td class="text-end border-0" id="subtotal_amount">₦0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border-0">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-transparent border-0 p-0 pe-2">Tax</span>
                                                    <input type="number" class="form-control form-control-sm border-1 px-1" id="tax_value" min="0" step="0.01" value="0" style="max-width: 70px;">
                                                    <select class="form-select form-select-sm border-1 px-1" id="tax_type" style="max-width: 70px;">
                                                        <option value="fixed">₦</option>
                                                        <option value="percentage">%</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="text-end align-middle border-0" id="tax_amount">₦0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border-0">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-transparent border-0 p-0 pe-2">Discount</span>
                                                    <input type="number" class="form-control form-control-sm border-1 px-1" id="discount_value" min="0" step="0.01" value="0" style="max-width: 70px;">
                                                    <select class="form-select form-select-sm border-1 px-1" id="discount_type" style="max-width: 70px;">
                                                        <option value="fixed">₦</option>
                                                        <option value="percentage">%</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td class="text-end align-middle border-0" id="discount_amount">-₦0.00</td>
                                        </tr>
                                        <tr>
                                            <td class="border-0">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-transparent border-0 p-0 pe-2">Shipping</span>
                                                    <input type="number" class="form-control form-control-sm border-1 px-1" id="shipping" min="0" step="0.01" value="0" style="max-width: 140px;">
                                                </div>
                                            </td>
                                            <td class="text-end border-0">
                                                <span id="shipping_amount">₦0.00</span>
                                            </td>
                                        </tr>
                                        <tr class="table-active">
                                            <th class="border-0">Total:</th>
                                            <th class="text-end border-0" id="total_amount">₦0.00</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
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

<!-- ============================================================ -->
<!-- MODULAR JAVASCRIPT ARCHITECTURE                               -->
<!-- ============================================================ -->

<!-- Load modular JavaScript files -->
<script src="/new-stock-system/assets/js/production/property-calculator.js"></script>
<script src="/new-stock-system/assets/js/production/addon-calculator.js"></script>
<script src="/new-stock-system/assets/js/production/property-renderer.js"></script>
<script src="/new-stock-system/assets/js/production/addon-renderer.js"></script>
<script src="/new-stock-system/assets/js/production/workflow-manager.js"></script>

<script>
// ============================================================
// COMPLETE WORKFLOW INITIALIZATION SCRIPT
// ============================================================

// Pass PHP constants to JavaScript
const STOCK_CATEGORIES = <?php echo json_encode(STOCK_CATEGORIES); ?>;

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Ensure workflowManager is available
    if (typeof workflowManager === 'undefined') {
        console.error('workflowManager is not defined. Check script loading order.');
        return;
    }

    // ============================================================
    // EVENT LISTENERS - CUSTOMER & WAREHOUSE
    // ============================================================
    const customerSelect = document.getElementById('customer_id');
    if (customerSelect) {
        customerSelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (this.value) {
                if (workflowManager.setCustomer) {
                    workflowManager.setCustomer({
                        id: this.value,
                        name: option.textContent.trim(),
                        phone: option.dataset.phone || '',
                        company: option.dataset.company || '',
                        address: option.dataset.address || ''
                    });
                }
            } else if (workflowManager.setCustomer) {
                workflowManager.setCustomer(null);
            }
        });
    }

    // ============================================================
    // EVENT LISTENERS - COIL & STOCK SELECTION
    // ============================================================
    const coilSelect = document.getElementById('coil_id');
    if (coilSelect) {
        coilSelect.addEventListener('change', async function() {
            const coilId = this.value;
            
            if (!coilId) {
                if (workflowManager.resetCoilAndProperties) {
                    workflowManager.resetCoilAndProperties();
                }
                const metadataEl = document.getElementById('coil_metadata');
                if (metadataEl) metadataEl.classList.add('d-none');
                return;
            }
            
            const option = this.options[this.selectedIndex];
            const coilData = {
                id: coilId,
                code: option.dataset.code,
                name: option.dataset.name,
                category: option.dataset.category,
                color_name: option.dataset.color_name,
                weight: option.dataset.weight,
                status: option.dataset.status
            };
            
            // Update coil metadata display
            const updateElement = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value || '-';
            };

            updateElement('coil_code', coilData.code);
            updateElement('coil_name', coilData.name);
            updateElement('coil_color', coilData.color_name);
            updateElement('coil_weight', coilData.weight || '0.00');
            updateElement('coil_category', STOCK_CATEGORIES[coilData.category] || coilData.category);
            
            const statusBadge = coilData.status === 'available' ? 'success' : 'warning';
            const statusEl = document.getElementById('coil_status');
            if (statusEl) {
                statusEl.innerHTML = 
                    `<span class="badge bg-${statusBadge}">${coilData.status.charAt(0).toUpperCase() + coilData.status.slice(1)}</span>`;
            }
            
            const metadataEl = document.getElementById('coil_metadata');
            if (metadataEl) metadataEl.classList.remove('d-none');
            
            // Let WorkflowManager handle the rest
            try {
                if (workflowManager.handleCoilSelection) {
                    await workflowManager.handleCoilSelection(coilData);
                }
            } catch (error) {
                console.error('Error in handleCoilSelection:', error);
                alert('Error processing coil selection: ' + error.message);
            }
        });
    }

    // ============================================================
    // STOCK ENTRY SELECTION
    // ============================================================
    const stockEntrySelect = document.getElementById('stock_entry_id');
    if (stockEntrySelect) {
        stockEntrySelect.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            
            if (this.value && workflowManager.setStockEntry) {
                workflowManager.setStockEntry({
                    id: this.value,
                    status: option.dataset.status,
                    meters_remaining: parseFloat(option.dataset.meters || 0)
                });
                
                if (workflowManager.showPropertiesForCategory) {
                    workflowManager.showPropertiesForCategory();
                }
            } else if (workflowManager.setStockEntry) {
                workflowManager.setStockEntry(null);
            }
        });
    }

    // ============================================================
    // TAB NAVIGATION
    // ============================================================
    const setupButton = (id, handler) => {
        const btn = document.getElementById(id);
        if (btn) btn.addEventListener('click', handler);
    };

    if (workflowManager) {
        setupButton('proceed_to_invoice_btn', () => {
            if (workflowManager.proceedToInvoice) {
                workflowManager.proceedToInvoice();
            }
        });
        
        setupButton('back_to_production_btn', () => {
            const productionTab = new bootstrap.Tab(document.getElementById('production-tab'));
            productionTab.show();
        });
        
        setupButton('proceed_to_confirm_btn', () => {
            if (workflowManager.proceedToConfirm) {
                workflowManager.proceedToConfirm();
            }
        });
        
        setupButton('back_to_invoice_btn', () => {
            const invoiceTab = new bootstrap.Tab(document.getElementById('invoice-tab'));
            invoiceTab.show();
        });
    }

    // ============================================================
    // INVOICE CALCULATIONS
    // ============================================================
    ['tax_type', 'tax_value', 'discount_type', 'discount_value', 'shipping'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener(id.includes('type') ? 'change' : 'input', function() {
                if (workflowManager.updateInvoiceAdjustments) {
                    workflowManager.updateInvoiceAdjustments({
                        tax_type: document.getElementById('tax_type').value,
                        tax_value: parseFloat(document.getElementById('tax_value').value) || 0,
                        discount_type: document.getElementById('discount_type').value,
                        discount_value: parseFloat(document.getElementById('discount_value').value) || 0,
                        shipping: parseFloat(document.getElementById('shipping').value) || 0
                    });
                }
            });
        }
    });

    // ============================================================
    // FORM SUBMISSION
    // ============================================================
    const confirmImmutable = document.getElementById('confirm_immutable');
    if (confirmImmutable) {
        confirmImmutable.addEventListener('change', function() {
            const submitBtn = document.getElementById('submit_order_btn');
            if (submitBtn) submitBtn.disabled = !this.checked;
        });
    }

    const confirmOrderForm = document.getElementById('confirm_order_form');
    if (confirmOrderForm) {
        confirmOrderForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!confirm('Are you sure you want to create this order? This action cannot be undone.')) {
                return;
            }
            
            const submitBtn = document.getElementById('submit_order_btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
            }
            
            try {
                if (workflowManager.submitOrder) {
                    const result = await workflowManager.submitOrder();
                    
                    if (result && result.success) {
                        alert('Order created successfully!');
                        window.location.href = '/new-stock-system/index.php?page=sales_view&id=' + result.sale_id;
                    } else {
                        throw new Error(result?.message || 'Failed to create order');
                    }
                } else {
                    throw new Error('Submit order functionality not available');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error: ' + error.message);
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="bi bi-check-circle"></i> Confirm & Create Order';
                }
            }
        });
    }

    console.log('Workflow manager initialized successfully');
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>