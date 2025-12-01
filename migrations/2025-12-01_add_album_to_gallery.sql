-- Migration: Add album functionality to gallery_images
-- Created: 2025-12-01

-- Check if column doesn't exist before adding
SET @column_exists = (
    SELECT COUNT(*) 
    FROM information_schema.columns 
    WHERE table_schema = DATABASE() 
    AND table_name = 'gallery_images' 
    AND column_name = 'album_name'
);

-- Add album_name column only if it doesn't exist
SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE gallery_images ADD COLUMN album_name VARCHAR(100) DEFAULT "Umum" AFTER title',
    'SELECT "Column album_name already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing records to have default album (safe update)
UPDATE gallery_images SET album_name = 'Umum' WHERE album_name IS NULL OR album_name = '';

-- Add index for better performance (if not exists)
SET @index_exists = (
    SELECT COUNT(*) 
    FROM information_schema.statistics 
    WHERE table_schema = DATABASE() 
    AND table_name = 'gallery_images' 
    AND index_name = 'idx_album_name'
);

SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE gallery_images ADD INDEX idx_album_name (album_name)',
    'SELECT "Index idx_album_name already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;