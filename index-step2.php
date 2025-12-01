<?php
// Step2 - Adding more sections from original index.php
require_once __DIR__ . '/inc/db.php';

// Base URL configuration
$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
}

// Load ALL data like in original
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

// Partners
$partners = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM partners ORDER BY id DESC LIMIT 12")) {
        while ($row = $res->fetch_assoc()) { $partners[] = $row; }
    }
}

// Schedules (upcoming)
$schedules = [];
if (function_exists('db') && db()) {
    $q = "SELECT * FROM schedules WHERE departure_date IS NOT NULL AND departure_date >= CURDATE() ORDER BY departure_date ASC LIMIT 6";
    if ($res = db()->query($q)) { while ($row = $res->fetch_assoc()) { $schedules[] = $row; } }
    if (empty($schedules)) {
        if ($res = db()->query("SELECT * FROM schedules ORDER BY id DESC LIMIT 6")) {
            while ($row = $res->fetch_assoc()) { $schedules[] = $row; }
        }
    }
}

// Videos 
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

// Mutawwif dan Tour Leader
$mutawwif = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM mutawwif WHERE is_active = 1 ORDER BY urutan ASC, id ASC")) {
        while ($row = $res->fetch_assoc()) { $mutawwif[] = $row; }
    }
}

// Testimonials
$testimonials = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 6")) {
        while ($row = $res->fetch_assoc()) { $testimonials[] = $row; }
    }
}

// Hero slides
$hero_slides = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")) {
        while ($row = $res->fetch_assoc()) { 
            $hero_slides[] = $row; 
        }
    }
}

// ALL settings from original index.php
$link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
$link_facebook = function_exists('get_setting') ? get_setting('facebook', '') : '';
$link_instagram = function_exists('get_setting') ? get_setting('instagram', '') : '';
$link_youtube = function_exists('get_setting') ? get_setting('youtube', '') : '';
$link_tiktok = function_exists('get_setting') ? get_setting('tiktok', '') : '';
$link_twitter = function_exists('get_setting') ? get_setting('twitter', '') : '';
$link_threads = function_exists('get_setting') ? get_setting('threads', '') : '';
$phone_number = function_exists('get_setting') ? get_setting('phone', '+6281234567890') : '+6281234567890';

// Company info settings
$company_address = function_exists('get_setting') ? get_setting('address', '') : '';
$company_email = function_exists('get_setting') ? get_setting('email', '') : '';
$company_hours = function_exists('get_setting') ? get_setting('hours', '') : '';

// Greeting section settings
$greeting_title = get_setting('greeting_title', 'Assalamu\'alaikum Warahmatullahi Wabarakatuh');
$greeting_subtitle = get_setting('greeting_subtitle', 'Calon Jamaah Yang Dirahmati Allah,');
$greeting_text = get_setting('greeting_text', 'Kami sepenuh hati siap membantu dan mendampingi Anda dalam mewujudkan impian untuk beribadah ke Tanah Suci.');

// About Us data
$about_title = get_setting('about_title', 'Tentang Kami');
$about_content_p1 = get_setting('about_content_p1', 'Raihan Travelindo resmi didirikan pada tahun 2005...');

// Keunggulan (Benefits) data
$keunggulan = [
    [
        'icon' => get_setting('about_keunggulan_1_icon', 'fas fa-certificate'),
        'title' => get_setting('about_keunggulan_1_title', 'Izin PPIU Resmi'),
        'description' => get_setting('about_keunggulan_1_desc', 'Terdaftar Kementerian Agama RI')
    ],
    [
        'icon' => get_setting('about_keunggulan_2_icon', 'fas fa-award'),
        'title' => get_setting('about_keunggulan_2_title', 'Izin PIHK Resmi'),
        'description' => get_setting('about_keunggulan_2_desc', 'Penyelenggara Ibadah Haji Khusus')
    ],
    [
        'icon' => get_setting('about_keunggulan_3_icon', 'fas fa-shield-alt'),
        'title' => get_setting('about_keunggulan_3_title', 'Sertifikat ISO 9001:2015'),
        'description' => get_setting('about_keunggulan_3_desc', 'Sistem Manajemen Mutu Terjamin')
    ]
];

// Prepare arrays for display
$phones = array_filter(array_map('trim', explode(',', $phone_number)));
$emails = array_filter(array_map('trim', explode(',', $company_email)));

// Add the COMPLETE JavaScript from original (this might cause the error)
$extra_footer_scripts = '
<script>
// Simplified Video Functionality - exactly from original
let currentPlayingVideo = null;
let autoplayTimeout = null;

