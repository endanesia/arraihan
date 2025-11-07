<?php
require_once __DIR__ . '/inc/db.php';

echo "<h2>Production Database Migration - Add Description Field</h2>\n";

try {
    $db = db();
    if ($db && !$db->connect_error) {
        echo "<p style='color: green;'>✅ Database connection successful</p>\n";
        
        // Check current table structure
        $result = $db->query("DESCRIBE packages");
        echo "<h3>Current Table Structure:</h3>\n";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
        echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
        
        $fields = [];
        while ($row = $result->fetch_assoc()) {
            $fields[] = $row['Field'];
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>\n";
        }
        echo "</table>\n";
        
        // Check if description field exists
        if (!in_array('description', $fields)) {
            echo "<h3>Adding description field...</h3>\n";
            $sql = "ALTER TABLE packages ADD COLUMN description TEXT NULL AFTER title";
            
            if ($db->query($sql)) {
                echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS: Description field added successfully!</p>\n";
                
                // Show updated structure
                $result = $db->query("DESCRIBE packages");
                echo "<h3>Updated Table Structure:</h3>\n";
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
                echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr";
                    if ($row['Field'] === 'description') echo " style='background-color: #d4edda; font-weight: bold;'";
                    echo ">";
                    echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['Default'] ?? '') . "</td>";
                    echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                    echo "</tr>\n";
                }
                echo "</table>\n";
                
                echo "<h3>Migration Summary:</h3>\n";
                echo "<ul>\n";
                echo "<li style='color: green;'>✅ Description field added as TEXT type</li>\n";
                echo "<li style='color: green;'>✅ Field allows NULL values</li>\n";
                echo "<li style='color: green;'>✅ Field positioned after 'title' column</li>\n";
                echo "<li style='color: green;'>✅ Ready for CKEditor rich text content</li>\n";
                echo "</ul>\n";
                
            } else {
                echo "<p style='color: red; font-weight: bold;'>❌ FAILED: Could not add description field</p>\n";
                echo "<p style='color: red;'>Error: " . htmlspecialchars($db->error) . "</p>\n";
            }
        } else {
            echo "<p style='color: green; font-weight: bold;'>✅ Description field already exists!</p>\n";
            
            // Show field details
            $result = $db->query("SHOW COLUMNS FROM packages WHERE Field = 'description'");
            if ($row = $result->fetch_assoc()) {
                echo "<h3>Description Field Details:</h3>\n";
                echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>\n";
                echo "<tr style='background: #f0f0f0;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>\n";
                echo "<tr style='background-color: #d4edda;'>";
                echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
                echo "<td>" . htmlspecialchars($row['Default'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
                echo "</tr></table>\n";
            }
        }
        
        // Test packages count
        $result = $db->query("SELECT COUNT(*) as total FROM packages");
        if ($row = $result->fetch_assoc()) {
            echo "<p><strong>Total packages in database:</strong> " . $row['total'] . "</p>\n";
        }
        
        echo "<hr>\n";
        echo "<h3>Next Steps:</h3>\n";
        echo "<ol>\n";
        echo "<li>✅ Database migration completed</li>\n";
        echo "<li>🔗 <a href='/admin/package-edit.php' target='_blank'>Test Package Edit Form</a></li>\n";
        echo "<li>🔗 <a href='/admin/packages.php' target='_blank'>View Packages List</a></li>\n";
        echo "</ol>\n";
        
    } else {
        echo "<p style='color: red; font-weight: bold;'>❌ Database connection failed</p>\n";
        if ($db) {
            echo "<p style='color: red;'>Error: " . htmlspecialchars($db->connect_error) . "</p>\n";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red; font-weight: bold;'>❌ Exception occurred</p>\n";
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>\n";
}
?>