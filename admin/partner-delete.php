<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    // Get file paths before deleting
    $stmt = db()->prepare("SELECT logo_url, img_url FROM partners WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($logo_url, $img_url);
    $stmt->fetch();
    $stmt->close();
    
    // Delete files if exist
    if (!empty($logo_url) && file_exists(__DIR__ . '/../' . $logo_url)) {
        unlink(__DIR__ . '/../' . $logo_url);
    }
    if (!empty($img_url) && file_exists(__DIR__ . '/../' . $img_url)) {
        unlink(__DIR__ . '/../' . $img_url);
    }
    
    // Delete from database
    $stmt = db()->prepare("DELETE FROM partners WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}
header('Location: ' . $base . '/admin/partners');
exit;
