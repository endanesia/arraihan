<?php
// Test Popup Banner section
require_once __DIR__ . '/inc/db.php';

$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
} else {
    $base = '';
}

// Popup Banner - get active popup
$popup_banner = null;
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM popup_banner WHERE is_active = 1 ORDER BY created_at DESC LIMIT 1")) {
        $popup_banner = $res->fetch_assoc();
    }
}

require_once __DIR__ . '/inc/header.php';
?>

<h1>Test Popup Banner</h1>
<p>Testing popup banner query and modal HTML</p>

<!-- Debug popup banner data -->
<div class="debug-section">
    <h3>Popup Banner Debug</h3>
    <?php if ($popup_banner): ?>
        <p><strong>Found popup banner:</strong></p>
        <ul>
            <li>ID: <?= e($popup_banner['id'] ?? '') ?></li>
            <li>Title: <?= e($popup_banner['title'] ?? '') ?></li>
            <li>Image: <?= e($popup_banner['image'] ?? '') ?></li>
            <li>Link: <?= e($popup_banner['link'] ?? '') ?></li>
            <li>Is Active: <?= e($popup_banner['is_active'] ?? '') ?></li>
        </ul>
    <?php else: ?>
        <p><strong>No active popup banner found</strong></p>
    <?php endif; ?>
</div>

<!-- Popup Banner Modal - EXACT copy from original index.php -->
<?php if ($popup_banner): ?>
<div class="popup-banner-modal" id="popupBanner">
    <div class="popup-content">
        <span class="popup-close">&times;</span>
        <?php if (!empty($popup_banner['image'])): ?>
            <?php if (!empty($popup_banner['link'])): ?>
                <a href="<?= e($popup_banner['link']) ?>" target="_blank">
                    <img src="<?= $base ?>images/popup/<?= e($popup_banner['image']) ?>" 
                         alt="<?= e($popup_banner['title']) ?>"
                         class="popup-image">
                </a>
            <?php else: ?>
                <img src="<?= $base ?>images/popup/<?= e($popup_banner['image']) ?>" 
                     alt="<?= e($popup_banner['title']) ?>"
                     class="popup-image">
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const popup = document.getElementById('popupBanner');
    const closeBtn = document.querySelector('.popup-close');
    
    if (popup) {
        // Show popup after 3 seconds
        setTimeout(() => {
            popup.style.display = 'flex';
        }, 3000);
        
        // Close popup
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                popup.style.display = 'none';
            });
        }
        
        // Close on outside click
        popup.addEventListener('click', (e) => {
            if (e.target === popup) {
                popup.style.display = 'none';
            }
        });
    }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/inc/footer.php'; ?>