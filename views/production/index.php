<?php
/**
 * Production List View
 * File: views/production/index.php
 *
 * Displays all production records (immutable)
 * Shows production status and related sale info
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/production.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Production Management - ' . APP_NAME;

// Pagination setup
$currentPage = isset($_GET['page_num']) ? (int) $_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';
$statusFilter = $_GET['status'] ?? '';

$productionModel = new Production();

// Get records with pagination
$limit = RECORDS_PER_PAGE;
$offset = ($currentPage - 1) * $limit;

// Get productions with search and status filtering
if (!empty($searchQuery) || !empty($statusFilter)) {
    $productions = $productionModel->search($searchQuery, $limit, $offset, $statusFilter);
    $totalProductions = $productionModel->countSearch($searchQuery, $statusFilter);
} else {
    $productions = $productionModel->getAll($limit, $offset, $statusFilter);
    $totalProductions = $productionModel->count($statusFilter);
}

$paginationData = getPaginationData($totalProductions, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<style>
.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}
.immutable-indicator {
    display: inline-block;
    padding: 2px 8px;
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 4px;
    font-size: 0.75rem;
    margin-left: 8px;
}
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-gear"></i> Production Management
                    <span class="immutable-indicator">
                        <i class="bi bi-lock"></i> Immutable Records
                    </span>
                </h1>
                <p class="text-muted">Track all production requests and their status</p>
            </div>
            <a href="/new-stock-system/index.php?page=sales_create_new" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Production Order
            </a>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="/new-stock-system/index.php" class="row g-3">
                <input type="hidden" name="page" value="production">
                
                <div class="col-md-5">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="Search by production ref, customer, coil..." 
                           value="<?php echo htmlspecialchars($searchQuery); ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <?php foreach (PRODUCTION_STATUSES as $key => $label): ?>
                        <option value="<?php echo $key; ?>" <?php echo $statusFilter === $key
    ? 'selected'
    : ''; ?>>
                            <?php echo $label; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="/new-stock-system/index.php?page=production" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Production List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-list"></i> Production Records (<?php echo $totalProductions; ?> total)
                </div>
                <div class=" small">
                    <i class="bi bi-info-circle"></i> Records are immutable once created
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($productions)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No production records found.
            </div>
            <?php
                // Ensure production_paper is an array
                // Ensure production_paper is an array
                // Ensure production_paper is an array
                // Ensure production_paper is an array
                // Ensure production_paper is an array
                // Ensure production_paper is an array
                // Ensure production_paper is an array
                // Ensure production_paper is an array
                else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Production ID</th>
                            <th>Sale Reference</th>
                            <th>Warehouse</th>
                            <th>Total Meters</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productions as $prod):

                            $prodPaper = [];
                            if (isset($prod['production_paper'])) {
                                if (is_array($prod['production_paper'])) {
                                    $prodPaper = $prod['production_paper'];
                                } elseif (is_string($prod['production_paper'])) {
                                    $prodPaper = json_decode($prod['production_paper'], true) ?: [];
                                }
                            }

                            $totalMeters = $prodPaper['total_meters'] ?? 0;
                            $totalAmount = $prodPaper['total_amount'] ?? 0;
                            ?>
                        <tr>
                            <td>
                                <strong>PR-<?php echo str_pad(
                                    $prod['id'],
                                    4,
                                    '0',
                                    STR_PAD_LEFT,
                                ); ?></strong>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-lock"></i> Immutable
                                </small>
                            </td>
                            <td>
                                <a href="/new-stock-system/index.php?page=sales_view&id=<?php echo $prod[
                                    'sale_id'
                                ]; ?>">
                                    #SO-<?php echo str_pad(
                                        $prod['sale_id'],
                                        6,
                                        '0',
                                        STR_PAD_LEFT,
                                    ); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($prod['warehouse_name']); ?></td>
                            <td><?php echo number_format($totalMeters, 2); ?>m</td>
                            <td>₦<?php echo number_format($totalAmount, 2); ?></td>
                            <td>
                                <?php
                                $statusColors = [
                                    PRODUCTION_STATUS_PENDING => 'warning',
                                    PRODUCTION_STATUS_IN_PROGRESS => 'info',
                                    PRODUCTION_STATUS_COMPLETED => 'success',
                                    PRODUCTION_STATUS_CANCELLED => 'danger',
                                ];
                                $color = $statusColors[$prod['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?php echo $color; ?> status-badge">
                                    <?php echo PRODUCTION_STATUSES[$prod['status']] ??
                                        $prod['status']; ?>
                                </span>
                            </td>
                            <td><?php echo date(
                                'M d, Y H:i',
                                strtotime($prod['created_at']),
                            ); ?></td>
                            <td><?php echo isset($prod['created_by_name'])
                                ? htmlspecialchars($prod['created_by_name'])
                                : 'System'; ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="/new-stock-system/index.php?page=production_view&id=<?php echo $prod[
                                        'id'
                                    ]; ?>" 
                                       class="btn btn-sm btn-info" title="View Production Paper">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    
                                    <?php if (
                                        hasPermission(MODULE_PRODUCTION_MANAGEMENT, ACTION_EDIT)
                                    ): ?>
                                    <!-- Status Update Dropdown -->
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-warning dropdown-toggle" 
                                                data-bs-toggle="dropdown" title="Update Status">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <?php foreach (
                                                PRODUCTION_STATUSES
                                                as $statusKey => $statusLabel
                                            ): ?>
                                            <?php if ($statusKey !== $prod['status']): ?>
                                            <li>
                                                <a class="dropdown-item" href="#" 
                                                   onclick="updateProductionStatus(<?php echo $prod[
                                                       'id'
                                                   ]; ?>, '<?php echo $statusKey; ?>')">
                                                    <?php echo $statusLabel; ?>
                                                </a>
                                            </li>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php
                        endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($productions)): ?>
        <div class="card-footer">
            <?php
            $queryParams = $_GET;
            include __DIR__ . '/../../layout/pagination.php';
            ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Status Update Form (Hidden) -->
<form id="statusUpdateForm" method="POST" action="/new-stock-system/controllers/production/update_status/index.php" style="display: none;">
    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
    <input type="hidden" name="production_id" id="status_production_id">
    <input type="hidden" name="status" id="status_new_status">
</form>

<script>
function updateProductionStatus(productionId, newStatus) {
    if (confirm('Are you sure you want to update the production status?\n\nNote: This action will be logged in the audit trail.')) {
        document.getElementById('status_production_id').value = productionId;
        document.getElementById('status_new_status').value = newStatus;
        document.getElementById('statusUpdateForm').submit();
    }
}
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
