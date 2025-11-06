<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    $stmt = db()->prepare('DELETE FROM gallery_videos WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
header('Location: ' . $base . '/admin/gallery-videos');
exit;
