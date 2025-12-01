<?php
require_once __DIR__ . '/inc/config.php';
require_once __DIR__ . '/inc/db.php';

echo "<h2>🔧 Fix Video Data Migration</h2>";

try {
    $db = db();
    
    echo "<h3>📋 Current Video Data:</h3>";
    $res = $db->query("SELECT id, title, youtube_id, platform, video_url FROM gallery_videos ORDER BY id DESC");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>YouTube ID</th><th>Platform</th><th>Video URL</th></tr>";
    
    $instagram_links_in_youtube_id = [];
    
    while ($row = $res->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . ($row['title'] ?: 'No title') . "</td>";
        echo "<td>" . ($row['youtube_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($row['platform'] ?: 'NULL') . "</td>";
        echo "<td>" . ($row['video_url'] ?: 'NULL') . "</td>";
        echo "</tr>";
        
        // Check if youtube_id contains Instagram URL
        if (!empty($row['youtube_id']) && strpos($row['youtube_id'], 'instagram.com') !== false) {
            $instagram_links_in_youtube_id[] = $row;
        }
    }
    echo "</table>";
    
    if (!empty($instagram_links_in_youtube_id)) {
        echo "<h3>🔧 Fixing Instagram Links in Wrong Column:</h3>";
        
        foreach ($instagram_links_in_youtube_id as $video) {
            echo "<p><strong>Fixing video ID {$video['id']}: {$video['title']}</strong></p>";
            
            // Update the video to correct columns
            $update_sql = "UPDATE gallery_videos SET 
                          platform = 'instagram',
                          video_url = ?,
                          youtube_id = NULL 
                          WHERE id = ?";
            
            $stmt = $db->prepare($update_sql);
            $stmt->bind_param('si', $video['youtube_id'], $video['id']);
            
            if ($stmt->execute()) {
                echo "✅ Fixed: Moved '{$video['youtube_id']}' to video_url column, set platform to 'instagram'<br>";
            } else {
                echo "❌ Error fixing video ID {$video['id']}: " . $db->error . "<br>";
            }
        }
        
        echo "<h3>✅ Migration Complete!</h3>";
        echo "<p>Instagram links have been moved to proper columns.</p>";
        
    } else {
        echo "<h3>✅ No Instagram Links Found in YouTube ID Column</h3>";
        echo "<p>All video data appears to be in correct format.</p>";
    }
    
    echo "<h3>📊 Updated Video Data:</h3>";
    $res2 = $db->query("SELECT id, title, youtube_id, platform, video_url FROM gallery_videos ORDER BY id DESC");
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Title</th><th>YouTube ID</th><th>Platform</th><th>Video URL</th></tr>";
    while ($row = $res2->fetch_assoc()) {
        $style = '';
        if ($row['platform'] === 'instagram') $style = 'background: #ffe6f0;';
        elseif ($row['platform'] === 'tiktok') $style = 'background: #f0f0f0;';
        elseif ($row['platform'] === 'youtube') $style = 'background: #fff2f2;';
        
        echo "<tr style='{$style}'>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . ($row['title'] ?: 'No title') . "</td>";
        echo "<td>" . ($row['youtube_id'] ?: 'NULL') . "</td>";
        echo "<td>" . ($row['platform'] ?: 'NULL') . "</td>";
        echo "<td>" . (strlen($row['video_url']) > 50 ? substr($row['video_url'], 0, 50) . '...' : ($row['video_url'] ?: 'NULL')) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<p><a href='/' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Test Homepage</a></p>";
    echo "<p><a href='/admin/gallery-videos' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Video Admin</a></p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>