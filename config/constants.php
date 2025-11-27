<?php
/**
 * Global Constants and Enumerations
 *
 * Centralized static data for the stock taking system
 * All enums, statuses, and reusable constants are defined here
 */

// Company Information
define('INVOICE_COMPANY_NAME', 'Obumek Alluminium Company Ltd.');
define('INVOICE_COMPANY_ADDRESS', 'Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja');
define('INVOICE_COMPANY_PHONE', '+2348065336645');
define('INVOICE_COMPANY_EMAIL', 'info@obumekalluminium.com');

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
    ROLE_VIEWER => 'Viewer',
];

// Stock Status
define('STOCK_STATUS_AVAILABLE', 'available');
define('STOCK_STATUS_FACTORY_USE', 'factory_use');
define('STOCK_STATUS_SOLD', 'sold');
define('STOCK_STATUS_OUT_OF_STOCK', 'out_of_stock');

// Stock Status Array
const STOCK_STATUSES = [
    STOCK_STATUS_AVAILABLE => 'Available',
    STOCK_STATUS_FACTORY_USE => 'Factory Use',
    STOCK_STATUS_SOLD => 'Sold',
    STOCK_STATUS_OUT_OF_STOCK => 'Out of stock',
];

// Stock Categories
define('STOCK_CATEGORY_ALUSTEEL', 'alusteel');
define('STOCK_CATEGORY_ALLOY_STEEL', STOCK_CATEGORY_ALUSTEEL); // backward compatibility
define('STOCK_CATEGORY_ALUMINUM', 'aluminum');
define('STOCK_CATEGORY_KZINC', 'kzinc');

// Stock Categories Array
const STOCK_CATEGORIES = [
    STOCK_CATEGORY_ALUSTEEL => 'Alusteel',
    STOCK_CATEGORY_ALUMINUM => 'Aluminum',
    STOCK_CATEGORY_KZINC => 'K-Zinc',
];

// Sale Types
define('SALE_TYPE_WHOLESALE', 'wholesale');
define('SALE_TYPE_RETAIL', 'retail');

// Sale Types Array
const SALE_TYPES = [
    SALE_TYPE_WHOLESALE => 'Wholesale',
    SALE_TYPE_RETAIL => 'Retail',
];

// Sale Status
define('SALE_STATUS_PENDING', 'pending');
define('SALE_STATUS_COMPLETED', 'completed');
define('SALE_STATUS_CANCELLED', 'cancelled');

// Sale Status Array
const SALE_STATUSES = [
    SALE_STATUS_PENDING => 'Pending',
    SALE_STATUS_COMPLETED => 'Completed',
    SALE_STATUS_CANCELLED => 'Cancelled',
];

// Production Status - NEW
define('PRODUCTION_STATUS_PENDING', 'pending');
define('PRODUCTION_STATUS_IN_PROGRESS', 'in_progress');
define('PRODUCTION_STATUS_COMPLETED', 'completed');
define('PRODUCTION_STATUS_CANCELLED', 'cancelled');

const PRODUCTION_STATUSES = [
    PRODUCTION_STATUS_PENDING => 'Pending',
    PRODUCTION_STATUS_IN_PROGRESS => 'In Progress',
    PRODUCTION_STATUS_COMPLETED => 'Completed',
    PRODUCTION_STATUS_CANCELLED => 'Cancelled',
];

// Invoice Status - NEW
define('INVOICE_STATUS_UNPAID', 'unpaid');
define('INVOICE_STATUS_PARTIAL', 'partial');
define('INVOICE_STATUS_PAID', 'paid');
define('INVOICE_STATUS_CANCELLED', 'cancelled');

const INVOICE_STATUSES = [
    INVOICE_STATUS_UNPAID => 'Unpaid',
    INVOICE_STATUS_PARTIAL => 'Partial',
    INVOICE_STATUS_PAID => 'Paid',
    INVOICE_STATUS_CANCELLED => 'Cancelled',
];

// Supply/Delivery Status - NEW
define('SUPPLY_STATUS_PENDING', 'pending');
define('SUPPLY_STATUS_SUPPLIED', 'supplied');
define('SUPPLY_STATUS_RETURNED', 'returned');

const SUPPLY_STATUSES = [
    SUPPLY_STATUS_PENDING => 'Pending',
    SUPPLY_STATUS_SUPPLIED => 'Supplied',
    SUPPLY_STATUS_RETURNED => 'Returned',
];

// Coil Colors
const COIL_COLORS = [
    'IBeige' => 'I/Beige',
    'PGreen' => 'P/Green',
    'SBlue' => 'S/Blue',
    'TBlack' => 'T/Black',
    'TCRed' => 'TC/Red',
    'GBeige' => 'G/Beige',
    'BGreen' => 'B/Green',
    'IWhite' => 'I/White',
];

// Permission Modules
define('MODULE_USER_MANAGEMENT', 'user_management');
define('MODULE_CUSTOMER_MANAGEMENT', 'customer_management');
define('MODULE_STOCK_MANAGEMENT', 'stock_management');
define('MODULE_SALES_MANAGEMENT', 'sales_management');
define('MODULE_WAREHOUSE_MANAGEMENT', 'warehouse_management');
// Add Color Management Module
define('MODULE_COLOR_MANAGEMENT', 'color_management');
define('MODULE_PRODUCTION_MANAGEMENT', 'production_management'); // NEW
define('MODULE_INVOICE_MANAGEMENT', 'invoice_management'); // NEW
define('MODULE_SUPPLY_MANAGEMENT', 'supply_management'); // NEW
define('MODULE_REPORTS', 'reports');
define('MODULE_DASHBOARD', 'dashboard');
define('MODULE_DESIGN_MANAGEMENT', 'design_management'); // NEW
define('MODULE_TILE_MANAGEMENT', 'tile_management'); // NEW
define('MODULE_TILE_SALES', 'tile_sales'); // NEW

