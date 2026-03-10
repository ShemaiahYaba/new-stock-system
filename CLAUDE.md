# CLAUDE.md — AI Assistant Guide for Stock Taking System

## Project Overview

A production-ready **stock management system** for Obumek Aluminium Company Ltd. built with PHP/MySQL following an MVC pattern. The system manages coil stock, sales workflows, production, invoicing, tile products, and role-based access control.

---

## Tech Stack

| Layer       | Technology                                    |
|-------------|-----------------------------------------------|
| Backend     | PHP 7.4+, PDO                                 |
| Database    | MySQL 5.7+ / MariaDB 10.4.32                  |
| Frontend    | Bootstrap 5.3, Vanilla JS, Bootstrap Icons    |
| Web Server  | Apache 2.4+ with `mod_rewrite`                |
| CSS         | Bootstrap 5 + custom `assets/css/responsive.css` |

No build tools, no package manager, no Composer. Frontend dependencies are loaded via CDN.

---

## Repository Structure

```
new-stock-system/
├── index.php                  # Main entry point & auth check
├── login.php                  # Login page
├── register.php               # Registration page
├── logout.php                 # Session cleanup
├── stock_system.sql           # Full database dump
├── .htaccess                  # Apache config, security headers
│
├── config/
│   ├── db.php                 # Singleton PDO connection
│   └── constants.php          # All enums, roles, permissions, statuses
│
├── controllers/
│   ├── routes.php             # Central routing (~445 lines)
│   ├── auth/                  # login/, register/
│   ├── coils/                 # create/, update/, delete/
│   ├── colors/
│   ├── customers/
│   ├── invoices/
│   ├── production_properties/
│   ├── sales/                 # create/, create_workflow/, production/, delete/
│   ├── stock_entries/
│   ├── tiles/
│   ├── users/
│   └── warehouses/
│
├── models/                    # 18 model files (~5,934 lines total)
│   ├── user.php               # User CRUD
│   ├── coil.php               # Coil operations
│   ├── sale.php               # Sales workflow
│   ├── stock_entry.php        # Stock tracking
│   ├── production_property.php
│   ├── invoice.php
│   ├── production.php
│   ├── tile_product.php
│   ├── tile_sale.php
│   ├── stock_ledger.php
│   └── ... (see models/ for full list)
│
├── views/                     # PHP templates per module
│   ├── dashboard.php
│   ├── users/, customers/, stock/, sales/
│   ├── production/, invoices/, receipts/
│   ├── colors/, tiles/, warehouses/
│   └── reports/
│
├── layout/
│   ├── header.php             # Navigation bar
│   ├── sidebar.php            # Left menu
│   ├── footer.php
│   ├── pagination.php
│   └── quick_action_buttons.php
│
├── utils/
│   ├── auth_middleware.php    # checkAuth(), hasPermission()
│   └── helpers.php            # sanitize(), generateCsrfToken(), etc.
│
├── assets/
│   ├── css/responsive.css
│   └── js/production/        # property-renderer.js, addon-calculator.js, workflow-manager.js, etc.
│
├── migrations/                # 001_initial_schema.sql … 022_seed_addons.sql
└── docs/                      # Supplementary documentation
```

---

## Database

**Database name:** `obumuvcg_stockdb`

Connection is in `config/db.php` as a Singleton using PDO. Credentials are hardcoded (localhost / root / empty password) — suitable for local dev only.

### Core Tables

| Table | Purpose |
|-------|---------|
| `users` | Accounts with soft delete |
| `user_permissions` | JSON permission map per user |
| `customers` | Customer records |
| `coils` | Stock coils with gauge/color |
| `stock_entries` | Per-coil meter/weight entries |
| `sales` | Sales transactions |
| `invoices` | Invoice records |
| `production` | Production orders |
| `production_properties` | Property definitions for production |
| `receipts` | Payment receipts |
| `colors` | Coil color catalog |
| `designs` | Design templates |
| `warehouses` | Storage locations |
| `supplies` / `supply_delivery` | Supply chain |
| `stock_ledger` | Audit log for stock moves |
| `tile_products` | Tile product catalog |
| `tile_sales` | Tile sales |
| `tile_stock_ledger` | Tile stock audit |

### Migrations

Run migrations in order from `migrations/001_initial_schema.sql` through `migrations/022_seed_addons.sql`. The full current dump is `stock_system.sql`.

---

## Routing System

All HTTP requests flow through `index.php` → `controllers/routes.php`.

**Route map format:**
```php
$routes = [
    'page_key' => [
        'view'   => 'views/module/file.php',
        'module' => MODULE_CONSTANT,
        'action' => ACTION_CONSTANT,
        'is_api' => true,  // optional — returns JSON instead of HTML
    ],
];
```

