-- Migration: Add multi-platform video support to gallery_videos
-- Created: 2025-12-01

-- Check if columns don't exist before adding
SET @platform_exists = (
    SELECT COUNT(*) 
    FROM information_schema.columns 
    WHERE table_schema = DATABASE() 
    AND table_name = 'gallery_videos' 
    AND column_name = 'platform'
);

SET @video_url_exists = (
    SELECT COUNT(*) 
    FROM information_schema.columns 
    WHERE table_schema = DATABASE() 
    AND table_name = 'gallery_videos' 
    AND column_name = 'video_url'
);

-- Add platform column (youtube, instagram, tiktok)
SET @sql = IF(@platform_exists = 0, 
    'ALTER TABLE gallery_videos ADD COLUMN platform ENUM("youtube", "instagram", "tiktok") DEFAULT "youtube" AFTER title',
    'SELECT "Column platform already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add video_url column for direct video links
SET @sql = IF(@video_url_exists = 0, 
    'ALTER TABLE gallery_videos ADD COLUMN video_url TEXT NULL AFTER youtube_id',
    'SELECT "Column video_url already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Update existing records to have platform 'youtube'
UPDATE gallery_videos SET platform = 'youtube' WHERE platform IS NULL AND youtube_id IS NOT NULL;

-- Add indexes for better performance
ALTER TABLE gallery_videos ADD INDEX idx_platform (platform);

-- Sample data conversion (optional)
-- Convert existing YouTube videos to new format
UPDATE gallery_videos 
SET video_url = CONCAT('https://www.youtube.com/watch?v=', youtube_id) 
WHERE platform = 'youtube' AND youtube_id IS NOT NULL AND video_url IS NULL;