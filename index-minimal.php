<?php
// Minimal working homepage - copy index.php structure but simplified
require_once __DIR__ . '/inc/db.php';

// Base URL configuration (from index.php)
$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
} else {
    $base = '';
}

// Load minimal data
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

$link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';

// Include header
require_once __DIR__ . '/inc/header.php';
?>

<!-- Minimal Hero Section -->
<section class="hero" id="home">
    <div class="container">
        <h1>Ar Raihan Travelindo</h1>
        <p>Program Haji dan Umroh Terpercaya</p>
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
                <div class="video-item">
                    <img src="<?= $thumb ?>" alt="<?= e($v['title'] ?: 'Video') ?>" style="width: 200px; height: 150px;">
                    <h4><?= e($v['title'] ?: 'Video') ?></h4>
                    <p>Platform: <?= e($platform) ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No videos available</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="kontak" id="kontak">
    <div class="container">
        <h2>Hubungi Kami</h2>
        <p>Siap melayani perjalanan spiritual Anda</p>
        <?php if ($link_whatsapp): ?>
        <a href="<?= e($link_whatsapp) ?>" target="_blank">Contact WhatsApp</a>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>