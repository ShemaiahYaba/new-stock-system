-- ============================================================
-- MIGRATION: Extended Properties Module - Add-On Support
-- ============================================================

-- Step 1: Alter existing table to support add-ons
ALTER TABLE production_properties
ADD COLUMN is_addon TINYINT(1) DEFAULT 0 AFTER property_type,
ADD COLUMN calculation_method ENUM('fixed', 'percentage', 'per_unit') DEFAULT 'fixed' AFTER is_addon,
ADD COLUMN applies_to ENUM('subtotal', 'total', 'per_item') DEFAULT 'total' AFTER calculation_method,
ADD COLUMN is_refundable TINYINT(1) DEFAULT 0 AFTER applies_to,
ADD COLUMN display_section ENUM('production', 'addon', 'adjustment') DEFAULT 'production' AFTER is_refundable;

-- Step 2: Update indexes for better performance
ALTER TABLE production_properties
ADD INDEX idx_is_addon (is_addon),
ADD INDEX idx_display_section (display_section),
ADD INDEX idx_category_addon (category, is_addon, is_active);

-- Step 3: Insert default add-on properties
INSERT INTO production_properties 
(code, name, category, property_type, is_addon, calculation_method, applies_to, default_price, is_refundable, display_section, sort_order, is_active, created_by) 
VALUES
-- Service-based add-ons (apply to all categories)
('bending', 'Bending Service', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 100, 1, 2),
('loading', 'Loading Charge', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 101, 1, 2),
('freight', 'Freight/Shipping', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 102, 1, 2),
('accessories', 'Accessories (Nails, Washers, etc)', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 103, 1, 2),
('installation', 'Installation Service', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 104, 1, 2),
('refund', 'Refund/Credit', 'alusteel', 'unit_based', 1, 'fixed', 'total', 0.00, 1, 'adjustment', 105, 1, 2),

-- Duplicate for aluminum
('bending_alu', 'Bending Service', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 100, 1, 2),
('loading_alu', 'Loading Charge', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 101, 1, 2),
('freight_alu', 'Freight/Shipping', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 102, 1, 2),
('accessories_alu', 'Accessories (Nails, Washers, etc)', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 103, 1, 2),
('installation_alu', 'Installation Service', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 104, 1, 2),
('refund_alu', 'Refund/Credit', 'aluminum', 'unit_based', 1, 'fixed', 'total', 0.00, 1, 'adjustment', 105, 1, 2),

-- Duplicate for kzinc
('bending_kzinc', 'Bending Service', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 100, 1, 2),
('loading_kzinc', 'Loading Charge', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 101, 1, 2),
('freight_kzinc', 'Freight/Shipping', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 102, 1, 2),
('accessories_kzinc', 'Accessories (Nails, Washers, etc)', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 103, 1, 2),
('installation_kzinc', 'Installation Service', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0.00, 0, 'addon', 104, 1, 2),
('refund_kzinc', 'Refund/Credit', 'kzinc', 'unit_based', 1, 'fixed', 'total', 0.00, 1, 'adjustment', 105, 1, 2);

-- Step 4: Update existing production properties to set is_addon = 0
UPDATE production_properties 
SET is_addon = 0, 
    display_section = 'production',
    calculation_method = CASE 
        WHEN property_type = 'meter_based' THEN 'per_unit'
        WHEN property_type = 'unit_based' THEN 'per_unit'
        WHEN property_type = 'bundle_based' THEN 'per_unit'
        ELSE 'fixed'
    END
WHERE is_addon IS NULL OR is_addon = 0;

-- Step 5: Create view for easier querying
CREATE OR REPLACE VIEW v_production_properties AS
SELECT 
    id,
    code,
    name,
    category,
    property_type,
    is_addon,
    calculation_method,
    applies_to,
    is_refundable,
    display_section,
    default_price,
    sort_order,
    is_active,
    metadata,
    CASE 
        WHEN is_addon = 1 AND is_refundable = 1 THEN 'Adjustment'
        WHEN is_addon = 1 THEN 'Add-On'
        ELSE 'Production'
    END as property_category_display
FROM production_properties
WHERE deleted_at IS NULL;

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- Check all properties
SELECT 
    id, code, name, category, 
    is_addon, display_section, default_price 
FROM production_properties 
ORDER BY category, is_addon, sort_order;

-- Check production properties only
SELECT * FROM v_production_properties 
WHERE is_addon = 0 
ORDER BY category, sort_order;

-- Check add-on properties only
SELECT * FROM v_production_properties 
WHERE is_addon = 1 
ORDER BY category, display_section, sort_order;

-- Count by type
SELECT 
    category,
    is_addon,
    display_section,
    COUNT(*) as count
FROM production_properties
WHERE deleted_at IS NULL
GROUP BY category, is_addon, display_section;