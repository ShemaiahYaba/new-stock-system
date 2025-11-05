-- Stock Ledger Table for Transaction Tracking
-- Dual-entry accounting system for factory-use coils

CREATE TABLE IF NOT EXISTS stock_ledger (
    id INT AUTO_INCREMENT PRIMARY KEY,
    coil_id INT NOT NULL,
    stock_entry_id INT NULL,
    transaction_type ENUM('inflow', 'outflow') NOT NULL,
    description VARCHAR(255) NOT NULL,
    inflow_meters DECIMAL(10, 2) DEFAULT 0.00,
    outflow_meters DECIMAL(10, 2) DEFAULT 0.00,
    balance_meters DECIMAL(10, 2) NOT NULL,
    reference_type VARCHAR(50) NULL COMMENT 'sale, wastage, adjustment, stock_entry',
    reference_id INT NULL COMMENT 'ID of the referenced record',
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coil_id) REFERENCES coils(id) ON DELETE CASCADE,
    FOREIGN KEY (stock_entry_id) REFERENCES stock_entries(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_coil_id (coil_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add comments
ALTER TABLE stock_ledger COMMENT = 'Tracks all stock movements with running balance for factory-use coils';
