-- Migration: Add tax and discount columns to invoices table
-- This migration adds support for both percentage and fixed value tax and discount calculations

-- Add new columns to invoices table
ALTER TABLE invoices
ADD COLUMN tax_type ENUM('fixed', 'percentage') NOT NULL DEFAULT 'fixed' AFTER invoice_shape,
ADD COLUMN tax_value DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Tax amount or percentage' AFTER tax_type,
ADD COLUMN tax_amount DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Calculated tax amount' AFTER tax_value,
ADD COLUMN discount_type ENUM('fixed', 'percentage') NOT NULL DEFAULT 'fixed' AFTER tax_amount,
ADD COLUMN discount_value DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Discount amount or percentage' AFTER discount_type,
ADD COLUMN discount_amount DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Calculated discount amount' AFTER discount_value,
ADD COLUMN subtotal DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Amount before tax and discount' AFTER invoice_shape,
MODIFY COLUMN total DECIMAL(12,2) NOT NULL COMMENT 'Final amount after all calculations' AFTER discount_amount,
ADD COLUMN other_charges DECIMAL(10,2) DEFAULT 0.00 AFTER shipping;

-- Update existing data to set subtotal = total (for backward compatibility)
UPDATE invoices SET subtotal = total;

-- Add comments to existing columns for better documentation
ALTER TABLE invoices 
MODIFY COLUMN shipping DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Shipping charges' AFTER other_charges,
MODIFY COLUMN paid_amount DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Amount paid by customer' AFTER other_charges;
