-- ============================================================
-- MIGRATION: Add Tile Category Support to Production Properties
-- File: migrations/021_add_tile_addons.sql
-- ============================================================

-- Step 1: Modify production_properties table to support 'tile' category
ALTER TABLE production_properties 
MODIFY COLUMN category ENUM('alusteel', 'aluminum', 'kzinc', 'tile') NOT NULL;

-- Step 2: Add index for better performance on tile queries
ALTER TABLE production_properties
ADD INDEX idx_category_addon_active (category, is_addon, is_active);

-- Step 3: Seed tile-specific add-on properties
INSERT INTO production_properties 
(code, name, category, property_type, is_addon, calculation_method, applies_to, 
 is_refundable, display_section, default_price, sort_order, is_active, created_by, created_at) 
VALUES
-- Service-based add-ons for tiles
('tile_delivery', 'Delivery/Freight', 'tile', 'unit_based', 1, 'fixed', 'total', 
 0, 'addon', 0.00, 100, 1, 1, NOW()),
 
('tile_loading', 'Loading Charge', 'tile', 'unit_based', 1, 'fixed', 'total', 
 0, 'addon', 0.00, 101, 1, 1, NOW()),
 
('tile_installation', 'Installation Service', 'tile', 'unit_based', 1, 'fixed', 'total', 
 0, 'addon', 0.00, 102, 1, 1, NOW()),
 
('tile_accessories', 'Accessories (Ridge Caps, Flashings)', 'tile', 'unit_based', 1, 'fixed', 'total', 
 0, 'addon', 0.00, 103, 1, 1, NOW()),
 
('tile_labor', 'Labor/Manpower', 'tile', 'unit_based', 1, 'fixed', 'total', 
 0, 'addon', 0.00, 104, 1, 1, NOW()),

-- Percentage-based add-ons
('tile_discount_percent', 'Discount', 'tile', 'unit_based', 1, 'percentage', 'subtotal', 
 1, 'adjustment', 0.00, 105, 1, 1, NOW()),

-- Fixed refunds/credits
('tile_refund', 'Refund/Credit', 'tile', 'unit_based', 1, 'fixed', 'total', 
 1, 'adjustment', 0.00, 106, 1, 1, NOW())

ON DUPLICATE KEY UPDATE name = name;

-- Step 4: Update constants.php reference (documentation only)
-- Add 'tile' to STOCK_CATEGORIES constant in config/constants.php

-- Verification query
SELECT 
    id, code, name, category, is_addon, calculation_method, 
    display_section, default_price, is_active
FROM production_properties 
WHERE category = 'tile' 
ORDER BY display_section, sort_order;

-- Migration completed
SELECT 'Tile Add-Ons Migration Completed' AS status;