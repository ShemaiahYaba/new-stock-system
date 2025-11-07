I'll adjust the plan to better align with the current codebase structure, focusing on modifying existing files and maintaining the current architecture. Here's the revised plan:

## Revised Implementation Plan

### 1. Database Schema (Already Done)

- [x] Verified `status` column in `stock_entries` table
- [x] Created migration to enforce ENUM values

### 2. Model Updates (1 file)

- [ ] **File**: [models/stock_entry.php](cci:7://file:///c:/xampp/htdocs/new-stock-system/models/stock_entry.php:0:0-0:0)
  - Consolidate [getByCoilId](cci:1://file:///c:/xampp/htdocs/new-stock-system/models/stock_entry.php:72:4-101:5) into [getByCoil](cci:1://file:///c:/xampp/htdocs/new-stock-system/models/stock_entry.php:103:4-122:5)
  - Add status filtering
  - Update method signature:
    ```php
    public function getByCoil(
        int $coilId,
        int $limit = 1000,
        int $offset = 0,
        ?string $status = null,
        bool $onlyAvailable = true
    )
    ```

### 3. Controller Updates (2 files)

1. **File**: `controllers/sales/create_available_stock.php`
   - Add validation for stock entry status
   - Create sale record
   - Create invoice
   - Update stock entry

2. **File**: [controllers/sales/create_workflow/index.php](cci:7://file:///c:/xampp/htdocs/new-stock-system/controllers/sales/create_workflow/index.php:0:0-0:0)
   - Add validation for factory_use status
   - Keep existing workflow
   - Ensure proper record creation order

### 4. Frontend Updates (1 file)

- **File**: `views/sales/create_workflow.php`
  - Add status check for stock entries
  - Toggle form fields based on status
  - Update form submission endpoints

### 5. API Endpoints (Use existing with modifications)

- Reuse existing endpoints with status validation
- Add status checks in request handlers

### 6. Testing

- Test both flows with existing test cases
- Verify data consistency
- Test error conditions

### 7. Documentation

- Update inline comments
- Document status-based behavior

### Key Changes from Original Plan:

1. No new controller files
2. Minimal changes to existing structure
3. Focus on modifying existing methods
4. Reuse current routing
