<?php
/**
 * New Sale - Production Workflow (3 Tabs)
 * File: views/sales/create_workflow.php
 *
 * Tab 1: Production - Define production requirements
 * Tab 2: Invoice - Financial breakdown
 * Tab 3: Confirm Order - Review and submit (creates immutable records)
 *
 * UPDATED: Added KZINC support while preserving Alusteel functionality
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

// Get only Factory Use coils for production workflow
$factoryCoils = $coilModel->getByStatus(STOCK_STATUS_AVAILABLE);

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
    justify-content: between;
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
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="coil_id" class="form-label">Select Coil <span class="text-danger">*</span></label>
                                    <select class="form-select" id="coil_id" name="coil_id" required>
                                        <option value="">-- Select Coil --</option>
                                        <?php
                                        $coilModel = new Coil();
                                        $coils = $coilModel->getForDropdown();

                                        if (empty($coils)): ?>
                                            <option value="">No coils found</option>
                                        <?php else:foreach ($coils as $coil): ?>
                                        <option value="<?php echo $coil['id']; ?>"
                                                data-code="<?php echo htmlspecialchars(
                                                    $coil['code'],
                                                ); ?>"
                                                data-name="<?php echo htmlspecialchars(
                                                    $coil['name'],
                                                ); ?>"
                                                data-category="<?php echo $coil['category']; ?>"
                                                data-status="<?php echo $coil['status']; ?>"
                                                data-color_name="<?php echo htmlspecialchars(
                                                    $coil['color_name'] ?? '',
                                                ); ?>"
                                                data-weight="<?php echo htmlspecialchars(
                                                    $coil['net_weight'] ?? 0,
                                                ); ?>">
                                            <?php echo htmlspecialchars($coil['code']); ?> - 
                                            <?php echo htmlspecialchars($coil['name']); ?>
                                            (<?php echo STOCK_CATEGORIES[$coil['category']] ??
                                                $coil['category']; ?>)
                                        </option>
                                        <?php endforeach;endif;
                                        ?>
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
                        
                        <!-- ✅ UPDATED: Property Selection for Alusteel (renamed ID) -->
                        <div id="property_selection_alusteel" class="d-none">
                            <hr>
                            <h5 class="mb-3">Property Configuration</h5>
                            <p class="text-muted">Define the properties for this stock</p>
                            
                            <div id="properties_container">
                                <!-- Alusteel property rows will be dynamically added here -->
                            </div>
                            
                            <button type="button" class="btn btn-success btn-sm" id="add_property_btn">
                                <i class="bi bi-plus-circle"></i> Add Another Property
                            </button>
                        </div>

                        <!-- ✅ NEW: Property Selection for KZINC -->
                        <div id="property_selection_kzinc" class="d-none">
                            <hr>
                            <h5 class="mb-3">KZINC Property Configuration</h5>
                            <div class="alert alert-info mb-3">
                                <i class="bi bi-info-circle"></i>
                                <strong>Fixed Pricing:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Scraps: <strong>₦2,500</strong> per scrap</li>
                                    <li>Pieces: <strong>₦4,500</strong> per piece</li>
                                    <li>Bundles: <strong>₦64,000</strong> per bundle (contains 15 pieces)</li>
                                </ul>
                            </div>
                            
                            <div id="kzinc_properties_container">
                                <!-- KZINC property rows will be dynamically added here -->
                            </div>
                            
                            <button type="button" class="btn btn-success btn-sm" id="add_kzinc_property_btn">
                                <i class="bi bi-plus-circle"></i> Add Another Item
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
                    <div class="row mb-4">
                        <!-- Left Column: Customer Details -->
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header ">
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
                                    <small class="text-muted">Date: <span id="invoice_date"><?php echo date(
                                        'Y-m-d',
                                    ); ?></span></small>
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
                                                    <span class="input-group-text bg-transparent border-1 p-0 pe-2">Tax</span>
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
                                                    <span class="input-group-text bg-transparent border-1 p-0 pe-2">Discount</span>
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
                                                    <span class="input-group-text bg-transparent border-1 p-0 pe-2">Shipping</span>
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

<script>
// Define STOCK_CATEGORIES in JavaScript
const STOCK_CATEGORIES = <?php echo json_encode(STOCK_CATEGORIES); ?>;

// ✅ NEW: KZINC Constants
const KZINC_PRICES = {
    scraps: 2500,
    pieces: 4500,
    bundles: 64000
};
const PIECES_PER_BUNDLE = 15;

// ============================================================
// GLOBAL STATE MANAGEMENT
// ============================================================
const workflowState = {
    tax: {
        type: 'fixed',
        value: 0,
        amount: 0
    },
    discount: {
        type: 'fixed',
        value: 0,
        amount: 0
    },
    customer: null,
    warehouse: null,
    coil: null,
    stockEntry: null,
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

let propertyCounter = 0;

// ============================================================
// HELPER FUNCTIONS
// ============================================================

function formatCurrency(amount) {
    return '₦' + parseFloat(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function hideAllPropertyPanes() {
    const alusteelPane = document.getElementById('property_selection_alusteel');
    const kzincPane = document.getElementById('property_selection_kzinc');
    
    if (alusteelPane) alusteelPane.classList.add('d-none');
    if (kzincPane) kzincPane.classList.add('d-none');
    
    workflowState.properties = [];
}

function showAlusteelProperties() {
    const propertyPane = document.getElementById('property_selection_alusteel');
    if (!propertyPane) return;
    
    propertyPane.classList.remove('d-none');
    
    if (workflowState.properties.length === 0) {
        addPropertyRow();
    }
}

function showKzincProperties() {
    const propertyPane = document.getElementById('property_selection_kzinc');
    if (!propertyPane) {
        console.error('KZINC property pane not found');
        return;
    }
    
    propertyPane.classList.remove('d-none');
    
    if (workflowState.properties.length === 0) {
        addKzincPropertyRow();
    }
}

function updateSelectionSummary() {
    const selectionTextEl = document.getElementById('selection_text');
    const selectionSummaryEl = document.getElementById('selection_summary');
    
    if (!selectionTextEl || !selectionSummaryEl) return;
    
    if (workflowState.customer && workflowState.warehouse) {
        const text = `Customer: ${workflowState.customer.name} | Warehouse: ${workflowState.warehouse.name}`;
        selectionTextEl.textContent = text;
        selectionSummaryEl.classList.remove('d-none');
    } else {
        selectionSummaryEl.classList.add('d-none');
    }
}

// ============================================================
// TAB 1: CUSTOMER & WAREHOUSE SELECTION
// ============================================================

document.getElementById('customer_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (this.value) {
        workflowState.customer = {
            id: this.value,
            name: option.textContent.trim(),
            phone: option.dataset.phone || '',
            company: option.dataset.company || '',
            address: option.dataset.address || ''
        };
    } else {
        workflowState.customer = null;
    }
    
    updateSelectionSummary();
    validateProductionTab();
});

document.getElementById('warehouse_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (this.value) {
        workflowState.warehouse = {
            id: this.value,
            name: option.textContent.trim(),
            code: option.dataset.code || ''
        };
    } else {
        workflowState.warehouse = null;
    }
    
    updateSelectionSummary();
    validateProductionTab();
});

// ============================================================
// TAB 1: COIL SELECTION (FIXED FOR ALUMINUM & ALUSTEEL)
// ============================================================

document.getElementById('coil_id').addEventListener('change', function() {
    const coilId = this.value;
    const stockEntrySelect = document.getElementById('stock_entry_id');
    const coilMetadata = document.getElementById('coil_metadata');
    
    // Reset state
    stockEntrySelect.innerHTML = '<option value="">-- Select Stock Entry --</option>';
    stockEntrySelect.disabled = !coilId;
    workflowState.coil = null;
    workflowState.stockEntry = null;
    
    hideAllPropertyPanes();
    
    if (coilId) {
        const option = this.options[this.selectedIndex];
        const category = String(option.dataset.category).toLowerCase();
        
        // Update metadata display
        document.getElementById('coil_code').textContent = option.dataset.code || '-';
        document.getElementById('coil_name').textContent = option.dataset.name || '-';
        document.getElementById('coil_color').textContent = option.dataset.color_name || '-';
        document.getElementById('coil_weight').textContent = option.dataset.weight || '0.00';
        document.getElementById('coil_category').textContent = STOCK_CATEGORIES[option.dataset.category] || option.dataset.category || '-';
        
        const status = option.dataset.status || 'unknown';
        const statusBadge = status === 'available' ? 'success' : 'warning';
        document.getElementById('coil_status').innerHTML = 
            `<span class="badge bg-${statusBadge}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
        
        coilMetadata.classList.remove('d-none');
        
        // ✅ FIXED: Check if KZINC (aluminum and alusteel use stock-based workflow)
        if (category === 'kzinc') {
            console.log('✓ KZINC coil detected - bypassing stock entry');
            
            // Hide stock entry dropdown
            const stockEntryCol = stockEntrySelect.closest('.col-md-6');
            if (stockEntryCol) stockEntryCol.style.display = 'none';
            
            document.getElementById('coil_available').textContent = 'N/A';
            
            // Set coil state
            workflowState.coil = {
                id: coilId,
                code: option.dataset.code,
                name: option.dataset.name,
                category: option.dataset.category,
                color_name: option.dataset.color_name,
                weight: option.dataset.weight,
                status: option.dataset.status
            };
            
            // Bypass stock entry
            workflowState.stockEntry = {
                id: 'kzinc_bypass',
                status: 'available',
                meters_remaining: 0
            };
            
            showKzincProperties();
            validateProductionTab();
            
        } else {
            // ✅ STOCK-BASED WORKFLOW: Show stock entry dropdown for ALUSTEEL & ALUMINUM
            console.log(`✓ ${category.toUpperCase()} coil detected - using stock-based workflow`);
            
            const stockEntryCol = stockEntrySelect.closest('.col-md-6');
            if (stockEntryCol) stockEntryCol.style.display = 'block';
            
            stockEntrySelect.disabled = true;
            stockEntrySelect.innerHTML = '<option value="">Loading stock entries...</option>';
            
            // ✅ Set coil state immediately (before fetching stock entries)
            workflowState.coil = {
                id: coilId,
                code: option.dataset.code,
                name: option.dataset.name,
                category: option.dataset.category,
                color_name: option.dataset.color_name,
                weight: option.dataset.weight,
                status: option.dataset.status
            };
            
            // Fetch stock entries
            fetch(`/new-stock-system/controllers/sales/get_stock_entries.php?coil_id=${coilId}`)
                .then(response => response.json())
                .then(data => {
                    stockEntrySelect.innerHTML = '<option value="">-- Select Stock Entry --</option>';
                    
                    if (data.success && data.entries && data.entries.length > 0) {
                        let totalAvailable = 0;
                        
                        const availableEntries = data.entries.filter(entry => 
                            parseFloat(entry.meters_remaining) > 0
                        );

                        if (availableEntries.length > 0) {
                            availableEntries.forEach(entry => {
                                const opt = document.createElement('option');
                                opt.value = entry.id;
                                opt.textContent = `#${entry.id} - ${parseFloat(entry.meters_remaining).toFixed(2)}m`;
                                opt.dataset.status = entry.status;
                                opt.dataset.meters = entry.meters_remaining;
                                stockEntrySelect.appendChild(opt);
                                
                                totalAvailable += parseFloat(entry.meters_remaining);
                            });
                        } else {
                            stockEntrySelect.innerHTML = '<option value="">No stock entries with available meters</option>';
                        }
                        
                        document.getElementById('coil_available').textContent = totalAvailable.toFixed(2);
                    } else {
                        stockEntrySelect.innerHTML = '<option value="">No available stock entries</option>';
                        document.getElementById('coil_available').textContent = '0.00';
                    }
                    
                    stockEntrySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error fetching stock entries:', error);
                    stockEntrySelect.innerHTML = '<option value="">Error loading entries</option>';
                    stockEntrySelect.disabled = false;
                });
        }
    } else {
        coilMetadata.classList.add('d-none');
        const stockEntryCol = stockEntrySelect.closest('.col-md-6');
        if (stockEntryCol) stockEntryCol.style.display = 'block';
    }
    
    validateProductionTab();
});

// ============================================================
// TAB 1: STOCK ENTRY SELECTION (FOR ALUSTEEL & ALUMINUM)
// ============================================================

document.getElementById('stock_entry_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    
    if (this.value) {
        workflowState.stockEntry = {
            id: this.value,
            status: option.dataset.status,
            meters_remaining: parseFloat(option.dataset.meters || 0)
        };
        
        // workflowState.coil is already set from coil selection
        const category = String(workflowState.coil.category).toLowerCase();
        
        hideAllPropertyPanes();
        
        // ✅ FIXED: Check for both 'alusteel' and 'aluminum' (without 'i')
        if (category === 'alusteel' || category === 'aluminum') {
            console.log(`✓ Showing property panel for ${category.toUpperCase()}`);
            showAlusteelProperties();
        }
    } else {
        workflowState.stockEntry = null;
        hideAllPropertyPanes();
    }
    
    validateProductionTab();
});

// ============================================================
// ALUSTEEL PROPERTY FUNCTIONS
// ============================================================

document.getElementById('add_property_btn').addEventListener('click', addPropertyRow);

function addPropertyRow() {
    const rowId = propertyCounter++;
    const container = document.getElementById('properties_container');
    
    const rowHtml = `
        <div class="property-row" id="property_row_${rowId}">
            <div class="property-row-header d-flex justify-content-between align-items-center mb-2">
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
    
    document.getElementById(`meters_${rowId}`).value = meters.toFixed(2) + 'm';
    document.getElementById(`subtotal_${rowId}`).textContent = formatCurrency(subtotal);
    
    updatePropertyState(rowId, {
        propertyType: row.querySelector('.property-type').value,
        sheetQty: sheetQty,
        sheetMeter: sheetMeter,
        meters: meters,
        unitPrice: unitPrice,
        subtotal: subtotal
    });
    
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
        document.getElementById(`property_row_${rowId}`)?.remove();
        workflowState.properties = workflowState.properties.filter(p => p.rowId !== rowId);
        calculateProductionTotals();
        validateProductionTab();
    }
}

// ============================================================
// KZINC PROPERTY FUNCTIONS
// ============================================================

document.addEventListener('DOMContentLoaded', function() {
    const addKzincBtn = document.getElementById('add_kzinc_property_btn');
    if (addKzincBtn) {
        addKzincBtn.addEventListener('click', addKzincPropertyRow);
    }
});

function addKzincPropertyRow() {
    const rowId = propertyCounter++;
    const container = document.getElementById('kzinc_properties_container');
    
    if (!container) {
        console.error('KZINC properties container not found');
        return;
    }
    
    const rowHtml = `
        <div class="property-row" id="property_row_${rowId}">
            <div class="property-row-header d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0">Item ${rowId + 1}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removePropertyRow(${rowId})">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-2">
                    <label class="form-label">Property Type <span class="text-danger">*</span></label>
                    <select class="form-select kzinc-property-type" data-row-id="${rowId}" required>
                        <option value="">Select type</option>
                        <option value="scraps">Scraps (₦2,500)</option>
                        <option value="pieces">Pieces (₦4,500)</option>
                        <option value="bundles">Bundles (₦64,000) - 15 pieces</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-2">
                    <label class="form-label">Quantity <span class="text-danger">*</span></label>
                    <input type="number" class="form-control kzinc-quantity" data-row-id="${rowId}" 
                           min="1" step="1" placeholder="e.g., 10" required>
                </div>
                
                <div class="col-md-2 mb-2" id="pieces_display_${rowId}" style="display: none;">
                    <label class="form-label">Total Pieces</label>
                    <input type="text" class="form-control bg-light" id="pieces_value_${rowId}" readonly>
                    <small class="text-muted">Auto-calculated</small>
                </div>
                
                <div class="col-md-2 mb-2">
                    <label class="form-label">Unit Price (₦) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control kzinc-unit-price" id="unit_price_${rowId}" 
                           data-row-id="${rowId}" min="0" step="0.01" required>
                    <small class="text-muted">Editable</small>
                </div>
                
                <div class="col-md-3 mb-2">
                    <label class="form-label">Subtotal (₦)</label>
                    <input type="text" class="form-control bg-light" id="kzinc_subtotal_${rowId}" readonly>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', rowHtml);
    attachKzincPropertyRowListeners(rowId);
}

function attachKzincPropertyRowListeners(rowId) {
    const row = document.getElementById(`property_row_${rowId}`);
    
    const propertyTypeSelect = row.querySelector('.kzinc-property-type');
    propertyTypeSelect.addEventListener('change', function() {
        const propertyType = this.value;
        const unitPrice = KZINC_PRICES[propertyType] || 0;
        
        const unitPriceInput = document.getElementById(`unit_price_${rowId}`);
        unitPriceInput.value = unitPrice;
        
        const piecesDisplay = document.getElementById(`pieces_display_${rowId}`);
        if (propertyType === 'bundles') {
            piecesDisplay.style.display = 'block';
        } else {
            piecesDisplay.style.display = 'none';
        }
        
        calculateKzincPropertyRow(rowId);
    });
    
    const quantityInput = row.querySelector('.kzinc-quantity');
    quantityInput.addEventListener('input', function() {
        calculateKzincPropertyRow(rowId);
    });
    
    const unitPriceInput = row.querySelector('.kzinc-unit-price');
    unitPriceInput.addEventListener('input', function() {
        calculateKzincPropertyRow(rowId);
    });
}

function calculateKzincPropertyRow(rowId) {
    const row = document.getElementById(`property_row_${rowId}`);
    const propertyType = row.querySelector('.kzinc-property-type').value;
    const quantity = parseFloat(row.querySelector('.kzinc-quantity').value) || 0;
    const unitPrice = parseFloat(row.querySelector('.kzinc-unit-price').value) || 0;
    
    if (!propertyType || quantity <= 0 || unitPrice <= 0) {
        return;
    }
    
    const subtotal = quantity * unitPrice;
    
    document.getElementById(`kzinc_subtotal_${rowId}`).value = formatCurrency(subtotal);
    
    let pieces = 0;
    if (propertyType === 'bundles') {
        pieces = quantity * PIECES_PER_BUNDLE;
        document.getElementById(`pieces_value_${rowId}`).value = pieces + ' pieces';
    } else if (propertyType === 'pieces') {
        pieces = quantity;
    }
    
    updateKzincPropertyState(rowId, {
        propertyType: propertyType,
        quantity: quantity,
        unitPrice: unitPrice,
        subtotal: subtotal,
        pieces: pieces,
        meters: 0,
        sheetQty: quantity,
        sheetMeter: 0
    });
    
    calculateProductionTotals();
}

function updateKzincPropertyState(rowId, data) {
    const existingIndex = workflowState.properties.findIndex(p => p.rowId === rowId);
    
    if (existingIndex >= 0) {
        workflowState.properties[existingIndex] = { rowId, ...data };
    } else {
        workflowState.properties.push({ rowId, ...data });
    }
}

// ============================================================
// PRODUCTION TOTALS & VALIDATION
// ============================================================

function calculateProductionTotals() {
    let totalMeters = 0;
    let totalAmount = 0;
    
    workflowState.properties.forEach(prop => {
        totalMeters += parseFloat(prop.meters) || 0;
        totalAmount += parseFloat(prop.subtotal) || 0;
    });
    
    workflowState.productionSummary = { totalMeters, totalAmount };
    
    const metersDisplay = document.getElementById('total_meters_display');
    const amountDisplay = document.getElementById('total_amount_display');
    
    if (metersDisplay) {
        metersDisplay.textContent = totalMeters > 0 ? totalMeters.toFixed(2) + 'm' : 'N/A';
    }
    
    if (amountDisplay) {
        amountDisplay.textContent = formatCurrency(totalAmount);
    }
    
    const summaryBox = document.getElementById('production_summary');
    if (summaryBox) {
        if (workflowState.properties.length > 0) {
            summaryBox.classList.remove('d-none');
        } else {
            summaryBox.classList.add('d-none');
        }
    }
    
    validateProductionTab();
}

function validateProductionTab() {
    const hasCustomer = !!workflowState.customer;
    const hasWarehouse = !!workflowState.warehouse;
    const hasCoil = !!workflowState.coil;
    const hasStockEntry = !!workflowState.stockEntry;
    const hasProperties = workflowState.properties.length > 0;
    const hasValidAmount = workflowState.productionSummary?.totalAmount > 0;
    
    const isValid = hasCustomer && hasWarehouse && hasCoil && hasStockEntry && hasProperties && hasValidAmount;
    
    const btn = document.getElementById('proceed_to_invoice_btn');
    if (btn) {
        btn.disabled = !isValid;
    }
    
    return isValid;
}

// ============================================================
// PROCEED TO INVOICE
// ============================================================

document.getElementById('proceed_to_invoice_btn').addEventListener('click', function(e) {
    document.getElementById('production-tab').classList.add('completed');
    
    const invoiceTabElement = document.getElementById('invoice-tab');
    invoiceTabElement.disabled = false;
    invoiceTabElement.removeAttribute('disabled');
    
    populateInvoiceTab();
    
    const invoiceTab = new bootstrap.Tab(invoiceTabElement);
    invoiceTab.show();
});

// ============================================================
// TAB 2: INVOICE
// ============================================================

function populateInvoiceTab() {
    // Populate customer details
    if (workflowState.customer) {
        const customerNameEl = document.getElementById('invoice_customer');
        if (customerNameEl) {
            customerNameEl.textContent = workflowState.customer.name || 'N/A';
        }
        
        const companyEl = document.getElementById('customer_company');
        if (companyEl && workflowState.customer.company) {
            companyEl.textContent = workflowState.customer.company;
            companyEl.style.display = 'block';
        }
        
        const phoneEl = document.getElementById('customer_phone');
        if (phoneEl && workflowState.customer.phone) {
            phoneEl.textContent = `Phone: ${workflowState.customer.phone}`;
            phoneEl.style.display = 'block';
        }
        
        const addressEl = document.getElementById('customer_address');
        if (addressEl && workflowState.customer.address) {
            addressEl.textContent = workflowState.customer.address;
            addressEl.style.display = 'block';
        }
    }
    
    // Populate warehouse
    if (workflowState.warehouse) {
        const warehouseEl = document.getElementById('invoice_warehouse');
        if (warehouseEl) {
            warehouseEl.textContent = workflowState.warehouse.name || 'N/A';
        }
    }
    
    // Set invoice number
    const invoiceNumberEl = document.getElementById('invoice_number');
    if (invoiceNumberEl && invoiceNumberEl.textContent === '-') {
        const date = new Date();
        const invoiceNumber = `INV-${date.getFullYear()}${String(date.getMonth() + 1).padStart(2, '0')}${String(date.getDate()).padStart(2, '0')}-${Math.floor(1000 + Math.random() * 9000)}`;
        invoiceNumberEl.textContent = invoiceNumber;
    }
    
    // Populate invoice items
    const tbody = document.getElementById('invoice_items_body');
    if (tbody) {
        tbody.innerHTML = '';
        
        if (workflowState.properties && workflowState.properties.length > 0) {
            let itemNumber = 1;
            workflowState.properties.forEach((prop) => {
                const lineTotal = parseFloat(prop.subtotal) || 0;
                
                let qtyDisplay = '';
                if (prop.meters > 0) {
                    qtyDisplay = `${prop.sheetQty || 0} sheets × ${prop.sheetMeter || 0}m`;
                } else {
                    qtyDisplay = `${prop.quantity || prop.sheetQty || 0} ${prop.propertyType}`;
                    if (prop.pieces > 0) {
                        qtyDisplay += ` (${prop.pieces} pcs)`;
                    }
                }
                
                const row = `
                    <tr>
                        <td>${itemNumber++}</td>
                        <td>
                            <strong>${workflowState.coil.name || 'N/A'}</strong><br>
                            <small class="text-muted">${prop.propertyType || 'N/A'} - ${qtyDisplay}</small>
                        </td>
                        <td class="text-end">${formatCurrency(prop.unitPrice)}</td>
                        <td class="text-center">${prop.meters > 0 ? (prop.meters.toFixed(2) + 'm') : (prop.quantity || prop.sheetQty || 0)}</td>
                        <td class="text-end">${formatCurrency(lineTotal)}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }
    }
    
    calculateInvoiceGrandTotal();
}

// Initialize invoice listeners
if (!workflowState.tax) workflowState.tax = { type: 'fixed', value: 0, amount: 0 };
if (!workflowState.discount) workflowState.discount = { type: 'fixed', value: 0, amount: 0 };
if (workflowState.shipping === undefined) workflowState.shipping = 0;

function initializeInvoiceListeners() {
    ['tax_type', 'tax_value', 'discount_type', 'discount_value', 'shipping'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        
        const clone = el.cloneNode(true);
        el.parentNode.replaceChild(clone, el);
        
        clone.addEventListener(id.includes('type') ? 'change' : 'input', function() {
            if (id === 'tax_type') workflowState.tax.type = this.value;
            else if (id === 'tax_value') workflowState.tax.value = parseFloat(this.value) || 0;
            else if (id === 'discount_type') workflowState.discount.type = this.value;
            else if (id === 'discount_value') workflowState.discount.value = parseFloat(this.value) || 0;
            else if (id === 'shipping') workflowState.shipping = parseFloat(this.value) || 0;
            
            calculateInvoiceGrandTotal();
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeInvoiceListeners);
} else {
    initializeInvoiceListeners();
}

function calculateInvoiceGrandTotal() {
    const subtotal = workflowState.productionSummary.totalAmount;
    
    let taxAmount = 0;
    if (workflowState.tax.type === 'percentage') {
        taxAmount = (subtotal * workflowState.tax.value) / 100;
    } else {
        taxAmount = workflowState.tax.value;
    }
    
    let discountAmount = 0;
    if (workflowState.discount.type === 'percentage') {
        discountAmount = (subtotal * workflowState.discount.value) / 100;
    } else {
        discountAmount = workflowState.discount.value;
    }
    
    const maxDiscount = subtotal + taxAmount;
    if (discountAmount > maxDiscount) {
        discountAmount = maxDiscount;
    }
    
    const shipping = workflowState.shipping || 0;
    const grandTotal = subtotal + taxAmount - discountAmount + shipping;
    
    document.getElementById('subtotal_amount').textContent = formatCurrency(subtotal);
    document.getElementById('tax_amount').textContent = formatCurrency(taxAmount);
    document.getElementById('discount_amount').textContent = '-' + formatCurrency(discountAmount);
    document.getElementById('total_amount').textContent = formatCurrency(grandTotal);
    
    workflowState.invoiceData = {
        tax: taxAmount,
        tax_type: workflowState.tax.type,
        tax_value: workflowState.tax.value,
        discount: discountAmount,
        discount_type: workflowState.discount.type,
        discount_value: workflowState.discount.value,
        shipping: shipping,
        grandTotal: grandTotal,
        notes: document.getElementById('invoice_notes')?.value || ''
    };
}

document.getElementById('back_to_production_btn').addEventListener('click', function() {
    const productionTab = new bootstrap.Tab(document.getElementById('production-tab'));
    productionTab.show();
});

document.getElementById('proceed_to_confirm_btn').addEventListener('click', function() {
    document.getElementById('invoice-tab').classList.add('completed');
    document.getElementById('confirm-tab').disabled = false;
    populateConfirmTab();
    const confirmTab = new bootstrap.Tab(document.getElementById('confirm-tab'));
    confirmTab.show();
});

// ============================================================
// TAB 3: CONFIRM ORDER
// ============================================================

function populateConfirmTab() {
    const productionPaperHtml = `
        <h6 class="text-primary">Customer & Warehouse</h6>
        <p class="mb-1"><strong>Customer:</strong> ${workflowState.customer.name} (${workflowState.customer.phone})</p>
        <p class="mb-2"><strong>Warehouse:</strong> ${workflowState.warehouse.name}</p>
        
        <h6 class="text-primary mt-3">Coil Information</h6>
        <p class="mb-1"><strong>Code:</strong> ${workflowState.coil.code}</p>
        <p class="mb-1"><strong>Name:</strong> ${workflowState.coil.name}</p>
        <p class="mb-2"><strong>Category:</strong> ${workflowState.coil.category}</p>
        
        <h6 class="text-primary mt-3">Properties</h6>
        ${workflowState.properties.map((prop, index) => {
            let displayText = '';
            if (prop.meters > 0) {
                displayText = `${prop.sheetQty} sheets × ${prop.sheetMeter}m = ${prop.meters.toFixed(2)}m`;
            } else {
                displayText = `${prop.quantity} ${prop.propertyType}`;
                if (prop.pieces > 0) {
                    displayText += ` (${prop.pieces} pieces)`;
                }
            }
            return `
            <div class="mb-2 p-2 bg-light border rounded">
                <strong>${index + 1}. ${prop.propertyType}</strong><br>
                <small>
                    ${displayText}<br>
                    Unit Price: ${formatCurrency(prop.unitPrice)} | Subtotal: ${formatCurrency(prop.subtotal)}
                </small>
            </div>
        `}).join('')}
        
        <h6 class="text-primary mt-3">Summary</h6>
        ${workflowState.productionSummary.totalMeters > 0 ? `<p class="mb-1"><strong>Total Meters:</strong> ${workflowState.productionSummary.totalMeters.toFixed(2)}m</p>` : ''}
        <p class="mb-0"><strong>Total Amount:</strong> ${formatCurrency(workflowState.productionSummary.totalAmount)}</p>
    `;
    document.getElementById('production_paper_preview').innerHTML = productionPaperHtml;
    
    const invoicePreviewHtml = `
        <h6 class="text-primary">Invoice Summary</h6>
        <table class="table table-sm">
            <tr>
                <td>Subtotal:</td>
                <td class="text-end">${formatCurrency(workflowState.productionSummary.totalAmount)}</td>
            </tr>
            <tr>
                <td>Tax (${workflowState.invoiceData.tax_type === 'percentage' ? workflowState.invoiceData.tax_value + '%' : formatCurrency(workflowState.invoiceData.tax_value)}):</td>
                <td class="text-end">${formatCurrency(workflowState.invoiceData.tax)}</td>
            </tr>
            <tr>
                <td>Discount (${workflowState.invoiceData.discount_type === 'percentage' ? workflowState.invoiceData.discount_value + '%' : formatCurrency(workflowState.invoiceData.discount_value)}):</td>
                <td class="text-end">-${formatCurrency(workflowState.invoiceData.discount)}</td>
            </tr>
            <tr>
                <td>Shipping:</td>
                <td class="text-end">${formatCurrency(workflowState.invoiceData.shipping || 0)}</td>
            </tr>
            <tr class="table-primary">
                <td><strong>Grand Total:</strong></td>
                <td class="text-end"><strong>${formatCurrency(workflowState.invoiceData.grandTotal)}</strong></td>
            </tr>
        </table>
        ${workflowState.invoiceData.notes ? `<h6 class="text-primary mt-3">Notes</h6><p class="small">${workflowState.invoiceData.notes}</p>` : ''}
    `;
    document.getElementById('invoice_shape_preview').innerHTML = invoicePreviewHtml;
    
    prepareFormData();
}

function prepareFormData() {
    const productionData = {
        customer_id: workflowState.customer.id,
        warehouse_id: workflowState.warehouse.id,
        coil_id: workflowState.coil.id,
        stock_entry_id: workflowState.stockEntry.id,
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
            quantity: prop.meters > 0 ? prop.meters : prop.quantity,
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

document.getElementById('back_to_invoice_btn').addEventListener('click', function() {
    const invoiceTab = new bootstrap.Tab(document.getElementById('invoice-tab'));
    invoiceTab.show();
});

document.getElementById('confirm_immutable').addEventListener('change', function() {
    document.getElementById('submit_order_btn').disabled = !this.checked;
});

document.getElementById('confirm_order_form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!confirm('Are you sure you want to create this order? This action cannot be undone.')) {
        return;
    }
    
    const submitBtn = document.getElementById('submit_order_btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    
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
