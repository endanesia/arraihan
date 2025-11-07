<?php
require_once __DIR__ . '/../inc/db.php';
require_login();

echo "<h2>Gallery Images Table Check</h2>";

try {
    // Check if table exists
    $result = db()->query("SHOW TABLES LIKE 'gallery_images'");
    if ($result->num_rows > 0) {
        echo "<p>✅ Table 'gallery_images' exists</p>";
        
        // Check table structure
        echo "<h3>Table Structure:</h3>";
        $result = db()->query("DESCRIBE gallery_images");
        if ($result) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>{$row['Default']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Check existing data
        echo "<h3>Existing Images:</h3>";
        $result = db()->query("SELECT id, title, file_path FROM gallery_images ORDER BY id DESC LIMIT 5");
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Title</th><th>File Path</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id']}</td>";
                echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                echo "<td>" . htmlspecialchars($row['file_path']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No images found in gallery</p>";
        }
        
    } else {
        echo "<p>❌ Table 'gallery_images' does not exist</p>";
        echo "<h3>Creating table...</h3>";
        
        $sql = "CREATE TABLE gallery_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NULL,
            file_path VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        if (db()->query($sql)) {
            echo "<p>✅ Table 'gallery_images' created successfully</p>";
        } else {
            echo "<p>❌ Error creating table: " . db()->error . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='gallery-images.php'>← Back to Gallery Images</a></p>";
?>