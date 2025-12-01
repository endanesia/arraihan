<?php
// Test HTML complexity - Partner section
require_once __DIR__ . '/inc/db.php';

$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
} else {
    $base = '';
}

// Partners
$partners = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM partners ORDER BY id DESC LIMIT 12")) {
        while ($row = $res->fetch_assoc()) { $partners[] = $row; }
    }
}

require_once __DIR__ . '/inc/header.php';
?>

<h1>Test Partner Section HTML</h1>
<p>Testing complex partner HTML from original index.php</p>

<!-- Partner Section - EXACT copy from index.php lines 1150-1180 -->
<section class="partners">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">Partner Kami</h2>
        <p class="section-subtitle" data-aos="fade-up" data-aos-delay="100">Bekerja sama dengan berbagai pihak terpercaya</p>
        
        <?php if (!empty($partners)): ?>
        <div class="partner-grid" data-aos="fade-up" data-aos-delay="200">
            <?php foreach ($partners as $partner): ?>
            <div class="partner-item">
                <?php if (!empty($partner['logo'])): ?>
                <img src="<?= $base ?>images/partners/<?= e($partner['logo']) ?>" 
                     alt="<?= e($partner['name']) ?>" 
                     title="<?= e($partner['name']) ?>"
                     loading="lazy">
                <?php else: ?>
                <div class="partner-placeholder">
                    <i class="fas fa-building"></i>
                    <span><?= e($partner['name']) ?></span>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-center">Belum ada partner yang ditambahkan.</p>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>