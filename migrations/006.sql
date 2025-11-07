ALTER TABLE `stock_entries` 
MODIFY COLUMN `status` ENUM('available', 'factory_use') 
NOT NULL DEFAULT 'available' 
COMMENT 'Stock entry status: available for direct sale or factory use';