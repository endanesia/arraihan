<?php
require_once __DIR__ . '/inc/db.php';

echo "<h2>Packages Table Structure Check</h2>\n";

try {
    $db = db();
    if ($db && !$db->connect_error) {
        // Check current table structure
        $result = $db->query("DESCRIBE packages");
        echo "<h3>Current Table Structure:</h3>\n";
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
        
        $fields = [];
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row['Field'];
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        // Check if description field exists
        if (!in_array('description', $fields)) {
            echo "<h3>Adding description field...</h3>\n";
            $sql = "ALTER TABLE packages ADD COLUMN description TEXT NULL AFTER title";
            if ($db->query($sql)) {
                echo "<p style='color: green;'>✅ Description field added successfully!</p>\n";
            } else {
                echo "<p style='color: red;'>❌ Failed to add description field: " . $db->error . "</p>\n";
            }
        } else {
            echo "<p style='color: green;'>✅ Description field already exists!</p>\n";
        }
        
        // Show updated structure
        $result = $db->query("DESCRIBE packages");
        echo "<h3>Updated Table Structure:</h3>\n";
        echo "<table border='1' style='border-collapse: collapse;'>\n";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr";
            if ($row['Field'] === 'description') echo " style='background-color: #d4edda;'";
            echo ">";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
    } else {
        echo "<p style='color: red;'>Database connection failed</p>\n";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>\n";
}
?>