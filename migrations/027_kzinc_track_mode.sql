-- Migration 027: Add kzinc_track_mode column to coils table
-- Values: 'meters' for raw roll coils (Stock Management)
--         'pallets' for pre-cut sheet coils (K-Zinc module)
--         NULL for non-KZinc coils

ALTER TABLE coils
    ADD COLUMN kzinc_track_mode ENUM('meters', 'pallets') NULL DEFAULT NULL
    AFTER pallet_size;

-- Backfill existing KZinc coils:
-- If pallet_size is set and > 0 → they were pallet/sheet coils
-- Otherwise → meter coils
UPDATE coils
SET kzinc_track_mode = CASE
    WHEN category = 'kzinc' AND pallet_size > 0 THEN 'pallets'
    WHEN category = 'kzinc' THEN 'meters'
    ELSE NULL
END;
