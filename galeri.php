<?php 
require_once __DIR__ . '/inc/db.php';

// Get active tab from URL parameter
$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'videos' ? 'videos' : 'images';

// Fetch all gallery images
$images = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT file_path, title, created_at FROM gallery_images ORDER BY id DESC")) {
        while ($row = $res->fetch_assoc()) { $images[] = $row; }
    }
}

// Fetch all gallery videos  
$videos = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT youtube_id, title, created_at FROM gallery_videos ORDER BY id DESC")) {
        while ($row = $res->fetch_assoc()) { $videos[] = $row; }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Galeri Foto dan Video Jamaah Umroh & Haji - Raihan Travelindo">
    <title>Galeri Dokumentasi - Raihan Travelindo</title>
    <link rel="stylesheet" href="css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
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
        .gallery-section {
            padding: 80px 0;
        }
        .gallery-tabs {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 50px;
            flex-wrap: wrap;
        }
        .tab-btn {
            padding: 12px 30px;
            border: 2px solid #1a6b4a;
            background: transparent;
            color: #1a6b4a;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .tab-btn.active,
        .tab-btn:hover {
            background: #1a6b4a;
            color: white;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }
        .gallery-item {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background: white;
        }
        .gallery-item:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        .gallery-image {
            position: relative;
            height: 250px;
            overflow: hidden;
        }
        .gallery-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .gallery-item:hover .gallery-image img {
            transform: scale(1.1);
        }
        .gallery-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(26, 107, 74, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .gallery-item:hover .gallery-overlay {
            opacity: 1;
        }
        .gallery-overlay i {
            font-size: 2rem;
            color: white;
        }
        .gallery-info {
            padding: 20px;
        }
        .gallery-info h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
        }
        .gallery-info .date {
            font-size: 0.9rem;
            color: #666;
        }
        .video-item {
            position: relative;
            cursor: pointer;
        }
        .video-play {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #1a6b4a;
            transition: all 0.3s ease;
        }
        .video-item:hover .video-play {
            background: white;
            transform: translate(-50%, -50%) scale(1.1);
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
        
        /* Modal for videos */
        .video-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
        }
        .video-modal-content {
            position: relative;
            margin: 5% auto;
            width: 90%;
            max-width: 800px;
        }
        .video-close {
            position: absolute;
            top: -40px;
            right: 0;
            color: white;
            font-size: 30px;
            cursor: pointer;
        }
        .video-frame {
            width: 100%;
            height: 450px;
            border: none;
        }
        
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            .gallery-grid {
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 20px;
            }
            .gallery-tabs {
                gap: 10px;
            }
            .tab-btn {
                padding: 10px 20px;
                font-size: 0.9rem;
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
                        <li><a href="galeri.php" class="nav-link active">Galeri</a></li>
                        <li><a href="artikel.php" class="nav-link">Artikel</a></li>
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
            <h1 class="page-title">Galeri Dokumentasi</h1>
            <p class="page-subtitle">Alhamdulillah, dokumentasi perjalanan ibadah jamaah bersama Raihan Travelindo</p>
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Galeri</span>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <a href="index.php#galeri" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Homepage
            </a>
            
            <!-- Gallery Tabs -->
            <div class="gallery-tabs">
                <button class="tab-btn <?= $activeTab === 'images' ? 'active' : '' ?>" onclick="showTab('images')" id="images-tab">
                    <i class="fas fa-images"></i> Foto (<?= count($images) ?>)
                </button>
                <button class="tab-btn <?= $activeTab === 'videos' ? 'active' : '' ?>" onclick="showTab('videos')" id="videos-tab">
                    <i class="fas fa-video"></i> Video (<?= count($videos) ?>)
                </button>
            </div>

            <!-- Images Gallery -->
            <div id="images-content" class="tab-content" style="display: <?= $activeTab === 'images' ? 'block' : 'none' ?>;">
                <?php if (!empty($images)): ?>
                <div class="gallery-grid">
                    <?php foreach ($images as $img): ?>
                    <div class="gallery-item">
                        <div class="gallery-image">
                            <a href="<?= e($img['file_path']) ?>" data-lightbox="gallery" data-title="<?= e($img['title'] ?: 'Dokumentasi Jamaah') ?>">
                                <img src="<?= e($img['file_path']) ?>" alt="<?= e($img['title'] ?: 'Galeri') ?>">
                                <div class="gallery-overlay">
                                    <i class="fas fa-search-plus"></i>
                                </div>
                            </a>
                        </div>
                        <div class="gallery-info">
                            <h4><?= e($img['title'] ?: 'Dokumentasi Jamaah') ?></h4>
                            <?php if (!empty($img['created_at'])): ?>
                            <div class="date">
                                <i class="fas fa-calendar"></i>
                                <?= date('d F Y', strtotime($img['created_at'])) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-images"></i>
                    <h3>Belum Ada Foto</h3>
                    <p>Galeri foto sedang dalam proses pengembangan. Silakan kembali lagi nanti.</p>
                </div>
                <?php endif; ?>
            </div>

            <!-- Videos Gallery -->  
            <div id="videos-content" class="tab-content" style="display: <?= $activeTab === 'videos' ? 'block' : 'none' ?>;">
                <?php if (!empty($videos)): ?>
                <div class="gallery-grid">
                    <?php foreach ($videos as $video): ?>
                    <div class="gallery-item video-item" onclick="openVideo('<?= e($video['youtube_id']) ?>')">
                        <div class="gallery-image">
                            <img src="https://img.youtube.com/vi/<?= e($video['youtube_id']) ?>/hqdefault.jpg" alt="<?= e($video['title'] ?: 'Video') ?>">
                            <div class="video-play">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <div class="gallery-info">
                            <h4><?= e($video['title'] ?: 'Video Dokumentasi') ?></h4>
                            <?php if (!empty($video['created_at'])): ?>
                            <div class="date">
                                <i class="fas fa-calendar"></i>
                                <?= date('d F Y', strtotime($video['created_at'])) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-video"></i>
                    <h3>Belum Ada Video</h3>
                    <p>Galeri video sedang dalam proses pengembangan. Silakan kembali lagi nanti.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Video Modal -->
    <div id="videoModal" class="video-modal">
        <div class="video-modal-content">
            <span class="video-close" onclick="closeVideo()">&times;</span>
            <iframe id="videoFrame" class="video-frame" src="" allowfullscreen></iframe>
        </div>
    </div>

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
                        <li><a href="index.php#tentang">Tentang Kami</a></li>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Tab functionality
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-content').style.display = 'block';
            
            // Add active class to selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
        }

        // Video modal functionality
        function openVideo(videoId) {
            const modal = document.getElementById('videoModal');
            const frame = document.getElementById('videoFrame');
            frame.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }

        function closeVideo() {
            const modal = document.getElementById('videoModal');
            const frame = document.getElementById('videoFrame');
            frame.src = '';
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('videoModal');
            if (event.target === modal) {
                closeVideo();
            }
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeVideo();
            }
        });

        // Lightbox configuration
        lightbox.option({
            'resizeDuration': 200,
            'wrapAround': true,
            'albumLabel': 'Gambar %1 dari %2'
        });
    </script>
</body>
</html>