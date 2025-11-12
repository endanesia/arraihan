<?php
session_start();
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../inc/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0 && function_exists('db') && db()) {
    $stmt = db()->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param('i', $id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = 'Pesan berhasil dihapus';
    } else {
        $_SESSION['error'] = 'Gagal menghapus pesan';
    }
    $stmt->close();
}

header('Location: messages.php');
exit;
