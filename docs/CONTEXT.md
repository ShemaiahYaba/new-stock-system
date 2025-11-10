### ⚙️ **SYSTEM EXTENSION GUIDE — STOCK SYSTEM AI REFERENCE**

#### 🧠 Purpose

This document defines how AI models should interpret, extend, or modify modules in the Stock Taking System without deviating from its existing architecture or domain logic.

---

### 🏗️ **1. Architectural Mindset**

- Always **reuse** the existing folder conventions:

  ```
  controllers/{module}/CRUD/
  models/{entity}.php
  views/{module}/...
  ```

- Each CRUD file performs **only one task** (Create, Read, Update, Delete).
- All CRUD files are re-exported via `controllers/{module}/index.php`.
- **No direct DB queries in views.** Views only render data passed from controllers.
- **No hardcoded permissions.** All access rules must reference `/config/roles.php`.

---

### 📚 **2. Domain Principles**

- Coils and Stocks are **core domain entities**.
- All new modules should:

  - Fit within the current business flow.
  - Maintain shared data visibility (multi-tenant).
  - Follow existing naming and CRUD conventions.

---

### 🔒 **3. When Extending Existing Modules**

When modifying or extending a module (e.g., Sales, Stock, User Management):

1. Identify which **controller layer** needs the change.
2. If it’s a new action, create a folder under:

   ```
   controllers/{module}/custom/
   ```

   Example: `controllers/stock/custom/changeStatus.php`

3. Export it via that module’s `index.php`.
4. Ensure that:

   - Validation logic lives in the model.
   - Routing is updated in `controllers/routes.php`.
   - Access is checked via `authMiddleware`.

---

### 🧱 **4. When Creating New Modules**

For a new domain (e.g., “Suppliers” or “Invoices”):

1. Create structure:

   ```
   controllers/suppliers/CRUD/
   models/supplier.php
   views/suppliers/
   ```

2. Implement the CRUDs in isolation.
3. Add module route in `controllers/routes.php`:

   ```php
   'suppliers' => 'views/suppliers/index.php',
   ```

4. Register role access in `config/roles.php` if needed.
5. Use existing layout components from `/layout/`.
6. Add SQL schema in `/migrations/` with the next version number.

---

### 🧩 **5. Reasoning Guide for AI Agents**

When reasoning about new logic:

- Infer patterns from **existing modules**, not from generic PHP practices.
- Prioritize **simplicity and reusability**.
- Follow “copy-modify-reuse” rather than “rebuild”.
- Always keep:

  - Data consistency.
  - Domain integrity (no sales without stock).
  - Role security (no controller access outside permitted roles).

---

### 💡 **6. Decision Hierarchy**

When unsure:

1. Check if similar functionality exists elsewhere → reuse pattern.
2. If not, create a minimal, isolated CRUD folder → export it.
3. Update router + roles.
4. Test with sample input from existing modules.
5. Never alter system-wide constants or DB schemas without creating a new migration.

---

### ✅ **7. Example Directive (AI Prompt Usage)**

If you want to add a new module:

```text
Goal: Add a Supplier Management Module.
Context: Suppliers provide raw coils to the company.
Scope: CRUD operations only.
Rules: Reuse the same MVC + routing structure, integrate permissions for Super Admin, and generate a migration file.
Follow the System Extension Guide. Keep logic minimal and consistent.
```
