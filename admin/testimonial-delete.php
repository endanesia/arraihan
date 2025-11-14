<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = db()->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    
    header('Location: testimonials.php?success=delete');
    exit;
}

header('Location: testimonials.php');
exit;
