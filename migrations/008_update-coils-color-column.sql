-- Migration: 008_update_coils_color_column.sql

-- Add temporary column for color_id
ALTER TABLE coils 
ADD COLUMN color_id INT NULL AFTER color,
ADD FOREIGN KEY (color_id) REFERENCES colors(id);

-- Migrate existing color data to color_id
UPDATE coils c
INNER JOIN colors col ON c.color = col.code
SET c.color_id = col.id;

-- Verify migration (should return 0)
SELECT COUNT(*) FROM coils WHERE color_id IS NULL AND deleted_at IS NULL;

-- Once verified, make color_id required and drop old color column
-- ALTER TABLE coils MODIFY COLUMN color_id INT NOT NULL;
-- ALTER TABLE coils DROP COLUMN color;

-- For safety during transition, keep both columns temporarily