STOCK TAKING SYSTEM

THIS IS A SYSTEM USED BY AN ALUMINIUM COMPANY TO DIGITALISE THEIR STOCK TAKING PROCESS TO ELIMINATE MANUAL PAPER RECORDS

Built strictly with bootstrap and php using MVC architecture

IT IS A MULTITENANT SYSTEM with the data layer being common and accessible to all user roles within the system. So data is shared not isolated across users

A USER MANAGEMENT MODULE EXIST WHERE A SUPER ADMIN WHO CAN CREATE OTHER USERS, ASSIGN DIFFERENT ROLES TO THEM AND EDIT THEIR PERMISSIONS I.E. THE VIEWS AND CONTROLLERS THEY HAVE ACCESS TO
THIS USERMANAGEMENT MODULE IS VERY USER FOCUSED AND USER CENTRIC SO THE SUPER-ADMIN HAS A VERY HIGH FLXIBILITY TO DEFINE A LOT OF PERMISSIONS AND ROLES. SO CONSIDER THIS WHEN YOU'RE APPROACHING THE CODE. SO THAT USERS FEEL MORE IN CONTROL THAN THE SYSTEM BEING IN CONTROL
THERE EXIST A VIEW IN THE USER MANAGEMENT MODULE AND A TABLE OF USERS WITH QUICK ACTION EDIT PERMISSION AND DELETE USER BUTTONS . AND ALSO A COLUMN TO CHANGE USER ROLES.
A VERY STRONG EXAMPLE OF CONTROLLED PERMISSION IS. THE ACCOUNTANT DOESN'T HAVE ACCESS TO SEE THE USER MANAGEMENT MODULE.. HE CAN ONLY SEE THE STOCK MANAGEMENT MODULE. ANOTHER EXAMPLE IS ONLY THE HR DIRECTOR HAS ACCESS TO THIS USER MANAGEMENT MODULE. SO THIS IS HOW I WANT YOU TO APPROACH CREATING THE USER MANAGEMNET MODULE AND CREATING THE CONTROLLER THAT SERVES THE VIEWS TO THE VARIOUS USER ROLES THAT EXIST

A customer module exist where a user can create customer entities which would be reused within the sales module

There is a sales module. The sales modules utilizes the customer definitions and coil stock definitions to prepopulate data for consistency and ease of use and also react to stock status when specify meter levels for coil sales. Coils which have not be pushed to factory use by a said user cannot be sold in rationed meters hence when such coil is selected, the meter specification is locked and fixed since the coil hasn't be pushed to factory use

The stock module is split into three categories which are alloy steel, aluminum and kzinc stocks respoectively.

THE COMPANY'S OPERATIONS ARE AS FOLLOWS
A LIST OF COILS ARE REGISTERED WITH CODE, NAME,COLOR,NET WRIGHT. AND A DEFAULT STATUS OF AVAILABLE
THEN STOCK ENTERIES ARE MADE ON THESE COILS WITH METER SPECIFICATIONS.
THESE STOCKS CAN EITHER BE SOLD OUT IN WHOLESALE WITH THE FIXED METER RATE THAT WAS ENTERED OR THESE STOCKS CAN ALSO BE PUSHED INTO FACTORY USE SO THE METERS CAN BE RATIONED AND SOLD IN RETAIL. SO THERE IS A STOCK STATUS TRACKER AND COTROLLER THAT GIVES THE USER AUTONOMY TO DICTATE STOCK STATUS

