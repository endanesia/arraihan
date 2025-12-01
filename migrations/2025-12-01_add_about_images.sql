-- Migration: Add about_images table for multiple images in about section
-- Created: 2025-12-01

CREATE TABLE IF NOT EXISTS `about_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image_path` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sort_order` (`sort_order`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create images directory if not exists
-- Note: Directory creation should be done manually or via PHP
-- mkdir -p images/about/

-- Insert sample data (optional)
INSERT IGNORE INTO `about_images` (`image_path`, `title`, `alt_text`, `sort_order`, `is_active`) VALUES
('about/sample1.jpg', 'Kantor Pusat', 'Kantor pusat Raihan Travelindo', 1, 1),
('about/sample2.jpg', 'Tim Professional', 'Tim professional yang berpengalaman', 2, 1),
('about/sample3.jpg', 'Fasilitas Modern', 'Fasilitas kantor yang modern dan nyaman', 3, 1);