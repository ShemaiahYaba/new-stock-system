<?php
/**
 * Production View Page
 * File: views/production/view.php
 *
 * Displays complete production paper details (immutable)
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/production.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'View Production - ' . APP_NAME;

$productionId = (int) ($_GET['id'] ?? 0);

if ($productionId <= 0) {
    setFlashMessage('error', 'Invalid production ID.');
    header('Location: /new-stock-system/index.php?page=production');
    exit();
}

$productionModel = new Production();
$production = $productionModel->findById($productionId);

if (!$production) {
    setFlashMessage('error', 'Production record not found.');
    header('Location: /new-stock-system/index.php?page=production');
    exit();
}

// Handle production paper data
$prodPaper = [];
if (isset($production['production_paper'])) {
    if (is_array($production['production_paper'])) {
        $prodPaper = $production['production_paper'];
    } elseif (is_string($production['production_paper'])) {
        $prodPaper = json_decode($production['production_paper'], true) ?: [];
    }
}

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<style>
.production-paper {
    background: white;
    padding: 30px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.section-header {
    background: #f8f9fa;
    padding: 10px 15px;
    border-left: 4px solid #007bff;
    margin: 20px 0 15px 0;
    font-weight: 600;
}

.immutable-banner {
    background: #fff3cd;
    border: 2px solid #ffc107;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.property-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
}

.hash-display {
    font-family: 'Courier New', monospace;
    font-size: 0.75rem;
    background: #e9ecef;
    padding: 8px;
    border-radius: 4px;
    word-break: break-all;
}
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-file-earmark-text"></i> Production Paper
                </h1>
                <p class="text-muted">
                    Production #<?php echo str_pad($production['id'], 4, '0', STR_PAD_LEFT); ?> - 
                    <?php echo htmlspecialchars($prodPaper['production_reference'] ?? 'N/A'); ?>
                </p>
            </div>
            <div>
                <button onclick="window.print()" class="btn btn-secondary">
                    <i class="bi bi-printer"></i> Print
                </button>
                <a href="/new-stock-system/index.php?page=production" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>
    
    <!-- Immutability Warning -->
    <div class="immutable-banner">
        <div class="d-flex align-items-center">
            <i class="bi bi-lock fs-3 me-3 text-warning"></i>
            <div>
                <h6 class="mb-1"><strong>Immutable Record</strong></h6>
                <p class="mb-0 small">This production record is permanently locked and cannot be modified. Changes require Super Admin approval and will be logged in the audit trail.</p>
            </div>
        </div>
    </div>
    
    <!-- Production Paper -->
    <div class="production-paper">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h4 class="text-primary">Production Information</h4>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Production ID:</th>
                        <td><strong>PR-<?php echo str_pad(
                            $production['id'],
                            4,
                            '0',
                            STR_PAD_LEFT,
                        ); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Sale Reference:</th>
                        <td>
                            <a href="/new-stock-system/index.php?page=sales_view&id=<?php echo $production[
                                'sale_id'
                            ]; ?>">
                                #SO-<?php echo str_pad(
                                    $production['sale_id'],
                                    6,
                                    '0',
                                    STR_PAD_LEFT,
                                ); ?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Status:</th>
                        <td>
                            <?php
                            $statusColors = [
                                PRODUCTION_STATUS_PENDING => 'warning',
                                PRODUCTION_STATUS_IN_PROGRESS => 'info',
                                PRODUCTION_STATUS_COMPLETED => 'success',
                                PRODUCTION_STATUS_CANCELLED => 'danger',
                            ];
                            $color = $statusColors[$production['status']] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?php echo $color; ?>">
                                <?php echo PRODUCTION_STATUSES[$production['status']] ??
                                    $production['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Created At:</th>
                        <td><?php echo date(
                            'F d, Y H:i',
                            strtotime($production['created_at']),
                        ); ?></td>
                    </tr>
                    <tr>
                        <th>Created By:</th>
                        <td><?php echo htmlspecialchars($production['created_by_name']); ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h4 class="text-primary">Warehouse & Customer</h4>
                <table class="table table-sm">
                    <tr>
                        <th width="40%">Warehouse:</th>
                        <td><strong><?php echo htmlspecialchars(
                            $prodPaper['warehouse']['name'] ?? 'N/A',
                        ); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Location:</th>
                        <td><?php echo htmlspecialchars(
                            $prodPaper['warehouse']['location'] ?? 'N/A',
                        ); ?></td>
                    </tr>
                    <tr>
                        <th>Customer:</th>
                        <td><strong><?php echo htmlspecialchars(
                            $prodPaper['customer']['name'] ?? 'N/A',
                        ); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><?php echo htmlspecialchars(
                            $prodPaper['customer']['phone'] ?? 'N/A',
                        ); ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><?php echo htmlspecialchars(
                            $prodPaper['customer']['address'] ?? 'N/A',
                        ); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Coil Information -->
        <div class="section-header">
            <i class="bi bi-box-seam"></i> Coil Information
        </div>
        <table class="table table-bordered">
            <tr>
                <th width="20%">Coil Code:</th>
                <td><?php echo htmlspecialchars($prodPaper['coil']['code'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Coil Name:</th>
                <td><?php echo htmlspecialchars($prodPaper['coil']['name'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Category:</th>
                <td><?php echo htmlspecialchars($prodPaper['coil']['category'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>Color:</th>
                <td>
                    <?php 
                    // Check if color exists in coil data, otherwise show N/A
                    if (isset($prodPaper['coil']['color'])) {
                        $color = $prodPaper['coil']['color'];
                        echo COIL_COLORS[$color] ?? htmlspecialchars($color);
                    } else {
                        echo 'N/A';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Weight:</th>
                <td><?php echo number_format($prodPaper['coil']['weight'] ?? 0, 2); ?> kg</td>
            </tr>
        </table>
        
        <!-- Properties Section -->
        <div class="section-header">
            <i class="bi bi-list-check"></i> Production Properties
        </div>
        
        <?php if (!empty($prodPaper['properties'])): ?>
            <?php foreach ($prodPaper['properties'] as $index => $prop): ?>
            <div class="property-card">
                <div class="d-flex justify-content-between align-items-start">
                    <h6 class="text-primary mb-2">
                        Property <?php echo $index + 1; ?>: 
                        <strong><?php echo ucfirst(
                            $prop['property_id'] ?? $prop['label'],
                        ); ?></strong>
                    </h6>
                    <span class="badge bg-info">
                        ₦<?php echo number_format($prop['row_subtotal'], 2); ?>
                    </span>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <small class="text-muted">Sheet Quantity</small>
                        <p class="mb-0"><strong><?php echo $prop[
                            'sheet_qty'
                        ]; ?> sheets</strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Meter per Sheet</small>
                        <p class="mb-0"><strong><?php echo number_format(
                            $prop['sheet_meter'],
                            2,
                        ); ?>m</strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Total Meters</small>
                        <p class="mb-0"><strong><?php echo number_format(
                            $prop['meters'],
                            2,
                        ); ?>m</strong></p>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Unit Price</small>
                        <p class="mb-0"><strong>₦<?php echo number_format(
                            $prop['unit_price'],
                            2,
                        ); ?>/m</strong></p>
                    </div>
                </div>
                
                <hr class="my-2">
                
                <div class="d-flex justify-content-between">
                    <small class="text-muted">
                        Calculation: <?php echo $prop['sheet_qty']; ?> × <?php echo number_format(
     $prop['sheet_meter'],
     2,
 ); ?>m × ₦<?php echo number_format($prop['unit_price'], 2); ?>
                    </small>
                    <small class="text-muted">
                        Subtotal: <strong>₦<?php echo number_format(
                            $prop['row_subtotal'],
                            2,
                        ); ?></strong>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-warning">No properties defined</div>
        <?php endif; ?>
        
        <!-- Summary Section -->
        <div class="section-header">
            <i class="bi bi-calculator"></i> Production Summary
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Meters</h6>
                        <h3 class="mb-0 text-primary">
                            <?php echo number_format($prodPaper['total_meters'] ?? 0, 2); ?>m
                        </h3>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="text-muted mb-2">Total Amount</h6>
                        <h3 class="mb-0 text-success">
                            ₦<?php echo number_format($prodPaper['total_amount'] ?? 0, 2); ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Immutable Hash -->
        <div class="section-header mt-4">
            <i class="bi bi-shield-check"></i> Immutability Verification
        </div>
        <div class="alert alert-secondary">
            <p class="mb-2"><strong>Immutable Hash (SHA256):</strong></p>
            <div class="hash-display">
                <?php echo htmlspecialchars($production['immutable_hash']); ?>
            </div>
            <small class="text-muted mt-2 d-block">
                This hash ensures data integrity. Any modification to this record will be detected.
            </small>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-4 d-flex justify-content-between">
            <a href="/new-stock-system/index.php?page=production" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            <div>
                <a href="/new-stock-system/index.php?page=sales_view&id=<?php echo $production[
                    'sale_id'
                ]; ?>" class="btn btn-info">
                    <i class="bi bi-eye"></i> View Related Sale
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
