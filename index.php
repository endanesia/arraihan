<?php require_once __DIR__ . '/inc/db.php';
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
    if ($res = db()->query("SELECT * FROM packages ORDER BY featured DESC, id DESC LIMIT 6")) {
        while ($row = $res->fetch_assoc()) { $packages[] = $row; }
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
    if ($res = db()->query("SELECT youtube_id, title FROM gallery_videos ORDER BY id DESC LIMIT 3")) {
        while ($row = $res->fetch_assoc()) { $videos[] = $row; }
    }
}
// Social links from settings
$link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
$link_facebook = function_exists('get_setting') ? get_setting('facebook', '') : '';
$link_instagram = function_exists('get_setting') ? get_setting('instagram', '') : '';
$link_youtube = function_exists('get_setting') ? get_setting('youtube', '') : '';
$link_tiktok = function_exists('get_setting') ? get_setting('tiktok', '') : '';
$link_twitter = function_exists('get_setting') ? get_setting('twitter', '') : '';
$phone_number = function_exists('get_setting') ? get_setting('phone', '+6281234567890') : '+6281234567890';
// Company info settings
$company_address = function_exists('get_setting') ? get_setting('address', '') : '';
$company_email = function_exists('get_setting') ? get_setting('email', '') : '';
$company_hours = function_exists('get_setting') ? get_setting('hours', '') : '';

// Prepare arrays for display (phones/emails may be comma separated)
$phones = array_filter(array_map('trim', explode(',', $phone_number)));
$emails = array_filter(array_map('trim', explode(',', $company_email)));
// For tel: link, use first phone if available
$primary_phone_for_tel = !empty($phones) ? $phones[0] : $phone_number;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Travel Umroh & Haji Terpercaya - Berizin Resmi Kemenag RI dengan Akreditasi A">
    <title>Raihan Travelindo - Travel Haji & Umroh Terpercaya</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Admin shortcut (optional): <link rel="nofollow" href="<?= e(($config['app']['base_url'] ?? '')); ?>/admin/login.php"> -->
  </head>
