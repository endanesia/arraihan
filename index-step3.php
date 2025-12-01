<?php
// Almost complete index.php - testing for the exact problematic section
require_once __DIR__ . '/inc/db.php';

// EXACT same initialization as original index.php
$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
} else {
    $base = '';
}

// Packages
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

// Testimonials - latest 4 approved for homepage
$testimonials = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 6")) {
        while ($row = $res->fetch_assoc()) { $testimonials[] = $row; }
    }
}

// Popup Banner - get active popup (THIS MIGHT BE THE CULPRIT!)
$popup_banner = null;
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM popup_banner WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1")) {
        $popup_banner = $res->fetch_assoc();
    }
}

// Social links from settings
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

// Hero slides data from database
$hero_slides = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")) {
        while ($row = $res->fetch_assoc()) { 
            $hero_slides[] = $row; 
        }
    }
}

// ALL greeting and about settings (might be causing issues)
$greeting_title = get_setting('greeting_title', 'Assalamu\'alaikum Warahmatullahi Wabarakatuh');
$greeting_subtitle = get_setting('greeting_subtitle', 'Calon Jamaah Yang Dirahmati Allah,');
$greeting_text = get_setting('greeting_text', 'Kami sepenuh hati siap membantu...');
$greeting_stats_title = get_setting('greeting_stats_title', 'Kepercayaan Jamaah');
$greeting_button_text = get_setting('greeting_button_text', 'Pelajari Lebih Lanjut');
$greeting_button_link = get_setting('greeting_button_link', '#paket');
$greeting_background = get_setting('greeting_background', '');

// Keunggulan data
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

// About Us data
$about_title = get_setting('about_title', 'Tentang Kami');
$about_content_p1 = get_setting('about_content_p1', 'Raihan Travelindo resmi didirikan...');
$about_content_p2 = get_setting('about_content_p2', 'Setelah itu, kami mulai merambah...');
$about_content_p3 = get_setting('about_content_p3', 'Kami telah mengantongi berbagai izin...');
$about_badge_number = get_setting('about_badge_number', '15.000+');
$about_badge_text = get_setting('about_badge_text', 'Jamaah Terlayani');
$about_image = get_setting('about_image', '');

// Prepare arrays for display
$phones = array_filter(array_map('trim', explode(',', $phone_number)));
$emails = array_filter(array_map('trim', explode(',', $company_email)));
$primary_phone_for_tel = !empty($phones) ? $phones[0] : $phone_number;

// EXACT same JavaScript as original
$extra_footer_scripts = '
<script>
// Simplified Video Functionality
let currentPlayingVideo = null;
let autoplayTimeout = null;

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

document.addEventListener("click", function(e) {
    if (e.target.closest(".video-play")) {
        e.preventDefault();
        const videoItem = e.target.closest(".video-item");
        const videoIndex = parseInt(videoItem.dataset.videoIndex);
        const platform = videoItem.dataset.platform || "youtube";
        const videoUrl = videoItem.dataset.videoUrl;
        
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

document.addEventListener("DOMContentLoaded", function() {
    console.log("Initializing video functionality");
    
    const videoSection = document.querySelector(".video-section");
    if (videoSection) {
        videoObserver.observe(videoSection);
    }
});
</script>';

require_once __DIR__ . '/inc/header.php';
?>

<h1>Test Step 3 - Almost Complete Homepage</h1>
<p>This version includes ALL data loading and settings from original index.php</p>
<p>Testing popup banner query and complex settings...</p>

<!-- Basic sections that we know work -->
<section class="hero" id="home">
    <div class="container">
        <h2>Hero Section</h2>
        <p>Hero slides: <?= count($hero_slides) ?></p>
    </div>
</section>

<section class="greeting">
    <div class="container">
        <h2><?= e($greeting_title) ?></h2>
        <h3><?= e($greeting_subtitle) ?></h3>
        <p><?= e($greeting_text) ?></p>
    </div>
</section>

<section class="packages">
    <div class="container">
        <h2>Packages: <?= count($packages) ?></h2>
    </div>
</section>

<section class="videos">
    <div class="container">
        <h2>Videos: <?= count($videos) ?></h2>
    </div>
</section>

<section class="testimonials">
    <div class="container">
        <h2>Testimonials: <?= count($testimonials) ?></h2>
    </div>
</section>

<!-- Test popup banner (suspect) -->
<?php if ($popup_banner): ?>
<div class="popup-test">
    <h3>Popup Banner Found</h3>
    <p>Title: <?= e($popup_banner['title']) ?></p>
</div>
<?php else: ?>
<div class="popup-test">
    <h3>No Popup Banner</h3>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/inc/footer.php'; ?>