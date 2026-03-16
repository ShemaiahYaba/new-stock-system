-- ============================================================
-- MIGRATION 023: KZinc Unit Tracking (Pallets / Bundles / Pieces)
-- ============================================================
-- KZinc stock arrives as pallets of bundles of sheets, not meters.
-- This migration adds:
--   coils.pallet_size          -- bundles per pallet (varies per coil: 85, 92, 112)
--   stock_entries.unit_type    -- meters | bundles | pieces | pallets
--   stock_entries.quantity     -- original entered quantity in unit_type
--   stock_entries.pieces_total -- computed total pieces (KZinc only)
--   stock_entries.pieces_remaining -- remaining pieces after sales (KZinc only)
--   sales.unit_type            -- unit in which quantity was sold
--   sales.quantity             -- quantity sold in that unit (KZinc only)
-- ============================================================

-- 1. Pallet configuration on coils (KZinc only, nullable for other categories)
ALTER TABLE coils
  ADD COLUMN pallet_size INT NULL
    COMMENT 'KZinc only: bundles per pallet (e.g. 85, 92, 112)'
    AFTER gauge;

-- 2. Unit tracking on stock entries
ALTER TABLE stock_entries
  ADD COLUMN unit_type ENUM('meters','bundles','pieces','pallets')
    NOT NULL DEFAULT 'meters'
    COMMENT 'Unit of measure; non-meters values only used for KZinc entries'
    AFTER weight_kg_remaining,
  ADD COLUMN quantity DECIMAL(10,2) NULL
    COMMENT 'Original entered quantity in unit_type (KZinc entries only)'
    AFTER unit_type,
  ADD COLUMN pieces_total INT NULL
    COMMENT 'Computed total pieces from quantity (KZinc entries only)'
    AFTER quantity,
  ADD COLUMN pieces_remaining INT NULL
    COMMENT 'Remaining pieces after sales deductions (KZinc entries only)'
    AFTER pieces_total;

-- 3. Unit tracking on sales
ALTER TABLE sales
  ADD COLUMN unit_type ENUM('meters','bundles','pieces','pallets')
    NOT NULL DEFAULT 'meters'
    COMMENT 'Unit in which the quantity was sold'
    AFTER weight_kg,
  ADD COLUMN quantity DECIMAL(10,2) NULL
    COMMENT 'Quantity sold in unit_type (KZinc sales only)'
    AFTER unit_type;

-- ============================================================
-- VERIFICATION
-- ============================================================
SHOW COLUMNS FROM coils LIKE 'pallet_size';
SHOW COLUMNS FROM stock_entries LIKE 'unit_type';
SHOW COLUMNS FROM stock_entries LIKE 'pieces_%';
SHOW COLUMNS FROM sales LIKE 'unit_type';
