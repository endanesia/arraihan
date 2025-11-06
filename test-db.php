<?php
// Test koneksi database - untuk debugging saja
// Hapus file ini setelah selesai test!

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Test Koneksi Database</h2>";
echo "<hr>";

// Load config
try {
    $config = require __DIR__ . '/inc/config.php';
    echo "✅ Config berhasil dimuat<br>";
    echo "Host: " . htmlspecialchars($config['db']['host']) . "<br>";
    echo "User: " . htmlspecialchars($config['db']['user']) . "<br>";
    echo "Database: " . htmlspecialchars($config['db']['name']) . "<br>";
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
            $result = $mysqli->query("SELECT COUNT(*) as count FROM users");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "👤 Jumlah user: " . $row['count'] . "<br>";
                
                if ($row['count'] > 0) {
                    $result = $mysqli->query("SELECT username FROM users LIMIT 1");
                    if ($result && $user = $result->fetch_assoc()) {
                        echo "👤 User pertama: " . htmlspecialchars($user['username']) . "<br>";
                    }
                }
            }
        }
        
        echo "<br>";
        echo "<strong>Status:</strong> ";
        
        if (count($existing_tables) === count($tables)) {
            echo "🎉 <span style='color: green;'>Semua tabel lengkap! CMS siap digunakan.</span><br>";
            echo "<a href='admin/login' style='padding: 10px 20px; background: #1a6b4a; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Login Admin</a>";
        } else {
            echo "⚠️ <span style='color: orange;'>Beberapa tabel belum ada. Jalankan installer.</span><br>";
            echo "<a href='install/install.php' style='padding: 10px 20px; background: #d4a853; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Jalankan Installer</a>";
        }
        
    } else {
        echo "❌ Database '$dbName' tidak ditemukan<br>";
        echo "ℹ️ Jalankan installer untuk membuat database<br>";
        echo "<a href='install/install.php' style='padding: 10px 20px; background: #d4a853; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Jalankan Installer</a>";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<hr>";
echo "<small>⚠️ <strong>Penting:</strong> Hapus file test-db.php ini setelah selesai test untuk keamanan!</small>";
?>