<?php
/**
 * Header Template
 * Include this file in all pages to show consistent header/navigation
 * 
 * Required variables (should be set before including this file):
 * - $page_title: Page title for <title> tag
 * - $page_description: Meta description (optional)
 * - $current_page: Current page identifier ('home', 'paket', 'artikel', etc.)
 */

// Default values if not set
$page_title = $page_title ?? 'Raihan Travelindo - Travel Haji & Umroh Terpercaya';
$page_description = $page_description ?? 'Travel Umroh & Haji Terpercaya - Berizin Resmi Kemenag RI dengan Akreditasi A';
$current_page = $current_page ?? 'home';

// Load social links if not already loaded
if (!isset($link_whatsapp)) {
    $link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($page_description) ?>">
    <title><?= e($page_title) ?></title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="<?= $base ?? '' ?>css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <?php if (isset($include_swiper) && $include_swiper): ?>
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <?php endif; ?>
    
    <?php if (isset($include_bootstrap) && $include_bootstrap): ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    
    <?php if (isset($extra_head_content)): ?>
    <!-- Extra head content -->
    <?= $extra_head_content ?>
    <?php endif; ?>
</head>
<body>
    <!-- Header & Navigation -->
    <header class="header" id="header">
        <nav class="navbar">
            <div class="container">
                <div class="nav-wrapper">
                    <div class="logo">
                        <img src="<?= $base ?? '' ?>images/logo.png" alt="Raihan Travelindo" style="height: 50px;">
                        <span>Ar Raihan</span>
                    </div>
                    <ul class="nav-menu" id="navMenu">
                        <li><a href="<?= $base ?? '' ?>#home" class="nav-link <?= $current_page === 'home' ? 'active' : '' ?>">Home</a></li>
                        <li><a href="<?= $base ?? '' ?>#paket" class="nav-link <?= $current_page === 'paket' ? 'active' : '' ?>">Paket</a></li>
                        <li><a href="<?= $base ?? '' ?>#jadwal" class="nav-link <?= $current_page === 'jadwal' ? 'active' : '' ?>">Jadwal</a></li>
                        <li><a href="<?= $base ?? '' ?>#galeri" class="nav-link <?= $current_page === 'galeri' ? 'active' : '' ?>">Galeri</a></li>
                        <li><a href="<?= $base ?? '' ?>artikel.php" class="nav-link <?= $current_page === 'artikel' ? 'active' : '' ?>">Artikel</a></li>
                        <li><a href="<?= $base ?? '' ?>#tentang" class="nav-link <?= $current_page === 'tentang' ? 'active' : '' ?>">Tentang Kami</a></li>
                        <li><a href="<?= $base ?? '' ?>#kontak" class="nav-link <?= $current_page === 'kontak' ? 'active' : '' ?>">Kontak</a></li>
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
