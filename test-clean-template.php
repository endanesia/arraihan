<?php
// Clean template test without function conflicts
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/inc/db.php';

echo "<h2>🧪 Clean Template Test</h2>";

// Test basic variables
$base = '';
$videos = [];
$test_data = [
    'title' => 'Test Video',
    'platform' => 'youtube',
    'youtube_id' => 'dQw4w9WgXcQ'
];

echo "<h3>✅ Testing Basic Template Output</h3>";

try {
    // Test HTML with PHP
    ?>
    <div class="test-section">
        <h4>Test Video Item</h4>
        <p>Title: <?= e($test_data['title']) ?></p>
        <p>Platform: <?= e($test_data['platform']) ?></p>
        
        <?php if ($test_data['platform'] === 'youtube'): ?>
            <p>YouTube ID: <?= e($test_data['youtube_id']) ?></p>
        <?php endif; ?>
        
        <?php 
        $thumb = 'https://img.youtube.com/vi/' . e($test_data['youtube_id']) . '/hqdefault.jpg';
        ?>
        
        <img src="<?= $thumb ?>" alt="Test thumbnail" style="width: 120px;">
    </div>
    
    <script>
    console.log("Template test JavaScript loaded");
    
    document.addEventListener("DOMContentLoaded", function() {
        console.log("DOM loaded in template test");
    });
    </script>
    
    <?php
    
    echo "<h3>✅ Template Test Successful!</h3>";
    echo "<p>If you see this, basic template rendering works fine.</p>";
    echo "<p>The homepage error must be in specific content or complex sections.</p>";
    
} catch (Exception $e) {
    echo "<h3>❌ Template Test Failed</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>