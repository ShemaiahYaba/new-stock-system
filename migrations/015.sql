-- ============================================
-- TILE MANAGEMENT MODULE MIGRATION
-- Version: 1.0
-- Date: 2024-11-27
-- ============================================

-- Step 1: Create designs table
CREATE TABLE IF NOT EXISTS designs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_code (code),
    INDEX idx_active (is_active),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Create tile_products table
CREATE TABLE IF NOT EXISTS tile_products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(100) UNIQUE NOT NULL COMMENT 'Format: DESIGN-COLOR-GAUGE',
    design_id INT NOT NULL,
    color_id INT NOT NULL,
    gauge ENUM('thick', 'normal', 'light') NOT NULL,
    status ENUM('available', 'out_of_stock') DEFAULT 'out_of_stock',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (design_id) REFERENCES designs(id),
    FOREIGN KEY (color_id) REFERENCES colors(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    UNIQUE KEY unique_product (design_id, color_id, gauge, deleted_at),
    INDEX idx_design (design_id),
    INDEX idx_color (color_id),
    INDEX idx_gauge (gauge),
    INDEX idx_status (status),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 3: Create tile_stock_ledger table
CREATE TABLE IF NOT EXISTS tile_stock_ledger (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tile_product_id INT NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    transaction_code VARCHAR(100),
    quantity_in DECIMAL(10,2) DEFAULT 0.00,
    quantity_out DECIMAL(10,2) DEFAULT 0.00,
    balance DECIMAL(10,2) NOT NULL,
    reference_type ENUM('stock_in', 'sale', 'adjustment', 'return') NOT NULL,
    reference_id INT,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tile_product_id) REFERENCES tile_products(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_product (tile_product_id),
    INDEX idx_date (transaction_date),
    INDEX idx_reference (reference_type, reference_id),
    INDEX idx_created (created_at),
    INDEX idx_ledger_balance_lookup (tile_product_id, created_at DESC, id DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 4: Create tile_sales table
CREATE TABLE IF NOT EXISTS tile_sales (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    tile_product_id INT NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'completed',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (tile_product_id) REFERENCES tile_products(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_customer (customer_id),
    INDEX idx_product (tile_product_id),
    INDEX idx_status (status),
    INDEX idx_date (created_at),
    INDEX idx_deleted (deleted_at),
    INDEX idx_sales_reporting (created_at, status, deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 5: Seed initial designs (optional)
INSERT INTO designs (code, name, description, is_active, created_by, created_at) VALUES
('MILANO', 'Milano', 'Milano roofing tile design', 1, 5, NOW()),
('SHINGLE', 'Shingle', 'Shingle roofing tile design', 5, 1, NOW()),
('CORONA', 'Corona', 'Corona roofing tile design', 1, 5, NOW())
ON DUPLICATE KEY UPDATE name = name;

-- Migration completed successfully
SELECT 'Tile Management Module Migration Completed' AS status;