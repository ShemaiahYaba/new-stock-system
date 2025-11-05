# 🚀 Quick Start Guide - Stock Taking System

## ⚡ 2-Minute Setup

### Option 1: Automatic Installation (Recommended)

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Click "Start" for Apache
   - Click "Start" for MySQL

2. **Run Installer**
   - Open browser: `http://localhost/new-stock-system/install.php`
   - Check all boxes
   - Click "Install Now"
   - **Delete `install.php` after installation!**

3. **Login**
   - Go to: `http://localhost/new-stock-system/`
   - Email: `admin@example.com`
   - Password: `admin123`

4. **Done!** 🎉

---

### Option 2: Manual Installation

1. **Start XAMPP**
   - Start Apache and MySQL

2. **Create Database**
   - Open: `http://localhost/phpmyadmin`
   - Click "SQL" tab
   - Copy contents of `migrations/001_initial_schema.sql`
   - Click "Go"

3. **Login**
   - Go to: `http://localhost/new-stock-system/`
   - Email: `admin@example.com`
   - Password: `admin123`

4. **Done!** 🎉

---

## 🎯 First Steps After Login

### 1. Change Admin Password (CRITICAL!)
Currently, you need to do this via database:
```sql
UPDATE users 
SET password = '$2y$10$YOUR_NEW_HASHED_PASSWORD' 
WHERE id = 1;
```

Or create a new super admin and delete the default one.

### 2. Create Your First User
1. Click "User Management" in sidebar
2. Click "Add New User"
3. Fill in details
4. Select role
5. Click "Create User"

### 3. Test Permissions
1. Logout
2. Login with new user
3. Notice different menu items based on role

### 4. Explore Dashboard
- View statistics
- Check quick links
- Navigate modules

---

## 📱 User Interface Tour

### Navigation
- **Top Bar**: Logo, user menu, logout
- **Sidebar**: Permission-based menu
- **Content Area**: Main workspace

### Key Pages
- **Dashboard**: Overview and statistics
- **User Management**: Manage users and permissions
- **Customers**: (Ready to implement)
- **Stock Management**: (Ready to implement)
- **Sales**: (Ready to implement)
- **Reports**: (Ready to implement)

---

## 👥 Understanding Roles

### Super Admin
- **Access**: Everything
- **Use Case**: System administrator

### HR Director
- **Access**: User management only
- **Use Case**: HR department

### Accountant
- **Access**: View stock and sales
- **Use Case**: Financial oversight

### Sales Manager
- **Access**: Customers and sales
- **Use Case**: Sales team lead

### Stock Manager
- **Access**: Stock management
- **Use Case**: Warehouse manager

### Viewer
- **Access**: Dashboard only
- **Use Case**: Read-only users

---

## 🔧 Common Tasks

### Create a User
```
1. User Management → Add New User
2. Enter name, email, password
3. Select role
4. Submit
```

### Delete a User
```
1. User Management → Users List
2. Find user
3. Click trash icon
4. Confirm
```

### Search Users
```
1. User Management → Users List
2. Type in search box
3. Click search icon
```

### View Your Profile
```
1. Click your name (top right)
2. Select "Profile"
```

### Logout
```
1. Click your name (top right)
2. Select "Logout"
```

---

## 🐛 Troubleshooting

### Can't Login?
- ✅ Check MySQL is running
- ✅ Verify database exists
- ✅ Use correct credentials
- ✅ Clear browser cache

### Page Not Found?
- ✅ Check Apache is running
- ✅ Verify URL is correct
- ✅ Check file permissions

### Access Denied?
- ✅ Check your role
- ✅ Verify permissions
- ✅ Contact admin

### Session Expired?
- ✅ Login again
- ✅ Session timeout is 1 hour

---

## 📚 Next Steps

### For Developers
1. Read `SETUP_GUIDE.md` for detailed setup
2. Read `PROJECT_SUMMARY.md` for architecture
3. Check `README.md` for features
4. Review code comments

### For Users
1. Familiarize with dashboard
2. Test different roles
3. Explore available modules
4. Report any issues

### For Administrators
1. Create user accounts
2. Assign appropriate roles
3. Customize permissions
4. Monitor system usage

---

## 🎓 Learning Path

### Day 1: Basics
- ✅ Login/Logout
- ✅ Navigate dashboard
- ✅ View profile

### Day 2: User Management
- ✅ Create users
- ✅ Assign roles
- ✅ Test permissions

### Day 3: Customization
- ✅ Understand permissions
- ✅ Explore modules
- ✅ Plan extensions

---

## 💡 Pro Tips

### Security
- 🔒 Always change default passwords
- 🔒 Use strong passwords (8+ characters)
- 🔒 Logout when done
- 🔒 Don't share credentials

### Performance
- ⚡ Clear browser cache regularly
- ⚡ Close unused tabs
- ⚡ Use latest browser version

### Best Practices
- ✨ Create users with minimal required permissions
- ✨ Test changes in development first
- ✨ Keep documentation updated
- ✨ Regular database backups

---

## 🆘 Getting Help

### Resources
- 📖 `README.md` - General overview
- 📖 `SETUP_GUIDE.md` - Detailed setup
- 📖 `PROJECT_SUMMARY.md` - Technical details
- 📖 Code comments - Inline documentation

### Support Channels
- Check error logs in PHP
- Review browser console
- Contact system administrator
- Refer to documentation

---

## ✅ Checklist

### Installation
- [ ] XAMPP installed
- [ ] Apache running
- [ ] MySQL running
- [ ] Database created
- [ ] Can access login page
- [ ] Can login successfully

### Post-Installation
- [ ] Changed admin password
- [ ] Created test user
- [ ] Tested different roles
- [ ] Explored dashboard
- [ ] Deleted install.php

### Security
- [ ] Default password changed
- [ ] Install.php deleted
- [ ] File permissions set
- [ ] .htaccess configured
- [ ] Error reporting disabled (production)

---

## 🎉 You're Ready!

Your Stock Taking System is now set up and ready to use!

**What's Working:**
- ✅ User authentication
- ✅ Role-based access
- ✅ User management
- ✅ Permission system
- ✅ Dashboard
- ✅ Search & pagination

**What to Build Next:**
- 📝 Customer management views
- 📝 Stock management views
- 📝 Sales management views
- 📝 Reports module

Follow the patterns in existing code to add new features!

---

**Need More Help?**
- Read the full documentation
- Check code examples
- Review inline comments
- Contact your administrator

**Happy Managing! 🚀**
