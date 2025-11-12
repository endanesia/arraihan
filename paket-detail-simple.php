<?php 
require_once __DIR__ . '/inc/db.php';

// Get package ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 5;

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
    die('Package not found! ID: ' . $id);
}

$link_whatsapp = get_setting('whatsapp', '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($package['title']) ?> - Simple Test</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #1a6b4a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #1a6b4a;
        }
        
        h2 {
            color: #333;
            margin: 20px 0 10px 0;
        }
        
        h3 {
            color: #1a6b4a;
            margin: 15px 0 10px 0;
        }
        
        .debug-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .debug-box h2 {
            color: #856404;
            margin-top: 0;
        }
        
        .breadcrumb {
            background: #e9ecef;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: -10px;
        }
        
        .col-8 {
            flex: 0 0 66.666667%;
            max-width: 66.666667%;
            padding: 10px;
        }
        
        .col-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 10px;
        }
        
        @media (max-width: 768px) {
            .col-8, .col-4 {
                flex: 0 0 100%;
                max-width: 100%;
            }
        }
        
        .box {
            background: white;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .price-box {
            background: linear-gradient(135deg, #1a6b4a 0%, #0d4a33 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .price-item {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        
        .price-label {
            display: block;
            font-size: 14px;
            opacity: 0.9;
        }
        
        .price-value {
            display: block;
            font-size: 24px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .btn {
            display: block;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
            font-weight: bold;
        }
        
        .btn-green {
            background: #25D366;
            color: white;
        }
        
        .btn-blue {
            background: #1a6b4a;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        ul {
            margin-left: 20px;
        }
        
        li {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="debug-box">
            <h2>🔧 SIMPLE TEST - NO CDN, PURE HTML+CSS</h2>
            <p><strong>Test ini menggunakan CSS inline 100%, tanpa Bootstrap, tanpa Font Awesome, tanpa CDN apapun.</strong></p>
            <ul>
                <li>Package ID: <?= $id ?></li>
                <li>Package Title: <?= htmlspecialchars($package['title'] ?? 'N/A') ?></li>
                <li>Has Poster: <?= !empty($package['poster']) ? 'Yes ✓' : 'No ✗' ?></li>
                <li>Database Connected: <?= function_exists('db') && db() ? 'Yes ✓' : 'No ✗' ?></li>
            </ul>
        </div>
        
        <div class="breadcrumb">
            <a href="index.php">Home</a> &gt; 
            <a href="index.php#paket">Paket</a> &gt; 
            <span><?= htmlspecialchars($package['title']) ?></span>
        </div>

        <h1><?= htmlspecialchars($package['title']) ?></h1>

        <div class="row">
            <div class="col-8">
                <div class="box">
                    <?php if (!empty($package['poster'])): ?>
                    <img src="<?= htmlspecialchars($package['poster']) ?>" 
                         alt="<?= htmlspecialchars($package['title']) ?>">
                    <?php endif; ?>
                    
                    <h2>Detail Paket</h2>
                    
                    <?php if (!empty($package['hotel'])): ?>
                    <h3>🏨 Hotel</h3>
                    <p><?= nl2br(htmlspecialchars($package['hotel'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($package['pesawat'])): ?>
                    <h3>✈️ Penerbangan</h3>
                    <p><?= nl2br(htmlspecialchars($package['pesawat'])) ?></p>
                    <?php endif; ?>

                    <?php if (!empty($package['features'])): ?>
                    <h3>✓ Fasilitas</h3>
                    <div>
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

            <div class="col-4">
                <div class="price-box">
                    <h2>💰 Harga Paket</h2>
                    
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
                    
                    <?php if (empty($package['price_quad']) && empty($package['price_triple']) && empty($package['price_double']) && !empty($package['price_value'])): ?>
                    <div class="price-item">
                        <span class="price-value"><?= htmlspecialchars($package['price_value']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="box">
                    <h3>Tertarik dengan Paket Ini?</h3>
                    <?php if (!empty($link_whatsapp)): ?>
                    <a href="<?= htmlspecialchars($link_whatsapp) ?>" class="btn btn-green" target="_blank">
                        💬 Konsultasi Gratis
                    </a>
                    <?php endif; ?>
                    <a href="tel:+6281234567890" class="btn btn-blue">
                        📞 Hubungi Kami
                    </a>
                </div>
            </div>
        </div>
        
        <div class="debug-box" style="margin-top: 30px;">
            <h3>📋 Raw Data (untuk debugging)</h3>
            <pre style="background: #f8f9fa; padding: 10px; overflow-x: auto; font-size: 12px;">
Hotel: <?= htmlspecialchars(substr($package['hotel'] ?? '', 0, 100)) ?>...
Pesawat: <?= htmlspecialchars(substr($package['pesawat'] ?? '', 0, 100)) ?>...
Features (first 200 chars): <?= htmlspecialchars(substr($package['features'] ?? '', 0, 200)) ?>...
            </pre>
        </div>
    </div>
</body>
</html>