// Intersection Observer for video autoplay
const videoObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            console.log("Video section is visible");
            const videoSection = entry.target;
            const videoItems = videoSection.querySelectorAll(".video-item");
            
            if (videoItems.length > 0 && currentPlayingVideo === null) {
                autoplayTimeout = setTimeout(() => {
                    playVideo(videoItems[0], 0);
                }, 1000);
            }
        } else {
            console.log("Video section is not visible");
            if (currentPlayingVideo !== null) {
                stopAllVideos();
            }
            if (autoplayTimeout) {
                clearTimeout(autoplayTimeout);
                autoplayTimeout = null;
            }
        }
    });
}, {
    threshold: 0.3
});

function playVideo(videoItem, index) {
    console.log(`Playing video ${index}`);
    
    const thumbnail = videoItem.querySelector(".video-thumbnail img");
    const playButton = videoItem.querySelector(".video-play");
    const iframeContainer = videoItem.querySelector(".video-iframe-container");
    const iframe = videoItem.querySelector("iframe");
    
    if (thumbnail && playButton && iframeContainer && iframe) {
        thumbnail.style.display = "none";
        playButton.style.display = "none";
        iframeContainer.style.display = "block";
        
        const currentSrc = iframe.src;
        if (currentSrc.indexOf("autoplay=0") !== -1) {
            iframe.src = currentSrc.replace("autoplay=0", "autoplay=1");
        }
        
        currentPlayingVideo = index;
        
        setTimeout(() => {
            stopVideo(videoItem, index);
        }, 5000);
    }
}

function stopVideo(videoItem, index) {
    console.log(`Stopping video ${index}`);
    
    const thumbnail = videoItem.querySelector(".video-thumbnail img");
    const playButton = videoItem.querySelector(".video-play");
    const iframeContainer = videoItem.querySelector(".video-iframe-container");
    const iframe = videoItem.querySelector("iframe");
    
    if (thumbnail && playButton && iframeContainer && iframe) {
        thumbnail.style.display = "block";
        playButton.style.display = "block";
        iframeContainer.style.display = "none";
        
        const currentSrc = iframe.src;
        if (currentSrc.indexOf("autoplay=1") !== -1) {
            iframe.src = currentSrc.replace("autoplay=1", "autoplay=0");
        }
        
        if (currentPlayingVideo === index) {
            currentPlayingVideo = null;
        }
    }
}

function stopAllVideos() {
    const videoItems = document.querySelectorAll(".video-item");
    videoItems.forEach((item, index) => {
        stopVideo(item, index);
    });
}

// Manual play button click handler
document.addEventListener("click", function(e) {
    if (e.target.closest(".video-play")) {
        e.preventDefault();
        const videoItem = e.target.closest(".video-item");
        const videoIndex = parseInt(videoItem.dataset.videoIndex);
        const platform = videoItem.dataset.platform || "youtube";
        const videoUrl = videoItem.dataset.videoUrl;
        
        console.log(`Manual click on video ${videoIndex}, platform: ${platform}`);
        
        if (platform === "instagram" || platform === "tiktok") {
            window.open(videoUrl, "_blank");
            return;
        }
        
        if (currentPlayingVideo !== null && currentPlayingVideo !== videoIndex) {
            const currentVideoItem = document.querySelector(`[data-video-index="${currentPlayingVideo}"]`);
            if (currentVideoItem) {
                stopVideo(currentVideoItem, currentPlayingVideo);
            }
        }
        
        playVideo(videoItem, videoIndex);
    }
});

// Initialize when page loads
document.addEventListener("DOMContentLoaded", function() {
    console.log("Initializing video functionality");
    
    const videoSection = document.querySelector(".video-section");
    if (videoSection) {
        console.log("Video section found, setting up observer");
        videoObserver.observe(videoSection);
    } else {
        console.log("Video section not found");
    }
});
</script>';

require_once __DIR__ . '/inc/header.php';
?>

<!-- Same content as Step1 but with more sections -->
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
    <?php endif; ?>
</section>

<!-- About Section -->
<section class="about" id="about">
    <div class="container">
        <h2><?= e($about_title) ?></h2>
        <p><?= e($about_content_p1) ?></p>
        
        <div class="keunggulan-grid">
            <?php foreach ($keunggulan as $k): ?>
            <div class="keunggulan-item">
                <i class="<?= e($k['icon']) ?>"></i>
                <h3><?= e($k['title']) ?></h3>
                <p><?= e($k['description']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Video Section with complex JavaScript -->
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
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="testimonials">
    <div class="container">
        <h2>Testimonial Jamaah</h2>
        <div class="testimonials-grid">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $test): ?>
                <div class="testimonial-item">
                    <h4><?= e($test['nama']) ?></h4>
                    <p><?= e($test['pesan']) ?></p>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>