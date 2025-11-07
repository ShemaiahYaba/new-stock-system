-- =====================================================
-- PRODUCTION WORKFLOW DATABASE SCHEMA
-- Run this migration to add new tables and update existing ones
-- =====================================================

-- 1. CREATE WAREHOUSE TABLE
CREATE TABLE IF NOT EXISTS warehouses (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    location TEXT,
    contact VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    INDEX idx_name (name),
    INDEX idx_active (is_active),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CREATE PRODUCTION TABLE
CREATE TABLE IF NOT EXISTS production (
    id INT(11) NOT NULL AUTO_INCREMENT,
    sale_id INT(11) NOT NULL,
    warehouse_id INT(11) NOT NULL,
    production_paper JSON NOT NULL COMMENT 'Stores complete production details',
    status ENUM('pending', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    created_by INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    immutable_hash VARCHAR(64) NOT NULL COMMENT 'SHA256 hash for immutability verification',
    PRIMARY KEY (id),
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_sale_id (sale_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    UNIQUE KEY unique_immutable_hash (immutable_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. CREATE INVOICE TABLE
CREATE TABLE IF NOT EXISTS invoices (
    id INT(11) NOT NULL AUTO_INCREMENT,
    sale_id INT(11) NOT NULL,
    production_id INT(11) DEFAULT NULL,
    invoice_number VARCHAR(50) NOT NULL,
    invoice_shape JSON NOT NULL COMMENT 'Complete invoice data structure',
    total DECIMAL(12,2) NOT NULL COMMENT 'Final amount after all calculations',
    shipping DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Shipping charges',
    
    paid_amount DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('unpaid', 'partial', 'paid', 'cancelled') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    immutable_hash VARCHAR(64) NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_invoice_number (invoice_number),
    FOREIGN KEY (sale_id) REFERENCES sales(id),
    FOREIGN KEY (production_id) REFERENCES production(id) ON DELETE SET NULL,
    INDEX idx_sale_id (sale_id),
    INDEX idx_production_id (production_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    UNIQUE KEY unique_immutable_hash (immutable_hash)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. CREATE RECEIPT TABLE
CREATE TABLE IF NOT EXISTS receipts (
    id INT(11) NOT NULL AUTO_INCREMENT,
    invoice_id INT(11) NOT NULL,
    amount_paid DECIMAL(12,2) NOT NULL,
    reference VARCHAR(100),
    payment_method VARCHAR(50) DEFAULT 'cash',
    created_by INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (invoice_id) REFERENCES invoices(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_invoice_id (invoice_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. CREATE SUPPLY/DELIVERY TABLE
CREATE TABLE IF NOT EXISTS supply_delivery (
    id INT(11) NOT NULL AUTO_INCREMENT,
    production_id INT(11) NOT NULL,
    warehouse_id INT(11) NOT NULL,
    status ENUM('pending', 'supplied', 'returned') DEFAULT 'pending',
    delivered_at TIMESTAMP NULL DEFAULT NULL,
    return_requested_at TIMESTAMP NULL DEFAULT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (production_id) REFERENCES production(id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    INDEX idx_production_id (production_id),
    INDEX idx_warehouse_id (warehouse_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. RENAME STOCK_LEDGER TO STOCK_CARD (or keep both for transition)
-- Option A: Rename existing table
-- RENAME TABLE stock_ledger TO stock_card;

-- Option B: Create new table and migrate data later
-- CREATE TABLE IF NOT EXISTS stock_card (
--     id INT(11) NOT NULL AUTO_INCREMENT,
--     coil_id INT(11) NOT NULL,
--     production_id INT(11) DEFAULT NULL,
--     sale_id INT(11) DEFAULT NULL,
--     change_type ENUM('inflow', 'outflow', 'drawdown', 'return', 'adjustment') NOT NULL,
--     meters_changed DECIMAL(10,2) NOT NULL,
--     balance_meters DECIMAL(10,2) NOT NULL,
--     note TEXT,
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     PRIMARY KEY (id),
--     FOREIGN KEY (coil_id) REFERENCES coils(id) ON DELETE CASCADE,
--     FOREIGN KEY (production_id) REFERENCES production(id) ON DELETE SET NULL,
--     FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE SET NULL,
--     INDEX idx_coil_id (coil_id),
--     INDEX idx_production_id (production_id),
--     INDEX idx_sale_id (sale_id),
--     INDEX idx_change_type (change_type),
--     INDEX idx_created_at (created_at)
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. UPDATE CONSTANTS TABLE (if you have one) or handle in PHP constants.php
-- Update stock categories: Alloy Steel → Alusteel

-- 8. INSERT DEFAULT WAREHOUSE
INSERT INTO warehouses (name, location, contact, is_active) 
VALUES ('Head Office', 'Plot E18-E19, Saburi, Dei-Dei, FCT, Abuja', '+2348065336645', 1)
ON DUPLICATE KEY UPDATE name=name;

-- 9. ADD INDEXES FOR PERFORMANCE
ALTER TABLE sales ADD INDEX idx_warehouse_id (customer_id);
ALTER TABLE production ADD INDEX idx_immutable_hash (immutable_hash);
ALTER TABLE invoices ADD INDEX idx_invoice_number (invoice_number);

-- 10. CREATE AUDIT LOG TABLE FOR IMMUTABLE RECORD CHANGES
CREATE TABLE IF NOT EXISTS audit_log (
    id INT(11) NOT NULL AUTO_INCREMENT,
    table_name VARCHAR(50) NOT NULL,
    record_id INT(11) NOT NULL,
    action ENUM('create', 'update', 'delete', 'restore') NOT NULL,
    old_data JSON,
    new_data JSON,
    changed_by INT(11) NOT NULL,
    reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (changed_by) REFERENCES users(id),
    INDEX idx_table_record (table_name, record_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MIGRATION COMPLETE
-- Next: Update config/constants.php to rename categories
-- =====================================================