I want a very modular and well documented domain driven design for the code structure 1. I want static data centralized in a constants file 2. I want a reuseable db connection layer 3. I want a layout module composing of header,footer,sidebar,reuseable ui items like table struture files,quickactionbuttons,a main file that exports the basic ui skeleton and then an index file that exports everyfile within this module for reuseablility 4. I want modular controllers with clear naming and index file conventions to enforce separation of concerns. All CRUD methods should be created as folders with a single index and all exported at the top layer alongside all other custom controller methods 5. I want A well documented and well implemented central routing controller system 6. A well documented and well implemented auth middleware for route protection 7. I want a db models module for all entities that would exist. And these models whould export reuseable methods 8. All recurrent tasks should be shipped into tiny utility helper files 9. I want a modular views folder structure. With each domain owning it's own modular views 10. Finally my app entry points 11. Db syntax shuold be written in my sql format 12. A migrations tracker folder 13. Very strict separation of concerns with clear intuitive snake naming. For example. A controller file shouldn't perform more than one task at a give time. The files should be nested as folders that export only an index. Then all these indexes should be cnetralised into the top file for that controller

This is an example of what I want my folder structure to look like

## 🧱 Folder Structure

```
stock-system/
│
├── index.php                     # Entry point / router bootstrap
├── login.php                     # Login entry page
├── register.php                  # Registration entry page
├── logout.php                    # Session termination & redirect
│
├── config/
│   ├── db.php                    # Database connection (Supabase via connection string)
│   └── constants.php             # Global constants and enums (colors, sale statuses, etc.)
│
├── controllers/
│   ├── authC.php                 # Authentication controller (login, register)
│   ├── routes.php                # Routing system (handles URL navigation)
│   └── records/
│       ├── CRUD/                 # CRUD logic grouped per record type
│       │   ├── create.php
│       │   ├── read.php
│       │   ├── update.php
│       │   └── delete.php
│       └── index.php             # Central export for all CRUD methods
│
├── layout/
│   ├── header.php
│   ├── footer.php
│   ├── navbar.php
│   ├── sidebar.php
│   ├── table-item.php            # Reusable row component for the stock book
│   └── quick-action-buttons.php # View / Update / Delete buttons component
│
├── models/
│   ├── user.php                  # User model (for authentication)
│   └── record.php                # Record model (represents one stock book entry)
│
├── utils/
│   ├── authMiddleware.php        # Middleware to protect routes
│   └── helpers.php               # Utility functions and formatters
│
└── views/
    ├── auth/
    │   ├── login.php
    │   └── register.php
    ├── dashboard.php
    └── stockbook.php
```

## ⚙️ Conventions & Architecture

### 1. MVC-Inspired Structure

The app loosely follows the **MVC (Model-View-Controller)** pattern:

- **Models**: Represent and manipulate data (`models/`)
- **Controllers**: Contain business logic and CRUD operations (`controllers/`)
- **Views**: Handle presentation (`views/`)
- **Layouts**: Modular UI components (`layout/`)

### 2. Routing Convention

- `index.php` loads `controllers/routes.php`, which maps query parameters (e.g., `?page=stockbook`) to specific views.
- Authentication routes (`login.php`, `register.php`, `logout.php`) act as direct entry points for simplicity.

### 3. CRUD Design

Each CRUD operation for a record is isolated in its own file under:

```
controllers/records/CRUD/
```

