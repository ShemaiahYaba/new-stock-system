-- Migration: 007_create_colors_table.sql
CREATE TABLE IF NOT EXISTS colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE COMMENT 'Short code like IBeige, PGreen',
    name VARCHAR(100) NOT NULL COMMENT 'Display name like I/Beige, P/Green',
    is_active TINYINT(1) DEFAULT 1,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_code (code),
    INDEX idx_active (is_active),
    INDEX idx_deleted (deleted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed existing colors from constants
INSERT INTO colors (code, name, is_active, created_by) VALUES
('IBeige', 'I/Beige', 1, 2),
('PGreen', 'P/Green', 1, 2),
('SBlue', 'S/Blue', 1, 2),
('TBlack', 'T/Black', 1, 2),
('TCRed', 'TC/Red', 1, 2),
('GBeige', 'G/Beige', 1, 2),
('BGreen', 'B/Green', 1, 2),
('IWhite', 'I/White', 1, 2);