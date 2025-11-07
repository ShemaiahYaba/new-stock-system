<?php
/**
 * Supply/Delivery List
 * File: views/supply/index.php
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/supply.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Supply/Delivery - ' . APP_NAME;

// Pagination setup
$currentPage = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$statusFilter = $_GET['status'] ?? '';

$supplyModel = new Supply();
$limit = RECORDS_PER_PAGE;
$offset = ($currentPage - 1) * $limit;

// Get supply records with pagination and status filter
$supplies = $supplyModel->getAll($limit, $offset, $statusFilter);
$totalSupplies = $supplyModel->count($statusFilter);

$paginationData = getPaginationData($totalSupplies, $currentPage);

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
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-supplied { background-color: #d1e7dd; color: #0f5132; }
    .status-returned { background-color: #f8d7da; color: #842029; }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-truck"></i> Supply/Delivery
                </h1>
                <p class="text-muted">Manage and track all supply/delivery records</p>
            </div>
            <div>
                <a href="/new-stock-system/index.php?page=supply_create" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> New Supply
                </a>
            </div>
        </div>
    </div>
    
    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="/new-stock-system/index.php" class="row g-3">
                <input type="hidden" name="page" value="supply">
                
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="pending" <?= $statusFilter === 'pending'
                            ? 'selected'
                            : '' ?>>Pending</option>
                        <option value="supplied" <?= $statusFilter === 'supplied'
                            ? 'selected'
                            : '' ?>>Supplied</option>
                        <option value="returned" <?= $statusFilter === 'returned'
                            ? 'selected'
                            : '' ?>>Returned</option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Supply/Delivery List -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <i class="bi bi-list"></i> Supply/Delivery Records (<?= $totalSupplies ?> total)
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <?php if (empty($supplies)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> No supply/delivery records found.
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Reference #</th>
                            <th>Production #</th>
                            <th>Customer</th>
                            <th>Warehouse</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($supplies as $supply):

                            $statusClass = 'status-' . $supply['status'];
                            $statusText = ucfirst($supply['status']);
                            ?>
                        <tr>
                            <td>SD-<?= str_pad($supply['id'], 5, '0', STR_PAD_LEFT) ?></td>
                            <td>PR-<?= str_pad(
                                $supply['production_id'],
                                5,
                                '0',
                                STR_PAD_LEFT,
                            ) ?></td>
                            <td><?= htmlspecialchars($supply['customer_name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($supply['warehouse_name'] ?? 'N/A') ?></td>
                            <td><span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                            <td><?= date('M d, Y', strtotime($supply['created_at'])) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="/new-stock-system/index.php?page=supply_view&id=<?= $supply[
                                        'id'
                                    ] ?>" 
                                       class="btn btn-sm btn-outline-primary" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <?php if ($supply['status'] !== 'returned'): ?>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-gear"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <?php if ($supply['status'] === 'pending'): ?>
                                            <li>
                                                <a class="dropdown-item update-status" 
                                                   href="#" 
                                                   data-id="<?= $supply['id'] ?>" 
                                                   data-status="supplied">
                                                    <i class="bi bi-check-circle text-success"></i> Mark as Supplied
                                                </a>
                                            </li>
                                            <?php elseif ($supply['status'] === 'supplied'): ?>
                                            <li>
                                                <a class="dropdown-item update-status" 
                                                   href="#" 
                                                   data-id="<?= $supply['id'] ?>" 
                                                   data-status="returned">
                                                    <i class="bi bi-arrow-return-left text-warning"></i> Mark as Returned
                                                </a>
                                            </li>
                                            <?php endif; ?>
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
            
            <!-- Pagination -->
            <?php if ($paginationData['totalPages'] > 1): ?>
            <div class="p-3 border-top">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center mb-0">
                        <?php if ($paginationData['currentPage'] > 1): ?>
                        <li class="page-item">
                            <a class="page-link" 
                               href="?page=supply&page_num=<?= $paginationData['currentPage'] -
                                   1 .
                                   ($statusFilter ? '&status=' . urlencode($statusFilter) : '') ?>" 
                               aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $paginationData['totalPages']; $i++): ?>
                        <li class="page-item <?= $i === $paginationData['currentPage']
                            ? 'active'
                            : '' ?>">
                            <a class="page-link" 
                               href="?page=supply&page_num=<?= $i .
                                   ($statusFilter ? '&status=' . urlencode($statusFilter) : '') ?>">
                                <?= $i ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if (
                            $paginationData['currentPage'] < $paginationData['totalPages']
                        ): ?>
                        <li class="page-item">
                            <a class="page-link" 
                               href="?page=supply&page_num=<?= $paginationData['currentPage'] +
                                   1 .
                                   ($statusFilter ? '&status=' . urlencode($statusFilter) : '') ?>" 
                               aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle status updates
    document.querySelectorAll('.update-status').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const supplyId = this.getAttribute('data-id');
            const newStatus = this.getAttribute('data-status');
            
            if (confirm(`Are you sure you want to mark this as ${newStatus}?`)) {
                fetch('/new-stock-system/controllers/supply/update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${supplyId}&status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.message || 'Error updating status');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the status');
                });
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
