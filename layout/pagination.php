<?php
/**
 * Pagination Component
 * 
 * Reusable pagination for tables
 * 
 * @param array $paginationData Pagination data from getPaginationData()
 * @param array $queryParams Additional query parameters to preserve
 */

$paginationData = $paginationData ?? [];
$queryParams = $queryParams ?? [];

if (empty($paginationData) || $paginationData['total_pages'] <= 1) {
    return;
}

$currentPage = $paginationData['current_page'];
$totalPages = $paginationData['total_pages'];
$hasPrevious = $paginationData['has_previous'];
$hasNext = $paginationData['has_next'];

// Build base URL with query parameters
$baseParams = $queryParams;
unset($baseParams['page_num']); // Remove page_num to add it dynamically

function buildPaginationUrl($pageNum, $params) {
    $params['page_num'] = $pageNum;
    return '/new-stock-system/index.php?' . http_build_query($params);
}
?>

<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <!-- Previous Button -->
        <li class="page-item <?php echo !$hasPrevious ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $hasPrevious ? buildPaginationUrl($currentPage - 1, $queryParams) : '#'; ?>">
                <i class="bi bi-chevron-left"></i> Previous
            </a>
        </li>
        
        <?php
        // Calculate page range to display
        $range = 2; // Pages to show on each side of current page
        $start = max(1, $currentPage - $range);
        $end = min($totalPages, $currentPage + $range);
        
        // First page
        if ($start > 1) {
            ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo buildPaginationUrl(1, $queryParams); ?>">1</a>
            </li>
            <?php if ($start > 2): ?>
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
            <?php endif; ?>
            <?php
        }
        
        // Page numbers
        for ($i = $start; $i <= $end; $i++) {
            ?>
            <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                <a class="page-link" href="<?php echo buildPaginationUrl($i, $queryParams); ?>"><?php echo $i; ?></a>
            </li>
            <?php
        }
        
        // Last page
        if ($end < $totalPages) {
            ?>
            <?php if ($end < $totalPages - 1): ?>
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo buildPaginationUrl($totalPages, $queryParams); ?>"><?php echo $totalPages; ?></a>
            </li>
            <?php
        }
        ?>
        
        <!-- Next Button -->
        <li class="page-item <?php echo !$hasNext ? 'disabled' : ''; ?>">
            <a class="page-link" href="<?php echo $hasNext ? buildPaginationUrl($currentPage + 1, $queryParams) : '#'; ?>">
                Next <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
    
    <div class="text-center text-muted mt-2">
        <small>
            Showing page <?php echo $currentPage; ?> of <?php echo $totalPages; ?> 
            (<?php echo $paginationData['total']; ?> total records)
        </small>
    </div>
</nav>