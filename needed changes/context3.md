Quick confirmations

Arithmetic confirmation

(188.6 + 22.8 + 28.5) \* unit_price is only correct if one unit_price applies to all rows (i.e., price per meter is the same across those rows).

If unit price can vary per row (your example shows two mainsheet rows both at 10,300 but rows could differ), compute per-row and sum:

total = Σ (row.meters \* row.unit_price)

In your described UX:

mainsheet rows require price → use per-row unit_price

cladding may be price-less → treat as 0 or handled by compute_rules (e.g., price derived elsewhere)

Actionable: compute_rules/ must define whether price is global-for-selection or per_row. Default: per_row.

Redundancy of the property table

The property metadata table you labeled (Requires Price / Requires Gauge / ... ) is not entirely redundant, but:

With the sheet-multiplier approach the following columns become less necessary at runtime: Requires Meter, Requires Sheet (because sheet multiplies already imply meters).

Keep the table as configuration metadata (in properties/) to define constraints and renderer behaviour (e.g., price_required, multiple_allowed, input_type: sheets|meters|gauge|both).

Actionable: compress that example table into the property config schema (see below).

🧱 Required Models (create only missing ones; reuse existing: customer, sale, stock/coil)

Do NOT recreate: customer, sale, stock / coil.

Create these models (code-level, handled in PHP models; no DB-level procedures):

warehouse (new)

key fields: id, name, location, contact, is_active

used by sale & production selection

production (new)

key fields: id, sale_id, warehouse_id, production_paper (JSON), status, created_by, created_at, immutable_hash

links to sale and coil(s)

invoice (new)

key fields: id, sale_id, production_id|null, invoice_number, invoice_shape (JSON), total, tax, shipping, paid_amount, status, created_at, immutable_hash

receipt (new)

key fields: id, invoice_id, amount_paid, reference, created_by, created_at

supply / delivery (new)

key fields: id, production_id, warehouse_id, status, delivered_at|null, return_requested_at|null, created_at

stock_card_entry (new)

key fields: id, coil_id, production_id|null, sale_id|null, change_type (drawdown/return/etc.), meters_changed, note, created_at

Implementation note: immutable_hash is a code-generated checksum of the record payload to enforce immutability and auditability. All changes create new audit records; do not mutate main record.

🗂 File / Code Strategy (atomic, one file per step / property)

Keep renderer separate (you chose this).

Each step = single controller file; each property input = single property file under the stock type.

Example structure (augmented):

config/production_workflow/stock/alusteel/
├─ properties/
│ ├─ selectMainsheet.php
│ ├─ selectFlatsheet.php
│ └─ selectCladding.php
├─ compute_rules/
│ └─ default.php
├─ workflow/
│ └─ workflow.php // exported flow consumed by sales
└─ renderer/
└─ render_helpers.php

controllers/sales/production/
├─ step_selectWarehouse.php
├─ step_selectCustomer.php
├─ step_selectStock.php
├─ properties/ // per-stock property handlers
│ ├─ alusteel_selectMainsheet.php
│ ├─ alusteel_selectFlatsheet.php
│ └─ alusteel_selectCladding.php
├─ step_confirmProduction.php
├─ step_financialDrawdown.php
├─ step_confirmOrder.php
└─ index.php

Reasoning: this isolates property behavior so anomalies are traceable to one file — not too much modularity for your system.

🔧 Property config schema (single-file example, config/.../properties/selectMainsheet.php)

Each property config must include:

return [
'id' => 'mainsheet',
'label' => 'Mainsheet',
'input_type' => 'sheets', // 'sheets'|'meters'|'gauge'|'both'
'price_required' => true,
'multiple_allowed' => true,
'compute' => [
'multiplier_field' => 'sheet_qty',
'multiplier_value_field' => 'sheet_meter', // e.g., 8.20
'result_field' => 'meters' // computed meters per row
],
'renderer' => 'alusteel/selectMainsheet', // renderer reference
];

🔁 Compute rules (behavioral)

Per-row computation:

row.meters = multiplier_value _ multiplier_count
row.subtotal = row.meters _ (row.unit_price ?? compute_rules.default_price(row))

Overall total:

total = Σ row.subtotal
total_meters = Σ row.meters

If property price_required === false → subtotal uses rule in compute_rules (e.g., price derived from another property or 0).

🔐 Immutability & Admin flow

After confirmOrder, create immutable production and invoice records with immutable_hash.

To delete or alter immutable record → create admin action request:

super_admin_approval flow creates audit entry and either creates corrective reversal records (never mutate original) or marks them superseded.

📄 Data Shape Definitions (explicit — use these in code)

