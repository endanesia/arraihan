<?php
// Debug script untuk mengecek error di homepage
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Debugging Homepage Error</h2>";

try {
    echo "<h3>1. Testing Database Connection</h3>";
    require_once __DIR__ . '/inc/config.php';
    require_once __DIR__ . '/inc/db.php';
    
    if (function_exists('db')) {
        $db = db();
        if ($db) {
            echo "✅ Database connection OK<br>";
        } else {
            echo "❌ Database connection failed<br>";
        }
    } else {
        echo "❌ db() function not found<br>";
    }
    
    echo "<h3>2. Testing gallery_videos Table</h3>";
    $result = $db->query("DESCRIBE gallery_videos");
    if ($result) {
        echo "✅ gallery_videos table exists<br>";
        echo "<strong>Columns:</strong><br>";
        while ($row = $result->fetch_assoc()) {
            echo "- {$row['Field']} ({$row['Type']})<br>";
        }
    } else {
        echo "❌ gallery_videos table error: " . $db->error . "<br>";
    }
    
    echo "<h3>3. Testing Video Query</h3>";
    $videos = [];
    $columns_result = $db->query("DESCRIBE gallery_videos");
    $video_table_columns = [];
    if ($columns_result) {
        while ($col = $columns_result->fetch_assoc()) {
            $video_table_columns[] = $col['Field'];
        }
    }
    
    $has_platform_column = in_array('platform', $video_table_columns);
    $has_video_url_column = in_array('video_url', $video_table_columns);
    
    echo "Platform column exists: " . ($has_platform_column ? "✅ YES" : "❌ NO") . "<br>";
    echo "Video URL column exists: " . ($has_video_url_column ? "✅ YES" : "❌ NO") . "<br>";
    
    if ($has_platform_column && $has_video_url_column) {
        $res = $db->query("SELECT youtube_id, title, platform, video_url FROM gallery_videos ORDER BY id DESC LIMIT 3");
        echo "Multi-platform query: ";
    } else {
        $res = $db->query("SELECT youtube_id, title FROM gallery_videos ORDER BY id DESC LIMIT 3");  
        echo "Legacy query: ";
    }
    
    if ($res) {
        $count = $res->num_rows;
        echo "✅ Success ({$count} rows)<br>";
        while ($row = $res->fetch_assoc()) {
            echo "- Video: " . ($row['title'] ?: 'No title') . " (ID: {$row['youtube_id']})<br>";
        }
    } else {
        echo "❌ Query failed: " . $db->error . "<br>";
    }
    
    echo "<h3>4. Testing Other Queries</h3>";
    
    // Test mutawwif query
    $mutawwif_res = $db->query("SELECT * FROM mutawwif WHERE is_active = 1 LIMIT 1");
    echo "Mutawwif query: " . ($mutawwif_res ? "✅ OK" : "❌ Error: " . $db->error) . "<br>";
    
    // Test testimonials query  
    $testimonial_res = $db->query("SELECT * FROM testimonials WHERE is_approved = 1 LIMIT 1");
    echo "Testimonials query: " . ($testimonial_res ? "✅ OK" : "❌ Error: " . $db->error) . "<br>";
    
    // Test hero_slides query
    $hero_res = $db->query("SELECT * FROM hero_slides WHERE is_active = 1 LIMIT 1");
    echo "Hero slides query: " . ($hero_res ? "✅ OK" : "❌ Error: " . $db->error) . "<br>";
    
    echo "<h3>✅ All Tests Completed!</h3>";
    echo "<p>If all tests pass, the issue might be in template rendering.</p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Fatal Error</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
}
?>