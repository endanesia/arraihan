<?php
// Check database structure in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Structure Check</h1>";

try {
    require_once __DIR__ . '/../inc/config.php';
    require_once __DIR__ . '/../inc/db.php';
    
    $db = db();
    echo "<p>Database connection: OK</p>";
    
    // Check if settings table exists
    $result = $db->query("SHOW TABLES LIKE 'settings'");
    if ($result->num_rows > 0) {
        echo "<p>Settings table: EXISTS</p>";
        
        // Show table structure
        $result = $db->query("DESCRIBE settings");
        echo "<h3>Current Table Structure:</h3>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show existing data
        $result = $db->query("SELECT * FROM settings LIMIT 10");
        if ($result->num_rows > 0) {
            echo "<h3>Sample Data:</h3>";
            echo "<table border='1' style='border-collapse: collapse;'>";
            $first = true;
            while ($row = $result->fetch_assoc()) {
                if ($first) {
                    echo "<tr>";
                    foreach (array_keys($row) as $key) {
                        echo "<th>" . htmlspecialchars($key) . "</th>";
                    }
                    echo "</tr>";
                    $first = false;
                }
                echo "<tr>";
                foreach ($row as $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No data in settings table</p>";
        }
        
    } else {
        echo "<p>Settings table: NOT EXISTS</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "<h3>ERROR:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "</div>";
}
?>