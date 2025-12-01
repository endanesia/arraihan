<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Get slide data first
    $stmt = db()->prepare("SELECT background_image FROM hero_slides WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($slide = $result->fetch_assoc()) {
        // Delete the slide
        $stmt = db()->prepare("DELETE FROM hero_slides WHERE id=?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        // Delete associated image file
        if (!empty($slide['background_image'])) {
            $image_path = __DIR__ . '/..' . $slide['background_image'];
            if (file_exists($image_path)) {
                @unlink($image_path);
            }
        }
    }
}

header('Location: ' . $base . '/admin/hero-slides');
exit;