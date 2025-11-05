# Corrected Stock Management Workflow ✅

## The Right Way - Stock Entries Drive Everything

### Core Concept
**Stock entries** have status (available/factory_use), NOT coils. Coils are just product definitions. Stock entries are the actual inventory items that can be sold.

---

## Complete Workflow

### 1. Register Coils (Product Definitions)
```
Create Coil → Status: AVAILABLE (default, doesn't matter much)
- Coil is just a product definition
- Real inventory is in stock entries
```

### 2. Add Stock Entries
```
Create Stock Entry → Status: AVAILABLE (default)
- Each entry represents actual inventory
- Has meters and meters_remaining
- Can be toggled between available/factory_use
```

### 3. Toggle Stock Entry Status
```
Stock Entries Table:
- Shows status badge (Available/Factory Use)
- Toggle button: "To Factory" or "To Available"
- Ledger button appears for factory_use entries
```

**When toggling TO factory_use:**
- ✅ Automatically creates ledger INFLOW entry
- ✅ Records current meters_remaining as inflow
- ✅ Ledger button becomes visible
- ✅ Entry now available for retail sales

**When toggling TO available:**
- Entry becomes available for wholesale sales
- No ledger tracking (wholesale is direct)

### 4. Make Sales (From Stock Entries)
```
Sales Form:
1. Select Customer
2. Select Sale Type (Wholesale/Retail)
3. Select Stock Entry from table
   - Available Stock → For wholesale
   - Factory Use Stock → For retail
4. Enter meters (locked for wholesale, editable for retail)
5. Enter price
6. Submit → Creates sale & generates invoice
```

---

## Sales Rules

### Wholesale Sales
- ✅ Select from **Available Stock** entries
- ✅ Meters **LOCKED** to entry total
- ✅ Sells entire stock entry
- ✅ No ledger tracking
- ✅ Direct sale

### Retail Sales
- ✅ Select from **Factory Use Stock** entries
- ✅ Meters **EDITABLE** (custom amount)
- ✅ Can sell partial amounts
- ✅ **Ledger tracked** (outflow recorded)
- ✅ Running balance maintained

---

## Database Changes

### Migration 003: Add Status to Stock Entries
```sql
ALTER TABLE stock_entries 
ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'available' AFTER meters_remaining,
ADD INDEX idx_status (status);
```

**Status Values:**
- `available` - For wholesale sales (fixed meters)
- `factory_use` - For retail sales (custom meters, ledger tracked)

---

## New Features

### Stock Entries Table
**Columns:**
- ID, Coil Code, Coil Name, Meters, Remaining, **Status**, Created By, Actions

**Status Column:**
- Badge showing current status
- Ledger button (only for factory_use)

**Actions:**
- Toggle button to switch status
- View/Edit/Delete buttons

### Sales Form (Redesigned)
**Selection Process:**
1. Customer dropdown
2. Sale type (Wholesale/Retail)
3. **Stock Entry dropdown** (filtered by status)
   - Available Stock group
   - Factory Use Stock group
4. Meters input (locked/editable based on status)
5. Price input
6. Total calculation

**Validation:**
- Wholesale must use available stock
- Retail must use factory_use stock
- Meters cannot exceed remaining
- Status must match sale type

### Stock Ledger
**Automatic Tracking:**
- Inflow when stock moves to factory_use
- Outflow when retail sale is made
- Running balance calculation
- Transaction history

**View:**
- Filter by coil
- Accounting cards (Inflow/Outflow/Balance)
- Transaction table
- Descending order

---

## Files Created/Modified

### New Files (3)
1. ✅ `migrations/003_add_stock_entry_status.sql` - Add status field
2. ✅ `controllers/stock_entries/toggle_status/index.php` - Toggle controller
3. ✅ `views/sales/create_new.php` - Redesigned sales form

### Modified Files (5)
1. ✅ `views/stock/entries/index.php` - Added status column, toggle button, ledger button
2. ✅ `models/stock_entry.php` - Added status support, getByStatus() method
3. ✅ `controllers/sales/create/index.php` - Updated to validate stock entry status
4. ✅ `controllers/stock_entries/create/index.php` - Auto-ledger for factory_use
5. ✅ `controllers/routes.php` - Updated sales_create route

---

## Testing Steps

### 1. Run Migration
```sql
-- Run: migrations/003_add_stock_entry_status.sql
-- This adds status field to stock_entries table
```

