# 🔧 Production Workflow Specification (Revised)

### ✅ Renaming

- **Alloy Steel → Alusteel**
- **Ledger Entries → Stock Card**
  - i.e. “View Ledger” → “View Stock Card”

---

## ⚙️ Workflow Overview

The **production workflow config** is a folder-based system.
Each **stock type** has its own file for configuration to ensure clean management and atomicity.

**Trigger:**
The workflow is initiated when the user clicks **New Sale**.

**Sidebar Update (Sales menu):**

- Sales
- Production
- Supply / Delivery
- Invoices
- Receipts

All listed records are **immutable** to maintain data integrity.

---

## 🧾 Functional Breakdown

### **1. Sales**

- Displays all logged sales.
- **Quick actions:**
  - View Production
  - View Invoice
  - View Receipts

---

### **2. Production**

- Displays all **production requests** triggered from sales.
- Tracks production status (start → finish).
- **Quick action:** View → shows detailed production and status.
- **Immutable:** once entered, cannot be modified directly.

---

### **3. Delivery / Supply**

- Tracks all production entries that have or have not been supplied.
- **Status flow:**
  - `Pending → Supplied → Returned (if applicable)`

- Supply records are immutable; return-to-factory is only possible if status = Supplied.

---

### **4. Invoices**

- Tracks generated invoices and payment installments.
- **Immutable.**
- Has a **Trigger Payment** utility.
- When payment is made → generates a **Receipt**.

---

### **5. Receipts**

- Tracks every payment receipt generated.
- One invoice can generate multiple receipt instances (per installment).

---

## 🧭 Sale Creation Flow (UI Flow)

When the user clicks **New Sale**, the workflow opens with **three tabs**:

1. **Production**
2. **Invoice**
3. **Confirm Order**

Flow:
Production → Invoice → Confirm Order

---

### **Tab 1: Production**

#### Step 1: Customer & Warehouse Selection

- Requires the new **Warehouse entity** (see model section below).

#### Step 2: Stock Selection

- Pulls available stocks (as in current sales utility).

#### Case Rules:

**Case 1 — “Available” Stocks:**

- Cannot be used for production workflow.
- Can only be sold at wholesale.
- Meter value locked (important UX note).
- User inputs _price per meter_, system computes total → data passed to invoice tab.

**Case 2 — “Factory Use” Stocks:**

- Usable for production workflow.

---

### 🧩 Example: Stock `C157 Alusteel`

When user selects stock `C157 Alusteel`:

**UX behavior:**

- Dropdowns appear for:
  - Color
  - Gauge
  - Property (contextual to stock type)

**Property Selection Example:**

- User selects property: **Mainsheet**
  - Input boxes appear for sheet count and meter per sheet.
  - E.g. `24 sheets * 8.20 = 188.6 meters` → displayed.
  - User inputs unit price `₦10,300` → system computes total.

**Additional Property Rows:**

- User clicks **+** to add another property line.

Example Sequence:

1. Mainsheet → 24 sheets × 8.20 = 188.6
2. Mainsheet → 1 sheet × 1.20 = 1.2
3. Cladding → 57 sheets × 0.40 = 22.8
4. Cladding → 57 sheets × 0.50 = 28.5

**Arithmetic confirmation:**

```
(188.6 + 22.8 + 28.5) * unit_price
```

→ valid **only if one unit price applies to all**.
If price differs by row →

```
total = Σ(row.meters * row.unit_price)
```

- _Mainsheet_ rows → require price
- _Cladding_ rows → price optional (handled in compute rules)

Each property’s behavior (price required, gauge dependency, etc.) is defined in the **property config**.

---

## 🗂 Config Folder Structure

```
config/
 └─ production_workflow/
     └─ stock/
         └─ alusteel/
             ├─ properties/
             │   ├─ selectMainsheet.php
             │   ├─ selectFlatsheet.php
             │   └─ selectCladding.php
             ├─ compute_rules/
             │   └─ default.php
             ├─ workflow/
             │   └─ workflow.php
             └─ renderer/
                 └─ render_helpers.php
```

