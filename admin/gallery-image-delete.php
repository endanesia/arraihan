<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $stmt = db()->prepare('SELECT file_path FROM gallery_images WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $url = $row['file_path'] ?? '';
        if ($url) {
            $file = str_replace($config['app']['uploads_url'], $config['app']['uploads_dir'], $url);
            if (is_file($file)) @unlink($file);
        }
    }
    $stmt = db()->prepare('DELETE FROM gallery_images WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
header('Location: ' . $base . '/admin/gallery-images');
exit;
