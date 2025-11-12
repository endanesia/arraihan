<?php 
require_once __DIR__ . '/inc/db.php';

// Pagination setup
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 9; // Articles per page
$offset = ($page - 1) * $limit;

// Search functionality
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchCondition = '';
$searchParams = [];

if (!empty($search)) {
    $searchCondition = "WHERE title LIKE ? OR content LIKE ?";
    $searchQuery = '%' . $search . '%';
    $searchParams = [$searchQuery, $searchQuery];
}

// Fetch articles with pagination
$articles = [];
$totalArticles = 0;
if (function_exists('db') && db()) {
    // Count total articles
    $countQuery = "SELECT COUNT(*) as total FROM posts $searchCondition";
    if (!empty($searchParams)) {
        $stmt = db()->prepare($countQuery);
        $stmt->bind_param('ss', ...$searchParams);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = db()->query($countQuery);
    }
    
    if ($result) {
        $row = $result->fetch_assoc();
        $totalArticles = $row['total'];
    }
    
    // Fetch articles
    $query = "SELECT id, title, content, created_at, cover_image FROM posts $searchCondition ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
    if (!empty($searchParams)) {
        $stmt = db()->prepare($query);
        $stmt->bind_param('ss', ...$searchParams);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }
    } else {
        if ($res = db()->query($query)) {
            while ($row = $res->fetch_assoc()) {
                $articles[] = $row;
            }
        }
    }
}

// Calculate pagination
$totalPages = ceil($totalArticles / $limit);

