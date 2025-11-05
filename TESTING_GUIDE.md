# Testing Guide - Stock Taking System

## ✅ All Issues Fixed

### Fixed Pages:
1. ✅ `stock_entries` - Stock entries list view created
2. ✅ `customers_create` - Customer creation form created
3. ✅ Coil creation controller - Now properly handles form submission

## 🧪 Test Each Module

### 1. Test Customer Creation

**Steps:**
1. Navigate to: `http://localhost/new-stock-system/index.php?page=customers`
2. Click "Add New Customer"
3. Fill in the form:
   - Name: `John Doe` (required)
   - Email: `john@example.com` (optional)
   - Phone: `08012345678` (required)
   - Company: `ABC Industries` (optional)
   - Address: `123 Main St` (optional)
4. Click "Create Customer"
5. Should redirect to customers list with success message

**Expected Result:**
- ✅ Customer created successfully
- ✅ Appears in customers list
- ✅ Can be used in sales dropdown

---

### 2. Test Coil Creation

**Steps:**
1. Navigate to: `http://localhost/new-stock-system/index.php?page=coils`
2. Click "Add New Coil"
3. Fill in the form:
   - Coil Code: `COL-001` (required)
   - Coil Name: `Premium Steel Coil` (required)
   - Color: Select any color (required)
   - Net Weight: `1500.50` (required)
   - Category: Select category (required)
4. Click "Create Coil"
5. Should redirect to coils list with success message

**Expected Result:**
- ✅ Coil created successfully
- ✅ Appears in coils list
- ✅ Status shows "Available"
- ✅ Can be used in sales

**Common Errors:**
- ❌ "A coil with this code already exists" - Use a different code
- ❌ "Invalid color" - Make sure to select from dropdown
- ❌ "Invalid category" - Make sure to select from dropdown

---

### 3. Test Stock Entries

**Steps:**
1. Navigate to: `http://localhost/new-stock-system/index.php?page=stock_entries`
2. Should see stock entries list (empty initially)
3. Click "Add Stock Entry" (view needs to be created)

**Expected Result:**
- ✅ Page loads without errors
- ✅ Shows empty list message
- ✅ "Add Stock Entry" button visible (if you have permission)

---

### 4. Test Sales Creation (With Customer & Coil)

**Prerequisites:**
- At least 1 customer created
- At least 1 coil created

**Steps:**
1. Navigate to: `http://localhost/new-stock-system/index.php?page=sales_create`
2. Fill in the form:
   - Customer: Select from dropdown
   - Sale Type: `wholesale` or `retail`
   - Coil: Select from dropdown
   - Meters: `100.50`
   - Price per Meter: `50.00`
3. Total should auto-calculate: `₦5,025.00`
4. Click "Create Sale"

**Expected Result:**
- ✅ Sale created successfully
- ✅ Appears in sales list
- ✅ Total amount calculated correctly

---

### 5. Test Reports

**Steps:**
1. Navigate to: `http://localhost/new-stock-system/index.php?page=reports`
2. Should see statistics dashboard

**Expected Result:**
- ✅ Total Sales count
- ✅ Total Revenue
- ✅ Monthly Revenue
- ✅ Total Customers
- ✅ Stock Overview by category
- ✅ Stock Status breakdown

---

## 🔍 Verify All Routes Work

### User Management Routes
- ✅ `/index.php?page=users` - Users list
- ✅ `/index.php?page=users_create` - Create user
- ✅ `/index.php?page=users_view&id=1` - View user
- ✅ `/index.php?page=users_edit&id=1` - Edit user
- ✅ `/index.php?page=users_permissions&id=1` - Manage permissions

### Customer Routes
- ✅ `/index.php?page=customers` - Customers list
- ✅ `/index.php?page=customers_create` - Create customer

### Stock Routes
- ✅ `/index.php?page=coils` - All coils
- ✅ `/index.php?page=coils&category=alloy_steel` - Alloy Steel coils
- ✅ `/index.php?page=coils&category=aluminum` - Aluminum coils
- ✅ `/index.php?page=coils&category=kzinc` - K-Zinc coils
- ✅ `/index.php?page=coils_create` - Create coil
- ✅ `/index.php?page=stock_entries` - Stock entries list

