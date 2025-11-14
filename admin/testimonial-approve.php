<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = $_GET['action'] ?? '';

if ($id > 0 && in_array($action, ['approve', 'unapprove'])) {
    $is_approved = ($action === 'approve') ? 1 : 0;
    
    $stmt = db()->prepare("UPDATE testimonials SET is_approved = ? WHERE id = ?");
    $stmt->bind_param('ii', $is_approved, $id);
    $stmt->execute();
    
    header('Location: testimonials.php?success=' . $action);
    exit;
}

header('Location: testimonials.php');
exit;
