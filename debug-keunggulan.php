<?php
// Debug database connection for production
echo "<h2>Database Connection Debug</h2>";

// Show current config
$config = require __DIR__ . '/inc/config.php';
echo "<h3>Current Configuration:</h3>";
echo "<pre>";
print_r([
    'host' => $config['db']['host'],
    'user' => $config['db']['user'],
    'name' => $config['db']['name'],
    'local_config_exists' => file_exists(__DIR__ . '/inc/config-local.php')
]);
echo "</pre>";

// Test database connection
echo "<h3>Database Connection Test:</h3>";
try {
    $mysqli = new mysqli(
        $config['db']['host'],
        $config['db']['user'],
        $config['db']['pass'],
        $config['db']['name']
    );
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ Connection failed: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ Connected successfully!</p>";
        
        // Check if keunggulan table exists
        $result = $mysqli->query("SHOW TABLES LIKE 'keunggulan'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Table 'keunggulan' exists</p>";
            
            // Count records
            $count_result = $mysqli->query("SELECT COUNT(*) as count FROM keunggulan");
            if ($count_result) {
                $count = $count_result->fetch_assoc()['count'];
                echo "<p>📊 Records in keunggulan table: $count</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Table 'keunggulan' does not exist</p>";
            
            // Try to create table
            echo "<h4>Attempting to create table...</h4>";
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
                echo "<p style='color: green;'>✅ Table created successfully!</p>";
                
                // Insert sample data
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
                echo "<p style='color: green;'>✅ Sample data inserted!</p>";
            } else {
                echo "<p style='color: red;'>❌ Failed to create table: " . $mysqli->error . "</p>";
            }
        }
        
        // Show all tables
        echo "<h4>All tables in database:</h4>";
        $tables_result = $mysqli->query("SHOW TABLES");
        if ($tables_result) {
            echo "<ul>";
            while ($table = $tables_result->fetch_array()) {
                echo "<li>" . $table[0] . "</li>";
            }
            echo "</ul>";
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='admin/keunggulan.php'>🔗 Try accessing Keunggulan admin page</a></p>";
echo "<p><a href='admin/dashboard.php'>🔗 Go to Dashboard</a></p>";
?>