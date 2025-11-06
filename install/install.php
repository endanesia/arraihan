<?php
// Simple installer to create database and tables, and seed admin account
ini_set('display_errors', 1);
error_reporting(E_ALL);

$config = require __DIR__ . '/../inc/config.php';

$mysqli = new mysqli($config['db']['host'], $config['db']['user'], $config['db']['pass']);
if ($mysqli->connect_errno) {
    die('Connection failed: ' . $mysqli->connect_error);
}

// Create database if not exists
$dbName = $config['db']['name'];
$mysqli->query("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
if ($mysqli->errno) die('Create DB error: ' . $mysqli->error);
$mysqli->select_db($dbName);

// Create tables
$sql = [];
$sql[] = "CREATE TABLE IF NOT EXISTS users (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  username VARCHAR(50) UNIQUE NOT NULL,\n  password VARCHAR(255) NOT NULL,\n  name VARCHAR(100) NOT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS posts (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  type ENUM('news','article') NOT NULL DEFAULT 'news',\n  title VARCHAR(255) NOT NULL,\n  slug VARCHAR(255) UNIQUE,\n  excerpt TEXT,\n  content LONGTEXT,\n  cover_image VARCHAR(255),\n  published TINYINT(1) NOT NULL DEFAULT 1,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,\n  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS gallery_images (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  title VARCHAR(255),\n  file_path VARCHAR(255) NOT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS gallery_videos (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  title VARCHAR(255),\n  youtube_id VARCHAR(20) NOT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS settings (\n  skey VARCHAR(100) PRIMARY KEY,\n  svalue TEXT NULL,\n  updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS partners (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  name VARCHAR(255) NOT NULL,\n  icon_class VARCHAR(100) NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS packages (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  title VARCHAR(255) NOT NULL,\n  price_label VARCHAR(100) DEFAULT 'Mulai dari',\n  price_value VARCHAR(100) NOT NULL,\n  price_unit VARCHAR(50) DEFAULT '/orang',\n  icon_class VARCHAR(100) DEFAULT 'fas fa-moon',\n  features TEXT NULL,\n  featured TINYINT(1) NOT NULL DEFAULT 0,\n  button_text VARCHAR(100) DEFAULT 'Lihat Detail',\n  button_link VARCHAR(255) DEFAULT '#kontak',\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

$sql[] = "CREATE TABLE IF NOT EXISTS schedules (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  title VARCHAR(255) NOT NULL,\n  departure_date DATE NULL,\n  description TEXT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

foreach ($sql as $q) {
    $mysqli->query($q);
    if ($mysqli->errno) die('Error: '.$mysqli->error);
}

// Seed default admin if not exists
$res = $mysqli->query("SELECT COUNT(*) c FROM users");
$row = $res ? $res->fetch_assoc() : ['c'=>0];
if ((int)$row['c'] === 0) {
    $username = 'admin';
    $password = password_hash('admin123', PASSWORD_BCRYPT);
    $name = 'Administrator';
    $stmt = $mysqli->prepare("INSERT INTO users(username,password,name) VALUES(?,?,?)");
    $stmt->bind_param('sss', $username, $password, $name);
    $stmt->execute();
}

?><!doctype html>
<html lang="id"><head><meta charset="utf-8"><title>Installer | Umroh CMS</title>
<style>body{font-family:Arial,sans-serif;padding:30px;background:#f7f7f7;color:#222} .card{max-width:720px;margin:40px auto;background:#fff;border-radius:10px;padding:30px;box-shadow:0 8px 24px rgba(0,0,0,.08)} .btn{display:inline-block;padding:12px 18px;border-radius:8px;background:#1a6b4a;color:#fff;text-decoration:none} .muted{color:#666}</style>
</head><body>
<div class="card">
  <h1>Installer Selesai ✅</h1>
  <p>Database dan tabel berhasil dibuat (<?php echo e($dbName ?? ''); ?>). Akun admin default:</p>
  <ul>
    <li>Username: <strong>admin</strong></li>
    <li>Password: <strong>admin123</strong></li>
  </ul>
  <p class="muted">Harap login dan segera ganti password.</p>
  <p>
    <a class="btn" href="<?php echo $config['app']['base_url']; ?>/admin/login">Ke Halaman Login</a>
  </p>
</div>
</body></html>
<?php
function e($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