The active route is determined by the `page` query parameter (`?page=dashboard`). Permission checks are applied per route using `module` + `action`.

---

## Authentication & Authorization

### Auth Middleware (`utils/auth_middleware.php`)

- `checkAuth()` — called on every page load; redirects unauthenticated users to `login.php`
- `hasPermission($module, $action)` — checks the current user's JSON permissions for the given module/action pair
- `requirePermission($module, $action)` — terminates with 403 if permission is absent

### Roles (defined in `config/constants.php`)

| Constant | Role |
|----------|------|
| `ROLE_SUPER_ADMIN` | Full access |
| `ROLE_HR_DIRECTOR` | User management |
| `ROLE_ACCOUNTANT` | View-only stock/sales |
| `ROLE_SALES_MANAGER` | Customers & sales |
| `ROLE_STOCK_MANAGER` | Stock operations |
| `ROLE_VIEWER` | Dashboard read-only |

### Permission Modules & Actions

Modules: `USER_MANAGEMENT`, `CUSTOMER_MANAGEMENT`, `STOCK_MANAGEMENT`, `SALES_MANAGEMENT`, `WAREHOUSE_MANAGEMENT`, `COLOR_MANAGEMENT`, `PRODUCTION_MANAGEMENT`, `INVOICE_MANAGEMENT`, `SUPPLY_MANAGEMENT`, `REPORTS`, `DASHBOARD`, `DESIGN_MANAGEMENT`, `TILE_MANAGEMENT`, `TILE_SALES`, `PRODUCTION_PROPERTIES`

Actions: `ACTION_VIEW`, `ACTION_CREATE`, `ACTION_EDIT`, `ACTION_DELETE`

---

## Key Constants (`config/constants.php`)

```php
APP_NAME              = 'Stock Taking System'
COMPANY_NAME          = 'Obumek Aluminium Company Ltd.'
RECORDS_PER_PAGE      = 10
SESSION_TIMEOUT       = 3600

// Stock statuses
STATUS_AVAILABLE, STATUS_FACTORY_USE, STATUS_SOLD, STATUS_OUT_OF_STOCK

// Stock categories
CATEGORY_ALUSTEEL, CATEGORY_ALUMINUM, CATEGORY_KZINC, CATEGORY_TILE

// Sale types / statuses
SALE_WHOLESALE, SALE_RETAIL
SALE_PENDING, SALE_COMPLETED, SALE_CANCELLED

// Invoice statuses
INVOICE_UNPAID, INVOICE_PARTIAL, INVOICE_PAID, INVOICE_CANCELLED

// Production statuses
PRODUCTION_PENDING, PRODUCTION_IN_PROGRESS, PRODUCTION_COMPLETED, PRODUCTION_CANCELLED

// Property / calculation types
PROPERTY_UNIT_BASED, PROPERTY_METER_BASED, PROPERTY_BUNDLE_BASED
CALC_FIXED, CALC_PERCENTAGE, CALC_PER_UNIT
```

---

## Code Conventions

### PHP Naming

- **Classes (models):** PascalCase — `User`, `StockEntry`, `ProductionProperty`
- **Methods:** camelCase — `create()`, `update()`, `findById()`, `softDelete()`
- **Files:** snake_case — `stock_entry.php`, `auth_middleware.php`
- **Constants:** UPPER_SNAKE_CASE — `ROLE_SUPER_ADMIN`, `ACTION_VIEW`
- **Database tables:** lowercase plural — `stock_entries`, `user_permissions`
- **Database columns:** snake_case — `created_at`, `deleted_at`, `user_id`

### Model Pattern

Every model follows this structure:
```php
class ModelName {
    private $db;
    private $table = 'table_name';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create(array $data): bool { ... }
    public function findById(int $id): ?array { ... }
    public function update(int $id, array $data): bool { ... }
    public function softDelete(int $id): bool { ... }  // sets deleted_at
    public function getAll(int $limit, int $offset): array { ... }
}
```

- Always use **PDO prepared statements** — never interpolate user input into SQL.
- Wrap all DB operations in `try/catch (PDOException $e)` and call `error_log()`.
- Soft deletes use `deleted_at IS NULL` filter on reads.

### Controller Pattern

Controllers are plain PHP files in `controllers/module/action/index.php`:
```php
// Validate CSRF
verifyCsrfToken($_POST['csrf_token']);

// Sanitize input
$name = sanitize($_POST['name']);

// Call model
$model = new ModelName();
$result = $model->create(['name' => $name]);

// Respond
if ($result) {
    $_SESSION['success'] = 'Created successfully.';
} else {
    $_SESSION['error'] = 'Operation failed.';
}
header('Location: ?page=module_list');
exit;
```