// Fetch featured articles (latest 3)
$featuredArticles = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT id, title, content, created_at, cover_image FROM posts WHERE published = 1 ORDER BY created_at DESC LIMIT 3")) {
        while ($row = $res->fetch_assoc()) {
            $featuredArticles[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Artikel dan Berita Terbaru dari Raihan Travelindo - Tips Umroh, Haji, dan Perjalanan Ibadah">
    <title>Artikel & Berita - Raihan Travelindo</title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .page-header {
            background: linear-gradient(135deg, #1a6b4a 0%, #123e2c 100%);
            color: white;
            padding: 120px 0 80px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .page-header::before {
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
        .page-header .container {
            position: relative;
            z-index: 2;
        }
        .page-title {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .page-subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        .breadcrumb {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        .breadcrumb a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }
        .breadcrumb a:hover {
            color: white;
        }
        .articles-section {
            padding: 80px 0;
        }
        .search-box {
            max-width: 500px;
            margin: 0 auto 50px;
            position: relative;
        }
        .search-input {
            width: 100%;
            padding: 15px 50px 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 50px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .search-input:focus {
            outline: none;
            border-color: #1a6b4a;
            box-shadow: 0 0 0 3px rgba(26, 107, 74, 0.1);
        }
        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: #1a6b4a;
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .search-btn:hover {
            background: #123e2c;
        }
        .articles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        .article-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        .article-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .article-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        .article-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .article-card:hover .article-image img {
            transform: scale(1.1);
        }
        .article-date {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(26, 107, 74, 0.9);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .article-content {
            padding: 25px;
        }
        .article-title {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: #333;
            line-height: 1.4;
        }
        .article-excerpt {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .article-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #999;
        }
        .read-more {
            color: #1a6b4a;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .read-more:hover {
            color: #123e2c;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 50px;
        }
        .page-btn {
            padding: 10px 15px;
            border: 2px solid #e1e8ed;
            background: white;
            color: #666;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .page-btn:hover,
        .page-btn.active {
            background: #1a6b4a;
            color: white;
            border-color: #1a6b4a;
        }
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        .featured-section {
            background: #f8f9fa;
            padding: 60px 0;
            margin-bottom: 60px;
        }
        .featured-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
        }
        .featured-badge {
            background: #f39c12;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            .articles-grid {
                grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                gap: 20px;
            }
            .search-box {
                margin: 0 20px 40px;
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

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Artikel & Berita</h1>
            <p class="page-subtitle">Tips, panduan, dan informasi terbaru seputar perjalanan ibadah haji dan umroh</p>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Artikel</span>
            </div>
        </div>
    </section>

    <!-- Featured Articles -->
    <?php if (!empty($featuredArticles) && empty($search)): ?>
    <section class="featured-section">
        <div class="container">
            <div class="featured-badge">
                <i class="fas fa-star"></i> Artikel Pilihan
            </div>
            <div class="featured-grid">
                <?php foreach (array_slice($featuredArticles, 0, 3) as $article): ?>
                <div class="article-card" onclick="location.href='artikel-detail.php?id=<?= $article['id'] ?>'">
                    <div class="article-image">
                        <img src="<?= !empty($article['cover_image']) ? e($article['cover_image']) : 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=500&h=300&fit=crop' ?>" alt="<?= e($article['title']) ?>">
                        <div class="article-date">
                            <i class="fas fa-calendar"></i>
                            <?= date('d M Y', strtotime($article['created_at'])) ?>
                        </div>
                    </div>
                    <div class="article-content">
                        <h3 class="article-title"><?= e($article['title']) ?></h3>
                        <p class="article-excerpt"><?= e(substr(strip_tags($article['content']), 0, 150)) ?>...</p>
                        <div class="article-meta">
                            <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($article['created_at'])) ?></span>
                            <a href="artikel-detail.php?id=<?= $article['id'] ?>" class="read-more">
                                Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Articles Section -->
    <section class="articles-section">
        <div class="container">
            <a href="index.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Homepage
            </a>
            
            <!-- Search Box -->
            <div class="search-box">
                <form method="GET" action="artikel.php">
                    <input type="text" name="search" class="search-input" placeholder="Cari artikel..." value="<?= e($search) ?>">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>

            <?php if (!empty($search)): ?>
            <div style="text-align: center; margin-bottom: 30px;">
                <p>Menampilkan hasil pencarian untuk: <strong>"<?= e($search) ?>"</strong></p>
                <a href="artikel.php" style="color: #1a6b4a;">Lihat semua artikel</a>
            </div>
            <?php endif; ?>

            <!-- Articles Grid -->
            <?php if (!empty($articles)): ?>
            <div class="articles-grid">
                <?php foreach ($articles as $article): ?>
                <div class="article-card" onclick="location.href='artikel-detail.php?id=<?= $article['id'] ?>'">
                    <div class="article-image">
                        <img src="<?= !empty($article['cover_image']) ? e($article['cover_image']) : 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?w=500&h=300&fit=crop' ?>" alt="<?= e($article['title']) ?>">
                        <div class="article-date">
                            <i class="fas fa-calendar"></i>
                            <?= date('d M Y', strtotime($article['created_at'])) ?>
                        </div>
                    </div>
                    <div class="article-content">
                        <h3 class="article-title"><?= e($article['title']) ?></h3>
                        <p class="article-excerpt"><?= e(substr(strip_tags($article['content']), 0, 150)) ?>...</p>
                        <div class="article-meta">
                            <span><i class="fas fa-clock"></i> <?= date('H:i', strtotime($article['created_at'])) ?></span>
                            <a href="artikel-detail.php?id=<?= $article['id'] ?>" class="read-more">
                                Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
                <?php endif; ?>

                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <a href="?page=<?= $i ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" 
                   class="page-btn <?= $i === $page ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="?page=<?= $page + 1 ?><?= !empty($search) ? '&search=' . urlencode($search) : '' ?>" class="page-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-newspaper"></i>
                <h3><?= !empty($search) ? 'Artikel Tidak Ditemukan' : 'Belum Ada Artikel' ?></h3>
                <p><?= !empty($search) ? 'Coba kata kunci yang berbeda atau ' : 'Artikel sedang dalam proses pengembangan. ' ?></p>
                <?php if (!empty($search)): ?>
                <a href="artikel.php" style="color: #1a6b4a;">lihat semua artikel</a>
                <?php else: ?>
                <p>Silakan kembali lagi nanti.</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
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
                </div>
                <div class="footer-col">
                    <h4>Menu</h4>
                    <ul>
                        <li><a href="index.php#home">Home</a></li>
                        <li><a href="index.php#paket">Paket Umroh</a></li>
                        <li><a href="index.php#jadwal">Jadwal</a></li>
                        <li><a href="galeri.php">Galeri</a></li>
                        <li><a href="artikel.php">Artikel</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="index.php#paket">Paket Umroh</a></li>
                        <li><a href="index.php#paket">Haji Khusus</a></li>
                        <li><a href="index.php#paket">Badal Haji</a></li>
                        <li><a href="index.php#paket">Badal Umroh</a></li>
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
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/script.js"></script>
</body>
</html>