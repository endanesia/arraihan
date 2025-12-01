<?php
// Step-by-step index.php - adding sections gradually
require_once __DIR__ . '/inc/db.php';

// Base URL configuration
$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
}

// Load packages data (from original index.php)
$packages = [];
if (function_exists('db') && db()) {
    try {
        if ($res = db()->query("SELECT * FROM packages ORDER BY featured DESC, id DESC")) {
            while ($row = $res->fetch_assoc()) { 
                $packages[] = $row; 
            }
        }
    } catch (Exception $e) {
        error_log("Database error in index.php: " . $e->getMessage());
    }
}

// Load videos (working version)
$videos = [];
try {
    if (function_exists('db') && db()) {
        $res = db()->query("SELECT youtube_id, title, platform, video_url FROM gallery_videos WHERE 
                            (platform = 'youtube' AND youtube_id IS NOT NULL AND youtube_id != '') OR 
                            (platform = 'instagram' AND video_url IS NOT NULL) OR 
                            (platform = 'tiktok' AND video_url IS NOT NULL)
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
}

// Load hero slides
$hero_slides = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")) {
        while ($row = $res->fetch_assoc()) { 
            $hero_slides[] = $row; 
        }
    }
}

// Load settings
$link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';

// Add JavaScript from original (this might be the culprit)
$extra_footer_scripts = '
<script>
let currentPlayingVideo = null;

function playVideo(videoItem, index) {
    console.log("Playing video " + index);
    currentPlayingVideo = index;
}

function stopVideo(videoItem, index) {
    console.log("Stopping video " + index);
    currentPlayingVideo = null;
}

document.addEventListener("DOMContentLoaded", function() {
    console.log("Homepage JS loaded");
});
</script>';

require_once __DIR__ . '/inc/header.php';
?>

<!-- Hero Section with Slideshow (like original but simplified) -->
<section class="hero" id="home">
    <?php if (!empty($hero_slides)): ?>
    <div class="swiper heroSwiper">
        <div class="swiper-wrapper">
            <?php foreach ($hero_slides as $slide): ?>
            <div class="swiper-slide">
                <div class="hero-slide"<?php if (!empty($slide['background_image'])): ?> style="background-image: url('<?= e($base . $slide['background_image']) ?>');"<?php endif; ?>>
                    <div class="hero-overlay"></div>
                    <div class="container">
                        <div class="hero-content">
                            <h1 class="hero-title"><?= e($slide['title']) ?></h1>
                            <p class="hero-subtitle"><?= e($slide['subtitle']) ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div class="container">
        <h1>Ar Raihan Travelindo</h1>
        <p>Program Haji dan Umroh Terpercaya</p>
    </div>
    <?php endif; ?>
</section>

<!-- Packages Section -->
<section class="paket" id="paket">
    <div class="container">
        <h2>Program Haji & Umroh</h2>
        <div class="packages-grid">
            <?php if (!empty($packages)): ?>
                <?php foreach (array_slice($packages, 0, 6) as $pkg): ?>
                <div class="package-card">
                    <h3><?= e($pkg['title']) ?></h3>
                    <div class="package-price">
                        <span class="price-label"><?= e($pkg['price_label']) ?></span>
                        <span class="price-value"><?= e($pkg['price_value']) ?></span>
                        <span class="price-unit"><?= e($pkg['price_unit']) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Belum ada paket tersedia</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Video Section -->
<section class="video-section">
    <div class="container">
        <h2>Galeri Video</h2>
        <div class="video-grid">
            <?php if (!empty($videos)): ?>
                <?php foreach ($videos as $index => $v): 
                    $platform = isset($v['platform']) ? $v['platform'] : 'youtube';
                    $youtube_id = isset($v['youtube_id']) ? $v['youtube_id'] : '';
                    $video_url = isset($v['video_url']) ? $v['video_url'] : '';
                    
                    if ($platform === 'youtube' && !empty($youtube_id)) {
                        $thumb = 'https://img.youtube.com/vi/'.e($youtube_id).'/hqdefault.jpg';
                    } elseif ($platform === 'instagram') {
                        $thumb = 'https://via.placeholder.com/480x360/E4405F/white?text=Instagram+Video';
                    } else {
                        $thumb = 'https://via.placeholder.com/480x360/6c757d/white?text=Video';
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
    </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>