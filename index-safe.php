<?php
// Safe version of index.php with error handling
require_once __DIR__ . '/inc/db.php';

// Initialize base URL
$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
}

// Helper function for safe database queries
function safe_query($sql, $default = []) {
    try {
        if (function_exists('db') && db()) {
            $result = db()->query($sql);
            if ($result) {
                $rows = [];
                while ($row = $result->fetch_assoc()) {
                    $rows[] = $row;
                }
                return $rows;
            }
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
    }
    return $default;
}

// Safe data loading
$packages = safe_query("SELECT * FROM packages ORDER BY featured DESC, id DESC");
$partners = safe_query("SELECT * FROM partners ORDER BY id DESC LIMIT 12");
$schedules = safe_query("SELECT * FROM schedules WHERE departure_date IS NOT NULL AND departure_date >= CURDATE() ORDER BY departure_date ASC LIMIT 6");
$mutawwif = safe_query("SELECT * FROM mutawwif WHERE is_active = 1 ORDER BY urutan ASC, id ASC");
$testimonials = safe_query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 6");

// Hero slides
$hero_slides = safe_query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC");

// Videos with multi-platform support
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
    error_log("Video query error: " . $e->getMessage());
}

// SKIP popup banner to avoid errors
$popup_banner = null;

// Safe settings loading with fallbacks
function safe_setting($key, $default = '') {
    try {
        return function_exists('get_setting') ? get_setting($key, $default) : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// Social links
$link_whatsapp = safe_setting('whatsapp', '');
$link_facebook = safe_setting('facebook', '');
$link_instagram = safe_setting('instagram', '');
$link_youtube = safe_setting('youtube', '');
$link_tiktok = safe_setting('tiktok', '');
$link_twitter = safe_setting('twitter', '');
$link_threads = safe_setting('threads', '');
$phone_number = safe_setting('phone', '+6281234567890');

// Company info
$company_address = safe_setting('address', '');
$company_email = safe_setting('email', '');
$company_hours = safe_setting('hours', '');

// Greeting settings
$greeting_title = safe_setting('greeting_title', 'Assalamu\'alaikum Warahmatullahi Wabarakatuh');
$greeting_subtitle = safe_setting('greeting_subtitle', 'Calon Jamaah Yang Dirahmati Allah,');
$greeting_text = safe_setting('greeting_text', 'Kami sepenuh hati siap membantu...');
$greeting_stats_title = safe_setting('greeting_stats_title', 'Kepercayaan Jamaah');
$greeting_button_text = safe_setting('greeting_button_text', 'Pelajari Lebih Lanjut');
$greeting_button_link = safe_setting('greeting_button_link', '#paket');
$greeting_background = safe_setting('greeting_background', '');

// About settings
$about_title = safe_setting('about_title', 'Tentang Kami');
$about_content_p1 = safe_setting('about_content_p1', 'Raihan Travelindo resmi didirikan...');
$about_content_p2 = safe_setting('about_content_p2', 'Setelah itu, kami mulai merambah...');
$about_content_p3 = safe_setting('about_content_p3', 'Kami telah mengantongi berbagai izin...');
$about_badge_number = safe_setting('about_badge_number', '15.000+');
$about_badge_text = safe_setting('about_badge_text', 'Jamaah Terlayani');
$about_image = safe_setting('about_image', '');

// Keunggulan data
$keunggulan = [
    [
        'icon' => safe_setting('about_keunggulan_1_icon', 'fas fa-certificate'),
        'title' => safe_setting('about_keunggulan_1_title', 'Izin PPIU Resmi'),
        'description' => safe_setting('about_keunggulan_1_desc', 'Terdaftar Kementerian Agama RI')
    ],
    [
        'icon' => safe_setting('about_keunggulan_2_icon', 'fas fa-award'),
        'title' => safe_setting('about_keunggulan_2_title', 'Izin PIHK Resmi'),
        'description' => safe_setting('about_keunggulan_2_desc', 'Penyelenggara Ibadah Haji Khusus')
    ],
    [
        'icon' => safe_setting('about_keunggulan_3_icon', 'fas fa-shield-alt'),
        'title' => safe_setting('about_keunggulan_3_title', 'Sertifikat ISO 9001:2015'),
        'description' => safe_setting('about_keunggulan_3_desc', 'Sistem Manajemen Mutu Terjamin')
    ]
];

// Prepare contact arrays
$phones = array_filter(array_map('trim', explode(',', $phone_number)));
$emails = array_filter(array_map('trim', explode(',', $company_email)));
$primary_phone_for_tel = !empty($phones) ? $phones[0] : $phone_number;

// Include header
require_once __DIR__ . '/inc/header.php';
?>

<h1>Safe Index Test</h1>
<p>Testing with comprehensive error handling and skipping popup banner</p>

<div class="test-results">
    <h3>Data Loading Results:</h3>
    <ul>
        <li>Packages: <?= count($packages) ?></li>
        <li>Partners: <?= count($partners) ?></li>
        <li>Videos: <?= count($videos) ?></li>
        <li>Testimonials: <?= count($testimonials) ?></li>
        <li>Hero Slides: <?= count($hero_slides) ?></li>
        <li>Schedules: <?= count($schedules) ?></li>
        <li>Mutawwif: <?= count($mutawwif) ?></li>
    </ul>
    
    <h3>Settings Test:</h3>
    <ul>
        <li>Greeting Title: <?= e($greeting_title) ?></li>
        <li>About Title: <?= e($about_title) ?></li>
        <li>Phone: <?= e($phone_number) ?></li>
    </ul>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>