<body>
    <!-- Header & Navigation -->
    <header class="header" id="header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-wrapper">
                    <div class="logo">
                        <img src="images/logo.png" alt="Raihan Travelindo" style="height: 50px;">
                        <span>Ar Raihan</span>
                    </div>
                    <ul class="nav-menu" id="navMenu">
                        <li><a href="#home" class="nav-link active">Home</a></li>
                        <li><a href="#paket" class="nav-link">Paket</a></li>
                     <!--   <li><a href="#keunggulan" class="nav-link">Keunggulan</a></li> -->
                        <li><a href="#jadwal" class="nav-link">Jadwal</a></li>
                        <li><a href="#galeri" class="nav-link">Galeri</a></li>
                        <li><a href="#tentang" class="nav-link">Tentang Kami</a></li>
                        <li><a href="#kontak" class="nav-link">Kontak</a></li>
                    </ul>
                    <div class="nav-buttons">
                        <?php if (!empty($link_whatsapp)): ?>
                        <a href="<?= e($link_whatsapp) ?>" class="btn-whatsapp" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp Kami</a>
                        <?php endif; ?>
                        <button class="nav-toggle" id="navToggle">
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-badge">
                    <i class="fas fa-certificate"></i>
                    <span>PT. ArRaihan Islami Travelindo</span>
                </div>
                <h1 class="hero-title">Perjalanan Suci Berkualitas, Biaya Bersahabat</h1>
                <p class="hero-subtitle">Jangan biarkan biaya menunda niat suci Anda. Paket Umroh terjangkau dengan tetap berkualitas layanan terbaik, mencakup akomodasi dan bimbingan yang profesional. Wujudkan ibadah khusyuk dan nyaman Anda, karena Umroh berkualitas kini bisa diakses oleh semua.</p>
                <div class="hero-buttons">
                    <a href="#paket" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Lihat Paket Umroh
                    </a>
                    <?php if (!empty($link_whatsapp)): ?>
                    <a href="<?= e($link_whatsapp) ?>" class="btn btn-secondary" target="_blank"><i class="fab fa-whatsapp"></i> Konsultasi Gratis</a>
                    <?php endif; ?>
                </div>
                <div class="hero-stats">
                    <div class="stat-item">
                        <h3><i class="fas fa-users"></i> 24 Januri 2026</h3>
                        <p>Jadwal Berangkat</p>
                    </div>

                    <div class="stat-item">
                        <h3><i class="fas fa-award"></i> Program Pembiayaan</h3>
                        <p>Pembiayaan dana talangan Umrah</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="hero-scroll">
            <a href="#keunggulan">
                <i class="fas fa-chevron-down"></i>
            </a>
        </div>
    </section>

    <!-- Greeting Section -->
    <section class="greeting">
        <div class="container">
            <div class="greeting-content">
                <h2 class="section-title">Assalamu'alaikum Warahmatullahi Wabarakatuh</h2>
                <h3 class="section-subtitle">Calon Jamaah Yang Dirahmati Allah,</h3>
                <p class="greeting-text">
                    Kami sepenuh hati siap membantu dan mendampingi Anda dalam mewujudkan impian untuk beribadah ke Tanah Suci. 
                    Mulai dari persiapan keberangkatan, pelaksanaan ibadah di Tanah Suci, hingga kepulangan Anda ke tanah air, 
                    kami akan memastikan setiap langkah perjalanan Anda berjalan lancar, aman, dan penuh berkah.
                </p>
                <p class="greeting-text">
                    Kami berkomitmen memberikan layanan terbaik untuk kenyamanan dan kekhusyukan ibadah Anda. 
                    Berikut ini adalah Nilai Plus yang kami tawarkan:
                </p>
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
                <div class="keunggulan-card">
                    <div class="keunggulan-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <h3>Legalitas Terjamin</h3>
                    <p>Terdaftar di Kementerian Agama RI dengan Izin PPIU dan PIHK resmi dari pemerintah</p>
                </div>
                <div class="keunggulan-card">
                    <div class="keunggulan-icon">
                        <i class="fas fa-medal"></i>
                    </div>
                    <h3>Akreditasi A</h3>
                    <p>Biro perjalanan haji umroh terbaik dengan predikat "A" dari Komite Akreditasi Nasional</p>
                </div>
                <div class="keunggulan-card">
                    <div class="keunggulan-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Mutu Pelayanan</h3>
                    <p>Telah lulus uji dan bersertifikasi memenuhi kualifikasi SNI ISO 9001:2015 QMS</p>
                </div>
                <div class="keunggulan-card">
                    <div class="keunggulan-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Jadwal Pasti</h3>
                    <p>Semua paket sudah tertera tanggal berangkat, nomor pesawat & itinerary perjalanan yang jelas</p>
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
            </div>
        </div>
    </section>

    <!-- Paket Section -->
    <section class="paket" id="paket">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Paket Perjalanan Kami</h2>
                <p class="section-desc">Pilih paket yang sesuai dengan kebutuhan ibadah Anda</p>
            </div>
            <div class="paket-grid">
                <?php if (!empty($packages)): ?>
                    <?php foreach ($packages as $p): ?>
                    <div class="paket-card <?= $p['featured'] ? 'featured' : '' ?>">
                        <?php if ($p['featured']): ?><div class="paket-badge">Populer</div><?php endif; ?>
                        <div class="paket-header">
                            <div class="paket-icon"><i class="<?= e($p['icon_class'] ?: 'fas fa-moon') ?>"></i></div>
                            <h3><?= e($p['title']) ?></h3>
                        </div>
                        <div class="paket-body">
                            <div class="paket-price">
                                <span class="price-label"><?= e($p['price_label']) ?></span>
                                <span class="price-value"><?= e($p['price_value']) ?></span>
                                <span class="price-person"><?= e($p['price_unit']) ?></span>
                            </div>
                            <ul class="paket-features">
                                <?php 
                                  $featLines = array_filter(array_map('trim', explode("\n", (string)$p['features'])));
                                  foreach ($featLines as $line): 
                                    $parts = explode('|', $line, 2); $fIcon = trim($parts[0] ?? 'fas fa-check'); $fText = trim($parts[1] ?? $line);
                                ?>
                                  <li><i class="<?= e($fIcon ?: 'fas fa-check') ?>"></i> <?= e($fText) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="<?= e($p['button_link'] ?: '#kontak') ?>" class="btn btn-primary"><?= e($p['button_text'] ?: 'Lihat Detail') ?></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- fallback to static cards if no data -->
                    <div class="paket-card">
                        <div class="paket-header">
                            <div class="paket-icon"><i class="fas fa-moon"></i></div>
                            <h3>Paket Umroh</h3>
                        </div>
                        <div class="paket-body">
                            <div class="paket-price">
                                <span class="price-label">Mulai dari</span>
                                <span class="price-value">Rp 24 Juta</span>
                                <span class="price-person">/orang</span>
                            </div>
                            <ul class="paket-features">
                                <li><i class="fas fa-check"></i> Direct Flight</li>
                                <li><i class="fas fa-check"></i> Hotel Bintang 4-5</li>
                                <li><i class="fas fa-check"></i> Ring 1 Masjidil Haram</li>
                                <li><i class="fas fa-check"></i> Muthawif Berpengalaman</li>
                                <li><i class="fas fa-check"></i> City Tour</li>
                                <li><i class="fas fa-check"></i> Ziarah Lengkap</li>
                            </ul>
                            <a href="#kontak" class="btn btn-primary">Lihat Detail</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Jadwal Section -->
    <section class="jadwal" id="jadwal">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Jadwal Keberangkatan</h2>
                <p class="section-desc">Jadwal umroh tersedia setiap bulan dengan keberangkatan pasti</p>
            </div>
            <?php if (!empty($schedules)): ?>
            <div class="mb-4">
                <ul>
                    <?php foreach ($schedules as $sc): ?>
                        <li>
                            <strong><?= e($sc['departure_date'] ?: '-') ?></strong> — <?= e($sc['title']) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <div class="jadwal-cta">
                <div class="jadwal-content">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Lihat Jadwal Lengkap Keberangkatan</h3>
                    <p>Tersedia berbagai pilihan tanggal keberangkatan yang bisa disesuaikan dengan jadwal Anda</p>
                    <a href="#kontak" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i> Cek Jadwal
                    </a>
                </div>
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
                            <img src="<?= e($img['file_path']) ?>" alt="<?= e($img['title'] ?: 'Galeri') ?>">
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
        </div>
    </section>

    <!-- Tentang Section -->
    <section class="tentang" id="tentang">
        <div class="container">
            <div class="tentang-wrapper">
                <div class="tentang-content">
                    <h2 class="section-title">Tentang Kami</h2>
                    <p>
                        <strong>Raihan Travelindo</strong> resmi didirikan pada tahun 2005 oleh para pendiri yang memiliki visi mulia 
                        untuk membantu umat Islam menunaikan ibadah ke Tanah Suci. Pada awal berdirinya, perusahaan ini bergerak 
                        di bidang pariwisata, mulai dari tiket domestik dan penerbangan internasional.
                    </p>
                    <p>
                        Setelah itu, kami mulai merambah ke bisnis layanan jasa travel umroh dan haji khusus. Nama 
                        <strong>Raihan Travelindo</strong> terinspirasi dari Ka'bah yang merupakan rumah Allah SWT yang suci dan penuh berkah, 
                        dengan harapan dapat memberikan pelayanan terbaik yang penuh keberkahan kepada setiap jamaah.
                    </p>
                    <p>
                        Kami telah mengantongi berbagai izin resmi dari Pemerintah RI. Hal ini merupakan bukti nyata keseriusan 
                        kami dalam memberikan layanan terbaik untuk para calon Tamu Allah SWT. Dengan pengalaman lebih dari 18 tahun 
                        dan telah melayani lebih dari 15.000 jamaah, kami berkomitmen untuk terus meningkatkan kualitas pelayanan.
                    </p>
                    <div class="tentang-features">
                        <div class="feature-item">
                            <i class="fas fa-certificate"></i>
                            <div>
                                <h4>Izin PPIU Resmi</h4>
                                <p>Terdaftar Kementerian Agama RI</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-award"></i>
                            <div>
                                <h4>Izin PIHK Resmi</h4>
                                <p>Penyelenggara Ibadah Haji Khusus</p>
                            </div>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <h4>Sertifikat ISO 9001:2015</h4>
                                <p>Sistem Manajemen Mutu Terjamin</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tentang-image">
                    <img src="images/bg.jpeg" alt="Kantor Raihan Travelindo">
                    <div class="tentang-badge">
                        <i class="fas fa-users"></i>
                        <div>
                            <h4>15.000+</h4>
                            <p>Jamaah Terlayani</p>
                        </div>
                    </div>
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
                    <?php foreach ($videos as $v): $thumb = 'https://img.youtube.com/vi/'.e($v['youtube_id']).'/hqdefault.jpg'; ?>
                    <div class="video-item">
                        <div class="video-thumbnail">
                            <img src="<?= $thumb ?>" alt="<?= e($v['title'] ?: 'Video') ?>">
                            <div class="video-play">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <h4><?= e($v['title'] ?: 'Video') ?></h4>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="video-item">
                        <div class="video-thumbnail">
                            <img src="https://muslimpergi.com/wp-content/uploads/2023/02/dokumentasi-saat-umroh-bisa-dijadikan-bukti-untuk-keluarga-tercinta-1024x574.jpg" alt="Video Testimoni">
                            <div class="video-play">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <h4>Testimoni Jamaah Umroh 2024</h4>
                    </div>
                    <div class="video-item">
                        <div class="video-thumbnail">
                            <img src="https://images.unsplash.com/photo-1542816417-0983c9c9ad53?w=400&h=300&fit=crop" alt="Video Perjalanan">
                            <div class="video-play">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <h4>Perjalanan Ibadah ke Tanah Suci</h4>
                    </div>
                    <div class="video-item">
                        <div class="video-thumbnail">
                            <img src="https://haqiqitour.com/wp-content/uploads/2024/02/DOKUMENTASI-JAMAAH-UMROH-HAQIQI-TOUR-TRAVEL-21.jpeg" alt="Video Fasilitas">
                            <div class="video-play">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <h4>Fasilitas Hotel & Layanan</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section class="partners">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Partner & Corporate Client</h2>
                <p class="section-desc">Dipercaya oleh berbagai perusahaan dan instansi</p>
            </div>
            <div class="partners-grid">
                <?php if (!empty($partners)): ?>
                    <?php foreach ($partners as $pr): ?>
                    <div class="partner-item">
                        <div class="partner-logo">
                            <i class="<?= e($pr['icon_class'] ?: 'fas fa-building') ?>"></i>
                            <span><?= e($pr['name']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="partner-item"><div class="partner-logo"><i class="fas fa-building"></i><span>PT. Maju Sejahtera</span></div></div>
                    <div class="partner-item"><div class="partner-logo"><i class="fas fa-industry"></i><span>CV. Berkah Jaya</span></div></div>
                    <div class="partner-item"><div class="partner-logo"><i class="fas fa-store"></i><span>Toko Amanah</span></div></div>
                    <div class="partner-item"><div class="partner-logo"><i class="fas fa-hospital"></i><span>RS. Harapan Sehat</span></div></div>
                    <div class="partner-item"><div class="partner-logo"><i class="fas fa-university"></i><span>Universitas Islam</span></div></div>
                    <div class="partner-item"><div class="partner-logo"><i class="fas fa-landmark"></i><span>Bank Syariah</span></div></div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Siap Berangkat ke Tanah Suci?</h2>
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
                            <select id="paket" required>
                                <option value="">Pilih Paket</option>
                                <option value="umroh">Paket Umroh</option>
                                <option value="haji">Haji Khusus</option>
                                <option value="badal-haji">Badal Haji</option>
                                <option value="badal-umroh">Badal Umroh</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <textarea id="message" rows="5" placeholder="Pesan Anda"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-kaaba"></i>
                        <span>Raihan Travelindo</span>
                    </div>
                    <p>Travel Haji & Umroh Terpercaya sejak 2005. Melayani dengan sepenuh hati untuk kenyamanan ibadah Anda.</p>
                    <div class="footer-social">
                        <?php if (!empty($link_facebook)): ?><a href="<?= e($link_facebook) ?>" target="_blank"><i class="fab fa-facebook"></i></a><?php endif; ?>
                        <?php if (!empty($link_instagram)): ?><a href="<?= e($link_instagram) ?>" target="_blank"><i class="fab fa-instagram"></i></a><?php endif; ?>
                        <?php if (!empty($link_youtube)): ?><a href="<?= e($link_youtube) ?>" target="_blank"><i class="fab fa-youtube"></i></a><?php endif; ?>
                        <?php if (!empty($link_twitter)): ?><a href="<?= e($link_twitter) ?>" target="_blank"><i class="fab fa-twitter"></i></a><?php endif; ?>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Menu</h4>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#paket">Paket Umroh</a></li>
                        <li><a href="#keunggulan">Keunggulan</a></li>
                        <li><a href="#jadwal">Jadwal</a></li>
                        <li><a href="#galeri">Galeri</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="#paket">Paket Umroh</a></li>
                        <li><a href="#paket">Haji Khusus</a></li>
                        <li><a href="#paket">Badal Haji</a></li>
                        <li><a href="#paket">Badal Umroh</a></li>
                        <li><a href="#tentang">Tentang Kami</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legalitas</h4>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Izin PPIU Kemenag RI</li>
                        <li><i class="fas fa-check-circle"></i> Izin PIHK Resmi</li>
                        <li><i class="fas fa-check-circle"></i> Akreditasi A</li>
                        <li><i class="fas fa-check-circle"></i> Sertifikat ISO 9001:2015</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Raihan Travelindo. All Rights Reserved.</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float Button -->
    <?php if (!empty($link_whatsapp)): ?>
    <a href="<?= e($link_whatsapp) ?>" class="whatsapp-float" target="_blank"><i class="fab fa-whatsapp"></i></a>
    <?php endif; ?>

    <!-- Social Media Float Buttons -->
    <?php if (!empty($link_facebook)): ?>
    <a href="<?= e($link_facebook) ?>" class="social-float facebook-float" target="_blank">
        <i class="fab fa-facebook-f"></i>
    </a>
    <?php endif; ?>
    <?php if (!empty($link_instagram)): ?>
    <a href="<?= e($link_instagram) ?>" class="social-float instagram-float" target="_blank">
        <i class="fab fa-instagram"></i>
    </a>
    <?php endif; ?>
    <?php if (!empty($link_tiktok)): ?>
    <a href="<?= e($link_tiktok) ?>" class="social-float tiktok-float" target="_blank">
        <i class="fab fa-tiktok"></i>
    </a>
    <?php endif; ?>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="js/script.js"></script>
  </body>
  </html>
