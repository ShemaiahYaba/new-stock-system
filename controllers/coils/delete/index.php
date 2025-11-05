<?php
/**
 * Coil Delete Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/coil.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=coils');
        exit();
    }
    
    $coilId = (int)($_POST['id'] ?? 0);
    
    if ($coilId <= 0) {
        setFlashMessage('error', 'Invalid coil ID.');
        header('Location: /new-stock-system/index.php?page=coils');
        exit();
    }
    
    $coilModel = new Coil();
    $coil = $coilModel->findById($coilId);
    
    if (!$coil) {
        setFlashMessage('error', 'Coil not found.');
        header('Location: /new-stock-system/index.php?page=coils');
        exit();
    }
    
    if ($coilModel->delete($coilId)) {
        logActivity('Coil deleted', "Code: {$coil['code']}");
        setFlashMessage('success', 'Coil deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete coil.');
    }
    
    header('Location: /new-stock-system/index.php?page=coils');
    exit();
}

header('Location: /new-stock-system/index.php?page=coils');
exit();