### Sales Routes
- ✅ `/index.php?page=sales` - Sales list
- ✅ `/index.php?page=sales_create` - Create sale

### Other Routes
- ✅ `/index.php?page=dashboard` - Dashboard
- ✅ `/index.php?page=profile` - User profile
- ✅ `/index.php?page=reports` - Reports

---

## 🐛 Troubleshooting

### "Page not found" Error
**Cause:** View file doesn't exist
**Solution:** Check if the view file exists in the correct path

### "404 Not Found" from Apache
**Cause:** Controller file doesn't exist or wrong path
**Solution:** 
1. Check controller path in form action
2. Verify controller file exists
3. Check file permissions

### "Invalid request" Error
**Cause:** CSRF token mismatch
**Solution:** 
1. Clear browser cache
2. Refresh the page
3. Try again

### "Permission denied" Error
**Cause:** User doesn't have required permission
**Solution:**
1. Login as super admin
2. Go to User Management
3. Edit user permissions
4. Grant required permissions

### Database Errors
**Cause:** Missing fields or invalid data
**Solution:**
1. Check error logs in PHP
2. Verify all required fields are filled
3. Check data types match database schema

---

## 📊 Test Data Suggestions

### Test Customers
```
1. John Doe - 08012345678 - ABC Industries
2. Jane Smith - 08098765432 - XYZ Corp
3. Bob Wilson - 08011112222 - Wilson Trading
```

### Test Coils
```
1. COL-001 - Premium Steel Coil - Red - 1500kg - Alloy Steel
2. COL-002 - Aluminum Sheet - Silver - 1200kg - Aluminum
3. COL-003 - Zinc Coated - White - 1800kg - K-Zinc
```

### Test Sales
```
1. John Doe - COL-001 - Wholesale - 100m - ₦50/m = ₦5,000
2. Jane Smith - COL-002 - Retail - 50m - ₦60/m = ₦3,000
3. Bob Wilson - COL-003 - Wholesale - 150m - ₦45/m = ₦6,750
```

---

## ✅ Verification Checklist

### Before Testing
- [ ] XAMPP Apache is running
- [ ] XAMPP MySQL is running
- [ ] Database is created and migrated
- [ ] Logged in as admin

### Core Functionality
- [ ] Can create customers
- [ ] Can create coils
- [ ] Can create sales
- [ ] Can view all lists
- [ ] Search works on all pages
- [ ] Pagination works
- [ ] Quick action buttons work

### Permissions
- [ ] Super admin sees all modules
- [ ] Accountant sees only stock/sales
- [ ] HR Director sees only user management
- [ ] Viewer sees only dashboard

### UI/UX
- [ ] Forms validate properly
- [ ] Flash messages appear
- [ ] Status badges show correct colors
- [ ] Currency formats correctly
- [ ] Dates format correctly
- [ ] Responsive on mobile

---

## 🎯 Next Steps After Testing

1. **Create more test data** to see pagination in action
2. **Test with different user roles** to verify permissions
3. **Try edge cases** (empty fields, invalid data, etc.)
4. **Test search functionality** on all list pages
5. **Verify calculations** in sales module
6. **Check reports** reflect accurate data

---

## 📝 Known Limitations

### Views Not Yet Created:
- Customer edit/view pages
- Coil edit/view pages
- Stock entry create/edit pages
- Sales edit/view pages

### Controllers Not Yet Created:
- Customer update/delete controllers
- Coil update/delete controllers
- Stock entry CRUD controllers
- Sales update/delete controllers

**Note:** These can be created following the same pattern as existing views/controllers.

---

## 🆘 Getting Help

If you encounter issues:

1. **Check PHP error logs** in XAMPP
2. **Check browser console** for JavaScript errors
3. **Verify database** has correct data
4. **Check file permissions** on Windows
5. **Clear browser cache** and try again

---

**Happy Testing! 🚀**
