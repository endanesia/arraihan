<?php require_once __DIR__ . '/inc/db.php';

// Base URL configuration
$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    // Local development environment in /dev/ subfolder
    $base = '/dev/';
} else {
    // Production environment (root domain)
    $base = '';
}

// Fetch dynamic content (if DB available)
$images = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT file_path, title FROM gallery_images ORDER BY id DESC LIMIT 6")) {
        while ($row = $res->fetch_assoc()) { $images[] = $row; }
    }
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
        // Fallback jika ada error
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
if (function_exists('db') && db()) {
    try {
        // Check if new columns exist for multi-platform support
        $video_table_columns = [];
        $columns_result = db()->query("DESCRIBE gallery_videos");
        if ($columns_result) {
            while ($col = $columns_result->fetch_assoc()) {
                $video_table_columns[] = $col['Field'];
            }
        }
        
        $has_platform_column = in_array('platform', $video_table_columns);
        $has_video_url_column = in_array('video_url', $video_table_columns);
        
        if ($has_platform_column && $has_video_url_column) {
            // Multi-platform query
            $res = db()->query("SELECT youtube_id, title, platform, video_url FROM gallery_videos ORDER BY id DESC LIMIT 3");
            if ($res) {
                while ($row = $res->fetch_assoc()) { 
                    $videos[] = $row; 
                }
            }
        } else {
            // Legacy YouTube-only query
            $res = db()->query("SELECT youtube_id, title FROM gallery_videos ORDER BY id DESC LIMIT 3");
            if ($res) {
                while ($row = $res->fetch_assoc()) { 
                    $videos[] = $row; 
                }
            }
        }
    } catch (Exception $e) {
        // Fallback to empty videos array if any error occurs
        $videos = [];
        error_log("Video fetching error: " . $e->getMessage());
    }
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

// Popup Banner - get active popup
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

// Greeting section settings - using existing get_setting function from db.php
$greeting_title = get_setting('greeting_title', 'Assalamu\'alaikum Warahmatullahi Wabarakatuh');
$greeting_subtitle = get_setting('greeting_subtitle', 'Calon Jamaah Yang Dirahmati Allah,');
$greeting_text = get_setting('greeting_text', 'Kami sepenuh hati siap membantu dan mendampingi Anda dalam mewujudkan impian untuk beribadah ke Tanah Suci. Mulai dari persiapan keberangkatan, pelaksanaan ibadah di Tanah Suci, hingga kepulangan Anda ke tanah air, kami akan memastikan setiap langkah perjalanan Anda berjalan lancar, aman, dan penuh berkah.');
$greeting_stats_title = get_setting('greeting_stats_title', 'Kepercayaan Jamaah');
$greeting_button_text = get_setting('greeting_button_text', 'Pelajari Lebih Lanjut');
$greeting_button_link = get_setting('greeting_button_link', '#paket');
$greeting_background = get_setting('greeting_background', '');

// Keunggulan (Benefits) data from settings
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

// About Us data from database
$about_title = get_setting('about_title', 'Tentang Kami');
$about_content_p1 = get_setting('about_content_p1', 'Raihan Travelindo resmi didirikan pada tahun 2005 oleh para pendiri yang memiliki visi mulia untuk membantu umat Islam menunaikan ibadah ke Tanah Suci. Pada awal berdirinya, perusahaan ini bergerak di bidang pariwisata, mulai dari tiket domestik dan penerbangan internasional.');
$about_content_p2 = get_setting('about_content_p2', 'Setelah itu, kami mulai merambah ke bisnis layanan jasa travel umroh dan haji khusus. Nama Raihan Travelindo terinspirasi dari Ka\'bah yang merupakan rumah Allah SWT yang suci dan penuh berkah, dengan harapan dapat memberikan pelayanan terbaik yang penuh keberkahan kepada setiap jamaah.');
$about_content_p3 = get_setting('about_content_p3', 'Kami telah mengantongi berbagai izin resmi dari Pemerintah RI. Hal ini merupakan bukti nyata keseriusan kami dalam memberikan layanan terbaik untuk para calon Tamu Allah SWT. Dengan pengalaman lebih dari 18 tahun dan telah melayani lebih dari 15.000 jamaah, kami berkomitmen untuk terus meningkatkan kualitas pelayanan.');
$about_badge_number = get_setting('about_badge_number', '15.000+');
$about_badge_text = get_setting('about_badge_text', 'Jamaah Terlayani');
$about_image = get_setting('about_image', '');

// Prepare arrays for display (phones/emails may be comma separated)
$phones = array_filter(array_map('trim', explode(',', $phone_number)));
$emails = array_filter(array_map('trim', explode(',', $company_email)));
// For tel: link, use first phone if available
$primary_phone_for_tel = !empty($phones) ? $phones[0] : $phone_number;

// Page configuration for header template
$page_title = 'Raihan Travelindo - Travel Haji & Umroh Terpercaya';
$page_description = 'Travel Umroh & Haji Terpercaya - Berizin Resmi Kemenag RI dengan Akreditasi A';
$current_page = 'home';
$include_swiper = true;

// Extra head content for Cloudflare Turnstile
$extra_head_content = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';

// Extra footer scripts for video autoplay functionality
$extra_footer_scripts = '
<script>
// Simplified Video Functionality
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
                // Start autoplay for the first video after a short delay
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
        // Hide thumbnail, show iframe
        thumbnail.style.display = "none";
        playButton.style.display = "none";
        iframeContainer.style.display = "block";
        
        // Change iframe src to autoplay
        const currentSrc = iframe.src;
        const newSrc = currentSrc.replace("autoplay=0", "autoplay=1");
        iframe.src = newSrc;
        
        currentPlayingVideo = index;
        console.log(`Video ${index} is now playing`);
        
        // Stop video after 5 seconds for autoplay
        if (autoplayTimeout) {
            setTimeout(() => {
                if (currentPlayingVideo === index) {
                    stopVideo(videoItem, index);
                }
            }, 5000);
        }
    }
}

