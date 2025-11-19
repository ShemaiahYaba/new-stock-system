-- =====================================================
-- COMPREHENSIVE FINANCIAL COLUMN UPGRADE
-- Upgrading all money columns to support up to 999 trillion
-- =====================================================

-- 1. SALES TABLE
ALTER TABLE `sales` 
  MODIFY COLUMN `price_per_meter` DECIMAL(15,2) NOT NULL,
  MODIFY COLUMN `total_amount` DECIMAL(15,2) NOT NULL;

-- 2. INVOICES TABLE  
ALTER TABLE `invoices`
  MODIFY COLUMN `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  MODIFY COLUMN `tax_value` DECIMAL(15,2) DEFAULT 0.00,
  MODIFY COLUMN `tax_amount` DECIMAL(15,2) DEFAULT 0.00,
  MODIFY COLUMN `discount_value` DECIMAL(15,2) DEFAULT 0.00,
  MODIFY COLUMN `discount_amount` DECIMAL(15,2) DEFAULT 0.00,
  MODIFY COLUMN `total` DECIMAL(15,2) NOT NULL,
  MODIFY COLUMN `tax` DECIMAL(15,2) DEFAULT 0.00,
  MODIFY COLUMN `other_charges` DECIMAL(15,2) DEFAULT 0.00,
  MODIFY COLUMN `paid_amount` DECIMAL(15,2) DEFAULT 0.00,
  MODIFY COLUMN `shipping` DECIMAL(15,2) DEFAULT 0.00;

-- 3. RECEIPTS TABLE
ALTER TABLE `receipts`
  MODIFY COLUMN `amount_paid` DECIMAL(15,2) NOT NULL;

-- =====================================================
-- VERIFICATION QUERIES
-- Run these to confirm the changes
-- =====================================================

-- Check sales table columns
SHOW COLUMNS FROM `sales` WHERE Field IN ('price_per_meter', 'total_amount');

-- Check invoices table columns  
SHOW COLUMNS FROM `invoices` WHERE Field IN ('subtotal', 'total', 'paid_amount', 'tax', 'tax_amount');

-- Check receipts table columns
SHOW COLUMNS FROM `receipts` WHERE Field = 'amount_paid';