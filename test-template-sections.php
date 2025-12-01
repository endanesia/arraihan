<?php
// Test homepage template rendering in sections
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/inc/db.php';

// Set up all variables like in real index.php
$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
}

// Load all data
$packages = [];
if (function_exists('db') && db()) {
    try {
        if ($res = db()->query("SELECT * FROM packages ORDER BY featured DESC, id DESC")) {
            while ($row = $res->fetch_assoc()) { 
                $packages[] = $row; 
            }
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
    }
}

$videos = [];
try {
    if (function_exists('db') && db()) {
        $res = db()->query("SELECT youtube_id, title, platform, video_url FROM gallery_videos WHERE 
                            (platform = 'youtube' AND youtube_id IS NOT NULL AND youtube_id != '') OR 
                            (platform = 'instagram' AND video_url IS NOT NULL) OR 
                            (platform = 'tiktok' AND video_url IS NOT NULL) OR
                            (platform IS NULL AND youtube_id IS NOT NULL AND youtube_id != '' AND youtube_id NOT LIKE '%instagram.com%' AND youtube_id NOT LIKE '%tiktok.com%')
                            ORDER BY id DESC LIMIT 3");
        if ($res) {
            while ($row = $res->fetch_assoc()) { 
                if (empty($row['platform'])) $row['platform'] = 'youtube';
                if ($row['platform'] === 'youtube' && empty($row['video_url']) && !empty($row['youtube_id'])) {
                    $row['video_url'] = "https://www.youtube.com/embed/{$row['youtube_id']}?enablejsapi=1&autoplay=0&mute=1&controls=1&rel=0";
                }
                $videos[] = $row; 
            }
        }
    }
} catch (Exception $e) {
    $videos = [];
    error_log("Video fetching error: " . $e->getMessage());
}

// Load other required data
$mutawwif = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM mutawwif WHERE is_active = 1 ORDER BY urutan ASC, id ASC")) {
        while ($row = $res->fetch_assoc()) { $mutawwif[] = $row; }
    }
}

$testimonials = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 6")) {
        while ($row = $res->fetch_assoc()) { $testimonials[] = $row; }
    }
}

$hero_slides = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")) {
        while ($row = $res->fetch_assoc()) { 
            $hero_slides[] = $row; 
        }
    }
}

// Function e() is already declared in inc/db.php

$link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';

echo "<h2>🧪 Template Section Test</h2>";

echo "<h3>✅ Data Loaded Successfully</h3>";
echo "<p>Videos: " . count($videos) . "</p>";
echo "<p>Packages: " . count($packages) . "</p>";
echo "<p>Hero slides: " . count($hero_slides) . "</p>";

echo "<h3>Testing Video Display Section</h3>";
try {
    ob_start();
    ?>
    <!-- Test video section -->
    <div class="video-grid">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $index => $v): 
                $platform = isset($v['platform']) && !empty($v['platform']) ? $v['platform'] : 'youtube';
                $youtube_id = isset($v['youtube_id']) ? $v['youtube_id'] : '';
                $video_url = isset($v['video_url']) && !empty($v['video_url']) ? $v['video_url'] : 
                            (!empty($youtube_id) ? "https://www.youtube.com/embed/{$youtube_id}?enablejsapi=1&autoplay=0&mute=1&controls=1&rel=0" : '');
                
                if ($platform === 'youtube' && !empty($youtube_id)) {
                    $thumb = 'https://img.youtube.com/vi/'.e($youtube_id).'/hqdefault.jpg';
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
            ?>
            <div class="video-item" data-video-url="<?= e($video_url) ?>" data-platform="<?= e($platform) ?>" data-video-index="<?= $index ?>">
                <div class="video-thumbnail">
                    <img src="<?= $thumb ?>" alt="<?= e($v['title'] ?: 'Video') ?>">
                    <div class="video-play">
                        <i class="fas fa-play"></i>
                    </div>
                    <?php if ($platform === 'youtube'): ?>
                    <div class="video-iframe-container" style="display: none;">
                        <iframe 
                            id="youtube-player-<?= $index ?>"
                            width="100%" 
                            height="200" 
                            src="<?= e($video_url) ?>" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    <?php endif; ?>
                </div>
                <h4><?= e($v['title'] ?: 'Video') ?></h4>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No videos available</p>
        <?php endif; ?>
    </div>
    <?php
    $video_section = ob_get_contents();
    ob_end_clean();
    echo "✅ Video section rendered successfully (" . strlen($video_section) . " bytes)<br>";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Video section failed: " . $e->getMessage() . "<br>";
}

echo "<h3>Testing JavaScript Section</h3>";
try {
    ob_start();
    ?>
    <script>
    let currentPlayingVideo = null;
    const videoObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                console.log("Video section in view - starting autoplay");
                setTimeout(() => {
                    const videoItems = entry.target.querySelectorAll('.video-item');
                    if (videoItems.length > 0) {
                        playVideo(videoItems[0], 0);
                    }
                }, 2000);
            }
        });
    }, { threshold: 0.5 });

    function playVideo(videoItem, videoIndex) {
        console.log(`Playing video ${videoIndex}`);
    }

    function stopVideo(videoItem, videoIndex) {
        console.log(`Stopping video ${videoIndex}`);
    }

    document.addEventListener("click", function(e) {
        if (e.target.closest(".video-play")) {
            e.preventDefault();
            const videoItem = e.target.closest(".video-item");
            const videoIndex = parseInt(videoItem.dataset.videoIndex);
            const platform = videoItem.dataset.platform || 'youtube';
            const videoUrl = videoItem.dataset.videoUrl;
            
            if (platform === 'instagram' || platform === 'tiktok') {
                window.open(videoUrl, '_blank');
                return;
            }
            
            playVideo(videoItem, videoIndex);
        }
    });

    document.addEventListener("DOMContentLoaded", function() {
        const videoSection = document.querySelector(".video-section");
        if (videoSection) {
            videoObserver.observe(videoSection);
        }
    });
    </script>
    <?php
    $js_section = ob_get_contents();
    ob_end_clean();
    echo "✅ JavaScript section rendered successfully (" . strlen($js_section) . " bytes)<br>";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ JavaScript section failed: " . $e->getMessage() . "<br>";
}

echo "<h3>🎉 All Template Sections Test Completed!</h3>";
echo "<p>If all sections pass, the problem might be in a specific combination or in the full template loading.</p>";
echo "<p><a href='/' target='_blank'>Try Homepage Now</a></p>";
?>