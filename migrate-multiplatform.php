<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';

echo "<h2>Running Multi-platform Video Migration...</h2>";

try {
    // Read and execute the migration file
    $migrationFile = __DIR__ . '/migrations/2025-12-01_add_multiplatform_videos.sql';
    
    if (!file_exists($migrationFile)) {
        throw new Exception("Migration file not found: $migrationFile");
    }
    
    $sql = file_get_contents($migrationFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $db = db();
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) continue;
        
        echo "<p>Executing: " . htmlspecialchars(substr($statement, 0, 100)) . "...</p>";
        
        try {
            $result = $db->query($statement);
            if ($result === true) {
                echo "<p style='color: green;'>✓ Success</p>";
            } elseif ($result !== false) {
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<p style='color: blue;'>→ " . htmlspecialchars(print_r($row, true)) . "</p>";
                    }
                }
                echo "<p style='color: green;'>✓ Success</p>";
            } else {
                echo "<p style='color: orange;'>⚠ " . htmlspecialchars($db->error) . "</p>";
            }
        } catch (Exception $e) {
            echo "<p style='color: orange;'>⚠ " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
    
    echo "<h3 style='color: green;'>Migration completed!</h3>";
    echo "<p><a href='admin/gallery-videos.php'>Go to Gallery Videos Admin</a></p>";
    
} catch (Exception $e) {
    echo "<h3 style='color: red;'>Migration failed!</h3>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>