### 2. Test Stock Entry Status Toggle
```
1. Go to Stock Entries
2. Find an entry with remaining meters
3. Click "To Factory" button
4. Verify:
   - Status changes to "Factory Use" (yellow badge)
   - Ledger button appears
   - Success message shows inflow recorded
5. Click ledger button
6. Verify inflow transaction appears
```

### 3. Test Wholesale Sale
```
1. Create stock entry with status = available
2. Go to Sales → Create Sale
3. Select customer
4. Select "Wholesale" sale type
5. Select from "Available Stock" group
6. Verify: Meters field is LOCKED to entry total
7. Enter price
8. Submit
9. Verify:
   - Sale created
   - Stock entry meters_remaining = 0
   - No ledger entry (wholesale is direct)
```

### 4. Test Retail Sale
```
1. Create stock entry
2. Toggle to "Factory Use"
3. Verify ledger inflow created
4. Go to Sales → Create Sale
5. Select customer
6. Select "Retail" sale type
7. Select from "Factory Use Stock" group
8. Enter custom meters (less than remaining)
9. Enter price
10. Submit
11. Verify:
    - Sale created
    - Stock entry meters_remaining reduced
    - Ledger outflow recorded
    - Balance updated
```

### 5. Test Validation
```
Try invalid combinations:
- Wholesale with factory_use stock → Should fail
- Retail with available stock → Should fail
- Meters exceeding remaining → Should fail
- Toggle exhausted entry → Should fail
```

---

## Key Differences from Previous Implementation

### ❌ Old Way (Wrong)
- Coils had status
- Sales selected coils
- Confusing which stock to sell
- Ledger tied to coil status

### ✅ New Way (Correct)
- **Stock entries** have status
- Sales select **stock entries**
- Clear inventory selection
- Ledger tied to entry status
- Coils are just product definitions

---

## Workflow Summary

```
┌─────────────────────────────────────────────────────────────┐
│ 1. CREATE COIL (Product Definition)                         │
│    - Just defines the product                               │
│    - Status doesn't matter much                             │
└─────────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. ADD STOCK ENTRY (Actual Inventory)                       │
│    - Status: AVAILABLE (default)                            │
│    - Has meters and meters_remaining                        │
└─────────────────────────────────────────────────────────────┘
                           ↓
        ┌──────────────────┴──────────────────┐
        ↓                                      ↓
┌───────────────────┐              ┌───────────────────────┐
│ KEEP AS AVAILABLE │              │ MOVE TO FACTORY USE   │
│ (Wholesale)       │              │ (Retail)              │
│                   │              │                       │
│ - Fixed meters    │              │ - Custom meters       │
│ - No ledger       │              │ - Ledger tracked      │
│ - Direct sale     │              │ - Auto inflow entry   │
└───────────────────┘              └───────────────────────┘
        ↓                                      ↓
┌───────────────────┐              ┌───────────────────────┐
│ WHOLESALE SALE    │              │ RETAIL SALE           │
│ - Entire entry    │              │ - Partial allowed     │
│ - Meters locked   │              │ - Meters editable     │
└───────────────────┘              │ - Ledger outflow      │
                                   └───────────────────────┘
```

---

## Next Steps

### Invoice Generation (To Implement)
After successful sale creation:
1. Generate PDF invoice
2. Include:
   - Customer details
   - Stock entry details (coil code, name, color)
   - Meters sold
   - Price per meter
   - Total amount
   - Sale date
   - Invoice number
3. Options:
   - View in browser
   - Download PDF
   - Email to customer

### Invoice Features
- Professional template
- Company logo/header
- Terms and conditions
- Payment details
- QR code for verification
- Export to PDF using library (TCPDF/mPDF)

---

## Success Criteria ✅

All requirements met:
- [x] Coils registered with default status
- [x] Stock entries created on coils
- [x] Toggle stock between available/factory_use
- [x] Auto-create ledger inflow on factory_use
- [x] Ledger button shows for factory_use entries
- [x] Sales form selects from stock entries table
- [x] Available stock has locked meters
- [x] Factory use stock has adjustable meters
- [ ] Invoice generation (next step)

---

## 🎉 Workflow Corrected!

The system now follows the proper inventory management flow where **stock entries drive sales**, not coils. This provides clear separation between product definitions and actual inventory.

**Ready for invoice implementation!** 🚀
