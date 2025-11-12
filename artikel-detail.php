<?php 
require_once __DIR__ . '/inc/db.php';

// Get article ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch article details
$article = null;
if ($id > 0 && function_exists('db') && db()) {
    $stmt = db()->prepare("SELECT id, title, content, created_at, cover_image FROM posts WHERE id = ? AND published = 1 LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $article = $result->fetch_assoc();
    }
}

// Redirect if article not found
if (!$article) {
    header('Location: artikel.php');
    exit;
}

// Fetch related articles (latest 3, excluding current)
$relatedArticles = [];
if (function_exists('db') && db()) {
    $stmt = db()->prepare("SELECT id, title, content, created_at, cover_image FROM posts WHERE id != ? AND published = 1 ORDER BY created_at DESC LIMIT 3");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $relatedArticles[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e(substr(strip_tags($article['content']), 0, 160)) ?>">
    <title><?= e($article['title']) ?> - Raihan Travelindo</title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .article-header {
            background: linear-gradient(135deg, #1a6b4a 0%, #123e2c 100%);
            color: white;
            padding: 120px 0 60px;
            position: relative;
            overflow: hidden;
        }
        .article-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('images/bg.jpeg') center/cover;
            opacity: 0.1;
            z-index: 1;
        }
        .article-header .container {
            position: relative;
            z-index: 2;
        }
        .article-meta {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 30px;
            font-size: 0.9rem;
        }
        .breadcrumb a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        .breadcrumb a:hover {
            color: white;
        }
        .article-section {
            padding: 80px 0;
        }
        .article-container {
            max-width: 800px;
            margin: 0 auto;
        }
        .article-featured-image {
            width: 100%;
            height: 400px;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .article-featured-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .article-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
        }
        .article-content h1,
        .article-content h2,
        .article-content h3,
        .article-content h4,
        .article-content h5,
        .article-content h6 {
            color: #1a6b4a;
            font-weight: 600;
            margin: 30px 0 20px;
        }
        .article-content h2 {
            font-size: 1.8rem;
            border-bottom: 3px solid #1a6b4a;
            padding-bottom: 10px;
        }
        .article-content h3 {
            font-size: 1.5rem;
        }
        .article-content p {
            margin-bottom: 20px;
        }
        .article-content ul,
        .article-content ol {
            margin: 20px 0;
            padding-left: 30px;
        }
        .article-content li {
            margin-bottom: 10px;
        }
        .article-content blockquote {
            background: #f8f9fa;
            border-left: 4px solid #1a6b4a;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
            font-style: italic;
        }
        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .article-footer {
            border-top: 2px solid #e1e8ed;
            padding-top: 30px;
            margin-top: 50px;
        }
        .article-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 30px;
        }
        .tag {
            background: #f8f9fa;
            color: #1a6b4a;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .share-section {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 15px;
            margin: 40px 0;
        }
        .share-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #333;
        }
        .share-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .share-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            color: white;
            transition: transform 0.3s ease;
        }
        .share-btn:hover {
            transform: translateY(-2px);
            color: white;
        }
        .share-btn.facebook { background: #4267B2; }
        .share-btn.twitter { background: #1DA1F2; }
        .share-btn.whatsapp { background: #25D366; }
        .share-btn.telegram { background: #0088cc; }
        .related-section {
            background: #f8f9fa;
            padding: 60px 0;
        }
        .related-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .related-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .related-card:hover {
            transform: translateY(-5px);
        }
        .related-image {
            height: 180px;
            overflow: hidden;
        }
        .related-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .related-card:hover .related-image img {
            transform: scale(1.1);
        }
        .related-content {
            padding: 20px;
        }
        .related-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            line-height: 1.4;
        }
        .related-excerpt {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        .related-date {
            font-size: 0.8rem;
            color: #999;
        }
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: #1a6b4a;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 30px;
            transition: color 0.3s ease;
        }
        .back-btn:hover {
            color: #123e2c;
        }
        
        @media (max-width: 768px) {
            .article-title {
                font-size: 2rem;
            }
            .article-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .article-featured-image {
                height: 250px;
            }
            .share-buttons {
                gap: 10px;
            }
            .share-btn {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            .related-grid {
                grid-template-columns: 1fr;
                gap: 20px;
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
                        <li><a href="index.php" class="nav-link">Home</a></li>
                        <li><a href="index.php#paket" class="nav-link">Paket</a></li>
                        <li><a href="index.php#jadwal" class="nav-link">Jadwal</a></li>
                        <li><a href="galeri.php" class="nav-link">Galeri</a></li>
                        <li><a href="artikel.php" class="nav-link active">Artikel</a></li>
                        <li><a href="index.php#tentang" class="nav-link">Tentang Kami</a></li>
                        <li><a href="index.php#kontak" class="nav-link">Kontak</a></li>
                    </ul>
                    <div class="nav-buttons">
                        <a href="#" class="btn-whatsapp"><i class="fab fa-whatsapp"></i> WhatsApp Kami</a>
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

    <!-- Article Header -->
    <section class="article-header">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <a href="artikel.php">Artikel</a>
                <i class="fas fa-chevron-right"></i>
                <span><?= e($article['title']) ?></span>
            </div>
            <div class="article-meta">
                <span><i class="fas fa-calendar"></i> <?= date('d F Y', strtotime($article['created_at'])) ?></span>
                <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($article['created_at'])) ?> WIB</span>
                <span><i class="fas fa-user"></i> Admin</span>
            </div>
            <h1 class="article-title"><?= e($article['title']) ?></h1>
        </div>
    </section>

    <!-- Article Content -->
    <section class="article-section">
        <div class="container">
            <a href="artikel.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Daftar Artikel
            </a>
            
            <div class="article-container">
                <?php if (!empty($article['cover_image'])): ?>
                <div class="article-featured-image">
                    <img src="<?= e($article['cover_image']) ?>" alt="<?= e($article['title']) ?>">
                </div>
                <?php endif; ?>
                
                <div class="article-content">
                    <?= $article['content'] ?>
                </div>

                <!-- Share Section -->
                <div class="share-section">
                    <h3 class="share-title">Bagikan Artikel Ini</h3>
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                           target="_blank" class="share-btn facebook">
                            <i class="fab fa-facebook-f"></i>
                            Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url=<?= urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>&text=<?= urlencode($article['title']) ?>" 
                           target="_blank" class="share-btn twitter">
                            <i class="fab fa-twitter"></i>
                            Twitter
                        </a>
                        <a href="https://wa.me/?text=<?= urlencode($article['title'] . ' - ' . "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>" 
                           target="_blank" class="share-btn whatsapp">
                            <i class="fab fa-whatsapp"></i>
                            WhatsApp
                        </a>
                        <a href="https://t.me/share/url?url=<?= urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]") ?>&text=<?= urlencode($article['title']) ?>" 
                           target="_blank" class="share-btn telegram">
                            <i class="fab fa-telegram"></i>
                            Telegram
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Articles -->
    <?php if (!empty($relatedArticles)): ?>
    <section class="related-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Artikel Terkait</h2>
                <p class="section-desc">Artikel lainnya yang mungkin menarik untuk Anda</p>
            </div>
            <div class="related-grid">
                <?php foreach ($relatedArticles as $related): ?>
                <div class="related-card" onclick="location.href='artikel-detail?id=<?= $related['id'] ?>'">
                    <div class="related-image">
                        <img src="<?= !empty($related['cover_image']) ? e($related['cover_image']) : 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=400&h=250&fit=crop' ?>" alt="<?= e($related['title']) ?>">
                    </div>
                    <div class="related-content">
                        <h4 class="related-title"><?= e($related['title']) ?></h4>
                        <p class="related-excerpt"><?= e(substr(strip_tags($related['content']), 0, 100)) ?>...</p>
                        <div class="related-date">
                            <i class="fas fa-calendar"></i>
                            <?= date('d M Y', strtotime($related['created_at'])) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

<?php
// Include footer template
require_once __DIR__ . '/inc/footer.php';
?>