# Stock Taking System

A comprehensive multi-tenant stock management system for an aluminium company, built with PHP and Bootstrap using MVC architecture.

## Features

- **Multi-tenant Architecture**: Shared data layer accessible to all user roles
- **User Management**: Role-based access control with flexible permissions
- **Customer Management**: Create and manage customer entities
- **Stock Management**: Track coils across three categories (Alloy Steel, Aluminum, K-Zinc)
- **Sales Module**: Handle wholesale and retail sales with meter tracking
- **Reports**: Generate insights and analytics
- **Responsive Design**: Modern UI with Bootstrap 5

## System Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP (recommended for local development)

## Installation

### 1. Clone or Extract Files

Place the `new-stock-system` folder in your `htdocs` directory (for XAMPP):
```
c:/xampp/htdocs/new-stock-system/
```

### 2. Database Setup

1. Start MySQL server (via XAMPP Control Panel)
2. Open phpMyAdmin or MySQL client
3. Run the migration script:
   ```sql
   source c:/xampp/htdocs/new-stock-system/migrations/001_initial_schema.sql
   ```

### 3. Configure Database Connection

Edit `config/db.php` if needed to match your MySQL credentials:
```php
private $host = 'localhost';
private $db_name = 'stock_system';
private $username = 'root';
private $password = '';
```

### 4. Start the Application

1. Start Apache and MySQL in XAMPP Control Panel
2. Navigate to: `http://localhost/new-stock-system/`
3. You'll be redirected to the login page

### 5. Default Login Credentials

```
Email: admin@example.com
Password: admin123
```

**⚠️ IMPORTANT: Change the default password after first login!**

## Folder Structure

```
new-stock-system/
│
├── config/
│   ├── db.php                    # Database connection
│   └── constants.php             # Global constants and enums
│
├── controllers/
│   ├── routes.php                # Central routing system
│   └── auth/                     # Authentication controllers
│       ├── login/
│       └── register/
│
├── models/
│   ├── user.php                  # User model
│   ├── customer.php              # Customer model
│   ├── coil.php                  # Coil model
│   ├── stock_entry.php           # Stock entry model
│   └── sale.php                  # Sale model
│
├── views/
│   ├── dashboard.php             # Main dashboard
│   ├── users/                    # User management views
│   ├── customers/                # Customer management views
│   ├── stock/                    # Stock management views
│   └── sales/                    # Sales management views
│
├── layout/
│   ├── header.php                # Common header
│   ├── footer.php                # Common footer
│   ├── sidebar.php               # Navigation sidebar
│   ├── quick_action_buttons.php  # Reusable action buttons
│   └── pagination.php            # Pagination component
│
├── utils/
│   ├── auth_middleware.php       # Authentication & authorization
│   └── helpers.php               # Utility functions
│
├── migrations/
│   └── 001_initial_schema.sql    # Database schema
│
├── index.php                     # Main entry point
├── login.php                     # Login page
├── register.php                  # Registration page
└── logout.php                    # Logout handler
```

## User Roles & Permissions

### Available Roles

1. **Super Admin**: Full system access
2. **HR Director**: User management access
3. **Accountant**: View-only access to stock and sales
4. **Sales Manager**: Customer and sales management
5. **Stock Manager**: Stock management
6. **Viewer**: Read-only dashboard access

### Permission Modules

- User Management
- Customer Management
- Stock Management
- Sales Management
- Reports
- Dashboard

### Permission Actions

- View
- Create
- Edit
- Delete

## Key Concepts

### Stock Status Flow

1. **Available**: Initial status when coil is registered
2. **Factory Use**: Coil pushed to factory for meter rationing (retail sales)
3. **Sold**: Coil completely sold
4. **Reserved**: Coil reserved for specific customer

### Sale Types

- **Wholesale**: Fixed meter rate from stock entry
- **Retail**: Rationed meters from factory-use coils

### Stock Categories

- Alloy Steel
- Aluminum
- K-Zinc

## Usage Guide

### Creating a New User

1. Navigate to **User Management** (requires permission)
2. Click **Add New User**
3. Fill in user details and assign role
4. Set custom permissions if needed

### Managing Stock

1. **Register Coil**: Create coil with code, name, color, weight, category
2. **Add Stock Entry**: Specify meter quantities for the coil
3. **Update Status**: Change coil status (Available → Factory Use)
4. **Track Meters**: System automatically tracks remaining meters

### Processing Sales

1. **Select Customer**: Choose from existing customers or create new
2. **Select Coil**: Pick coil from available stock
3. **Specify Meters**: 
   - Wholesale: Fixed meters from stock entry
   - Retail: Custom meters (only for factory-use coils)
4. **Set Price**: Enter price per meter
5. **Complete Sale**: System updates stock automatically

## Security Features

- Password hashing (bcrypt)
- CSRF protection
- Session management with timeout
- SQL injection prevention (PDO prepared statements)
- XSS protection (input sanitization)
- Role-based access control
- Soft deletes for data integrity

## Development Notes

### Adding New Routes

Edit `controllers/routes.php`:
```php
'new_page' => [
    'view' => 'views/new_page.php',
    'module' => MODULE_NAME,
    'action' => ACTION_VIEW
]
```

### Creating New Models

Follow the pattern in existing models:
- Extend base functionality
- Use PDO for database operations
- Implement CRUD methods
- Add soft delete support

### Custom Permissions

Permissions can be customized per user via the User Management module, overriding default role permissions.

## Troubleshooting

### Database Connection Error

- Verify MySQL is running
- Check credentials in `config/db.php`
- Ensure database exists

### Permission Denied

- Check user role and permissions
- Verify session is active
- Clear browser cache/cookies

### Session Timeout

- Default timeout: 1 hour
- Adjust in `config/constants.php`:
  ```php
  define('SESSION_TIMEOUT', 3600);
  ```

## Future Enhancements

- Export reports to PDF/Excel
- Email notifications
- Inventory alerts
- Advanced analytics dashboard
- Mobile app integration
- Barcode scanning
- Multi-language support

## Support

For issues or questions:
- Check the documentation
- Review error logs in PHP error log
- Contact system administrator

## License

Proprietary - © 2024 Aluminium Company Ltd.

## Version

1.0.0 - Initial Release
