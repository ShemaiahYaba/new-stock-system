-- Migration: 013_add_meters_gauge_to_coils.sql
-- Purpose: Add meters and gauge columns to coils table for presentation purposes
-- These fields are informational only and do not affect stock entry transactions

ALTER TABLE coils
ADD COLUMN meters DECIMAL(10,2) NULL COMMENT 'Approximate meters per coil (informational only)' AFTER net_weight,
ADD COLUMN gauge VARCHAR(50) NULL COMMENT 'Material gauge/thickness (e.g., 0.45mm, 0.50mm)' AFTER meters;

-- Optional: Add index for gauge if you plan to filter by it
ALTER TABLE coils
ADD INDEX idx_gauge (gauge);

-- Verify the changes
-- SELECT * FROM coils LIMIT 1;