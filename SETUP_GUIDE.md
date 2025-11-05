# Stock Taking System - Setup Guide

## Quick Start (5 Minutes)

### Step 1: Database Setup

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL**

2. **Create Database**
   - Open browser and go to: `http://localhost/phpmyadmin`
   - Click "SQL" tab
   - Copy and paste the entire contents of `migrations/001_initial_schema.sql`
   - Click "Go" to execute

### Step 2: Access the Application

1. Open browser and navigate to:
   ```
   http://localhost/new-stock-system/
   ```

2. You'll be redirected to the login page

3. **Login with default credentials:**
   ```
   Email: admin@example.com
   Password: admin123
   ```

4. **IMPORTANT:** Change the default password immediately after first login!

## Complete System Overview

### What You Have

✅ **Fully Functional MVC Application**
- Clean, modular architecture
- Domain-driven design
- Separation of concerns

✅ **User Management System**
- Role-based access control (RBAC)
- 6 predefined roles with customizable permissions
- User CRUD operations
- Permission management per user

✅ **Authentication & Security**
- Secure login/registration
- Password hashing (bcrypt)
- CSRF protection
- Session management with timeout
- SQL injection prevention
- XSS protection

✅ **Database Models**
- User model
- Customer model
- Coil model
- Stock Entry model
- Sale model

✅ **Layout Components**
- Responsive header
- Dynamic sidebar with permission-based menu
- Footer
- Reusable action buttons
- Pagination component

✅ **Routing System**
- Central routing controller
- Permission-based route protection
- Clean URL structure

✅ **Helper Utilities**
- Authentication middleware
- Input sanitization
- Date formatting
- Flash messages
- CSRF token generation
- And more...

## File Structure Explained

```
new-stock-system/
│
├── config/                          # Configuration files
│   ├── db.php                       # Database connection (Singleton pattern)
│   └── constants.php                # All constants, roles, permissions, enums
│
├── controllers/                     # Business logic
│   ├── routes.php                   # Central routing system
│   ├── auth/                        # Authentication controllers
│   │   ├── login/index.php         # Login handler
│   │   └── register/index.php      # Registration handler
│   └── users/                       # User management controllers
│       ├── create/index.php        # Create user
│       └── delete/index.php        # Delete user
│
├── models/                          # Data layer
│   ├── user.php                     # User database operations
│   ├── customer.php                 # Customer database operations
│   ├── coil.php                     # Coil database operations
│   ├── stock_entry.php             # Stock entry database operations
│   └── sale.php                     # Sale database operations
│
├── views/                           # Presentation layer
│   ├── dashboard.php                # Main dashboard
│   ├── access_denied.php            # 403 page
│   └── users/                       # User management views
│       ├── index.php               # Users list
│       └── create.php              # Create user form
│
├── layout/                          # Reusable UI components
│   ├── header.php                   # Common header
│   ├── footer.php                   # Common footer
│   ├── sidebar.php                  # Navigation sidebar
│   ├── quick_action_buttons.php    # Action buttons component
│   └── pagination.php               # Pagination component
│
├── utils/                           # Utility functions
│   ├── auth_middleware.php          # Authentication & authorization
│   └── helpers.php                  # Helper functions
│
├── migrations/                      # Database migrations
│   └── 001_initial_schema.sql      # Initial database schema
│
├── index.php                        # Main entry point
├── login.php                        # Login page
├── register.php                     # Registration page
├── logout.php                       # Logout handler
├── README.md                        # Documentation
└── SETUP_GUIDE.md                  # This file
```

## How to Extend the System

### Adding a New Module (Example: Products)

#### 1. Create Model (`models/product.php`)
```php
<?php
require_once __DIR__ . '/../config/db.php';

class Product {
    private $db;
    private $table = 'products';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    // Add CRUD methods here
}
```

#### 2. Add Routes (`controllers/routes.php`)
```php
'products' => [
    'view' => 'views/products/index.php',
    'module' => MODULE_STOCK_MANAGEMENT,
    'action' => ACTION_VIEW
],
```

#### 3. Create View (`views/products/index.php`)
```php
<?php
require_once __DIR__ . '/../../layout/header.php';
require_once __DIR__ . '/../../layout/sidebar.php';
?>

<div class="content-wrapper">
    <!-- Your content here -->
</div>

<?php require_once __DIR__ . '/../../layout/footer.php'; ?>
```

#### 4. Create Controller (`controllers/products/create/index.php`)
```php
<?php
session_start();
require_once __DIR__ . '/../../../utils/auth_middleware.php';

requirePermission(MODULE_STOCK_MANAGEMENT, ACTION_CREATE);

// Handle form submission
```

