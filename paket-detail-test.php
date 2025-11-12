<?php 
require_once __DIR__ . '/inc/db.php';

// Get package ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 5; // Default to 5 for testing

// Fetch package data
$package = null;
if (function_exists('db') && db()) {
    $stmt = db()->prepare("SELECT * FROM packages WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $package = $result->fetch_assoc();
    $stmt->close();
}

if (!$package) {
    die('Package not found!');
}

$link_whatsapp = get_setting('whatsapp', '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($package['title']) ?> - Test</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Custom Inline CSS for Testing -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            padding-top: 20px;
        }
        
        .test-header {
            background: linear-gradient(135deg, #1a6b4a 0%, #0d4a33 100%);
            color: white;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 10px;
        }
        
        .page-breadcrumb {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .package-info {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        
        .package-info h3 {
            color: #1a6b4a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #1a6b4a;
        }
        
        .price-section {
            background: linear-gradient(135deg, #1a6b4a 0%, #0d4a33 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 20px;
        }
        
        .price-item {
            background: rgba(255,255,255,0.1);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
        }
        
        .price-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .price-value {
            font-size: 1.5rem;
            font-weight: 700;
            display: block;
        }
        
        .btn-consultation,
        .btn-booking {
            display: block;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .btn-consultation {
            background: #25D366;
            color: white;
        }
        
        .btn-booking {
            background: #1a6b4a;
            color: white;
        }
        
        .package-poster {
            width: 100%;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="test-header">
            <h1>🧪 TEST MODE - Package Detail</h1>
            <p>Testing paket-detail.php tanpa template system</p>
        </div>
        
        <nav class="page-breadcrumb">
            <a href="index.php">Home</a> > 
            <a href="index.php#paket">Paket</a> > 
            <span><?= htmlspecialchars($package['title']) ?></span>
        </nav>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="package-info">
                    <h3><i class="fas fa-kaaba"></i> <?= htmlspecialchars($package['title']) ?></h3>
                    
                    <?php if (!empty($package['poster'])): ?>
                    <img src="<?= htmlspecialchars($package['poster']) ?>" 
                         alt="<?= htmlspecialchars($package['title']) ?>" 
                         class="package-poster">
                    <?php endif; ?>
                    
                    <?php if (!empty($package['hotel'])): ?>
                    <h5><i class="fas fa-hotel"></i> Hotel</h5>
                    <p><?= nl2br(htmlspecialchars($package['hotel'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($package['pesawat'])): ?>
                    <h5><i class="fas fa-plane"></i> Penerbangan</h5>
                    <p><?= nl2br(htmlspecialchars($package['pesawat'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($package['features'])): ?>
                    <h5><i class="fas fa-check-circle"></i> Fasilitas</h5>
                    <div class="features-content">
                        <?php 
                        $features = html_entity_decode($package['features'], ENT_QUOTES, 'UTF-8');
                        if (strip_tags($features) !== $features) {
                            echo strip_tags($features, '<p><br><strong><b><em><i><ul><li><ol>');
                        } else {
                            echo nl2br(htmlspecialchars($features));
                        }
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="price-section">
                    <h3>💰 Harga Paket</h3>
                    
                    <?php if (!empty($package['price_quad'])): ?>
                    <div class="price-item">
                        <span class="price-label">Quad</span>
                        <span class="price-value"><?= htmlspecialchars($package['price_quad']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($package['price_triple'])): ?>
                    <div class="price-item">
                        <span class="price-label">Triple</span>
                        <span class="price-value"><?= htmlspecialchars($package['price_triple']) ?></span>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($package['price_double'])): ?>
                    <div class="price-item">
                        <span class="price-label">Double</span>
                        <span class="price-value"><?= htmlspecialchars($package['price_double']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="package-info">
                    <h4>Tertarik dengan Paket Ini?</h4>
                    <?php if (!empty($link_whatsapp)): ?>
                    <a href="<?= htmlspecialchars($link_whatsapp) ?>" class="btn-consultation" target="_blank">
                        <i class="fab fa-whatsapp"></i> Konsultasi Gratis
                    </a>
                    <?php endif; ?>
                    <a href="tel:+6281234567890" class="btn-booking">
                        <i class="fas fa-phone"></i> Hubungi Kami
                    </a>
                </div>
            </div>
        </div>

        <div class="alert alert-info mt-4">
            <h5>Debug Info:</h5>
            <ul>
                <li>Package ID: <?= $id ?></li>
                <li>Package Title: <?= htmlspecialchars($package['title'] ?? 'N/A') ?></li>
                <li>Has Poster: <?= !empty($package['poster']) ? 'Yes' : 'No' ?></li>
                <li>Bootstrap Loaded: <span id="bs-check">Checking...</span></li>
            </ul>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Check if Bootstrap is loaded
        document.getElementById('bs-check').textContent = 
            typeof bootstrap !== 'undefined' ? '✅ Yes' : '❌ No';
    </script>
</body>
</html>
