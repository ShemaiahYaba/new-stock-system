<?php
/**
 * Global Constants and Enumerations
 * 
 * Centralized static data for the stock taking system
 * All enums, statuses, and reusable constants are defined here
 */

// User Roles
define('ROLE_SUPER_ADMIN', 'super_admin');
define('ROLE_HR_DIRECTOR', 'hr_director');
define('ROLE_ACCOUNTANT', 'accountant');
define('ROLE_SALES_MANAGER', 'sales_manager');
define('ROLE_STOCK_MANAGER', 'stock_manager');
define('ROLE_VIEWER', 'viewer');

// User Roles Array
const USER_ROLES = [
    ROLE_SUPER_ADMIN => 'Super Admin',
    ROLE_HR_DIRECTOR => 'HR Director',
    ROLE_ACCOUNTANT => 'Accountant',
    ROLE_SALES_MANAGER => 'Sales Manager',
    ROLE_STOCK_MANAGER => 'Stock Manager',
    ROLE_VIEWER => 'Viewer'
];

// Stock Status
define('STOCK_STATUS_AVAILABLE', 'available');
define('STOCK_STATUS_FACTORY_USE', 'factory_use');
define('STOCK_STATUS_SOLD', 'sold');

// Stock Status Array
const STOCK_STATUSES = [
    STOCK_STATUS_AVAILABLE => 'Available',
    STOCK_STATUS_FACTORY_USE => 'Factory Use',
    STOCK_STATUS_SOLD => 'Sold',
];

// Stock Categories
define('STOCK_CATEGORY_ALLOY_STEEL', 'alloy_steel');
define('STOCK_CATEGORY_ALUMINUM', 'aluminum');
define('STOCK_CATEGORY_KZINC', 'kzinc');

// Stock Categories Array
const STOCK_CATEGORIES = [
    STOCK_CATEGORY_ALLOY_STEEL => 'Alloy Steel',
    STOCK_CATEGORY_ALUMINUM => 'Aluminum',
    STOCK_CATEGORY_KZINC => 'K-Zinc'
];

// Sale Types
define('SALE_TYPE_WHOLESALE', 'wholesale');
define('SALE_TYPE_RETAIL', 'retail');

// Sale Types Array
const SALE_TYPES = [
    SALE_TYPE_WHOLESALE => 'Wholesale',
    SALE_TYPE_RETAIL => 'Retail'
];

// Sale Status
define('SALE_STATUS_PENDING', 'pending');
define('SALE_STATUS_COMPLETED', 'completed');
define('SALE_STATUS_CANCELLED', 'cancelled');

// Sale Status Array
const SALE_STATUSES = [
    SALE_STATUS_PENDING => 'Pending',
    SALE_STATUS_COMPLETED => 'Completed',
    SALE_STATUS_CANCELLED => 'Cancelled'
];

// Coil Colors
const COIL_COLORS = [
    'red' => 'Red',
    'blue' => 'Blue',
    'green' => 'Green',
    'yellow' => 'Yellow',
    'black' => 'Black',
    'white' => 'White',
    'silver' => 'Silver',
    'grey' => 'Grey',
    'brown' => 'Brown',
    'orange' => 'Orange',
    'custom' => 'Custom'
];

// Permission Modules
define('MODULE_USER_MANAGEMENT', 'user_management');
define('MODULE_CUSTOMER_MANAGEMENT', 'customer_management');
define('MODULE_STOCK_MANAGEMENT', 'stock_management');
define('MODULE_SALES_MANAGEMENT', 'sales_management');
define('MODULE_REPORTS', 'reports');
define('MODULE_DASHBOARD', 'dashboard');

// Permission Modules Array
const PERMISSION_MODULES = [
    MODULE_USER_MANAGEMENT => 'User Management',
    MODULE_CUSTOMER_MANAGEMENT => 'Customer Management',
    MODULE_STOCK_MANAGEMENT => 'Stock Management',
    MODULE_SALES_MANAGEMENT => 'Sales Management',
    MODULE_REPORTS => 'Reports',
    MODULE_DASHBOARD => 'Dashboard'
];

// Permission Actions
define('ACTION_VIEW', 'view');
define('ACTION_CREATE', 'create');
define('ACTION_EDIT', 'edit');
define('ACTION_DELETE', 'delete');

// Permission Actions Array
const PERMISSION_ACTIONS = [
    ACTION_VIEW => 'View',
    ACTION_CREATE => 'Create',
    ACTION_EDIT => 'Edit',
    ACTION_DELETE => 'Delete'
];

// Default Permissions by Role
const DEFAULT_PERMISSIONS = [
    ROLE_SUPER_ADMIN => [
        MODULE_USER_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_CUSTOMER_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_SALES_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_REPORTS => [ACTION_VIEW],
        MODULE_DASHBOARD => [ACTION_VIEW]
    ],
    ROLE_HR_DIRECTOR => [
        MODULE_USER_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_DASHBOARD => [ACTION_VIEW]
    ],
    ROLE_ACCOUNTANT => [
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW],
        MODULE_SALES_MANAGEMENT => [ACTION_VIEW],
        MODULE_REPORTS => [ACTION_VIEW],
        MODULE_DASHBOARD => [ACTION_VIEW]
    ],
    ROLE_SALES_MANAGER => [
        MODULE_CUSTOMER_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT],
        MODULE_SALES_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT],
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW],
        MODULE_DASHBOARD => [ACTION_VIEW]
    ],
    ROLE_STOCK_MANAGER => [
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_DASHBOARD => [ACTION_VIEW]
    ],
    ROLE_VIEWER => [
        MODULE_DASHBOARD => [ACTION_VIEW],
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW],
        MODULE_SALES_MANAGEMENT => [ACTION_VIEW]
    ]
];

// Pagination
define('RECORDS_PER_PAGE', 20);

// Date Format
define('DATE_FORMAT', 'Y-m-d H:i:s');
define('DATE_DISPLAY_FORMAT', 'd/m/Y');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Application Settings
define('APP_NAME', 'Stock Taking System');
define('APP_VERSION', '1.0.0');
define('COMPANY_NAME', 'Obumek Aluminium Company Ltd.');