// Permission Modules Array
const PERMISSION_MODULES = [
    MODULE_USER_MANAGEMENT => 'User Management',
    MODULE_CUSTOMER_MANAGEMENT => 'Customer Management',
    MODULE_STOCK_MANAGEMENT => 'Stock Management',
    MODULE_SALES_MANAGEMENT => 'Sales Management',
    MODULE_WAREHOUSE_MANAGEMENT => 'Warehouse Management',
    MODULE_COLOR_MANAGEMENT => 'Color Management', // NEW
    MODULE_PRODUCTION_MANAGEMENT => 'Production Management', // NEW
    MODULE_INVOICE_MANAGEMENT => 'Invoice Management', // NEW
    MODULE_SUPPLY_MANAGEMENT => 'Supply Management', // NEW
    MODULE_REPORTS => 'Reports',
    MODULE_DASHBOARD => 'Dashboard',
    MODULE_DESIGN_MANAGEMENT => 'Design Management',
    MODULE_TILE_MANAGEMENT => 'Tile Management',
    MODULE_TILE_SALES => 'Tile Sales',
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
    ACTION_DELETE => 'Delete',
];

// Default Permissions by Role
const DEFAULT_PERMISSIONS = [
    ROLE_SUPER_ADMIN => [
        MODULE_USER_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_CUSTOMER_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_SALES_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_WAREHOUSE_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_PRODUCTION_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE], // NEW
        MODULE_INVOICE_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE], // NEW
        MODULE_SUPPLY_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE], // NEW
        MODULE_REPORTS => [ACTION_VIEW],
        MODULE_DASHBOARD => [ACTION_VIEW],
        MODULE_COLOR_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE], // NEW  
        MODULE_DESIGN_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_TILE_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_TILE_SALES => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        
    ],
    ROLE_STOCK_MANAGER => [
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_WAREHOUSE_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_PRODUCTION_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_REPORTS => [ACTION_VIEW],
        MODULE_DASHBOARD => [ACTION_VIEW],
        MODULE_COLOR_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT], // NEW
        MODULE_DESIGN_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT],
        MODULE_TILE_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_TILE_SALES => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT],

    ],
    ROLE_HR_DIRECTOR => [
        MODULE_USER_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE],
        MODULE_DASHBOARD => [ACTION_VIEW],
    ],
    ROLE_ACCOUNTANT => [
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW],
        MODULE_SALES_MANAGEMENT => [ACTION_VIEW],
        MODULE_REPORTS => [ACTION_VIEW],
        MODULE_DASHBOARD => [ACTION_VIEW],
    ],
    ROLE_SALES_MANAGER => [
        MODULE_CUSTOMER_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT],
        MODULE_SALES_MANAGEMENT => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT],
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW],
        MODULE_DASHBOARD => [ACTION_VIEW],
    ],
    ROLE_VIEWER => [
        MODULE_DASHBOARD => [ACTION_VIEW],
        MODULE_STOCK_MANAGEMENT => [ACTION_VIEW],
        MODULE_SALES_MANAGEMENT => [ACTION_VIEW],
    ],
];

// Alusteel Properties - NEW
const ALUSTEEL_PROPERTIES = [
    'mainsheet' => 'Mainsheet',
    'flatsheet' => 'Flatsheet',
    'cladding' => 'Cladding',
];

// Payment Methods - NEW
const PAYMENT_METHODS = [
    'cash' => 'Cash',
    'bank_transfer' => 'Bank Transfer',
    'cheque' => 'Cheque',
    'pos' => 'POS',
];

// Pagination
define('RECORDS_PER_PAGE', 10);

// Date Format
define('DATE_FORMAT', 'Y-m-d H:i:s');
define('DATE_DISPLAY_FORMAT', 'd/m/Y');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 30 minutes in seconds

// Module Permissions
if (!defined('MODULE_SALES')) {
    define('MODULE_SALES', 'sales');
}
if (!defined('MODULE_STOCK')) {
    define('MODULE_STOCK', 'stock');
}
if (!defined('MODULE_CUSTOMERS')) {
    define('MODULE_CUSTOMERS', 'customers');
}
if (!defined('MODULE_USERS')) {
    define('MODULE_USERS', 'users');
}
if (!defined('MODULE_COLORS')) {
    define('MODULE_COLORS', 'colors');
}

// Action Permissions
if (!defined('ACTION_VIEW')) {
    define('ACTION_VIEW', 'view');
}
if (!defined('ACTION_CREATE')) {
    define('ACTION_CREATE', 'create');
}
if (!defined('ACTION_EDIT')) {
    define('ACTION_EDIT', 'edit');
}
if (!defined('ACTION_DELETE')) {
    define('ACTION_DELETE', 'delete');
}

// Application Settings
define('APP_NAME', 'Stock Taking System');
define('APP_VERSION', '1.0.0');
define('COMPANY_NAME', 'Obumek Aluminium Company Ltd.');

// Tile Management Constants
const TILE_GAUGES = [
    'thick' => 'Thick',
    'normal' => 'Normal',
    'light' => 'Light'
];

const TILE_STOCK_STATUS = [
    'available' => 'Available',
    'out_of_stock' => 'Out of Stock'
];

const TILE_TRANSACTION_TYPES = [
    'stock_in' => 'Stock In',
    'sale' => 'Sale',
    'adjustment' => 'Adjustment',
    'return' => 'Return'
];

const TILE_SALE_STATUS = [
    'pending' => 'Pending',
    'completed' => 'Completed',
    'cancelled' => 'Cancelled'
];