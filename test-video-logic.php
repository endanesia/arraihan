<?php
// Simple test to isolate the homepage error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Homepage Error Isolation Test</h2>";

try {
    require_once __DIR__ . '/inc/config.php';
    require_once __DIR__ . '/inc/db.php';
    
    echo "✅ Config and DB loaded<br>";
    
    // Test the exact query from index.php
    $db = db();
    $videos = [];
    
    echo "<h3>Testing Homepage Video Query:</h3>";
    $query = "SELECT youtube_id, title, platform, video_url FROM gallery_videos WHERE 
                (platform = 'youtube' AND youtube_id IS NOT NULL AND youtube_id != '') OR 
                (platform = 'instagram' AND video_url IS NOT NULL) OR 
                (platform = 'tiktok' AND video_url IS NOT NULL) OR
                (platform IS NULL AND youtube_id IS NOT NULL AND youtube_id != '' AND youtube_id NOT LIKE '%instagram.com%' AND youtube_id NOT LIKE '%tiktok.com%')
                ORDER BY id DESC LIMIT 3";
    
    echo "<p><strong>Query:</strong> " . htmlspecialchars($query) . "</p>";
    
    $res = $db->query($query);
    if ($res) {
        echo "✅ Query executed successfully<br>";
        echo "<p><strong>Rows returned:</strong> " . $res->num_rows . "</p>";
        
        while ($row = $res->fetch_assoc()) {
            echo "<h4>Video Data:</h4>";
            echo "<ul>";
            echo "<li>Title: " . htmlspecialchars($row['title'] ?: 'No title') . "</li>";
            echo "<li>Platform: " . htmlspecialchars($row['platform'] ?: 'NULL') . "</li>";
            echo "<li>YouTube ID: " . htmlspecialchars($row['youtube_id'] ?: 'NULL') . "</li>";
            echo "<li>Video URL: " . htmlspecialchars($row['video_url'] ?: 'NULL') . "</li>";
            echo "</ul>";
            
            // Process like in homepage
            if (empty($row['platform'])) $row['platform'] = 'youtube';
            if ($row['platform'] === 'youtube' && empty($row['video_url']) && !empty($row['youtube_id'])) {
                $row['video_url'] = "https://www.youtube.com/embed/{$row['youtube_id']}?enablejsapi=1&autoplay=0&mute=1&controls=1&rel=0";
                echo "<p>✅ Generated video URL for YouTube</p>";
            }
            
            $videos[] = $row;
        }
    } else {
        echo "❌ Query failed: " . $db->error . "<br>";
    }
    
    echo "<h3>Testing Video Display Logic:</h3>";
    
    foreach ($videos as $index => $v) {
        echo "<h4>Processing Video {$index}:</h4>";
        
        // Test the exact logic from homepage template
        $platform = !empty($v['platform']) ? $v['platform'] : 'youtube';
        $youtube_id = isset($v['youtube_id']) ? $v['youtube_id'] : '';
        $video_url = isset($v['video_url']) && !empty($v['video_url']) ? $v['video_url'] : 
                    (!empty($youtube_id) ? "https://www.youtube.com/embed/{$youtube_id}?enablejsapi=1&autoplay=0&mute=1&controls=1&rel=0" : '');
        
        if ($platform === 'youtube' && !empty($youtube_id)) {
            $thumb = 'https://img.youtube.com/vi/' . htmlspecialchars($youtube_id) . '/hqdefault.jpg';
            $platform_icon = '<i class="fab fa-youtube text-danger"></i>';
        } elseif ($platform === 'instagram') {
            $thumb = 'https://via.placeholder.com/480x360/E4405F/white?text=Instagram+Video';
            $platform_icon = '<i class="fab fa-instagram text-primary"></i>';
        } elseif ($platform === 'tiktok') {
            $thumb = 'https://via.placeholder.com/480x360/000000/white?text=TikTok+Video';
            $platform_icon = '<i class="fab fa-tiktok text-dark"></i>';
        } else {
            $thumb = 'https://via.placeholder.com/480x360/6c757d/white?text=Video';
            $platform_icon = '<i class="fas fa-video text-secondary"></i>';
        }
        
        echo "<ul>";
        echo "<li>Platform: {$platform}</li>";
        echo "<li>Thumbnail: {$thumb}</li>";
        echo "<li>Video URL: {$video_url}</li>";
        echo "<li>Platform Icon: {$platform_icon}</li>";
        echo "</ul>";
        
        // Test HTML output
        echo "<p><strong>Test HTML Output:</strong></p>";
        echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px 0;'>";
        echo "<img src='{$thumb}' alt='" . htmlspecialchars($v['title'] ?: 'Video') . "' style='width: 100px; height: 75px;'>";
        echo "<br><strong>" . htmlspecialchars($v['title'] ?: 'Video') . "</strong>";
        echo "<br>Platform: {$platform_icon} {$platform}";
        echo "</div>";
    }
    
    echo "<h3>✅ Video Processing Test Complete</h3>";
    echo "<p>If you see this, the video logic is working. The error might be elsewhere in index.php</p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error Found:</h3>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Stack Trace:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>