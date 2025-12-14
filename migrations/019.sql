-- ============================================================
-- MIGRATION: Production Properties Module
-- ============================================================

-- 1. Create production_properties table
CREATE TABLE IF NOT EXISTS production_properties (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    category ENUM('alusteel', 'aluminum', 'kzinc') NOT NULL,
    property_type ENUM('unit_based', 'meter_based', 'bundle_based') NOT NULL,
    default_price DECIMAL(10,2) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    metadata JSON DEFAULT NULL COMMENT 'Store additional config like pieces_per_bundle, calculation_notes, etc.',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_category_active (category, is_active),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Seed default Alusteel/Aluminum properties
INSERT INTO production_properties (code, name, category, property_type, default_price, sort_order, created_by) VALUES
('mainsheet', 'Mainsheet', 'alusteel', 'meter_based', 10300.00, 1, 1),
('flatsheet', 'Flatsheet', 'alusteel', 'meter_based', 9800.00, 2, 1),
('cladding', 'Cladding', 'alusteel', 'meter_based', 11200.00, 3, 1),
('mainsheet_alu', 'Mainsheet', 'aluminum', 'meter_based', 10300.00, 1, 1),
('flatsheet_alu', 'Flatsheet', 'aluminum', 'meter_based', 9800.00, 2, 1),
('cladding_alu', 'Cladding', 'aluminum', 'meter_based', 11200.00, 3, 1);

-- 3. Seed default KZINC properties
INSERT INTO production_properties (code, name, category, property_type, default_price, metadata, sort_order, created_by) VALUES
('scraps', 'Scraps', 'kzinc', 'unit_based', 2500.00, NULL, 1, 1),
('pieces', 'Pieces', 'kzinc', 'unit_based', 4500.00, NULL, 2, 1),
('bundles', 'Bundles', 'kzinc', 'bundle_based', 64000.00, JSON_OBJECT('pieces_per_bundle', 15), 3, 1);

-- 4. Add index for better query performance
ALTER TABLE production_properties ADD INDEX idx_code (code);
ALTER TABLE production_properties ADD INDEX idx_sort_order (sort_order);

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- Check all properties
SELECT * FROM production_properties ORDER BY category, sort_order;

-- Check by category
SELECT * FROM production_properties WHERE category = 'alusteel' AND is_active = 1;
SELECT * FROM production_properties WHERE category = 'kzinc' AND is_active = 1;