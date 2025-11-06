<?php
// Database connection (mysqli) and session bootstrap
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$config = require __DIR__ . '/config.php';

$mysqli = new mysqli(
    $config['db']['host'],
    $config['db']['user'],
    $config['db']['pass'],
    $config['db']['name']
);

if ($mysqli->connect_errno) {
    // If DB doesn't exist yet (installer not run), we don't fatal here for frontend
    // Admin pages should handle this and prompt to run installer
}

$mysqli->set_charset($config['db']['charset']);

function db() {
    global $mysqli; return $mysqli;
}

function e($str) { return htmlspecialchars($str, ENT_QUOTES, 'UTF-8'); }

function is_logged_in() { return !empty($_SESSION['admin_id']); }

function require_login() {
    global $config;
    if (!is_logged_in()) {
        $base = $config['app']['base_url'] ?? '';
        header('Location: ' . rtrim($base, '/') . '/admin/login');
        exit;
    }
}

function youtube_id_from_url($url) {
    // support various formats
    $pattern = '/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/';
    if (preg_match($pattern, $url, $matches)) {
        return $matches[1];
    }
    // if user already gives id
    if (preg_match('/^[\w-]{11}$/', $url)) return $url;
    return null;
}

// --- Simple settings storage (key/value) ---
function ensure_settings_table() {
    $db = db(); if (!$db) return false;
    $sql = "CREATE TABLE IF NOT EXISTS settings (\n        skey VARCHAR(100) PRIMARY KEY,\n        svalue TEXT NULL,\n        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $db->query($sql);
    return $db->errno === 0;
}

function get_setting($key, $default = '') {
    $db = db(); if (!$db) return $default;
    ensure_settings_table();
    $stmt = $db->prepare("SELECT svalue FROM settings WHERE skey=? LIMIT 1");
    if (!$stmt) return $default;
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $stmt->bind_result($val);
    if ($stmt->fetch()) {
        $stmt->close();
        return (string)$val;
    }
    $stmt->close();
    return $default;
}

function set_setting($key, $value) {
    $db = db(); if (!$db) return false;
    ensure_settings_table();
    $stmt = $db->prepare("INSERT INTO settings(skey, svalue) VALUES(?, ?) ON DUPLICATE KEY UPDATE svalue=VALUES(svalue)");
    if (!$stmt) return false;
    $stmt->bind_param('ss', $key, $value);
    $ok = $stmt->execute();
    $stmt->close();
    return $ok;
}
