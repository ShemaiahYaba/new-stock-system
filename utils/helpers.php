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
function sanitize($data)
{
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
function isValidEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Generate CSRF token
 *
 * @return string
 */
function generateCsrfToken()
{
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
function verifyCsrfToken($token)
{
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
function formatDate($date, $format = 'Y-m-d H:i:s')
{
    if (empty($date) || $date === '0000-00-00 00:00:00') {
        return 'N/A';
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
function formatCurrency($amount, $currency = '₦')
{
    return $currency . number_format($amount, 2);
}

/**
 * Generate random string
 *
 * @param int $length Length of string
 * @return string
 */
function generateRandomString($length = 10)
{
    return substr(
        str_shuffle(
            str_repeat(
                $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                ceil($length / strlen($x)),
            ),
        ),
        1,
        $length,
    );
}

/**
 * Redirect to a URL
 *
 * @param string $url URL to redirect to
 */
function redirect($url)
{
    header("Location: $url");
    exit();
}

/**
 * Set flash message
 *
 * @param string $type Message type (success, error, warning, info)
 * @param string $message Message content
 */
function setFlashMessage($type, $message)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message,
    ];
}

/**
 * Get and clear flash message
 *
 * @return array|null
 */
function getFlashMessage()
{
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
function hasFlashMessage()
{
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
function generateUniqueCode($prefix = '')
{
    return $prefix . strtoupper(uniqid());
}

/**
 * Calculate percentage
 *
 * @param float $value Value
 * @param float $total Total
 * @return float
 */
function calculatePercentage($value, $total)
{
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
function truncateText($text, $length = 50, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }

    return substr($text, 0, $length) . $suffix;
}

/**
 * Get status badge class - UPDATED
 *
 * @param string $status Status value
 * @return string Bootstrap badge class
 */
function getStatusBadgeClass($status)
{
    $classes = [
        STOCK_STATUS_AVAILABLE => 'bg-success', // Green
        STOCK_STATUS_FACTORY_USE => 'bg-warning', // Yellow
        STOCK_STATUS_SOLD => 'bg-danger', // Red
        STOCK_STATUS_OUT_OF_STOCK => 'bg-secondary', // Gray - NEW
        SALE_STATUS_PENDING => 'bg-warning',
        SALE_STATUS_COMPLETED => 'bg-success',
        SALE_STATUS_CANCELLED => 'bg-danger',
    ];

    return $classes[$status] ?? 'bg-secondary';
}
/**
 * Log activity
 *
 * @param string $action Action performed
 * @param string $details Details of the action
 */
function logActivity($action, $details = '')
{
    // This can be extended to log to database or file
    error_log(
        '[' .
            date('Y-m-d H:i:s') .
            '] User: ' .
            ($_SESSION['user_email'] ?? 'Guest') .
            " - Action: $action - Details: $details",
    );
}

/**
 * Validate required fields
 *
 * @param array $data Data array
 * @param array $required Required field names
 * @return array Array of missing fields
 */
function validateRequiredFields($data, $required)
{
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
function arrayToCsv($data, $filename = 'export.csv')
{
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
function getPaginationData($total, $page = 1, $perPage = RECORDS_PER_PAGE)
{
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
        'has_next' => $page < $totalPages,
    ];
}

/**
 * Build query string
 *
 * @param array $params Parameters
 * @param array $exclude Parameters to exclude
 * @return string
 */
function buildQueryString($params, $exclude = [])
{
    $query = [];
    foreach ($params as $key => $value) {
        if (!in_array($key, $exclude) && $value !== '') {
            $query[] = $key . '=' . urlencode($value);
        }
    }
    return implode('&', $query);
}

/**
 * Convert number to words
 *
 * @param float $number Number to convert
 * @return string Number in words
 */
/**
 * Convert number to words (Nigerian Naira)
 * Handles up to billions
 */
function numberToWords($number)
{
    // Handle negative numbers
    if ($number < 0) {
        return 'Minus ' . numberToWords(abs($number));
    }

    // Handle zero
    if ($number == 0) {
        return 'Zero Naira Only';
    }

    // Separate integer and decimal parts
    $number = number_format($number, 2, '.', '');
    $parts = explode('.', $number);
    $naira = (int) $parts[0];
    $kobo = (int) $parts[1];

    $words = '';

    // Convert naira part
    if ($naira > 0) {
        $words = convertNumberToWords($naira) . ' Naira';
    }

    // Add kobo part if not zero
    if ($kobo > 0) {
        if ($words != '') {
            $words .= ' and ';
        }
        $words .= convertNumberToWords($kobo) . ' Kobo';
    }

    return $words . ' Only';
}

/**
 * Core conversion function
 */
function convertNumberToWords($number)
{
    $ones = [
        0 => '',
        1 => 'One',
        2 => 'Two',
        3 => 'Three',
        4 => 'Four',
        5 => 'Five',
        6 => 'Six',
        7 => 'Seven',
        8 => 'Eight',
        9 => 'Nine',
        10 => 'Ten',
        11 => 'Eleven',
        12 => 'Twelve',
        13 => 'Thirteen',
        14 => 'Fourteen',
        15 => 'Fifteen',
        16 => 'Sixteen',
        17 => 'Seventeen',
        18 => 'Eighteen',
        19 => 'Nineteen',
    ];

    $tens = [
        0 => '',
        1 => '',
        2 => 'Twenty',
        3 => 'Thirty',
        4 => 'Forty',
        5 => 'Fifty',
        6 => 'Sixty',
        7 => 'Seventy',
        8 => 'Eighty',
        9 => 'Ninety',
    ];

    $hundreds = ['', 'Thousand', 'Million', 'Billion', 'Trillion'];

    if ($number == 0) {
        return 'Zero';
    }

    // Split number into groups of three
    $groups = [];
    while ($number > 0) {
        $groups[] = $number % 1000;
        $number = (int) ($number / 1000);
    }

    $words = [];

    foreach ($groups as $index => $group) {
        if ($group == 0) {
            continue;
        }

        $groupWords = '';

        // Handle hundreds
        $hundred = (int) ($group / 100);
        if ($hundred > 0) {
            $groupWords .= $ones[$hundred] . ' Hundred';
            if ($group % 100 > 0) {
                $groupWords .= ' and ';
            }
        }

        // Handle tens and ones
        $remainder = $group % 100;
        if ($remainder < 20) {
            $groupWords .= $ones[$remainder];
        } else {
            $ten = (int) ($remainder / 10);
            $one = $remainder % 10;
            $groupWords .= $tens[$ten];
            if ($one > 0) {
                $groupWords .= '-' . $ones[$one];
            }
        }

        // Add scale (thousand, million, etc.)
        if ($index > 0) {
            $groupWords .= ' ' . $hundreds[$index];
        }

        $words[] = trim($groupWords);
    }

    // Reverse and join
    $words = array_reverse($words);
    return implode(', ', $words);
}

/**
 * Format currency with proper Naira symbol
 */

/**
 * Format date for invoices/receipts
 */
function formatInvoiceDate($date, $format = 'F d, Y')
{
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return 'N/A';
    }

    $timestamp = is_numeric($date) ? $date : strtotime($date);
    return date($format, $timestamp);
}

/**
 * Generate receipt number from ID
 */
function generateReceiptNumber($id, $prefix = 'RCPT', $length = 5)
{
    return $prefix . '-' . str_pad($id, $length, '0', STR_PAD_LEFT);
}

/**
 * Test the number to words conversion
 * Uncomment to test different values
 */
/*
echo numberToWords(100000) . "\n";      // One Hundred Thousand Naira Only
echo numberToWords(500000) . "\n";      // Five Hundred Thousand Naira Only
echo numberToWords(1000000) . "\n";     // One Million Naira Only
echo numberToWords(100000.50) . "\n";   // One Hundred Thousand Naira and Fifty Kobo Only
echo numberToWords(1234567.89) . "\n";  // One Million, Two Hundred Thirty-Four Thousand, Five Hundred Sixty-Seven Naira and Eighty-Nine Kobo Only
*/
