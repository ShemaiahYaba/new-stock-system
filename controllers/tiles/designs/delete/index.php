<?php
/**
 * ============================================
 * FILE: controllers/tiles/designs/delete/index.php
 * ============================================
 */
session_start();

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/constants.php';
require_once __DIR__ . '/../../../../models/design.php';
require_once __DIR__ . '/../../../../utils/helpers.php';
require_once __DIR__ . '/../../../../utils/auth_middleware.php';

requirePermission(MODULE_DESIGN_MANAGEMENT, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=designs');
        exit();
    }
    
    $designId = (int)($_POST['id'] ?? 0);
    
    if ($designId <= 0) {
        setFlashMessage('error', 'Invalid design ID.');
        header('Location: /new-stock-system/index.php?page=designs');
        exit();
    }
    
    $designModel = new Design();
    $design = $designModel->findById($designId);
    
    if (!$design) {
        setFlashMessage('error', 'Design not found.');
        header('Location: /new-stock-system/index.php?page=designs');
        exit();
    }
    
    // Check if design is being used
    if ($designModel->isUsedInProducts($designId)) {
        setFlashMessage('error', 'Cannot delete design because it is being used by tile products.');
        header('Location: /new-stock-system/index.php?page=designs');
        exit();
    }
    
    if ($designModel->delete($designId)) {
        logActivity('Design deleted', "Code: {$design['code']}");
        setFlashMessage('success', 'Design deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete design.');
    }
    
    header('Location: /new-stock-system/index.php?page=designs');
    exit();
}

header('Location: /new-stock-system/index.php?page=designs');
exit();
