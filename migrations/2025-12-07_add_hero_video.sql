-- Migration: Add hero_video table
-- Created: 2025-12-07
-- Description: Table for storing hero video that autoplays after hero slideshow

CREATE TABLE IF NOT EXISTS hero_video (
    id INT AUTO_INCREMENT PRIMARY KEY,
    video_path VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default record (will be replaced by admin upload)
INSERT INTO hero_video (video_path, title, description, is_active) 
VALUES ('', 'Hero Video', 'Video yang ditampilkan setelah hero slideshow', 1)
ON DUPLICATE KEY UPDATE id=id;
