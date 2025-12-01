<?php
// Test database table existence
require_once __DIR__ . '/inc/db.php';

echo "<h1>Database Table Check</h1>";

$tables_to_check = [
    'popup_banner',
    'hero_slides', 
    'partners',
    'packages',
    'gallery_videos',
    'testimonials',
    'mutawwif',
    'schedules'
];

foreach ($tables_to_check as $table) {
    echo "<h3>Table: $table</h3>";
    
    try {
        $result = db()->query("SHOW TABLES LIKE '$table'");
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Table EXISTS</p>";
            
            // Check table structure
            $structure = db()->query("DESCRIBE $table");
            if ($structure) {
                echo "<ul>";
                while ($row = $structure->fetch_assoc()) {
                    echo "<li>{$row['Field']} - {$row['Type']}</li>";
                }
                echo "</ul>";
            }
            
            // Check row count
            $count = db()->query("SELECT COUNT(*) as total FROM $table");
            if ($count) {
                $total = $count->fetch_assoc()['total'];
                echo "<p>Rows: <strong>$total</strong></p>";
            }
        } else {
            echo "<p style='color: red;'>❌ Table MISSING</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ ERROR: " . $e->getMessage() . "</p>";
    }
    
    echo "<hr>";
}
?>