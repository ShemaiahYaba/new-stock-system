ALTER TABLE `stock_entries` 
ADD COLUMN `weight_kg` DECIMAL(10,2) DEFAULT NULL AFTER `meters_remaining`,
ADD COLUMN `weight_kg_remaining` DECIMAL(10,2) DEFAULT NULL AFTER `weight_kg`;