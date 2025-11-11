-- Migration: 009_map_coils_to_colors.sql
-- Purpose: Map existing coils.color values to the correct colors.id

-- 1. Update coils.color_id where color name matches colors.name
UPDATE coils c
JOIN colors clr ON c.color = clr.name
SET c.color_id = clr.id
WHERE c.color_id IS NULL;

-- 2. Update coils.color_id where color code matches colors.code (just in case)
UPDATE coils c
JOIN colors clr ON c.color = clr.code
SET c.color_id = clr.id
WHERE c.color_id IS NULL;

-- 3. Verify results
SELECT c.id, c.code, c.name, c.color, c.color_id, clr.name AS color_name
FROM coils c
LEFT JOIN colors clr ON c.color_id = clr.id
ORDER BY c.id;