function stopVideo(videoItem, index) {
    console.log(`Stopping video ${index}`);
    
    const thumbnail = videoItem.querySelector(".video-thumbnail img");
    const playButton = videoItem.querySelector(".video-play");
    const iframeContainer = videoItem.querySelector(".video-iframe-container");
    const iframe = videoItem.querySelector("iframe");
    
    if (thumbnail && playButton && iframeContainer && iframe) {
        // Show thumbnail, hide iframe
        thumbnail.style.display = "block";
        playButton.style.display = "block";
        iframeContainer.style.display = "none";
        
        // Reset iframe src to stop video
        const currentSrc = iframe.src;
        const newSrc = currentSrc.replace("autoplay=1", "autoplay=0");
        iframe.src = newSrc;
        
        if (currentPlayingVideo === index) {
            currentPlayingVideo = null;
        }
    }
}

function stopAllVideos() {
    console.log("Stopping all videos");
    const videoItems = document.querySelectorAll(".video-item");
    videoItems.forEach((item, index) => {
        if (currentPlayingVideo === index) {
            stopVideo(item, index);
        }
    });
}

// Manual play button click handler
document.addEventListener("click", function(e) {
    if (e.target.closest(".video-play")) {
        e.preventDefault();
        const videoItem = e.target.closest(".video-item");
        const videoIndex = parseInt(videoItem.dataset.videoIndex);
        const platform = videoItem.dataset.platform || 'youtube';
        const videoUrl = videoItem.dataset.videoUrl;
        
        console.log(`Manual click on video ${videoIndex}, platform: ${platform}`);
        
        // For non-YouTube platforms, open in new window/tab
        if (platform === 'instagram' || platform === 'tiktok') {
            window.open(videoUrl, '_blank');
            return;
        }
        
        // For YouTube videos, continue with current functionality
        // Stop currently playing video
        if (currentPlayingVideo !== null && currentPlayingVideo !== videoIndex) {
            const currentVideoItem = document.querySelector(`[data-video-index="${currentPlayingVideo}"]`);
            if (currentVideoItem) {
                stopVideo(currentVideoItem, currentPlayingVideo);
            }
        }
        
        // Play clicked video
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

// Include header template
require_once __DIR__ . '/inc/header.php';
?>

    <!-- Hero Section with Slideshow -->
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
                                <div class="hero-badge">
                                    <i class="fas fa-certificate"></i>
                                    <span>PT. Raihan Islami Travelindo</span>
                                </div>
                                <h1 class="hero-title"><?= e($slide['title']) ?></h1>
                                <p class="hero-subtitle"><?= e($slide['subtitle']) ?></p>
                                <div class="hero-buttons">
                                    <?php if (!empty($slide['button_text'])): ?>
                                    <a href="<?= e($slide['button_link']) ?>" class="btn btn-primary">
                                        <i class="fas fa-calendar-check"></i> <?= e($slide['button_text']) ?>
                                    </a>
                                    <?php endif; ?>
                                    <?php if (!empty($link_whatsapp)): ?>
                                    <a href="<?= e($link_whatsapp) ?>" class="btn btn-secondary" target="_blank">
                                        <i class="fab fa-whatsapp"></i> Konsultasi Gratis
                                    </a>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($slide['stat1_text']) || !empty($slide['stat2_text'])): ?>
                                <div class="hero-stats">
                                    <?php if (!empty($slide['stat1_text'])): ?>
                                    <div class="stat-item">
                                        <h3><i class="fas fa-users"></i> <?= e($slide['stat1_text']) ?></h3>
                                        <p><?= e($slide['stat1_desc']) ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($slide['stat2_text'])): ?>
                                    <div class="stat-item">
                                        <h3><i class="fas fa-award"></i> <?= e($slide['stat2_text']) ?></h3>
                                        <p><?= e($slide['stat2_desc']) ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Navigation -->
            <div class="swiper-button-next hero-button-next"></div>
            <div class="swiper-button-prev hero-button-prev"></div>
            
            <!-- Pagination -->
            <div class="swiper-pagination hero-pagination"></div>
        </div>
        <?php else: ?>
        <!-- Fallback single hero -->
        <div class="hero-slide" style="background-image: url('<?= $base ?>/images/hero-bg.jpg');">
            <div class="hero-overlay"></div>
            <div class="container">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fas fa-certificate"></i>
                        <span>PT. Raihan Islami Travelindo</span>
                    </div>
                    <h1 class="hero-title">Perjalanan Suci Berkualitas, Biaya Bersahabat</h1>
                    <p class="hero-subtitle">Jangan biarkan biaya menunda niat suci Anda. Program Umroh terjangkau dengan tetap berkualitas layanan terbaik, mencakup akomodasi dan bimbingan yang profesional.</p>
                    <div class="hero-buttons">
                        <a href="#paket" class="btn btn-primary">
                            <i class="fas fa-calendar-check"></i> Lihat Program Umroh
                        </a>
                        <?php if (!empty($link_whatsapp)): ?>
                        <a href="<?= e($link_whatsapp) ?>" class="btn btn-secondary" target="_blank">
                            <i class="fab fa-whatsapp"></i> Konsultasi Gratis
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="hero-scroll">
            <a href="#paket">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Greeting Section -->
    <section class="greeting"<?php if ($greeting_background): ?> style="background-image: url('<?= e($greeting_background) ?>');"<?php endif; ?>>
        <div class="container">
            <div class="greeting-content">
                <h2 class="section-title"><?= e($greeting_title) ?></h2>
                <h3 class="section-subtitle"><?= e($greeting_subtitle) ?></h3>
                <div class="greeting-text">
                    <?= nl2br(e($greeting_text)) ?>
                </div>
                <?php if (!empty($greeting_stats_title)): ?>
                <div class="greeting-stats">
                    <h4 class="stats-title"><?= e($greeting_stats_title) ?></h4>
                </div>
                <?php endif; ?>
                <?php if (!empty($greeting_button_text) && !empty($greeting_button_link)): ?>
                <div class="greeting-action">
                    <a href="<?= e($greeting_button_link) ?>" class="btn btn-primary"><?= e($greeting_button_text) ?></a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Program Section -->
    <section class="paket" id="paket">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Program Perjalanan Kami</h2>
                <p class="section-desc">Pilih program yang sesuai dengan kebutuhan ibadah Anda</p>
            </div>
            
            <!-- Package Swiper Slider -->
            <?php if (!empty($packages)): ?>
            <div class="swiper packageSwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($packages as $p): ?>
                    <div class="swiper-slide">
                        <div class="package-poster-card">
                            <?php if ($p['featured']): ?>
                            <div class="package-popular-badge">
                                <i class="fas fa-star"></i> Populer
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($p['category'])): ?>
                            <div class="package-category-badge">
                                <?= e($p['category']) ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="package-poster-image">
                                <?php if (!empty($p['poster'])): ?>
                                    <img src="<?= $base ?>/images/packages/<?= e($p['poster']) ?>" 
                                         alt="<?= e($p['title']) ?>" 
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="package-no-image">
                                        <i class="<?= e($p['icon_class'] ?: 'fas fa-moon') ?> fa-3x"></i>
                                    </div>
                                <?php endif; ?>

                            </div>
                            
                            <div class="package-poster-info">
                                <h4><?= e($p['title']) ?></h4>
                                <a href="paket-detail?id=<?= (int)$p['id'] ?>" class="btn btn-detail">
                                    <i class="fas fa-info-circle"></i> Detail Program
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Swiper Navigation -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>
            </div>
            <?php else: ?>
            <!-- No programs message -->
            <p class="text-center">Belum ada program tersedia.</p>
            <?php endif; ?>
        </div>
    </section>

    <!-- Jadwal Section -->
    <section class="jadwal" id="jadwal">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Jadwal Keberangkatan</h2>
                <p class="section-desc">Jadwal umroh tersedia setiap bulan dengan keberangkatan pasti</p>
            </div>

            <div class="jadwal-cta">
                <div class="jadwal-content">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Lihat Jadwal Lengkap Keberangkatan</h3>
                    <p>Tersedia berbagai pilihan tanggal keberangkatan yang bisa disesuaikan dengan jadwal Anda</p>
                    <a href="jadwal.php" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Cek Jadwal
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Keunggulan Section -->
    <section class="keunggulan" id="keunggulan">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Keunggulan Kami</h2>
                <p class="section-desc">Mengapa memilih Raihan Travelindo untuk perjalanan ibadah Anda</p>
            </div>
            <div class="keunggulan-grid">
                <?php if (!empty($keunggulan)): ?>
                    <?php foreach ($keunggulan as $item): ?>
                    <div class="keunggulan-card">
                        <div class="keunggulan-icon">
                            <i class="<?= e($item['icon']) ?>"></i>
                        </div>
                        <h3><?= e($item['title']) ?></h3>
                        <p><?= e($item['description']) ?></p>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback content if no keunggulan data -->
                    <div class="keunggulan-card">
                        <div class="keunggulan-icon">
                            <i class="fas fa-award"></i>
                        </div>
                        <h3>Mutu Pelayanan</h3>
                        <p>Telah lulus uji dan bersertifikasi memenuhi kualifikasi SNI ISO 9001:2015 QMS</p>
                    </div>
                    <div class="keunggulan-card">
                        <div class="keunggulan-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Jadwal Pasti</h3>
                        <p>Semua program sudah tertera tanggal berangkat, nomor pesawat & itinerary perjalanan yang jelas</p>
                    </div>
                    <div class="keunggulan-card">
                        <div class="keunggulan-icon">
                            <i class="fas fa-plane"></i>
                        </div>
                        <h3>Direct Flight</h3>
                        <p>Penerbangan langsung tanpa transit sehingga jamaah tidak perlu menunggu dan lebih nyaman</p>
                    </div>
                    <div class="keunggulan-card">
                        <div class="keunggulan-icon">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <h3>Hotel Strategis</h3>
                        <p>Hotel kami di ring 1 dekat masjid, baik di Masjidil Haram maupun di Masjid Nabawi</p>
                    </div>
                    <div class="keunggulan-card">
                        <div class="keunggulan-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3>Muthawif Profesional</h3>
                        <p>Pembimbing ibadah yang berpengalaman dan bermukim di Mekkah untuk melayani jamaah</p>
                    </div>
                    <div class="keunggulan-card">
                        <div class="keunggulan-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h3>Wireless Headset</h3>
                        <p>Asistensi menggunakan alat bantu wireless headset untuk memudahkan thawaf dan sa'i</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Galeri Section -->
    <section class="galeri" id="galeri">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Dokumentasi Jamaah</h2>
                <p class="section-desc">Alhamdulillah, Insya Allah Mabrur</p>
            </div>
            <div class="galeri-grid">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $img): ?>
                    <div class="galeri-item">
                        <div class="galeri-image">
                            <img src="<?= $base . e($img['file_path']) ?>" alt="<?= e($img['title'] ?: 'Galeri') ?>">
                            <div class="galeri-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="galeri-item">
                        <div class="galeri-image">
                            <img src="https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=500&h=350&fit=crop" alt="Jamaah di Masjidil Haram">
                            <div class="galeri-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    </div>
                    <div class="galeri-item">
                        <div class="galeri-image">
                            <img src="https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=500&h=350&fit=crop" alt="Jamaah di Masjid Nabawi">
                            <div class="galeri-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    </div>
                    <div class="galeri-item">
                        <div class="galeri-image">
                            <img src="https://i0.wp.com/tfamanasek.com/wp-content/uploads/2025/07/tfa-manasek-spesialis-handling-umroh-jasa-dokumentasi-umroh2-686e531b9331e.webp?ssl=1" alt="Jamaah Thawaf">
                            <div class="galeri-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    </div>
                    <div class="galeri-item">
                        <div class="galeri-image">
                            <img src="https://images.unsplash.com/photo-1580418827493-f2b22c0a76cb?w=500&h=350&fit=crop" alt="Jamaah di Jabal Rahmah">
                            <div class="galeri-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    </div>
                    <div class="galeri-item">
                        <div class="galeri-image">
                            <img src="https://images.unsplash.com/photo-1583422409516-2895a77efded?w=500&h=350&fit=crop" alt="Jamaah Keberangkatan">
                            <div class="galeri-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    </div>
                    <div class="galeri-item">
                        <div class="galeri-image">
                            <img src="https://muslimpergi.com/wp-content/uploads/2023/02/dokumentasi-saat-umroh-bisa-dijadikan-bukti-untuk-keluarga-tercinta-1024x574.jpg" alt="Jamaah di Tanah Suci">
                            <div class="galeri-overlay">
                                <i class="fas fa-search-plus"></i>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Gallery More Button -->
            <div class="section-more">
                <a href="galeri.php" class="btn btn-outline">
                    <i class="fas fa-images"></i> Lihat Semua Galeri
                </a>
            </div>
        </div>
    </section>

    <!-- Mutawwif dan Tour Leader Section -->
    <section class="mutawwif">
        <div class="container">
            <div class="section-header">
                <p class="section-subtitle">MUTAWWIF DAN TOUR LEADER</p>
                <h2 class="section-title">Mutawwif dan Tour Leader Professional</h2>
            </div>
            
            <div class="swiper mutawwifSwiper">
                <div class="swiper-wrapper">
                <?php if (!empty($mutawwif)): ?>
                    <?php foreach ($mutawwif as $mw): ?>
                    <div class="swiper-slide">
                        <div class="mutawwif-card">
                            <div class="mutawwif-image">
                                <?php if (!empty($mw['foto'])): ?>
                                    <img src="<?= $base . 'images/mutawwif/' . e($mw['foto']) ?>" alt="<?= e($mw['nama']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="mutawwif-placeholder">
                                        <i class="fas fa-user-tie fa-3x"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="mutawwif-info">
                                <h3><?= e($mw['nama']) ?></h3>
                                <p class="mutawwif-jabatan"><?= e($mw['jabatan']) ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback jika belum ada data -->
                    <div class="swiper-slide">
                        <div class="mutawwif-card">
                            <div class="mutawwif-image">
                                <div class="mutawwif-placeholder">
                                    <i class="fas fa-user-tie fa-3x"></i>
                                </div>
                            </div>
                            <div class="mutawwif-info">
                                <h3>Uztad Mochammad Munir Djamil</h3>
                                <p class="mutawwif-jabatan">Mutawwif Indonesia</p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="mutawwif-card">
                            <div class="mutawwif-image">
                                <div class="mutawwif-placeholder">
                                    <i class="fas fa-user-tie fa-3x"></i>
                                </div>
                            </div>
                            <div class="mutawwif-info">
                                <h3>Ustad Achmad Suudi Bin Sulaiman</h3>
                                <p class="mutawwif-jabatan">Mutawwif Indonesia</p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="mutawwif-card">
                            <div class="mutawwif-image">
                                <div class="mutawwif-placeholder">
                                    <i class="fas fa-user-tie fa-3x"></i>
                                </div>
                            </div>
                            <div class="mutawwif-info">
                                <h3>Ustad Muhammad Yazid Abdul Malik</h3>
                                <p class="mutawwif-jabatan">Mutawwif Indonesia</p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="mutawwif-card">
                            <div class="mutawwif-image">
                                <div class="mutawwif-placeholder">
                                    <i class="fas fa-user-tie fa-3x"></i>
                                </div>
                            </div>
                            <div class="mutawwif-info">
                                <h3>Ustad Muzakki Munawi</h3>
                                <p class="mutawwif-jabatan">Mutawwif Indonesia</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                </div>
                
                <!-- Swiper Navigation -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- Tentang Section -->
    <section class="tentang" id="tentang">
        <div class="container">
            <div class="tentang-wrapper">
                <div class="tentang-content">
                    <h2 class="section-title"><?= e($about_title) ?></h2>
                    <?php if ($about_content_p1): ?>
                    <p><?= nl2br(e($about_content_p1)) ?></p>
                    <?php endif; ?>
                    
                    <?php if ($about_content_p2): ?>
                    <p><?= nl2br(e($about_content_p2)) ?></p>
                    <?php endif; ?>
                    
                    <?php if ($about_content_p3): ?>
                    <p><?= nl2br(e($about_content_p3)) ?></p>
                    <?php endif; ?>
                    
                    <div class="tentang-features">
                        <div class="feature-item">
                            <i class="fas fa-certificate"></i>
                            <div>
                                <h4>Izin PPIU Resmi</h4>
                                <p>Terdaftar Kementerian Agama RI</p>
                            </div>
                        </div>
                        <!-- <div class="feature-item">
                            <i class="fas fa-award"></i>
                            <div>
                                <h4>Izin PIHK Resmi</h4>
                                <p>Penyelenggara Ibadah Haji Khusus</p>
                            </div>
                        </div>
                        -->
                    </div>
                </div>
                <div class="tentang-image">
                    <img src="<?= !empty($about_image) ? $base . e($about_image) : $base . 'images/bg.jpeg' ?>" alt="<?= e($about_title) ?>">
                    <?php if ($about_badge_number || $about_badge_text): ?>
                    <div class="tentang-badge">
                        <i class="fas fa-users"></i>
                        <div>
                            <h4><?= e($about_badge_number) ?></h4>
                            <p><?= e($about_badge_text) ?></p>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Video Section -->
    <section class="video-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Video Dokumentasi</h2>
                <p class="section-desc">Testimoni dan pengalaman jamaah bersama kami</p>
            </div>
            <div class="video-grid">
                <?php if (!empty($videos)): ?>
                    <?php foreach ($videos as $index => $v): 
                        // Safely determine platform and thumbnail
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
                            <div class="position-relative">
                                <img src="<?= $thumb ?>" alt="<?= e($v['title'] ?: 'Video') ?>">
                                <div class="position-absolute top-0 end-0 m-2">
                                    <span class="badge bg-light text-dark"><?= $platform_icon ?></span>
                                </div>
                            </div>
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
                    <div class="video-item" data-youtube-id="dQw4w9WgXcQ" data-video-index="0">
                        <div class="video-thumbnail">
                            <img src="https://muslimpergi.com/wp-content/uploads/2023/02/dokumentasi-saat-umroh-bisa-dijadikan-bukti-untuk-keluarga-tercinta-1024x574.jpg" alt="Video Testimoni">
                            <div class="video-play">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="video-iframe-container" style="display: none;">
                                <iframe 
                                    id="youtube-player-0"
                                    width="100%" 
                                    height="200" 
                                    src="https://www.youtube.com/embed/dQw4w9WgXcQ?enablejsapi=1&autoplay=0&mute=1&controls=1&rel=0" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                        <h4>Testimoni Jamaah Umroh 2024</h4>
                    </div>
                    <div class="video-item" data-youtube-id="ScMzIvxBSi4" data-video-index="1">
                        <div class="video-thumbnail">
                            <img src="https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=400&h=300&fit=crop" alt="Video Perjalanan">
                            <div class="video-play">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="video-iframe-container" style="display: none;">
                                <iframe 
                                    id="youtube-player-1"
                                    width="100%" 
                                    height="200" 
                                    src="https://www.youtube.com/embed/ScMzIvxBSi4?enablejsapi=1&autoplay=0&mute=1&controls=1&rel=0" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                        <h4>Perjalanan Ibadah ke Tanah Suci</h4>
                    </div>
                    <div class="video-item" data-youtube-id="M7lc1UVf-VE" data-video-index="2">
                        <div class="video-thumbnail">
                            <img src="https://haqiqitour.com/wp-content/uploads/2024/02/DOKUMENTASI-JAMAAH-UMROH-HAQIQI-TOUR-TRAVEL-21.jpeg" alt="Video Fasilitas">
                            <div class="video-play">
                                <i class="fas fa-play"></i>
                            </div>
                            <div class="video-iframe-container" style="display: none;">
                                <iframe 
                                    id="youtube-player-2"
                                    width="100%" 
                                    height="200" 
                                    src="https://www.youtube.com/embed/M7lc1UVf-VE?enablejsapi=1&autoplay=0&mute=1&controls=1&rel=0" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                        <h4>Fasilitas Hotel & Layanan</h4>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Video More Button -->
            <div class="section-more">
                <a href="galeri.php?tab=videos" class="btn btn-outline">
                    <i class="fas fa-video"></i> Lihat Semua Video
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Apa yang bisa kami bantu untuk berangkat ke Tanah Suci?</h2>
                <p>Hubungi kami sekarang untuk konsultasi gratis dan dapatkan penawaran terbaik</p>
                <div class="cta-buttons">
                    <?php if (!empty($link_whatsapp)): ?>
                    <a href="<?= e($link_whatsapp) ?>" class="btn btn-light" target="_blank"><i class="fab fa-whatsapp"></i> Hubungi via WhatsApp</a>
                    <?php endif; ?>
                    <a href="tel:<?= e($primary_phone_for_tel) ?>" class="btn btn-outline">
                        <i class="fas fa-phone"></i> Telepon Kami
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="testimonial-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Testimoni Jamaah</h2>
                <p class="section-desc">Pengalaman berharga jamaah bersama Arraihan Travelindo</p>
            </div>
            
            <?php if (!empty($testimonials)): ?>
            <div class="testimonial-grid">
                <?php foreach ($testimonials as $testi): ?>
                <div class="testimonial-card">
                    <div class="testimonial-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <h4 class="testimonial-title"><?= e($testi['judul']) ?></h4>
                    <p class="testimonial-text"><?= e(substr($testi['pesan'], 0, 150)) . (strlen($testi['pesan']) > 150 ? '...' : '') ?></p>
                    <div class="testimonial-author">
                        <div class="author-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="author-info">
                            <h5><?= e($testi['nama']) ?></h5>
                            <p><?= date('d M Y', strtotime($testi['created_at'])) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <p class="text-muted">Belum ada testimonial.</p>
            </div>
            <?php endif; ?>
            
            <!-- Testimonial More Button -->
            <div class="section-more">
                <a href="testimonial" class="btn btn-primary">
                    <i class="fas fa-comments"></i> Lihat Semua Testimonial
                </a>
            </div>
        </div>
    </section>

    <!-- Kontak Section -->
    <section class="kontak" id="kontak">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Hubungi Kami</h2>
                <p class="section-desc">Kami siap melayani dan menjawab pertanyaan Anda</p>
            </div>
            <div class="kontak-wrapper">
                <div class="kontak-info">
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h4>Alamat Kantor</h4>
                            <p>
                            <?php if (!empty($company_address)): ?>
                                <?= nl2br(e($company_address)) ?>
                            <?php else: ?>
                                Jl. Raya Tanah Kusir No. 123<br>Jakarta Selatan, DKI Jakarta 12345<br>Indonesia
                            <?php endif; ?>
                            </p>
                            <div class="map-wrapper">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4172.767350248647!2d112.64759151083824!3d-7.9447585791088065!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd629cc01e9cfc9%3A0x1b33c41ad59b5f4d!2sPT%20Raihan%20Islami%20Travelindo%20(HEAD%20OFFICE)!5e1!3m2!1sid!2sid!4v1764310119321!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>

                        </div>
                        </div>
                    </div>
                    
  
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <h4>Telepon</h4>
                            <p>
                            <?php if (!empty($phones)): ?>
                                <?php foreach ($phones as $ph): ?>
                                    <?= e($ph) ?><br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                +62 812-3456-7890<br>+62 21-1234-5678
                            <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h4>Email</h4>
                            <p>
                            <?php if (!empty($emails)): ?>
                                <?php foreach ($emails as $em): ?>
                                    <?= e($em) ?><br>
                                <?php endforeach; ?>
                            <?php else: ?>
                                info@baitulmabrur.com<br>customer@baitulmabrur.com
                            <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h4>Jam Operasional</h4>
                            <p>
                            <?php if (!empty($company_hours)): ?>
                                <?= nl2br(e($company_hours)) ?>
                            <?php else: ?>
                                Senin - Jumat: 08:00 - 17:00<br>Sabtu: 08:00 - 14:00<br>Minggu: Tutup
                            <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="kontak-form">
                    <h3>Kirim Pesan</h3>
                    <form id="contactForm">
                        <div class="form-group">
                            <input type="text" id="name" placeholder="Nama Lengkap" required>
                        </div>
                        <div class="form-group">
                            <input type="email" id="email" placeholder="Email" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" id="phone" placeholder="Nomor WhatsApp" required>
                        </div>
                        <div class="form-group">
                            <textarea id="message" rows="5" placeholder="Pesan Anda"></textarea>
                        </div>
                        <!-- Cloudflare Turnstile -->
                        <div class="form-group">
                            <div class="cf-turnstile" data-sitekey="0x4AAAAAACAl8S6dya4dFd3k"></div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Partner & Corporate Client</h2>
                <p class="section-desc">Dipercaya oleh berbagai lembaga dan instansi</p>
            </div>
            
            <div class="swiper partnersSwiper">
                <div class="swiper-wrapper">
                <?php if (!empty($partners)): ?>
                    <?php foreach ($partners as $pr): ?>
                    <div class="swiper-slide">
                        <div class="partner-item <?= !empty($pr['img_url']) ? 'partner-clickable' : '' ?>" 
                             <?= !empty($pr['img_url']) ? 'data-certificate="' . e($base . '/' . $pr['img_url']) . '" data-partner-name="' . e($pr['name']) . '"' : '' ?>>
                            <?php if (!empty($pr['logo_url'])): ?>
                                <div class="partner-logo-img">
                                    <img src="<?= e($base . '/' . $pr['logo_url']) ?>" alt="<?= e($pr['name']) ?>">
                                </div>
                                <div class="partner-name">
                                    <?= e($pr['name']) ?>
                                    
                                </div>
                            <?php else: ?>
                                <div class="partner-logo">
                                    <i class="<?= e($pr['icon_class'] ?: 'fas fa-building') ?>"></i>
                                    <span><?= e($pr['name']) ?></span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($pr['img_url'])): ?>
                                <div class="partner-certificate-badge">
                                    <i class="fas fa-certificate"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="swiper-slide"><div class="partner-item"><div class="partner-logo"><i class="fas fa-building"></i><span>PT. Maju Sejahtera</span></div></div></div>
                    <div class="swiper-slide"><div class="partner-item"><div class="partner-logo"><i class="fas fa-industry"></i><span>CV. Berkah Jaya</span></div></div></div>
                    <div class="swiper-slide"><div class="partner-item"><div class="partner-logo"><i class="fas fa-store"></i><span>Toko Amanah</span></div></div></div>
                    <div class="swiper-slide"><div class="partner-item"><div class="partner-logo"><i class="fas fa-hospital"></i><span>RS. Harapan Sehat</span></div></div></div>
                    <div class="swiper-slide"><div class="partner-item"><div class="partner-logo"><i class="fas fa-university"></i><span>Universitas Islam</span></div></div></div>
                    <div class="swiper-slide"><div class="partner-item"><div class="partner-logo"><i class="fas fa-landmark"></i><span>Bank Syariah</span></div></div></div>
                <?php endif; ?>
                </div>
                
                <!-- Swiper Navigation -->
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                
                <!-- Swiper Pagination -->
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

<!-- Partner Certificate Modal -->
<div id="partner-certificate-modal" class="certificate-modal" style="display: none;">
    <div class="certificate-modal-overlay"></div>
    <div class="certificate-modal-content">
        <button class="certificate-modal-close" onclick="closeCertificateModal()">&times;</button>
        <h4 id="certificate-partner-name"></h4>
        <img id="certificate-image" src="" alt="Sertifikat">
    </div>
</div>

<!-- Popup Banner Modal -->
<?php if ($popup_banner): ?>
<div id="popup-banner-modal" class="popup-modal" style="display: none;">
    <div class="popup-overlay"></div>
    <div class="popup-content">
        <button class="popup-close" onclick="closePopup()">&times;</button>
        <?php if (!empty($popup_banner['link_url'])): ?>
            <a href="<?= e($popup_banner['link_url']) ?>" target="_blank">
                <img src="<?= e($popup_banner['image_url']) ?>" alt="<?= e($popup_banner['title']) ?>">
            </a>
        <?php else: ?>
            <img src="<?= e($popup_banner['image_url']) ?>" alt="<?= e($popup_banner['title']) ?>">
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<?php
// Include footer template
require_once __DIR__ . '/inc/footer.php';
?>
