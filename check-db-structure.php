<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';

echo "<h2>🔍 Database Structure Check</h2>";

try {
    $db = db();
    
    $tables = ['packages', 'schedules', 'gallery_videos', 'mutawwif', 'testimonials', 'hero_slides'];
    
    foreach ($tables as $table) {
        echo "<h3>Table: {$table}</h3>";
        
        $result = $db->query("DESCRIBE {$table}");
        if ($result) {
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['Field']}</td>";
                echo "<td>{$row['Type']}</td>";
                echo "<td>{$row['Null']}</td>";
                echo "<td>{$row['Key']}</td>";
                echo "<td>" . ($row['Default'] ?? 'NULL') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "❌ Table {$table} does not exist or error: " . $db->error . "<br>";
        }
        echo "<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>