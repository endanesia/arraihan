-- Migration: Add album functionality to gallery_images
-- Created: 2025-12-01

-- Add album_name column to gallery_images table
ALTER TABLE gallery_images ADD COLUMN album_name VARCHAR(100) DEFAULT 'Umum' AFTER title;

-- Update existing records to have default album
UPDATE gallery_images SET album_name = 'Umum' WHERE album_name IS NULL;

-- Add index for better performance
ALTER TABLE gallery_images ADD INDEX idx_album_name (album_name);

-- Sample album data (optional)
UPDATE gallery_images SET album_name = 'Umroh Desember 2024' WHERE id BETWEEN 1 AND 5;
UPDATE gallery_images SET album_name = 'Haji 2024' WHERE id BETWEEN 6 AND 10;
UPDATE gallery_images SET album_name = 'Ziarah Palestina' WHERE id BETWEEN 11 AND 15;