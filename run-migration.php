<?php
require_once __DIR__ . '/inc/db.php';

echo "Running about_images migration...\n";

$sql = "CREATE TABLE IF NOT EXISTS `about_images` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

try {
    db()->query($sql);
    echo "✅ Table about_images created successfully!\n";
} catch (Exception $e) {
    echo "❌ Error creating table: " . $e->getMessage() . "\n";
}

// Create directory
$dir = __DIR__ . '/images/about/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
    echo "✅ Directory /images/about/ created successfully!\n";
} else {
    echo "✅ Directory /images/about/ already exists!\n";
}

echo "Migration completed!\n";
?>