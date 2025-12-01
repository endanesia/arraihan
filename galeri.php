<?php 
require_once __DIR__ . '/inc/db.php';

// Get active tab from URL parameter
$activeTab = isset($_GET['tab']) && $_GET['tab'] === 'videos' ? 'videos' : 'images';

// Get selected album from URL parameter
$selectedAlbum = isset($_GET['album']) ? $_GET['album'] : 'all';

// Fetch all albums
$albums = [];
if (function_exists('db') && db()) {
    // Check if album_name column exists first
    $checkColumn = db()->query("SHOW COLUMNS FROM gallery_images LIKE 'album_name'");
    if ($checkColumn && $checkColumn->num_rows > 0) {
        if ($res = db()->query("SELECT album_name, COUNT(*) as count FROM gallery_images WHERE album_name IS NOT NULL GROUP BY album_name ORDER BY album_name ASC")) {
            while ($row = $res->fetch_assoc()) { $albums[] = $row; }
        }
    }
}

// Fetch gallery images based on selected album
$images = [];
if (function_exists('db') && db()) {
    // Check if album_name column exists first
    $checkColumn = db()->query("SHOW COLUMNS FROM gallery_images LIKE 'album_name'");
    if ($checkColumn && $checkColumn->num_rows > 0) {
        $albumCondition = $selectedAlbum !== 'all' ? "WHERE album_name = '" . db()->real_escape_string($selectedAlbum) . "'" : '';
        if ($res = db()->query("SELECT file_path, title, album_name, created_at FROM gallery_images $albumCondition ORDER BY id DESC")) {
            while ($row = $res->fetch_assoc()) { $images[] = $row; }
        }
    } else {
        // Fallback if album_name column doesn't exist yet
        if ($res = db()->query("SELECT file_path, title, created_at FROM gallery_images ORDER BY id DESC")) {
            while ($row = $res->fetch_assoc()) { 
                $row['album_name'] = 'Umum'; // Default album
                $images[] = $row; 
            }
        }
    }
}

// Fetch all gallery videos with platform support
$videos = [];
if (function_exists('db') && db()) {
    // Check if new columns exist
    $checkColumns = db()->query("SHOW COLUMNS FROM gallery_videos LIKE 'platform'");
    if ($checkColumns && $checkColumns->num_rows > 0) {
        // New structure with platform support
        if ($res = db()->query("SELECT youtube_id, video_url, platform, title, created_at FROM gallery_videos ORDER BY id DESC")) {
            while ($row = $res->fetch_assoc()) { $videos[] = $row; }
        }
    } else {
        // Fallback for old structure
        if ($res = db()->query("SELECT youtube_id, title, created_at FROM gallery_videos ORDER BY id DESC")) {
            while ($row = $res->fetch_assoc()) { 
                $row['platform'] = 'youtube';
                $row['video_url'] = null;
                $videos[] = $row; 
            }
        }
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

            <!-- Album Filter (only show for images tab) -->
            <?php if (!empty($albums)): ?>
            <div id="album-filter" style="display: <?= $activeTab === 'images' ? 'block' : 'none' ?>;">
                <div class="album-filter-section">
                    <h3><i class="fas fa-folder"></i> Pilih Album</h3>
                    <div class="album-buttons">
                        <button class="album-btn <?= $selectedAlbum === 'all' ? 'active' : '' ?>" onclick="filterByAlbum('all')">
                            <i class="fas fa-th-large"></i>
                            <span>Semua Album</span>
                            <span class="count"><?= array_sum(array_column($albums, 'count')) ?></span>
                        </button>
                        <?php foreach ($albums as $album): ?>
                        <button class="album-btn <?= $selectedAlbum === $album['album_name'] ? 'active' : '' ?>" onclick="filterByAlbum('<?= e($album['album_name']) ?>')">
                            <i class="fas fa-folder"></i>
                            <span><?= e($album['album_name']) ?></span>
                            <span class="count"><?= $album['count'] ?></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

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
                            <div class="gallery-meta">
                                <div class="album-tag">
                                    <i class="fas fa-folder"></i>
                                    <?= e($img['album_name'] ?: 'Umum') ?>
                                </div>
                                <?php if (!empty($img['created_at'])): ?>
                                <div class="date">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d F Y', strtotime($img['created_at'])) ?>
                                </div>
                                <?php endif; ?>
                            </div>
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
                    <?php
                    $platform = $video['platform'] ?? 'youtube';
                    $videoId = '';
                    $thumbnailUrl = '';
                    $videoUrl = '';
                    
                    if ($platform === 'youtube') {
                        $videoId = $video['youtube_id'];
                        $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
                        $videoUrl = "https://www.youtube.com/embed/{$videoId}?autoplay=1";
                    } elseif ($platform === 'instagram') {
                        $videoUrl = $video['video_url'] ?? '';
                        $thumbnailUrl = "https://images.unsplash.com/photo-1611262588024-d12430b98920?w=400&h=300&fit=crop"; // Instagram placeholder
                    } elseif ($platform === 'tiktok') {
                        $videoUrl = $video['video_url'] ?? '';
                        $thumbnailUrl = "https://images.unsplash.com/photo-1611605698335-8b1569810432?w=400&h=300&fit=crop"; // TikTok placeholder
                    }
                    ?>
                    <div class="gallery-item video-item" data-platform="<?= e($platform) ?>" onclick="openVideoMultiPlatform('<?= e($videoUrl) ?>', '<?= e($platform) ?>')">
                        <div class="gallery-image">
                            <img src="<?= e($thumbnailUrl) ?>" alt="<?= e($video['title'] ?: 'Video') ?>">
                            <div class="video-play">
                                <?php if ($platform === 'youtube'): ?>
                                    <i class="fab fa-youtube"></i>
                                <?php elseif ($platform === 'instagram'): ?>
                                    <i class="fab fa-instagram"></i>
                                <?php elseif ($platform === 'tiktok'): ?>
                                    <i class="fab fa-tiktok"></i>
                                <?php else: ?>
                                    <i class="fas fa-play"></i>
                                <?php endif; ?>
                            </div>
                            <div class="platform-badge platform-<?= e($platform) ?>">
                                <?= ucfirst($platform) ?>
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
        
        // Show/hide album filter based on tab
        const albumFilter = document.getElementById("album-filter");
        if (tabName === "images") {
            albumFilter.style.display = "block";
        } else {
            albumFilter.style.display = "none";
        }
    }

    // Album filter functionality
    function filterByAlbum(albumName) {
        const currentTab = "<?= $activeTab ?>";
        const baseUrl = window.location.pathname;
        let newUrl = baseUrl + "?tab=" + currentTab;
        
        if (albumName !== "all") {
            newUrl += "&album=" + encodeURIComponent(albumName);
        }
        
        window.location.href = newUrl;
    }

    // Multi-platform video modal functionality
    function openVideoMultiPlatform(videoUrl, platform) {
        const modal = document.getElementById("videoModal");
        const frame = document.getElementById("videoFrame");
        
        if (platform === "youtube") {
            frame.src = videoUrl;
        } else if (platform === "instagram" || platform === "tiktok") {
            // For Instagram and TikTok, open in new window/tab
            window.open(videoUrl, "_blank");
            return;
        }
        
        modal.style.display = "block";
        document.body.style.overflow = "hidden";
    }

    // Legacy function for backward compatibility
    function openVideo(videoId) {
        openVideoMultiPlatform(`https://www.youtube.com/embed/${videoId}?autoplay=1`, "youtube");
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