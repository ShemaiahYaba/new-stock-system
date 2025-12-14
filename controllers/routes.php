<?php
/**
 * Central Routing System
 *
 * Maps page requests to appropriate view files
 * Enforces authentication and authorization
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../utils/auth_middleware.php';
require_once __DIR__ . '/../utils/helpers.php';

// Ensure user is authenticated
checkAuth();

// Get requested page
$page = $_GET['page'] ?? 'dashboard';

// Handle API routes
if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
    $apiPath = str_replace('/api/', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    $apiRoute = 'api_' . str_replace('/', '_', trim($apiPath, '/'));

    if (isset($routes[$apiRoute])) {
        $page = $apiRoute;
    }
}

// Define route mappings with required permissions
$routes = [
    // Dashboard
    'dashboard' => [
        'view' => 'views/dashboard.php',
        'module' => MODULE_DASHBOARD,
        'action' => ACTION_VIEW,
    ],

    // User Management
    'users' => [
        'view' => 'views/users/index.php',
        'module' => MODULE_USER_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'users_create' => [
        'view' => 'views/users/create.php',
        'module' => MODULE_USER_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'users_edit' => [
        'view' => 'views/users/edit.php',
        'module' => MODULE_USER_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],
    'users_view' => [
        'view' => 'views/users/view.php',
        'module' => MODULE_USER_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'users_permissions' => [
        'view' => 'views/users/permissions.php',
        'module' => MODULE_USER_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],

    // Customer Management
    'customers' => [
        'view' => 'views/customers/index.php',
        'module' => MODULE_CUSTOMER_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'customers_create' => [
        'view' => 'views/customers/create.php',
        'module' => MODULE_CUSTOMER_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'customers_edit' => [
        'view' => 'views/customers/edit.php',
        'module' => MODULE_CUSTOMER_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],
    'customers_view' => [
        'view' => 'views/customers/view.php',
        'module' => MODULE_CUSTOMER_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Warehouse Management
    'warehouses' => [
        'view' => 'views/warehouses/index.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'warehouses_create' => [
        'view' => 'views/warehouses/create.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'warehouses_edit' => [
        'view' => 'views/warehouses/edit.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],
    'warehouses_view' => [
        'view' => 'views/warehouses/view.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Color Management
    'colors' => [
        'view' => 'views/colors/index.php',
        'module' => MODULE_COLOR_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'colors_create' => [
        'view' => 'views/colors/create.php',
        'module' => MODULE_COLOR_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'colors_edit' => [
        'view' => 'views/colors/edit.php',
        'module' => MODULE_COLOR_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],
    'colors_view' => [
        'view' => 'views/colors/view.php',
        'module' => MODULE_COLOR_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'colors_delete' => [
        'view' => 'views/colors/delete.php',
        'module' => MODULE_COLOR_MANAGEMENT,
        'action' => ACTION_DELETE,
    ],

    // ============================================================
    // PRODUCTION PROPERTIES MANAGEMENT - NEW MODULE
    // ============================================================
    'production_properties' => [
        'view' => 'views/production_properties/index.php',
        'module' => MODULE_PRODUCTION_PROPERTIES,
        'action' => ACTION_VIEW,
    ],
    'production_properties_create' => [
        'view' => 'views/production_properties/create.php',
        'module' => MODULE_PRODUCTION_PROPERTIES,
        'action' => ACTION_CREATE,
    ],
    'production_properties_edit' => [
        'view' => 'views/production_properties/edit.php',
        'module' => MODULE_PRODUCTION_PROPERTIES,
        'action' => ACTION_EDIT,
    ],
    'production_properties_view' => [
        'view' => 'views/production_properties/view.php',
        'module' => MODULE_PRODUCTION_PROPERTIES,
        'action' => ACTION_VIEW,
    ],
    // ============================================================

    // Stock Management - Coils
    'coils' => [
        'view' => 'views/stock/coils/index.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'coils_create' => [
        'view' => 'views/stock/coils/create.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'coils_edit' => [
        'view' => 'views/stock/coils/edit.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],
    'coils_view' => [
        'view' => 'views/stock/coils/view.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Stock Management - Stock Entries
    'stock_entries' => [
        'view' => 'views/stock/entries/index.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'stock_entries_create' => [
        'view' => 'views/stock/entries/create.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'stock_entries_edit' => [
        'view' => 'views/stock/entries/edit.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],
    'stock_entries_view' => [
        'view' => 'views/stock/entries/view.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'stock_ledger' => [
        'view' => 'views/stock/ledger/index.php',
        'module' => MODULE_STOCK_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Sales Management
    'sales' => [
        'view' => 'views/sales/index.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    // New Sale (Production Workflow)
    'sales_create_new' => [
        'view' => 'views/sales/create_workflow.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'sales_create_available' => [
        'view' => 'views/sales/create_available_workflow.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'sales_create' => [
        'view' => 'views/sales/create.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'sales_edit' => [
        'view' => 'views/sales/edit.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],
    'sales_view' => [
        'view' => 'views/sales/view.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'sales_invoice' => [
        'view' => 'views/sales/invoice.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Production Management
    'production' => [
        'view' => 'views/production/index.php',
        'module' => MODULE_PRODUCTION_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'production_view' => [
        'view' => 'views/production/view.php',
        'module' => MODULE_PRODUCTION_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Invoice Management
    'invoices' => [
        'view' => 'views/invoices/index.php',
        'module' => MODULE_INVOICE_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'invoice_view' => [
        'view' => 'views/invoices/view.php',
        'module' => MODULE_INVOICE_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Supply/Delivery Management
    'supply' => [
        'view' => 'views/supply/index.php',
        'module' => MODULE_SUPPLY_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Receipts
    'receipts' => [
        'view' => 'views/receipts/index.php',
        'module' => MODULE_INVOICE_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Reports
    'reports' => [
        'view' => 'views/reports/index.php',
        'module' => MODULE_REPORTS,
        'action' => ACTION_VIEW,
    ],

    // Profile
    'profile' => [
        'view' => 'views/profile.php',
        'module' => MODULE_DASHBOARD,
        'action' => ACTION_VIEW,
    ],

    // Access Denied
    'access_denied' => [
        'view' => 'views/access_denied.php',
        'module' => null,
        'action' => null,
    ],

    // Available Stock Sales
    'sales_available_stock' => [
        'view' => 'views/sales/available_stock/index.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'sales_available_stock_create' => [
        'view' => 'views/sales/available_stock/create.html',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'api_sales_available_stock' => [
        'view' => 'api/sales/available_stock/index.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_CREATE,
        'is_api' => true,
    ],

    // Factory Use Workflow
    'sales_factory_use' => [
        'view' => 'views/sales/factory_use/index.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'sales_factory_use_create' => [
        'view' => 'views/sales/factory_use/create.html',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'api_sales_factory_use' => [
        'view' => 'api/sales/factory_use/index.php',
        'module' => MODULE_SALES_MANAGEMENT,
        'action' => ACTION_CREATE,
        'is_api' => true,
    ],

    // Design Management
    'designs' => [
        'view' => 'views/tiles/designs/index.php',
        'module' => MODULE_DESIGN_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'designs_create' => [
        'view' => 'views/tiles/designs/create.php',
        'module' => MODULE_DESIGN_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'designs_edit' => [
        'view' => 'views/tiles/designs/edit.php',
        'module' => MODULE_DESIGN_MANAGEMENT,
        'action' => ACTION_EDIT,
    ],

    // Tile Products
    'tile_products' => [
        'view' => 'views/tiles/products/index.php',
        'module' => MODULE_TILE_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'tile_products_create' => [
        'view' => 'views/tiles/products/create.php',
        'module' => MODULE_TILE_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'tile_products_view' => [
        'view' => 'views/tiles/products/view.php',
        'module' => MODULE_TILE_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Tile Stock
    'tile_stock' => [
        'view' => 'views/tiles/stock/index.php',
        'module' => MODULE_TILE_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],
    'tile_stock_add' => [
        'view' => 'views/tiles/stock/add.php',
        'module' => MODULE_TILE_MANAGEMENT,
        'action' => ACTION_CREATE,
    ],
    'tile_stock_card' => [
        'view' => 'views/tiles/stock/card.php',
        'module' => MODULE_TILE_MANAGEMENT,
        'action' => ACTION_VIEW,
    ],

    // Tile Sales
    'tile_sales' => [
        'view' => 'views/tiles/sales/index.php',
        'module' => MODULE_TILE_SALES,
        'action' => ACTION_VIEW,
    ],
    'tile_sales_create' => [
        'view' => 'views/tiles/sales/create.php',
        'module' => MODULE_TILE_SALES,
        'action' => ACTION_CREATE,
    ],
    'tile_sales_view' => [
        'view' => 'views/tiles/sales/view.php',
        'module' => MODULE_TILE_SALES,
        'action' => ACTION_VIEW,
    ],
];

// Check if route exists
if (!isset($routes[$page])) {
    // Default to dashboard if route not found
    $page = 'dashboard';
}

$route = $routes[$page];

// Check permissions (except for access_denied page)
if ($route['module'] !== null && !hasPermission($route['module'], $route['action'])) {
    // Redirect to access denied page
    $route = $routes['access_denied'];
}

// Load the view or API endpoint
$viewPath = __DIR__ . '/../' . $route['view'];

if (file_exists($viewPath)) {
    if (isset($route['is_api']) && $route['is_api']) {
        // For API endpoints, set JSON content type
        header('Content-Type: application/json');
    }
    require_once $viewPath;
} else {
    if (isset($route['is_api']) && $route['is_api']) {
        // Return JSON error for API endpoints
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
    } else {
        // Return HTML error for regular pages
        echo "<div class='alert alert-danger'>Page not found: {$page}</div>";
    }
    error_log("View file not found: {$viewPath}");
}