### Adding a New Permission Module

Edit `config/constants.php`:

```php
// Add new module constant
define('MODULE_NEW_MODULE', 'new_module');

// Add to modules array
const PERMISSION_MODULES = [
    // ... existing modules
    MODULE_NEW_MODULE => 'New Module Name'
];

// Add to default permissions
const DEFAULT_PERMISSIONS = [
    ROLE_SUPER_ADMIN => [
        // ... existing permissions
        MODULE_NEW_MODULE => [ACTION_VIEW, ACTION_CREATE, ACTION_EDIT, ACTION_DELETE]
    ],
];
```

## Common Tasks

### Change Database Credentials

Edit `config/db.php`:
```php
private $host = 'localhost';      // Your host
private $db_name = 'stock_system'; // Your database name
private $username = 'root';        // Your username
private $password = '';            // Your password
```

### Add New User Role

Edit `config/constants.php`:
```php
define('ROLE_NEW_ROLE', 'new_role');

const USER_ROLES = [
    // ... existing roles
    ROLE_NEW_ROLE => 'New Role Name'
];

const DEFAULT_PERMISSIONS = [
    ROLE_NEW_ROLE => [
        MODULE_DASHBOARD => [ACTION_VIEW],
        // Add more permissions
    ]
];
```

### Customize Session Timeout

Edit `config/constants.php`:
```php
define('SESSION_TIMEOUT', 7200); // 2 hours in seconds
```

### Change Records Per Page

Edit `config/constants.php`:
```php
define('RECORDS_PER_PAGE', 50); // Show 50 records per page
```

## Testing the System

### 1. Test Authentication
- ✅ Login with default credentials
- ✅ Try invalid credentials
- ✅ Register new user
- ✅ Logout and login again
- ✅ Test session timeout (wait 1 hour)

### 2. Test User Management
- ✅ Create new user with different roles
- ✅ Edit user details
- ✅ Try to delete your own account (should fail)
- ✅ Delete another user
- ✅ Search for users

### 3. Test Permissions
- ✅ Login as different roles
- ✅ Verify sidebar shows only permitted modules
- ✅ Try accessing unauthorized pages (should redirect to access denied)

## Troubleshooting

### Issue: "Database connection failed"
**Solution:**
- Ensure MySQL is running in XAMPP
- Check database credentials in `config/db.php`
- Verify database exists: `SHOW DATABASES;` in phpMyAdmin

### Issue: "Page not found"
**Solution:**
- Check if view file exists in `views/` folder
- Verify route is defined in `controllers/routes.php`
- Check file permissions

### Issue: "Access Denied" for Super Admin
**Solution:**
- Check if permissions are set in database:
  ```sql
  SELECT * FROM user_permissions WHERE user_id = 1;
  ```
- Re-run migration script if needed

### Issue: Session expires too quickly
**Solution:**
- Increase `SESSION_TIMEOUT` in `config/constants.php`
- Check PHP session settings in `php.ini`

### Issue: CSRF token error
**Solution:**
- Clear browser cookies
- Ensure session is started before form submission
- Check if `generateCsrfToken()` is called in form

## Next Steps

### Immediate Tasks
1. ✅ Change default admin password
2. ✅ Create additional users with different roles
3. ✅ Test all permission levels
4. ✅ Customize company name and branding

### Development Tasks
1. 📝 Complete customer module views and controllers
2. 📝 Complete stock module views and controllers
3. 📝 Complete sales module views and controllers
4. 📝 Add reports functionality
5. 📝 Add export to PDF/Excel
6. 📝 Add email notifications
7. 📝 Add audit logging

### Production Deployment
1. 🔒 Change all default credentials
2. 🔒 Enable HTTPS
3. 🔒 Set proper file permissions
4. 🔒 Configure backups
5. 🔒 Set up error logging
6. 🔒 Optimize database indexes
7. 🔒 Enable production error handling

## Support & Resources

### Documentation
- **README.md**: General overview and features
- **SETUP_GUIDE.md**: This file - setup and extension guide
- **Code Comments**: Every file has detailed comments

### Code Standards
- PSR-12 coding standards
- Descriptive variable names
- Single responsibility principle
- DRY (Don't Repeat Yourself)

### Security Best Practices
- Never commit credentials to version control
- Always validate and sanitize user input
- Use prepared statements for database queries
- Implement proper error handling
- Keep dependencies updated

## Congratulations! 🎉

Your Stock Taking System is now ready to use. The foundation is solid, modular, and ready for extension. Happy coding!

---

**Version:** 1.0.0  
**Last Updated:** 2024  
**Author:** Stock System Development Team
