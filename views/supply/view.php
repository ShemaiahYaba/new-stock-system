<?php
/**
 * Supply/Delivery Detail View
 * File: views/supply/view.php
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../models/supply.php';
require_once __DIR__ . '/../../models/production.php';
require_once __DIR__ . '/../../utils/helpers.php';

$supplyId = (int) ($_GET['id'] ?? 0);

if ($supplyId <= 0) {
    setFlashMessage('error', 'Invalid supply/delivery ID.');
    header('Location: /new-stock-system/index.php?page=supply');
    exit();
}

$supplyModel = new Supply();
$supply = $supplyModel->getById($supplyId);

if (!$supply) {
    setFlashMessage('error', 'Supply/Delivery record not found.');
    header('Location: /new-stock-system/index.php?page=supply');
    exit();
}

$productionModel = new Production();
$production = $productionModel->getById($supply['production_id']);

$pageTitle = 'Supply/Delivery #SD-' . str_pad($supplyId, 5, '0', STR_PAD_LEFT) . ' - ' . APP_NAME;

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';

// Helper function to get status badge
function getStatusBadge($status)
{
    $classes = [
        'pending' => 'status-pending',
        'supplied' => 'status-supplied',
        'returned' => 'status-returned',
    ];
    $class = $classes[$status] ?? 'bg-secondary';
    return '<span class="status-badge ' . $class . '">' . ucfirst($status) . '</span>';
}
?>

<style>
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-block;
    }
    .status-pending { background-color: #fff3cd; color: #856404; }
    .status-supplied { background-color: #d1e7dd; color: #0f5132; }
    .status-returned { background-color: #f8d7da; color: #842029; }
    .timeline {
        position: relative;
        padding-left: 30px;
        margin: 20px 0;
    }
    .timeline:before {
        content: '';
        position: absolute;
        left: 10px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        left: -30px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .timeline-dot i {
        font-size: 10px;
        color: #0d6efd;
    }
    .timeline-date {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-truck"></i> Supply/Delivery Details
                </h1>
                <p class="text-muted">#SD-<?= str_pad($supplyId, 5, '0', STR_PAD_LEFT) ?></p>
            </div>
            <div class="d-flex gap-2">
                <a href="/new-stock-system/index.php?page=supply" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
                <?php if ($supply['status'] !== 'returned'): ?>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" 
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear"></i> Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if ($supply['status'] === 'pending'): ?>
                        <li>
                            <a class="dropdown-item update-status" href="#" 
                               data-id="<?= $supply['id'] ?>" data-status="supplied">
                                <i class="bi bi-check-circle text-success"></i> Mark as Supplied
                            </a>
                        </li>
                        <?php elseif ($supply['status'] === 'supplied'): ?>
                        <li>
                            <a class="dropdown-item update-status" href="#"
                               data-id="<?= $supply['id'] ?>" data-status="returned">
                                <i class="bi bi-arrow-return-left text-warning"></i> Mark as Returned
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Delivery Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Reference #</div>
                        <div class="col-md-8">SD-<?= str_pad(
                            $supply['id'],
                            5,
                            '0',
                            STR_PAD_LEFT,
                        ) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Production #</div>
                        <div class="col-md-8">
                            <a href="/new-stock-system/index.php?page=production_view&id=<?= $supply[
                                'production_id'
                            ] ?>">
                                PR-<?= str_pad($supply['production_id'], 5, '0', STR_PAD_LEFT) ?>
                            </a>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Customer</div>
                        <div class="col-md-8"><?= htmlspecialchars(
                            $supply['customer_name'],
                        ) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Warehouse</div>
                        <div class="col-md-8"><?= htmlspecialchars(
                            $supply['warehouse_name'],
                        ) ?></div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Status</div>
                        <div class="col-md-8"><?= getStatusBadge($supply['status']) ?></div>
                    </div>
                    <?php if (!empty($supply['notes'])): ?>
                    <div class="row">
                        <div class="col-md-4 fw-bold">Notes</div>
                        <div class="col-md-8"><?= nl2br(htmlspecialchars($supply['notes'])) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Activity Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-dot">
                                <i class="bi bi-circle-fill"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Supply/Delivery Created</span>
                                    <span class="timeline-date"><?= date(
                                        'M d, Y h:i A',
                                        strtotime($supply['created_at']),
                                    ) ?></span>
                                </div>
                                <p class="mb-0">Record was created in the system.</p>
                            </div>
                        </div>

                        <?php if (
                            $supply['status'] === 'supplied' ||
                            $supply['status'] === 'returned'
                        ): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Marked as Supplied</span>
                                    <span class="timeline-date"><?= date(
                                        'M d, Y h:i A',
                                        strtotime($supply['delivered_at']),
                                    ) ?></span>
                                </div>
                                <p class="mb-0">Items were delivered to the warehouse.</p>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if ($supply['status'] === 'returned'): ?>
                        <div class="timeline-item">
                            <div class="timeline-dot">
                                <i class="bi bi-arrow-return-left"></i>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Marked as Returned</span>
                                    <span class="timeline-date"><?= date(
                                        'M d, Y h:i A',
                                        strtotime($supply['return_requested_at']),
                                    ) ?></span>
                                </div>
                                <p class="mb-0">Items were returned from the warehouse.</p>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/new-stock-system/index.php?page=production_view&id=<?= $supply[
                            'production_id'
                        ] ?>" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-box-seam"></i> View Production
                        </a>
                        <?php if (!empty($supply['sale_id'])): ?>
                        <a href="/new-stock-system/index.php?page=sales_view&id=<?= $supply[
                            'sale_id'
                        ] ?>" 
                           class="btn btn-outline-secondary">
                            <i class="bi bi-receipt"></i> View Sale
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Delivery Details</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Delivery Date</label>
                        <p><?= $supply['delivered_at']
                            ? date('M d, Y', strtotime($supply['delivered_at']))
                            : 'Not delivered yet' ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Created By</label>
                        <p>System</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Updated</label>
                        <p><?= date(
                            'M d, Y h:i A',
                            strtotime($supply['updated_at'] ?? $supply['created_at']),
                        ) ?></p>
                    </div>
                </div>
            </div>
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
            const action = newStatus === 'supplied' ? 'deliver' : 'return';
            
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
