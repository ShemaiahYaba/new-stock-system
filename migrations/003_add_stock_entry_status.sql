-- Add status field to stock_entries table
-- Stock entries can be 'available' or 'factory_use'

ALTER TABLE stock_entries 
ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'available' AFTER meters_remaining,
ADD INDEX idx_status (status);

-- Update existing entries to 'available'
UPDATE stock_entries SET status = 'available' WHERE status IS NULL OR status = '';

-- Add comment
ALTER TABLE stock_entries COMMENT = 'Stock entries with individual status tracking (available/factory_use)';
