-- Migration: 008_add_hex_code_to_colors.sql
-- Adds a hex color code column for UI display (e.g. #FFFFFF for white)

ALTER TABLE colors
ADD COLUMN hex_code VARCHAR(7) NULL COMMENT 'Optional hex color code for UI display'
AFTER name;
