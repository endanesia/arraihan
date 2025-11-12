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

// Page configuration for header template
$page_title = 'Galeri Dokumentasi - Ar Raihan Travelindo';
$page_description = 'Galeri Foto dan Video Jamaah Umroh & Haji - Raihan Travelindo';
$current_page = 'galeri';

// Extra head content for lightbox and custom styles
$extra_head_content = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
<link rel="stylesheet" href="css/galeri.css?v=' . time() . '">';

// Include header template
require_once __DIR__ . '/inc/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Galeri Dokumentasi</h1>
            <p class="page-subtitle">Alhamdulillah, dokumentasi perjalanan ibadah jamaah bersama Raihan Travelindo</p>
            <div class="breadcrumb-nav">
                <a href="<?= $base ?>index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Galeri</span>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <a href="<?= $base ?>index.php#galeri" class="back-btn">
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

<?php
// Extra footer scripts for lightbox and gallery functionality
$extra_footer_scripts = '
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
<script>
    // Tab functionality
    function showTab(tabName) {
        document.querySelectorAll(".tab-content").forEach(content => {
            content.style.display = "none";
        });
        document.querySelectorAll(".tab-btn").forEach(btn => {
            btn.classList.remove("active");
        });
        document.getElementById(tabName + "-content").style.display = "block";
        document.getElementById(tabName + "-tab").classList.add("active");
    }

    // Video modal functionality
    function openVideo(videoId) {
        const modal = document.getElementById("videoModal");
        const frame = document.getElementById("videoFrame");
        frame.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
        modal.style.display = "block";
        document.body.style.overflow = "hidden";
    }

    function closeVideo() {
        const modal = document.getElementById("videoModal");
        const frame = document.getElementById("videoFrame");
        frame.src = "";
        modal.style.display = "none";
        document.body.style.overflow = "auto";
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById("videoModal");
        if (event.target === modal) {
            closeVideo();
        }
    }

    // Close modal with ESC key
    document.addEventListener("keydown", function(event) {
        if (event.key === "Escape") {
            closeVideo();
        }
    });

    // Lightbox configuration
    if (typeof lightbox !== "undefined") {
        lightbox.option({
            "resizeDuration": 200,
            "wrapAround": true,
            "albumLabel": "Gambar %1 dari %2"
        });
    }
</script>';

// Include footer template
require_once __DIR__ . '/inc/footer.php';
?>