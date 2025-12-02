ALTER TABLE `sales` 
ADD COLUMN `weight_kg` DECIMAL(10,2) DEFAULT NULL COMMENT 'Quantity in KG' AFTER `meters`,
ADD COLUMN `price_per_kg` DECIMAL(10,2) DEFAULT NULL COMMENT 'Price per KG' AFTER `price_per_meter`;

ALTER TABLE `sales` 
ADD COLUMN `notes` TEXT NULL COMMENT 'Sale notes/remarks' 
AFTER `created_by`;