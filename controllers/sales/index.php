<?php
/**
 * Sales List Controller
 * 
 * Handles listing and searching of sales records
 */

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/sale.php';
require_once __DIR__ . '/../../utils/helpers.php';

// Initialize variables
$currentPage = isset($_GET['page_num']) ? max(1, (int)$_GET['page_num']) : 1;
$searchQuery = trim($_GET['search'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

// Initialize the sale model
$saleModel = new Sale();

// Set default records per page
$recordsPerPage = RECORDS_PER_PAGE;
$offset = ($currentPage - 1) * $recordsPerPage;

// Build the base query and count query
$queryParams = [];
$whereClause = 'WHERE s.deleted_at IS NULL';

// Apply search filter
if (!empty($searchQuery)) {
    $whereClause .= ' AND (c.name LIKE :query OR co.code LIKE :query OR co.name LIKE :query OR s.id = :id)';
    $queryParams[':query'] = "%$searchQuery%";
    
    // If search query is numeric, try to match by sale ID
    if (is_numeric($searchQuery)) {
        $queryParams[':id'] = (int)$searchQuery;
    }
}

// Apply status filter
if (!empty($statusFilter) && array_key_exists($statusFilter, SALE_STATUSES)) {
    $whereClause .= ' AND s.status = :status';
    $queryParams[':status'] = $statusFilter;
}

// Apply date range filter
if (!empty($fromDate)) {
    $whereClause .= ' AND DATE(s.created_at) >= :from_date';
    $queryParams[':from_date'] = $fromDate;
}

if (!empty($toDate)) {
    $whereClause .= ' AND DATE(s.created_at) <= :to_date';
    $queryParams[':to_date'] = $toDate;
}

try {
    // Get paginated sales
    $sales = $saleModel->getFilteredSales($whereClause, $queryParams, $recordsPerPage, $offset);
    
    // Get total count for pagination
    $totalSales = $saleModel->countFilteredSales($whereClause, $queryParams);
    
    // Calculate pagination data
    $paginationData = getPaginationData($totalSales, $currentPage, $recordsPerPage);
    
} catch (Exception $e) {
    // Log error and set empty results
    error_log("Sales list error: " . $e->getMessage());
    $sales = [];
    $totalSales = 0;
    $paginationData = null;
    
    // Set error message
    $errorMessage = "An error occurred while fetching sales. Please try again later.";
    if (DEBUG_MODE) {
        $errorMessage .= " Error: " . $e->getMessage();
    }
}

// Set page title
$pageTitle = 'Sales List - ' . APP_NAME;

// Include the view
require_once __DIR__ . '/../../views/sales/index.php';
