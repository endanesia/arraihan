<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) {
    // fetch cover to delete file
    $stmt = db()->prepare('SELECT cover_image FROM posts WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $cover = $row['cover_image'] ?? '';
        if ($cover) {
            $file = str_replace($config['app']['uploads_url'], $config['app']['uploads_dir'], $cover);
            if (is_file($file)) @unlink($file);
        }
    }
    $stmt = db()->prepare('DELETE FROM posts WHERE id=?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
header('Location: ' . $base . '/admin/posts');
exit;