**Note:**
Keep `renderer` **separate** from the workflow engine to simplify debugging and reuse.

---

## 🧱 Models to Create (Code-Level Only)

Do **not** recreate `customer`, `sale`, or `coil`.

Create these:

| Model                 | Key Fields                                                                                                                              |
| --------------------- | --------------------------------------------------------------------------------------------------------------------------------------- |
| **warehouse**         | id, name, location, contact, is_active                                                                                                  |
| **production**        | id, sale_id, warehouse_id, production_paper (JSON), status, created_by, created_at, immutable_hash                                      |
| **invoice**           | id, sale_id, production_id, invoice_number, invoice_shape (JSON), total, tax, shipping, paid_amount, status, created_at, immutable_hash |
| **receipt**           | id, invoice_id, amount_paid, reference, created_by, created_at                                                                          |
| **supply / delivery** | id, production_id, warehouse_id, status, delivered_at, return_requested_at, created_at                                                  |
| **stock_card_entry**  | id, coil_id, production_id, sale_id, change_type, meters_changed, note, created_at                                                      |

> `immutable_hash` = checksum for immutability and audit verification.

---

## 🧩 Property Config Schema (Example: `selectMainsheet.php`)

```php
return [
  'id' => 'mainsheet',
  'label' => 'Mainsheet',
  'input_type' => 'sheets', // sheets|meters|gauge|both
  'price_required' => true,
  'multiple_allowed' => true,
  'compute' => [
    'multiplier_field' => 'sheet_qty',
    'multiplier_value_field' => 'sheet_meter',
    'result_field' => 'meters',
  ],
  'renderer' => 'alusteel/selectMainsheet',
];
```

---

## ⚖️ Compute Rules

**Per-row:**

```
row.meters = sheet_qty * sheet_meter
row.subtotal = row.meters * (row.unit_price ?? compute_rules.default_price(row))
```

**Overall totals:**

```
total_meters = Σ(row.meters)
total_amount = Σ(row.subtotal)
```

Properties with `price_required = false` defer pricing logic to compute_rules.

---

## 🧩 Controller File Strategy

Each step = one PHP controller file.

```
controllers/sales/production/
 ├─ step_selectWarehouse.php
 ├─ step_selectCustomer.php
 ├─ step_selectStock.php
 ├─ properties/
 │   ├─ alusteel_selectMainsheet.php
 │   ├─ alusteel_selectFlatsheet.php
 │   └─ alusteel_selectCladding.php
 ├─ step_confirmProduction.php
 ├─ step_financialDrawdown.php
 ├─ step_confirmOrder.php
 └─ index.php
```

Rationale:
If one property misbehaves, you can isolate and debug that file — **not too modular**, just enough.

---

## 🧾 Confirm Order (Tab 3)

Two-column layout:

- **Left:** Production Paper
- **Right:** Invoice

User can navigate back to either tab to edit values.
Edits trigger recalculation.
On confirm, system prompts user that record is **immutable once logged**.
Deleting requires **Super Admin confirmation**.

---

## 🔄 Immutable Logic & Status Flow

- Confirm Order → creates immutable Production + Invoice
- Deduct coil meters → log Stock Card entry
- Mark Production → Complete → creates Supply record
- Mark Supply → Supplied → may trigger Return (factory)
- Invoice → Receipt trigger → generate Receipt, update paid amount
- All “edits” = new immutable versions (audit-tracked)

---

## 🧭 Developer Action Checklist

- [ ] Rename Alloy Steel → Alusteel
- [ ] Rename Ledger → Stock Card
- [ ] Create models: Warehouse, Production, Invoice, Receipt, Supply, StockCardEntry
- [ ] Build folder `config/production_workflow/stock/alusteel`
- [ ] Implement property configs and compute rules
- [ ] Separate Renderer from Workflow
- [ ] Implement controller steps (one per stage)
- [ ] Apply `immutable_hash` logic to enforce record immutability
- [ ] Update Sales sidebar to include dropdowns (Sales / Production / Delivery / Invoices / Receipts)
- [ ] Make all these modules immutable, deletions by Super Admin only
