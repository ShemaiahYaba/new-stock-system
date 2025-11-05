<?php
/**
 * Access Denied View
 */

$pageTitle = 'Access Denied - ' . APP_NAME;

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card border-danger">
                    <div class="card-body text-center p-5">
                        <i class="bi bi-shield-x text-danger" style="font-size: 5rem;"></i>
                        <h2 class="mt-4 text-danger">Access Denied</h2>
                        <p class="lead text-muted">
                            You do not have permission to access this page.
                        </p>
                        <p class="text-muted">
                            If you believe this is an error, please contact your system administrator.
                        </p>
                        <div class="mt-4">
                            <a href="/new-stock-system/index.php?page=dashboard" class="btn btn-primary">
                                <i class="bi bi-house"></i> Go to Dashboard
                            </a>
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Go Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
