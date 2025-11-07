<?php
// Clean production - remove local config file
$localConfigPath = __DIR__ . '/inc/config-local.php';

if (file_exists($localConfigPath)) {
    if (unlink($localConfigPath)) {
        echo "✅ Local config file removed from production\n";
    } else {
        echo "❌ Failed to remove local config file\n";
    }
} else {
    echo "ℹ️ Local config file not found (already clean)\n";
}

// Test database connection with production config
echo "\n🔍 Testing production database connection...\n";

$config = require __DIR__ . '/inc/config.php';

try {
    $mysqli = new mysqli(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['name']
    );
    
    if ($mysqli->connect_error) {
        echo "❌ Connection failed: " . $mysqli->connect_error . "\n";
    } else {
        echo "✅ Production database connection successful!\n";
        
        // Check keunggulan table
        $result = $mysqli->query("SHOW TABLES LIKE 'keunggulan'");
        if ($result && $result->num_rows > 0) {
            echo "✅ Table 'keunggulan' exists\n";
        } else {
            echo "⚠️ Creating keunggulan table...\n";
            
            $sql = "CREATE TABLE keunggulan (
                id INT PRIMARY KEY AUTO_INCREMENT,
                title VARCHAR(255) NOT NULL,
                description TEXT NOT NULL,
                icon VARCHAR(100) NOT NULL,
                order_num INT DEFAULT 0,
                is_active TINYINT(1) DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
            
            if ($mysqli->query($sql)) {
                echo "✅ Table created successfully!\n";
                
                // Insert default data
                $defaults = [
                    ['Pelayanan Terbaik', 'Kami memberikan pelayanan terbaik untuk setiap perjalanan Anda dengan guide yang berpengalaman dan ramah.', 'fas fa-award', 1, 1],
                    ['Harga Terjangkau', 'Paket perjalanan dengan harga yang kompetitif dan terjangkau tanpa mengurangi kualitas pelayanan.', 'fas fa-money-bill-wave', 2, 1],
                    ['Keamanan Terjamin', 'Keamanan dan kenyamanan perjalanan Anda adalah prioritas utama kami dengan asuransi perjalanan.', 'fas fa-shield-alt', 3, 1]
                ];
                
                $stmt = $mysqli->prepare("INSERT INTO keunggulan (title, description, icon, order_num, is_active) VALUES (?, ?, ?, ?, ?)");
                foreach ($defaults as $data) {
                    $stmt->bind_param('sssii', $data[0], $data[1], $data[2], $data[3], $data[4]);
                    $stmt->execute();
                }
                echo "✅ Default data inserted!\n";
            } else {
                echo "❌ Failed to create table: " . $mysqli->error . "\n";
            }
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n🎯 Production cleanup completed!\n";
?>