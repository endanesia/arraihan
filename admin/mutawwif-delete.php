<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../inc/db.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // Get mutawwif data
    $stmt = db()->prepare("SELECT foto FROM mutawwif WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Delete photo file if exists
        if (!empty($row['foto']) && file_exists(__DIR__ . '/../images/mutawwif/' . $row['foto'])) {
            unlink(__DIR__ . '/../images/mutawwif/' . $row['foto']);
        }
        
        // Delete from database
        $stmt = db()->prepare("DELETE FROM mutawwif WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        header('Location: mutawwif-list.php?success=delete');
        exit;
    }
}

header('Location: mutawwif-list.php');
exit;