### View Pattern

Views are PHP templates included by the router:
```php
<?php
$pageTitle = 'Page Title';
$model = new ModelName();
$records = $model->getAll(RECORDS_PER_PAGE, $offset);

include 'layout/header.php';
include 'layout/sidebar.php';
?>
<!-- Bootstrap HTML -->
<?php include 'layout/footer.php'; ?>
```

- Escape all output with `htmlspecialchars()` or the `sanitize()` helper.
- Use Bootstrap 5 classes exclusively for layout and components.
- Check permissions with `hasPermission()` before rendering action buttons.

### JavaScript (assets/js/production/)

Files use module-like pattern (IIFE or plain objects):
- `property-renderer.js` — renders production property fields
- `property-calculator.js` — calculates property values
- `addon-renderer.js` — renders add-on UI
- `addon-calculator.js` — add-on pricing calculations
- `workflow-manager.js` — coordinates the full sales/production workflow

Conventions: `camelCase` variables, `const`/`let`, no external JS framework.

---

## Security Practices

| Concern | Implementation |
|---------|---------------|
| SQL injection | PDO prepared statements always |
| XSS | `htmlspecialchars()` on all output |
| CSRF | `generateCsrfToken()` / `verifyCsrfToken()` on every POST |
| Authentication | `checkAuth()` in `index.php` before routing |
| Authorization | `hasPermission()` / `requirePermission()` per action |
| Password storage | `password_hash()` with `PASSWORD_DEFAULT` |
| Session security | HTTPOnly cookies, strict mode, 1-hour timeout |
| Sensitive files | `.htaccess` blocks `.env`, `.ini`, `.log`, `.conf` |

---

## Development Workflow

### Local Setup

1. Import `stock_system.sql` into MySQL (database name: `obumuvcg_stockdb`).
2. Point Apache document root at the project directory with `AllowOverride All`.
3. Visit `http://localhost/` — you will be redirected to `login.php`.

### Running Migrations (fresh install)

```bash
mysql -u root obumuvcg_stockdb < migrations/001_initial_schema.sql
# ... through ...
mysql -u root obumuvcg_stockdb < migrations/022_seed_addons.sql
```

### No Build Step

There is no compilation, transpilation, or asset bundling. Edit files and refresh the browser.

### Adding a New Module

1. **Constants** — Add module/role/status constants to `config/constants.php`.
2. **Migration** — Create the next numbered SQL file in `migrations/`.
3. **Model** — Create `models/module_name.php` with the standard CRUD pattern.
4. **Controllers** — Create `controllers/module_name/create/index.php`, `update/`, `delete/` etc.
5. **Views** — Create `views/module_name/index.php`, `create.php`, `edit.php`, `view.php`.
6. **Routes** — Register routes in `controllers/routes.php`.
7. **Sidebar** — Add navigation link in `layout/sidebar.php` with permission guard.
8. **Permissions** — Add default role permissions in `config/constants.php` under `DEFAULT_ROLE_PERMISSIONS`.

### Adding a New Route

In `controllers/routes.php`, add to `$routes`:
```php
'new_route' => [
    'view'   => 'views/module/file.php',
    'module' => MODULE_NEW_CONSTANT,
    'action' => ACTION_VIEW,
],
```

---

## Testing

No automated test framework is configured. Testing is **manual and browser-based**.

- `test_tile_addon.html` — standalone HTML test for tile add-on UI
- `docs/TESTING_GUIDE.md` — manual test scenarios and acceptance criteria
- Use the seeded data in `stock_system.sql` for test data

When verifying a change:
1. Log in with a Super Admin account.
2. Exercise the affected module through the UI.
3. Check `stock_ledger` / `tile_stock_ledger` for correct audit entries.
4. Verify permission restrictions work for a lower-privileged role.

---

## Important Files Quick Reference

| File | Purpose |
|------|---------|
| `config/constants.php` | All enums, roles, permissions — edit here first |
| `config/db.php` | Database connection singleton |
| `controllers/routes.php` | URL routing table |
| `utils/auth_middleware.php` | Auth/permission helpers |
| `utils/helpers.php` | `sanitize()`, `generateCsrfToken()`, pagination, etc. |
| `layout/sidebar.php` | Navigation menu — update when adding modules |
| `stock_system.sql` | Current full database dump |
| `docs/CONTEXT.md` | Original AI context document |

---

## Git Branch

Active development branch: `claude/claude-md-mmkw01siqejtpsrw-XSkfn`
Base branch: `master`
Remote: `http://local_proxy@127.0.0.1:32965/git/ShemaiahYaba/new-stock-system`