1. Production Paper (production.production_paper JSON)
   {
   "production_reference": "PR-20251105-0001",
   "sale_id": 123,
   "warehouse_id": 2,
   "coil_id": "C157",
   "stock_code": "C157-Alusteel",
   "properties": [
   {
   "property_id": "mainsheet",
   "label": "Mainsheet",
   "sheet_qty": 24,
   "sheet_meter": 8.20,
   "meters": 188.6,
   "unit_price": 10300.00,
   "row_subtotal": 194... // meters * unit_price
   },
   {
   "property_id": "mainsheet",
   "sheet_qty": 1,
   "sheet_meter": 1.20,
   "meters": 1.2,
   "unit_price": 10300.00,
   "row_subtotal": 123600.00
   },
   {
   "property_id": "cladding",
   "sheet_qty": 57,
   "sheet_meter": 0.40,
   "meters": 22.8,
   "price_required": false,
   "row_subtotal": 0.00
   }
   ],
   "total_meters": 212.6,
   "total_amount": // computed by compute_rules, sum(row_subtotal) + other computed rows
   }

2. Invoice shape (invoice.invoice_shape JSON) — view-ready

Use the exact fields you provided; minimal mapping below:

{
"company": {
"logo": "path_or_url",
"banner": "Invoice Banner",
"address": "Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja",
"phone": "+2348065336645",
"email": "admin@obumek360.app"
},
"customer": {
"name": "Stevo Alum",
"phone": "08032218808",
"address": "No 7. Kano street, Area 1, Garki Abuja"
},
"meta": {
"date": "2025-11-05 15:50",
"ref": "#SO-20251105-025252",
"payment_status": "Partial"
},
"items": [
{
"product_code": "BSRT-000001",
"description": "Black Shingle Roofing Tiles light 0.35mm",
"unit_price": 7000.00,
"qty_text": "6.00/SQM",
"subtotal": 45150.00
}
],
"order_tax": 0.00,
"discount": 0.00,
"shipping": 0.00,
"grand_total": 45150.00,
"paid": 20000.00,
"due": 25150.00,
"notes": {
"receipt_statement": "Received the above goods in good condition.",
"refund_policy": "No refund of money after payment",
"amount_in_words": ""
},
"signatures": {
"customer": null,
"for_company": "Obumek Aluminium LTD"
}
}

3. Sale Detail Shape (view-ready)
   {
   "customer_info": {
   "name": "Stevo Alum",
   "phone": "08032218808",
   "address": "No 7. Kano street, Area 1, Garki Abuja"
   },
   "company_info": {
   "name": "Obumek Aluminium",
   "email": "admin@obumek360.app",
   "phone": "+2348065336645",
   "address": "Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja"
   },
   "sale_info": {
   "reference": "SO-20251015-010332",
   "status": "Completed",
   "payment_status": "Partial",
   "date": "2025-10-15 02:00:00",
   "warehouse": "Head Office"
   },
   "order_summary": [
   {
   "product": "BMRT-000001 (Black Milano Roofing Tiles light)",
   "net_unit_price": 5000.00,
   "quantity_text": "50.00 SQM",
   "unit_price": 5000.00,
   "discount": 0.00,
   "tax": 0.00,
   "subtotal": 250000.00
   }
   ],
   "order_tax": 0.00,
   "discount": 0.00,
   "shipping": 0.00,
   "grand_total": 740000.00,
   "paid": 500000.00,
   "due": 240000.00,
   "notes": "Bulk sale from a real estate"
   }

🔗 Links & Actions between modules (procedural rules — code level)

New Sale → triggers production selection page (3 tabs: Production | Invoice | Confirm Order).

On Confirm Production:

create production record (immutable)

deduct meters from coil → create stock_card_entry

On Financial Drawdown / Invoice creation:

create invoice record (immutable)

On Payment:

create receipt record → update invoice.paid_amount and invoice.status

On Production status → Complete:

create supply entry (delivery)

All changes that look like edits create new audit objects (do not mutate original), require Super Admin to delete/override.

✅ Final actionable checklist for dev agent (do these, exactly)

Create warehouse model and controller & add selection step.

Create production, invoice, receipt, supply, stock_card_entry models.

Add config/production_workflow/stock/alusteel folder with subfolders (properties, compute_rules, workflow, renderer).

Implement controller step files under controllers/sales/production/ (one file per step).

Implement property files under controllers/sales/production/properties/ (one per property).

Implement compute_rules/default.php and make per_row price the default.

Wire creation flows:

ConfirmProduction → create immutable production

FinancialDrawdown → create immutable invoice

Receipt triggers → create receipt and update invoice paid_amount

Update sidebar (Sales dropdown) and rename UI labels (Alloy Steel→Alusteel, Ledger→Stock Card).

Enforce immutability with immutable_hash and require Super Admin for deletions/corrections.

Renderer separate from workflow; renderer only reads config/.../renderer references.
