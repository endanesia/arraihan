<?php
/**
 * Safe Migration Runner for Album Feature
 * Run this file once to add album functionality to gallery_images table
 */

require_once __DIR__ . '/inc/db.php';

function runMigration() {
    $errors = [];
    $success = [];
    
    try {
        $db = db();
        if (!$db) {
            throw new Exception("Database connection failed");
        }
        
        // Check if album_name column exists
        $checkColumn = $db->query("SHOW COLUMNS FROM gallery_images LIKE 'album_name'");
        
        if ($checkColumn->num_rows == 0) {
            // Add album_name column
            $alterQuery = "ALTER TABLE gallery_images ADD COLUMN album_name VARCHAR(100) DEFAULT 'Umum' AFTER title";
            if ($db->query($alterQuery)) {
                $success[] = "✅ Column 'album_name' added successfully";
            } else {
                $errors[] = "❌ Failed to add column: " . $db->error;
            }
            
            // Update existing records
            $updateQuery = "UPDATE gallery_images SET album_name = 'Umum' WHERE album_name IS NULL OR album_name = ''";
            if ($db->query($updateQuery)) {
                $success[] = "✅ Existing records updated with default album";
            } else {
                $errors[] = "❌ Failed to update existing records: " . $db->error;
            }
            
            // Add index
            $indexQuery = "ALTER TABLE gallery_images ADD INDEX idx_album_name (album_name)";
            if ($db->query($indexQuery)) {
                $success[] = "✅ Index 'idx_album_name' added successfully";
            } else {
                // Index might already exist, check error
                if (strpos($db->error, 'Duplicate key name') === false) {
                    $errors[] = "❌ Failed to add index: " . $db->error;
                }
            }
            
        } else {
            $success[] = "✅ Column 'album_name' already exists - no migration needed";
        }
        
    } catch (Exception $e) {
        $errors[] = "❌ Migration failed: " . $e->getMessage();
    }
    
    return ['success' => $success, 'errors' => $errors];
}

// Run migration if accessed via web or CLI
if (php_sapi_name() === 'cli' || isset($_GET['run'])) {
    $result = runMigration();
    
    echo "<h2>Gallery Album Migration Results</h2>";
    
    if (!empty($result['success'])) {
        echo "<h3>Success:</h3><ul>";
        foreach ($result['success'] as $msg) {
            echo "<li>$msg</li>";
        }
        echo "</ul>";
    }
    
    if (!empty($result['errors'])) {
        echo "<h3>Errors:</h3><ul>";
        foreach ($result['errors'] as $msg) {
            echo "<li style='color: red;'>$msg</li>";
        }
        echo "</ul>";
    }
    
    if (empty($result['errors'])) {
        echo "<p style='color: green; font-weight: bold;'>Migration completed successfully! You can now use the album feature.</p>";
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Gallery Album Migration</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
            .btn { background: #007cba; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
            .warning { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <h1>Gallery Album Feature Migration</h1>
        <div class="warning">
            <strong>⚠️ Important:</strong> This will add the 'album_name' column to your gallery_images table. Make sure to backup your database first.
        </div>
        <p>This migration will:</p>
        <ul>
            <li>Add 'album_name' column to gallery_images table</li>
            <li>Set default album 'Umum' for existing images</li>
            <li>Add database index for better performance</li>
        </ul>
        <p><a href="?run=1" class="btn" onclick="return confirm('Are you sure you want to run the migration?')">Run Migration</a></p>
    </body>
    </html>
    <?php
}
?>