-- Migration: 010_update_colors_hex_codes.sql
-- Purpose: Update colors table with hex color codes

UPDATE colors SET hex_code = '#F5F5DC' WHERE code = 'IBeige';
UPDATE colors SET hex_code = '#008000' WHERE code = 'PGreen';
UPDATE colors SET hex_code = '#0000FF' WHERE code = 'SBlue';
UPDATE colors SET hex_code = '#000000' WHERE code = 'TBlack';
UPDATE colors SET hex_code = '#FF0000' WHERE code = 'TCRed';
UPDATE colors SET hex_code = '#E6BE8A' WHERE code = 'GBeige';
UPDATE colors SET hex_code = '#006400' WHERE code = 'BGreen';
UPDATE colors SET hex_code = '#FFFFFF' WHERE code = 'IWhite';

-- Optional: verify
SELECT id, code, name, hex_code FROM colors ORDER BY id;
