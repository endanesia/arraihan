<?php 
require_once __DIR__ . '/inc/db.php';

// Base URL configuration
$base = '';
if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== 'localhost:8080') {
    $base = '';
} else {
    $base = '';
}

// Get package ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: index.php#paket');
    exit;
}

// Fetch package data
$package = null;
if (function_exists('db') && db()) {
    // Check if poster column exists
    $has_poster = false;
    try {
        $check_result = db()->query("SHOW COLUMNS FROM packages LIKE 'poster'");
        $has_poster = $check_result && $check_result->num_rows > 0;
    } catch (Exception $e) {
        $has_poster = false;
    }

    if ($has_poster) {
        $stmt = db()->prepare("SELECT title, poster, price_label, price_value, price_unit, icon_class, features, featured, hotel, pesawat, price_quad, price_triple, price_double FROM packages WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $package = $result->fetch_assoc();
        $stmt->close();
    } else {
        $stmt = db()->prepare("SELECT title, price_label, price_value, price_unit, icon_class, features, featured, hotel, pesawat, price_quad, price_triple, price_double FROM packages WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $package = $result->fetch_assoc();
        $stmt->close();
    }
}

if (!$package) {
    header('Location: index.php#paket');
    exit;
}

// Social links from settings
$link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
$phone_number = function_exists('get_setting') ? get_setting('phone', '+6281234567890') : '+6281234567890';
$primary_phone_for_tel = !empty($phone_number) ? $phone_number : '+6281234567890';

// Page configuration for header template
$page_title = e($package['title']) . ' - Ar Raihan Travelindo';
$page_description = 'Detail ' . e($package['title']) . ' - Travel Umroh & Haji Terpercaya';
$current_page = 'paket';
$include_bootstrap = true;

// Extra head content for page-specific styles
$extra_head_content = '<link rel="stylesheet" href="css/paket-detail.css?v=' . time() . '">';

// Include header template
require_once __DIR__ . '/inc/header.php';
?>

    <!-- Package Detail Section -->
    <section class="package-detail">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb">
                <a href="index.php">Home</a> > 
                <a href="index.php#paket">Paket</a> > 
                <span><?= e($package['title']) ?></span>
            </nav>

            <div class="package-header text-center">
                <div class="container">
                    <?php if ($package['icon_class']): ?>
                    <i class="<?= e($package['icon_class']) ?> fa-3x mb-3"></i>
                    <?php endif; ?>
                    <h1><?= e($package['title']) ?></h1>
                    <?php if ($package['featured']): ?>
                    <span class="badge bg-warning text-dark">⭐ Paket Populer</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8 mb-4">
                    <!-- Package Poster -->
                    <?php if (!empty($package['poster'])): ?>
                    <img src="<?= e($package['poster']) ?>" alt="<?= e($package['title']) ?>" class="package-poster">
                    <?php endif; ?>

                    <!-- Package Features/Description -->
                    <div class="package-info">
                        <h3>Detail Paket</h3>
                        
                        <?php if (!empty($package['hotel'])): ?>
                        <h5><i class="fas fa-hotel"></i> Hotel</h5>
                        <p><?= nl2br(e($package['hotel'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($package['pesawat'])): ?>
                        <h5><i class="fas fa-plane"></i> Penerbangan</h5>
                        <p><?= nl2br(e($package['pesawat'])) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($package['features'])): ?>
                        <h5><i class="fas fa-check-circle"></i> Fasilitas</h5>
                        <div class="features-content">
                            <?php 
                            // Check if features contain HTML tags
                            if (strip_tags($package['features']) !== $package['features']) {
                                // Has HTML, display as-is
                                echo $package['features'];
                            } else {
                                // Plain text, escape and add line breaks
                                echo nl2br(e($package['features']));
                            }
                            ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Price Section -->
                    <div class="price-section">
                        <h3>Harga Paket</h3>
                        <?php if (!empty($package['price_quad'])): ?>
                        <div class="price-item">
                            <span class="price-label">Quad</span>
                            <span class="price-value"><?= e($package['price_quad']) ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($package['price_triple'])): ?>
                        <div class="price-item">
                            <span class="price-label">Triple</span>
                            <span class="price-value"><?= e($package['price_triple']) ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($package['price_double'])): ?>
                        <div class="price-item">
                            <span class="price-label">Double</span>
                            <span class="price-value"><?= e($package['price_double']) ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if (empty($package['price_quad']) && empty($package['price_triple']) && empty($package['price_double'])): ?>
                        <div class="price-main">
                            <?= e($package['price_value']) ?> <?= e($package['price_unit'] ?? '') ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- CTA Section -->
                    <div class="cta-section">
                        <h4>Tertarik dengan Paket Ini?</h4>
                        <?php if (!empty($link_whatsapp)): ?>
                        <a href="<?= e($link_whatsapp) ?>" class="btn-consultation" target="_blank">
                            <i class="fab fa-whatsapp"></i> Konsultasi Gratis
                        </a>
                        <?php endif; ?>
                        <a href="tel:<?= e($primary_phone_for_tel) ?>" class="btn-booking">
                            <i class="fas fa-phone"></i> Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
// Include footer template
require_once __DIR__ . '/inc/footer.php';
?>
