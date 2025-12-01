<?php
// Progressive test to find where homepage fails
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 Progressive Homepage Test</h2>";

try {
    echo "<h3>Step 1: Basic includes</h3>";
    require_once __DIR__ . '/inc/db.php';
    echo "✅ db.php loaded<br>";
    
    echo "<h3>Step 2: Base URL config</h3>";
    $base = '';
    if (isset($_SERVER['HTTP_HOST']) && 
        (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
         $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
        $base = '/dev/';
    } else {
        // Production environment (root domain)
        $base = '';
    }
    echo "✅ Base URL: '$base'<br>";
    
    echo "<h3>Step 3: Database connection</h3>";
    if (function_exists('db') && db()) {
        echo "✅ Database connected<br>";
    } else {
        echo "❌ Database connection failed<br>";
        exit;
    }
    
    echo "<h3>Step 4: Test each data query individually</h3>";
    
    // Packages
    echo "<p><strong>Testing packages:</strong></p>";
    $packages = [];
    if ($res = db()->query("SELECT * FROM packages WHERE is_active = 1 ORDER BY created_at DESC")) {
        $count = $res->num_rows;
        echo "✅ Packages query: {$count} rows<br>";
        while ($row = $res->fetch_assoc()) { $packages[] = $row; }
    } else {
        echo "❌ Packages query failed: " . db()->error . "<br>";
    }
    
    // Schedules
    echo "<p><strong>Testing schedules:</strong></p>";
    $schedules = [];
    if ($res = db()->query("SELECT * FROM schedules WHERE departure_date >= CURDATE() ORDER BY departure_date ASC LIMIT 6")) {
        $count = $res->num_rows;
        echo "✅ Schedules query: {$count} rows<br>";
        while ($row = $res->fetch_assoc()) { $schedules[] = $row; }
    } else {
        echo "❌ Schedules query failed: " . db()->error . "<br>";
    }
    
    // Videos (we know this works)
    echo "<p><strong>Testing videos:</strong></p>";
    $videos = [];
    $res = db()->query("SELECT youtube_id, title, platform, video_url FROM gallery_videos WHERE 
                        (platform = 'youtube' AND youtube_id IS NOT NULL AND youtube_id != '') OR 
                        (platform = 'instagram' AND video_url IS NOT NULL) OR 
                        (platform = 'tiktok' AND video_url IS NOT NULL) OR
                        (platform IS NULL AND youtube_id IS NOT NULL AND youtube_id != '' AND youtube_id NOT LIKE '%instagram.com%' AND youtube_id NOT LIKE '%tiktok.com%')
                        ORDER BY id DESC LIMIT 3");
    if ($res) {
        $count = $res->num_rows;
        echo "✅ Videos query: {$count} rows<br>";
        while ($row = $res->fetch_assoc()) { $videos[] = $row; }
    } else {
        echo "❌ Videos query failed: " . db()->error . "<br>";
    }
    
    // Mutawwif
    echo "<p><strong>Testing mutawwif:</strong></p>";
    $mutawwif = [];
    if ($res = db()->query("SELECT * FROM mutawwif WHERE is_active = 1 ORDER BY urutan ASC, id ASC")) {
        $count = $res->num_rows;
        echo "✅ Mutawwif query: {$count} rows<br>";
        while ($row = $res->fetch_assoc()) { $mutawwif[] = $row; }
    } else {
        echo "❌ Mutawwif query failed: " . db()->error . "<br>";
    }
    
    // Testimonials
    echo "<p><strong>Testing testimonials:</strong></p>";
    $testimonials = [];
    if ($res = db()->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 6")) {
        $count = $res->num_rows;
        echo "✅ Testimonials query: {$count} rows<br>";
        while ($row = $res->fetch_assoc()) { $testimonials[] = $row; }
    } else {
        echo "❌ Testimonials query failed: " . db()->error . "<br>";
    }
    
    // Hero slides
    echo "<p><strong>Testing hero slides:</strong></p>";
    $hero_slides = [];
    if ($res = db()->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")) {
        $count = $res->num_rows;
        echo "✅ Hero slides query: {$count} rows<br>";
        while ($row = $res->fetch_assoc()) { $hero_slides[] = $row; }
    } else {
        echo "❌ Hero slides query failed: " . db()->error . "<br>";
    }
    
    echo "<h3>Step 5: Test settings functions</h3>";
    if (function_exists('get_setting')) {
        echo "✅ get_setting function exists<br>";
        $test_setting = get_setting('greeting_title', 'Default Title');
        echo "✅ get_setting test: " . htmlspecialchars($test_setting) . "<br>";
    } else {
        echo "❌ get_setting function not found<br>";
    }
    
    echo "<h3>Step 6: Test social links</h3>";
    $link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
    $link_facebook = function_exists('get_setting') ? get_setting('facebook', '') : '';
    $link_instagram = function_exists('get_setting') ? get_setting('instagram', '') : '';
    echo "✅ Social links loaded<br>";
    
    echo "<h3>✅ All Basic Tests Passed!</h3>";
    echo "<p>Data queries are working. The error might be in the template HTML or a specific function.</p>";
    echo "<p>Let's test loading the header template:</p>";
    
    // Test header include (this might be where it fails)
    echo "<h3>Step 7: Testing header template</h3>";
    ob_start();
    try {
        require_once __DIR__ . '/inc/header.php';
        $header_content = ob_get_contents();
        ob_end_clean();
        echo "✅ Header template loaded successfully<br>";
        echo "<p>Header size: " . strlen($header_content) . " bytes</p>";
    } catch (Exception $e) {
        ob_end_clean();
        echo "❌ Header template failed: " . $e->getMessage() . "<br>";
        throw $e;
    }
    
    echo "<h3>🎉 All Tests Completed Successfully!</h3>";
    
} catch (Exception $e) {
    echo "<h3>❌ Error Found at Step:</h3>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>File:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
}
?>