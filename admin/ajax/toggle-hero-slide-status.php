<?php
require_once __DIR__ . '/../../inc/db.php';
require_login();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$id = (int)($input['id'] ?? 0);
$status = (bool)($input['status'] ?? false);

if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

$stmt = db()->prepare("UPDATE hero_slides SET is_active=? WHERE id=?");
$stmt->bind_param('ii', $status, $id);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true, 
        'is_active' => (bool)$status,
        'message' => 'Status berhasil diubah'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Gagal mengubah status: ' . db()->error
    ]);
}