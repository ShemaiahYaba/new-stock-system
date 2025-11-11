<?php
/**
 * Color Delete Controller
 */

session_start();

require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/constants.php';
require_once __DIR__ . '/../../../models/color.php';
require_once __DIR__ . '/../../../utils/helpers.php';
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_COLOR_MANAGEMENT, ACTION_DELETE);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        setFlashMessage('error', 'Invalid request.');
        header('Location: /new-stock-system/index.php?page=colors');
        exit();
    }
    
    $colorId = (int)($_POST['id'] ?? 0);
    
    if ($colorId <= 0) {
        setFlashMessage('error', 'Invalid color ID.');
        header('Location: /new-stock-system/index.php?page=colors');
        exit();
    }
    
    $colorModel = new Color();
    $color = $colorModel->findById($colorId);
    
    if (!$color) {
        setFlashMessage('error', 'Color not found.');
        header('Location: /new-stock-system/index.php?page=colors');
        exit();
    }
    
    // Check if color is being used by any coils
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM coils WHERE color_id = :color_id AND deleted_at IS NULL");
    $stmt->execute([':color_id' => $colorId]);
    $result = $stmt->fetch();
    
    if ($result['count'] > 0) {
        setFlashMessage('error', "Cannot delete color '{$color['name']}' because it is being used by {$result['count']} coil(s).");
        header('Location: /new-stock-system/index.php?page=colors');
        exit();
    }
    
    if ($colorModel->delete($colorId)) {
        logActivity('Color deleted', "Code: {$color['code']}");
        setFlashMessage('success', 'Color deleted successfully!');
    } else {
        setFlashMessage('error', 'Failed to delete color.');
    }
    
    header('Location: /new-stock-system/index.php?page=colors');
    exit();
}

header('Location: /new-stock-system/index.php?page=colors');
exit();