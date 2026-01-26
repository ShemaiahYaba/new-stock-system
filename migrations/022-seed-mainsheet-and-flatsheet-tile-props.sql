-- ============================================================
-- MIGRATION: Seed Tile Configuration Properties (Mainsheet/Flatsheet)
-- ============================================================

INSERT INTO production_properties
(code, name, category, property_type, is_addon, calculation_method, applies_to,
 is_refundable, display_section, default_price, sort_order, is_active, created_by, created_at)
VALUES
('tile_mainsheet', 'Mainsheet', 'tile', 'unit_based', 0, 'per_unit', 'total',
 0, 'production', 800.00, 10, 1, 1, NOW()),
('tile_flatsheet', 'Flatsheet', 'tile', 'unit_based', 0, 'per_unit', 'total',
 0, 'production', 600.00, 20, 1, 1, NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    default_price = VALUES(default_price),
    is_active = VALUES(is_active),
    updated_at = NOW();

-- Verification
SELECT id, code, name, category, is_addon, display_section, default_price
FROM production_properties
WHERE category = 'tile' AND code IN ('tile_mainsheet', 'tile_flatsheet')
ORDER BY sort_order;
