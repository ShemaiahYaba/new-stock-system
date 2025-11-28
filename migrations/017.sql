-- Migration: Add polymorphic invoice support for multiple sale types
-- Date: 2024-11-28
-- Purpose: Allow invoices to reference tile_sales, coil sales, and production

-- Add new columns for polymorphic relationship
ALTER TABLE invoices 
ADD COLUMN sale_type ENUM('coil_sale', 'tile_sale', 'production') NULL AFTER sale_id,
ADD COLUMN sale_reference_id INT NULL AFTER sale_type;

-- Make old sale_id nullable for backwards compatibility
ALTER TABLE invoices 
MODIFY COLUMN sale_id INT NULL;

-- Add index for performance on polymorphic queries
CREATE INDEX idx_sale_reference ON invoices(sale_type, sale_reference_id);

-- Migrate existing coil sales to new structure
UPDATE invoices 
SET sale_type = 'coil_sale',
    sale_reference_id = sale_id
WHERE sale_id IS NOT NULL AND sale_type IS NULL;

-- Migrate existing production-based invoices
UPDATE invoices 
SET sale_type = 'production'
WHERE production_id IS NOT NULL AND sale_type IS NULL;