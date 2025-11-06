<?php
// Test koneksi database - untuk debugging saja
// Hapus file ini setelah selesai test!

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Test Koneksi Database - Admin</h2>";
echo "<hr>";

// Load config
try {
    $config = require __DIR__ . '/../inc/config.php';
    echo "✅ Config berhasil dimuat<br>";
    echo "Host: " . htmlspecialchars($config['db']['host']) . "<br>";
    echo "User: " . htmlspecialchars($config['db']['user']) . "<br>";
    echo "Database: " . htmlspecialchars($config['db']['name']) . "<br>";
    echo "Base URL: " . htmlspecialchars($config['app']['base_url']) . "<br>";
    echo "<br>";
} catch (Exception $e) {
    echo "❌ Error loading config: " . htmlspecialchars($e->getMessage()) . "<br>";
    exit;
}

// Test koneksi
try {
    $mysqli = new mysqli(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['pass']
    );
    
    if ($mysqli->connect_errno) {
        echo "❌ Koneksi ke MySQL gagal: " . htmlspecialchars($mysqli->connect_error) . "<br>";
        echo "Error Code: " . $mysqli->connect_errno . "<br>";
        exit;
    }
    
    echo "✅ Koneksi ke MySQL berhasil<br>";
    echo "MySQL Version: " . htmlspecialchars($mysqli->server_info) . "<br>";
    echo "<br>";
    
    // Test database exists
    $dbName = $config['db']['name'];
    $result = $mysqli->query("SHOW DATABASES LIKE '$dbName'");
    
    if ($result && $result->num_rows > 0) {
        echo "✅ Database '$dbName' ditemukan<br>";
        
        // Select database
        $mysqli->select_db($dbName);
        
        // Test tables
        echo "<h3>Status Tabel:</h3>";
        $tables = ['users', 'posts', 'gallery_images', 'gallery_videos', 'settings', 'partners', 'packages', 'schedules'];
        $existing_tables = [];
        
        foreach ($tables as $table) {
            $result = $mysqli->query("SHOW TABLES LIKE '$table'");
            if ($result && $result->num_rows > 0) {
                $existing_tables[] = $table;
                echo "✅ Tabel '$table' ada<br>";
            } else {
                echo "❌ Tabel '$table' tidak ditemukan<br>";
            }
        }
        
        echo "<br>";
        
        // Test admin user
        if (in_array('users', $existing_tables)) {
            echo "<h3>Info User Admin:</h3>";
            $result = $mysqli->query("SELECT COUNT(*) as count FROM users");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "👤 Jumlah user: " . $row['count'] . "<br>";
                
                if ($row['count'] > 0) {
                    $result = $mysqli->query("SELECT username, name FROM users LIMIT 1");
                    if ($result && $user = $result->fetch_assoc()) {
                        echo "👤 Username: " . htmlspecialchars($user['username']) . "<br>";
                        echo "👤 Nama: " . htmlspecialchars($user['name']) . "<br>";
                    }
                } else {
                    echo "⚠️ Tidak ada user admin. Jalankan installer untuk membuat user default.<br>";
                }
            }
        }
        
        // Test session functionality
        echo "<br><h3>Test Session:</h3>";
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        echo "✅ Session berjalan normal<br>";
        echo "Session ID: " . session_id() . "<br>";
        
        echo "<br>";
        echo "<strong>Diagnosis Admin Problem:</strong><br>";
        
        if (count($existing_tables) === count($tables)) {
            echo "🎉 <span style='color: green;'>Database OK! Kemungkinan masalah:</span><br>";
            echo "• Cek file .htaccess untuk rewrite rules<br>";
            echo "• Pastikan mod_rewrite aktif di server<br>";
            echo "• Periksa permission file/folder<br>";
            echo "• Cek error log server<br>";
            
            $base = rtrim($config['app']['base_url'] ?? '', '/');
            echo "<br><strong>Link untuk dicoba:</strong><br>";
            echo "<a href='{$base}/admin/login.php'>Direct: login.php</a> | ";
            echo "<a href='{$base}/admin/login'>Pretty URL: login</a> | ";
            echo "<a href='{$base}/admin/dashboard.php'>Direct: dashboard.php</a><br>";
            
        } else {
            echo "⚠️ <span style='color: orange;'>Database belum lengkap. Jalankan installer dulu.</span><br>";
            echo "<a href='../install/install.php' style='padding: 10px 20px; background: #d4a853; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Jalankan Installer</a>";
        }
        
    } else {
        echo "❌ Database '$dbName' tidak ditemukan<br>";
        echo "ℹ️ Jalankan installer untuk membuat database<br>";
        echo "<a href='../install/install.php' style='padding: 10px 20px; background: #d4a853; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Jalankan Installer</a>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "❌ Error detail: " . htmlspecialchars($e->getTraceAsString()) . "<br>";
}

// Test file permissions dan path
echo "<br><hr><h3>Test File System:</h3>";
$files_to_check = [
    '../inc/config.php',
    '../inc/db.php', 
    'login.php',
    'dashboard.php',
    '../.htaccess'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "✅ File '$file' ada";
        if (is_readable($file)) {
            echo " (readable)";
        } else {
            echo " ❌ (not readable)";
        }
        echo "<br>";
    } else {
        echo "❌ File '$file' tidak ditemukan<br>";
    }
}

echo "<hr>";
echo "<small>⚠️ <strong>Penting:</strong> Hapus file test-db.php ini setelah selesai test untuk keamanan!</small>";
?>