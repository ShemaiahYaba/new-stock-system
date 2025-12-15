<?php
/**
 * EXTENDED Production Properties List View - WITH ADD-ON SUPPORT
 * File: views/production_properties/index.php
 * REPLACE the existing index.php with this version
 */

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../models/production_property.php';
require_once __DIR__ . '/../../utils/helpers.php';

$pageTitle = 'Production Properties - ' . APP_NAME;

$currentPage = isset($_GET['page_num']) ? (int)$_GET['page_num'] : 1;
$searchQuery = $_GET['search'] ?? '';
$filterType = $_GET['filter_type'] ?? 'all'; // all, production, addon

$propertyModel = new ProductionProperty();

// Build filter conditions
$whereConditions = [];
$params = [];

if (!empty($searchQuery)) {
    $whereConditions[] = '(pp.code LIKE ? OR pp.name LIKE ?)';
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}

if ($filterType === 'production') {
    $whereConditions[] = 'pp.is_addon = 0';
} elseif ($filterType === 'addon') {
    $whereConditions[] = 'pp.is_addon = 1';
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get paginated results
$sql = "SELECT pp.*, u.name as created_by_name 
        FROM production_properties pp
        LEFT JOIN users u ON pp.created_by = u.id
        $whereClause
        ORDER BY pp.is_addon ASC, pp.display_section ASC, pp.category ASC, pp.sort_order ASC 
        LIMIT ? OFFSET ?";

$db = Database::getInstance()->getConnection();
$stmt = $db->prepare($sql);
$allParams = array_merge($params, [(int)RECORDS_PER_PAGE, (int)(($currentPage - 1) * RECORDS_PER_PAGE)]);
$stmt->execute($allParams);
$properties = $stmt->fetchAll();

// Count total
$countSql = "SELECT COUNT(*) as total FROM production_properties pp $whereClause";
$countStmt = $db->prepare($countSql);
$countStmt->execute($params);
$totalProperties = $countStmt->fetch()['total'];

$paginationData = getPaginationData($totalProperties, $currentPage);

require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="page-title">Production Properties & Add-Ons</h1>
                <p class="text-muted">Manage production properties and additional charges</p>
            </div>
            <?php if (hasPermission(MODULE_PRODUCTION_PROPERTIES, ACTION_CREATE)): ?>
            <a href="/new-stock-system/index.php?page=production_properties_create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Property
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Filter Tabs -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="btn-group" role="group">
                        <a href="/new-stock-system/index.php?page=production_properties&filter_type=all" 
                           class="btn btn-sm <?php echo $filterType === 'all' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <i class="bi bi-grid-3x3"></i> All Properties
                        </a>
                        <a href="/new-stock-system/index.php?page=production_properties&filter_type=production" 
                           class="btn btn-sm <?php echo $filterType === 'production' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <i class="bi bi-box-seam"></i> Production Only
                        </a>
                        <a href="/new-stock-system/index.php?page=production_properties&filter_type=addon" 
                           class="btn btn-sm <?php echo $filterType === 'addon' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            <i class="bi bi-plus-circle"></i> Add-Ons Only
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <form method="GET" action="/new-stock-system/index.php" class="d-flex">
                        <input type="hidden" name="page" value="production_properties">
                        <input type="hidden" name="filter_type" value="<?php echo htmlspecialchars($filterType); ?>">
                        <input type="text" name="search" class="form-control form-control-sm me-2" 
                               placeholder="Search properties..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                        <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i></button>
                        <?php if (!empty($searchQuery)): ?>
                        <a href="/new-stock-system/index.php?page=production_properties&filter_type=<?php echo $filterType; ?>" 
                           class="btn btn-sm btn-secondary ms-2">
                            <i class="bi bi-x"></i>
                        </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list-ul"></i> Properties List (<?= $totalProperties ?> total<?= !empty($searchQuery) ? ' - filtered' : '' ?>)
        </div>
        <div class="card-body p-0">
            <?php if (empty($properties)): ?>
            <div class="alert alert-info m-3">
                <i class="bi bi-info-circle"></i> 
                <?php if (!empty($searchQuery)): ?>
                    No properties found matching "<?php echo htmlspecialchars($searchQuery); ?>".
                <?php else: ?>
                    No properties found.
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Section</th>
                            <th>Calculation</th>
                            <th>Default Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($properties as $property): ?>
                        <tr>
                            <td>
                                <?php if ($property['is_addon']): ?>
                                    <?php if ($property['is_refundable']): ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-dash-circle"></i> Refund
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success">
                                            <i class="bi bi-plus-circle"></i> Add-On
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-primary">
                                        <i class="bi bi-box-seam"></i> Production
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><code><?php echo htmlspecialchars($property['code']); ?></code></td>
                            <td><strong><?php echo htmlspecialchars($property['name']); ?></strong></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo STOCK_CATEGORIES[$property['category']] ?? ucfirst($property['category']); ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?php echo DISPLAY_SECTIONS[$property['display_section']] ?? $property['display_section']; ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($property['is_addon']): ?>
                                    <small>
                                        <?php echo CALCULATION_METHODS[$property['calculation_method']] ?? $property['calculation_method']; ?>
                                    </small>
                                <?php else: ?>
                                    <small>
                                        <?php echo PROPERTY_TYPES[$property['property_type']] ?? $property['property_type']; ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($property['calculation_method'] === 'percentage'): ?>
                                    <?php echo number_format($property['default_price'], 2); ?>%
                                <?php else: ?>
                                    <?php echo formatCurrency($property['default_price'] ?? 0); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($property['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $id = $property['id'];
                                $module = 'production_properties';
                                $canView = hasPermission(MODULE_PRODUCTION_PROPERTIES, ACTION_VIEW);
                                $canEdit = hasPermission(MODULE_PRODUCTION_PROPERTIES, ACTION_EDIT);
                                $canDelete = hasPermission(MODULE_PRODUCTION_PROPERTIES, ACTION_DELETE);
                                include __DIR__ . '/../../layout/quick_action_buttons.php';
                                ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        <?php if (!empty($properties)): ?>
        <div class="card-footer">
            <?php $queryParams = $_GET; include __DIR__ . '/../../layout/pagination.php'; ?>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Legend -->
    <div class="card mt-3">
        <div class="card-body">
            <h6 class="mb-3"><i class="bi bi-info-circle"></i> Property Types Legend</h6>
            <div class="row">
                <div class="col-md-4">
                    <span class="badge bg-primary"><i class="bi bi-box-seam"></i> Production</span>
                    <p class="small mb-0">Quantity/meter-based properties for production items</p>
                </div>
                <div class="col-md-4">
                    <span class="badge bg-success"><i class="bi bi-plus-circle"></i> Add-On</span>
                    <p class="small mb-0">Additional charges for services (bending, freight, etc.)</p>
                </div>
                <div class="col-md-4">
                    <span class="badge bg-warning text-dark"><i class="bi bi-dash-circle"></i> Refund</span>
                    <p class="small mb-0">Credits or refunds applied to invoices</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>