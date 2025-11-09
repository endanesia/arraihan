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
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Detail <?= e($package['title']) ?> - Travel Umroh & Haji Terpercaya">
    <title><?= e($package['title']) ?> - Ar Raihan Travelindo</title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
    .package-detail {
        padding: 100px 0 80px;
        background: #f8f9fa;
    }
    
    .package-header {
        background: linear-gradient(135deg, #1a6b4a 0%, #0d4a33 100%);
        color: white;
        padding: 60px 0;
        margin-bottom: 50px;
    }
    
    .package-poster {
        max-width: 100%;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        margin-bottom: 30px;
    }
    
    .package-info {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .price-section {
        background: linear-gradient(135deg, #1a6b4a 0%, #0d4a33 100%);
        color: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        margin-bottom: 30px;
    }
    
    .price-main {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 10px 0;
    }
    
    .price-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }
    
    .price-option {
        background: rgba(255,255,255,0.1);
        padding: 15px;
        border-radius: 10px;
        backdrop-filter: blur(10px);
    }
    
    .features-section {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }
    
    .features-section h3 {
        color: #1a6b4a;
        margin-bottom: 25px;
        font-size: 1.5rem;
    }
    
    .cta-section {
        background: linear-gradient(135deg, #1a6b4a 0%, #0d4a33 100%);
        color: white;
        padding: 40px;
        border-radius: 15px;
        text-align: center;
    }
    
    .btn-cta {
        background: white;
        color: #1a6b4a;
        padding: 15px 30px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        margin: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        color: #1a6b4a;
    }
    
    .breadcrumb {
        padding: 20px 0;
        background: transparent;
    }
    
    .breadcrumb a {
        color: #1a6b4a;
        text-decoration: none;
    }
    
    .breadcrumb a:hover {
        text-decoration: underline;
    }
    
    @media (max-width: 768px) {
        .package-detail {
            padding: 80px 0 60px;
        }
        
        .package-header {
            padding: 40px 0;
        }
        
        .package-info, .features-section {
            padding: 25px;
        }
        
        .price-main {
            font-size: 2rem;
        }
    }
    </style>
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
                        <li><a href="index.php#home" class="nav-link">Home</a></li>
                        <li><a href="index.php#paket" class="nav-link">Paket</a></li>
                        <li><a href="index.php#jadwal" class="nav-link">Jadwal</a></li>
                        <li><a href="index.php#galeri" class="nav-link">Galeri</a></li>
                        <li><a href="artikel.php" class="nav-link">Artikel</a></li>
                        <li><a href="index.php#tentang" class="nav-link">Tentang Kami</a></li>
                        <li><a href="index.php#kontak" class="nav-link">Kontak</a></li>
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
                    <span class="badge bg-warning text-dark fs-6 mt-2">
                        <i class="fas fa-star"></i> Paket Populer
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Poster -->
                    <?php if (!empty($package['poster'])): ?>
                    <div class="text-center mb-4">
                        <img src="<?= $base ?>/images/packages/<?= e($package['poster']) ?>" 
                             alt="<?= e($package['title']) ?>" 
                             class="package-poster">
                    </div>
                    <?php endif; ?>

                    <!-- Package Info -->
                    <div class="package-info">
                        <h3 class="mb-4"><i class="fas fa-info-circle me-2"></i>Informasi Paket</h3>
                        
                        <?php if ($package['hotel']): ?>
                        <div class="mb-3">
                            <h5><i class="fas fa-hotel me-2 text-primary"></i>Hotel</h5>
                            <p><?= e($package['hotel']) ?></p>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($package['pesawat']): ?>
                        <div class="mb-3">
                            <h5><i class="fas fa-plane me-2 text-primary"></i>Pesawat</h5>
                            <p><?= e($package['pesawat']) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Features -->
                    <?php if ($package['features']): ?>
                    <div class="features-section">
                        <h3><i class="fas fa-list-check me-2"></i>Fasilitas & Layanan</h3>
                        <div class="features-content">
                            <?= $package['features'] ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <!-- Price Section -->
                    <div class="price-section">
                        <div class="price-label"><?= e($package['price_label']) ?></div>
                        <div class="price-main"><?= e($package['price_value']) ?></div>
                        <div class="price-unit"><?= e($package['price_unit']) ?></div>
                        
                        <?php if ($package['price_quad'] || $package['price_triple'] || $package['price_double']): ?>
                        <div class="price-options">
                            <?php if ($package['price_quad']): ?>
                            <div class="price-option">
                                <strong>Quad</strong><br>
                                <?= e($package['price_quad']) ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($package['price_triple']): ?>
                            <div class="price-option">
                                <strong>Triple</strong><br>
                                <?= e($package['price_triple']) ?>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($package['price_double']): ?>
                            <div class="price-option">
                                <strong>Double</strong><br>
                                <?= e($package['price_double']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- CTA Section -->
                    <div class="cta-section">
                        <h4 class="mb-3">Tertarik dengan paket ini?</h4>
                        <p class="mb-4">Hubungi kami untuk informasi lebih lanjut dan reservasi</p>
                        
                        <?php if (!empty($link_whatsapp)): ?>
                        <a href="<?= e($link_whatsapp) ?>" class="btn-cta" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>WhatsApp Kami
                        </a>
                        <?php endif; ?>
                        
                        <a href="tel:<?= e($primary_phone_for_tel) ?>" class="btn-cta">
                            <i class="fas fa-phone me-2"></i>Telepon Kami
                        </a>
                        
                        <a href="index.php#kontak" class="btn-cta">
                            <i class="fas fa-envelope me-2"></i>Kirim Pesan
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- WhatsApp Float Button -->
    <?php if (!empty($link_whatsapp)): ?>
    <a href="<?= e($link_whatsapp) ?>" class="whatsapp-float" target="_blank"><i class="fab fa-whatsapp"></i></a>
    <?php endif; ?>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script src="js/script.js"></script>
</body>
</html>