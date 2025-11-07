<?php
require_once __DIR__ . '/../inc/db.php';
require_login();

echo "<h2>Database Migration: Schedules Table</h2>";

try {
    // Check if jml_hari column exists
    $has_jml_hari = false;
    if ($res = db()->query("SHOW COLUMNS FROM schedules LIKE 'jml_hari'")) {
        $has_jml_hari = $res->num_rows > 0;
    }
    
    // Check if id_packages column exists
    $has_id_packages = false;
    if ($res = db()->query("SHOW COLUMNS FROM schedules LIKE 'id_packages'")) {
        $has_id_packages = $res->num_rows > 0;
    }
    
    echo "<h3>Current Status:</h3>";
    echo "<p>Column 'jml_hari': " . ($has_jml_hari ? "✅ EXISTS" : "❌ MISSING") . "</p>";
    echo "<p>Column 'id_packages': " . ($has_id_packages ? "✅ EXISTS" : "❌ MISSING") . "</p>";
    
    $migrations_run = 0;
    
    // Add jml_hari column if missing
    if (!$has_jml_hari) {
        echo "<h3>Adding 'jml_hari' column...</h3>";
        $sql = "ALTER TABLE schedules ADD COLUMN jml_hari INT NULL AFTER description";
        if (db()->query($sql)) {
            echo "<p>✅ Successfully added 'jml_hari' column</p>";
            $migrations_run++;
        } else {
            echo "<p>❌ Error adding 'jml_hari' column: " . db()->error . "</p>";
        }
    }
    
    // Add id_packages column if missing
    if (!$has_id_packages) {
        echo "<h3>Adding 'id_packages' column...</h3>";
        $sql = "ALTER TABLE schedules ADD COLUMN id_packages INT NULL AFTER jml_hari, ADD INDEX idx_id_packages (id_packages)";
        if (db()->query($sql)) {
            echo "<p>✅ Successfully added 'id_packages' column with index</p>";
            $migrations_run++;
        } else {
            echo "<p>❌ Error adding 'id_packages' column: " . db()->error . "</p>";
        }
    }
    
    if ($migrations_run === 0) {
        echo "<h3>✅ No migrations needed - all columns already exist!</h3>";
    } else {
        echo "<h3>✅ Migration completed! $migrations_run column(s) added.</h3>";
    }
    
    // Show final table structure
    echo "<h3>Final Table Structure:</h3>";
    if ($res = db()->query("DESCRIBE schedules")) {
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
        while ($r = $res->fetch_assoc()) {
            echo "<tr>";
            echo "<td>{$r['Field']}</td>";
            echo "<td>{$r['Type']}</td>";
            echo "<td>{$r['Null']}</td>";
            echo "<td>{$r['Key']}</td>";
            echo "<td>{$r['Default']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Migration failed: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='schedules.php'>← Back to Schedules</a></p>";
echo "<p><small>⚠️ <strong>Important:</strong> Delete this migration file after successful completion for security!</small></p>";
?>