Each file has **a single responsibility**, making the system modular and DRY (Don't Repeat Yourself). They are all imported and exported centrally via:

```
controllers/records/index.php
```

### 5. Constants & Enums

Common enums like `COLORS`, `SALE_STATUSES`, etc., are defined in:

```
config/constants.php
```

These constants can be reused across views and controllers to keep logic consistent and prevent hardcoding.

### 6. Middleware

`utils/authMiddleware.php` ensures that protected routes (dashboard, stockbook) are only accessible to authenticated users.
Usage:

```php
require_once './utils/authMiddleware.php';
checkAuth();
``






Short answer: yes — if implemented properly, your structure would execute cleanly and be AI-agent–ready (including for models like Claude Sonnet 4.5). But there are a few refinements you’ll need to make sure the agent interprets, generates, and extends it predictably in a real development workflow.
Let’s break it down practically 👇🏽

🧩 1. Architectural Soundness (✅)
Your structure is already AI-friendly and developer-ready because it’s:
	• Strictly modular — each folder represents a domain, each file a single responsibility.
	• Domain-driven — your modules (stock, sales, customer, user management) are well-defined.
	• Flat where it matters — e.g. all CRUD exports are centralized in an index.php, which is perfect for AI codegen since it avoids recursive confusion.
	• Consistent naming and conventions — clear, snake_case, no overloaded files.
An AI model like Claude 4.5 Sonnet (or GPT-5) will parse and follow your patterns without confusion because:
	• All layers (config, controllers, models, views) are discoverable by convention.
	• There’s a single entry (index.php) and a single routing hub (controllers/routes.php).
	• Middleware and utilities are separated — helps the agent extend logic safely.
✅ Verdict: Clean, modular, and predictable.

⚙️ 2. What Would Make It "Dev-Ready" for AI Agents
These refinements make the structure plug-and-play for AI-assisted builds or auto-extensions (like adding modules, controllers, or DB migrations):
(a) Explicit Bootstrap Order
	AI agents struggle when dependency bootstrapping isn’t explicit.
	Create a clear bootstrap chain:
index.php
  ├── require config/db.php
  ├── require config/constants.php
  ├── require controllers/routes.php

Then ensure each included file doesn’t re-include others recursively.
You can even define a bootstrap.php file that centralizes all requires — so the agent doesn’t get lost in inclusion order.
✅ index.php should only start the router after bootstrapping everything.

(b) Controller Convention Registry
You mentioned exporting all controllers centrally — good.
But to make it agent-navigable, add a small manifest.php per module:
return [
  'create' => __DIR__.'/CRUD/create.php',
  'read'   => __DIR__.'/CRUD/read.php',
  'update' => __DIR__.'/CRUD/update.php',
  'delete' => __DIR__.'/CRUD/delete.php',
];
Then in index.php:
$recordControllers = include __DIR__.'/records/manifest.php';
→ AI can instantly see the module’s full CRUD contract.

(c) Router Design
Agents perform best when the router uses declarative route maps, not conditionals.
✅ Instead of:
if ($_GET['page'] === 'dashboard') { include 'views/dashboard.php'; }
use:
$routes = [
  'dashboard' => 'views/dashboard.php',
  'stockbook' => 'views/stockbook.php',
];
$page = $_GET['page'] ?? 'dashboard';
include $routes[$page] ?? 'views/404.php';
That’s AI-friendly: the model can add, modify, or refactor routes by editing the $routes array — no nested logic to misread.

(d) Strong Naming for Role-Based Access
You’ve defined complex permission logic (e.g., accountant ≠ HR).
Make this config-driven rather than hardcoded inside controllers.
AI agents thrive on data-driven patterns.
✅ Example:
// config/roles.php
return [
  'super_admin' => ['user_management', 'sales', 'stock'],
  'accountant'  => ['sales'],
  'hr_director' => ['user_management'],
];
Then in authMiddleware.php:
$role = $_SESSION['user_role'] ?? null;
$access = include './config/roles.php';
if (!in_array($module, $access[$role] ?? [])) redirect('unauthorized');
Now an AI model can safely expand new roles by updating one file.

(e) Migrations Tracker
Since you asked for a migration tracker, have:
migrations/
  ├── 001_create_users.sql
  ├── 002_create_customers.sql
  ├── 003_create_sales.sql
  └── migration_log.json

If the agent is told to “add a new table,” it can append a file and update migration_log.json.
This makes the repo stateful for automated schema evolution.

1. Refined Domain Logic Rephrases (Copy-Paste Ready)
These are cleaned, consistent, and logic-friendly formulations of your domain rules — written to be unambiguous for implementation or AI reasoning.
🔧 Stock Operations

1. Every coil is registered with: code, name, color, net_weight, and a default status of "AVAILABLE".
2. Stocks are created from coils with defined meter specifications.
3. A stock entry may be:
   - Sold wholesale at its fixed meter specification, OR
   - Pushed to factory use, which enables the coil’s meters to be rationed and sold in retail.
4. When a coil is not yet pushed to factory use (status ≠ "FACTORY_USE"):
   - Its meter specification is locked (immutable).
   - Retail sale options are disabled.
5. Once pushed to factory use (status = "FACTORY_USE"):
   - The coil’s meters can be rationed.
   - Retail sale options are enabled.
6. Stock status must always be explicitly updated through a stock status controller to ensure audit traceability.

🔧 Sales Operations

1. Sales entries reference customer entities and coil/stock definitions.
2. The system should prepopulate sale forms using related customer and stock data for consistency.
3. Sales can only be created for coils that are currently AVAILABLE or in FACTORY_USE.
4. Wholesale sales deduct from full coil meters.
5. Retail sales deduct from factory-use rationed meters.
🔧 User Management / Permissions

1. The system supports multiple user roles under a multi-tenant shared data layer.
2. A Super Admin can:
   - Create users,
   - Assign roles,
   - Define or edit permissions per user.
3. Access control is role-based, enforced at both view and controller level.
4. Each role defines:
   - Which modules the user can see.
   - Which actions (CRUD) they can perform.
5. Examples:
   - Accountant: Access only to Sales module.
   - HR Director: Access to User Management module.
   - Super Admin: Access to all modules.
6. Users should feel in control — permissions must allow granular adjustments by the Super Admin, not hardcoded restrictions.


🧱 2. Data Model Expression of Rules (Copy-Paste Ready)
These are schemas you can hand to an AI or database builder — they communicate the business rules in data terms, not prose.
🗂️ Roles & Permissions Model

// roles.php
return [
  'super_admin' => [
    'modules' => ['user_management', 'stock', 'sales', 'customers', 'reports'],
    'permissions' => ['create', 'read', 'update', 'delete', 'assign_roles']
  ],
  'hr_director' => [
    'modules' => ['user_management'],
    'permissions' => ['read', 'create', 'update']
  ],
  'accountant' => [
    'modules' => ['sales', 'reports'],
    'permissions' => ['create', 'read']
  ],
];

🧱 Core Entity Schema Summary (for context reuse)

// Entity: Coil
{
  id: INT,
  code: VARCHAR,
  name: VARCHAR,
  color: VARCHAR,
  net_weight: DECIMAL(10,2),
  status: ENUM('AVAILABLE', 'FACTORY_USE', 'SOLD'),
  created_at: DATETIME,
  updated_at: DATETIME
}
// Entity: Stock
{
  id: INT,
  coil_id: INT,
  meter_spec: DECIMAL(10,2),
  status: ENUM('LOCKED', 'ACTIVE', 'SOLD_OUT'),
  created_by: INT,
  created_at: DATETIME,
  updated_at: DATETIME
}
// Entity: Sale
{
  id: INT,
  customer_id: INT,
  stock_id: INT,
  type: ENUM('WHOLESALE', 'RETAIL'),
  meters_sold: DECIMAL(10,2),
  amount: DECIMAL(12,2),
  created_by: INT,
  created_at: DATETIME
}
// Entity: User
{
  id: INT,
  name: VARCHAR,
  email: VARCHAR UNIQUE,
  password: VARCHAR,
  role: VARCHAR,
  status: ENUM('ACTIVE', 'DISABLED'),
  created_at: DATETIME
}

⚙️ Access Control Middleware (Reference Implementation)

// utils/authMiddleware.php
function checkAccess($module, $action) {
    $userRole = $_SESSION['role'] ?? null;
    $roles = include './config/roles.php';

    if (!$userRole || !isset($roles[$userRole])) {
        header("Location: ./views/unauthorized.php");
        exit;
    }

    $allowedModules = $roles[$userRole]['modules'];
    $allowedActions = $roles[$userRole]['permissions'];

    if (!in_array($module, $allowedModules) || !in_array($action, $allowedActions)) {
        header("Location: ./views/unauthorized.php");
        exit;
    }
}
```
