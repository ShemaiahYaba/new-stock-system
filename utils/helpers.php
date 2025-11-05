<?php
/**
 * Helper Utility Functions
 * 
 * Reusable utility functions for common tasks
 */

/**
 * Sanitize input data
 * 
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email address
 * 
 * @param string $email Email address
 * @return bool
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 * 
 * @return string
 */
function generateCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool
 */
function verifyCsrfToken($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @param string $format Output format
 * @return string
 */
function formatDate($date, $format = DATE_DISPLAY_FORMAT) {
    if (empty($date)) {
        return '';
    }
    
    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : $date;
}

/**
 * Format currency
 * 
 * @param float $amount Amount
 * @param string $currency Currency symbol
 * @return string
 */
function formatCurrency($amount, $currency = '₦') {
    return $currency . number_format($amount, 2);
}

/**
 * Generate random string
 * 
 * @param int $length Length of string
 * @return string
 */
function generateRandomString($length = 10) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)))), 1, $length);
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Set flash message
 * 
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 */
function setFlashMessage($type, $message) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * 
 * @return array|null
 */
function getFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    
    return null;
}

/**
 * Check if flash message exists
 * 
 * @return bool
 */
function hasFlashMessage() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['flash_message']);
}

/**
 * Generate unique code
 * 
 * @param string $prefix Prefix for the code
 * @return string
 */
function generateUniqueCode($prefix = '') {
    return $prefix . strtoupper(uniqid());
}

/**
 * Calculate percentage
 * 
 * @param float $value Value
 * @param float $total Total
 * @return float
 */
function calculatePercentage($value, $total) {
    if ($total == 0) {
        return 0;
    }
    
    return round(($value / $total) * 100, 2);
}

/**
 * Truncate text
 * 
 * @param string $text Text to truncate
 * @param int $length Maximum length
 * @param string $suffix Suffix to append
 * @return string
 */
function truncateText($text, $length = 50, $suffix = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    return substr($text, 0, $length) . $suffix;
}

/**
 * Get status badge class
 * 
 * @param string $status Status value
 * @return string Bootstrap badge class
 */
function getStatusBadgeClass($status) {
    $classes = [
        STOCK_STATUS_AVAILABLE => 'badge-success',
        STOCK_STATUS_FACTORY_USE => 'badge-warning',
        STOCK_STATUS_SOLD => 'badge-danger',
        STOCK_STATUS_RESERVED => 'badge-info',
        SALE_STATUS_PENDING => 'badge-warning',
        SALE_STATUS_COMPLETED => 'badge-success',
        SALE_STATUS_CANCELLED => 'badge-danger'
    ];
    
    return $classes[$status] ?? 'badge-secondary';
}

/**
 * Log activity
 * 
 * @param string $action Action performed
 * @param string $details Details of the action
 */
function logActivity($action, $details = '') {
    // This can be extended to log to database or file
    error_log("[" . date('Y-m-d H:i:s') . "] User: " . ($_SESSION['user_email'] ?? 'Guest') . " - Action: $action - Details: $details");
}

/**
 * Validate required fields
 * 
 * @param array $data Data array
 * @param array $required Required field names
 * @return array Array of missing fields
 */
function validateRequiredFields($data, $required) {
    $missing = [];
    
    foreach ($required as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            $missing[] = $field;
        }
    }
    
    return $missing;
}

/**
 * Convert array to CSV
 * 
 * @param array $data Data array
 * @param string $filename Filename
 */
function arrayToCsv($data, $filename = 'export.csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    if (!empty($data)) {
        fputcsv($output, array_keys($data[0]));
        
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    exit();
}

/**
 * Get pagination data
 * 
 * @param int $total Total records
 * @param int $page Current page
 * @param int $perPage Records per page
 * @return array
 */
function getPaginationData($total, $page = 1, $perPage = RECORDS_PER_PAGE) {
    $totalPages = ceil($total / $perPage);
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_previous' => $page > 1,
        'has_next' => $page < $totalPages
    ];
}

/**
 * Build query string
 * 
 * @param array $params Parameters
 * @param array $exclude Parameters to exclude
 * @return string
 */
function buildQueryString($params, $exclude = []) {
    $filtered = array_diff_key($params, array_flip($exclude));
    return http_build_query($filtered);
}
