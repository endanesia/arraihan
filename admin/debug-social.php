<?php
// Debug social media variables
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../inc/config.php';
require_once __DIR__ . '/../inc/db.php';

echo "<h1>Debug Social Media Links</h1>";

// Test database connection
try {
    $db = db();
    echo "<p>Database connection: OK</p>";
    
    // Get all social media settings
    $social_keys = ['whatsapp', 'facebook', 'instagram', 'youtube', 'tiktok', 'twitter', 'threads'];
    
    echo "<h3>Social Media Settings from Database:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Key</th><th>Value</th><th>Empty Check</th></tr>";
    
    foreach ($social_keys as $key) {
        $value = get_setting($key, '');
        $is_empty = empty($value) ? 'YES' : 'NO';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($key) . "</td>";
        echo "<td>" . htmlspecialchars($value) . "</td>";
        echo "<td>" . $is_empty . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test the same way as in index.php
    echo "<h3>Variables as in index.php:</h3>";
    $link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
    $link_facebook = function_exists('get_setting') ? get_setting('facebook', '') : '';
    $link_instagram = function_exists('get_setting') ? get_setting('instagram', '') : '';
    $link_youtube = function_exists('get_setting') ? get_setting('youtube', '') : '';
    $link_tiktok = function_exists('get_setting') ? get_setting('tiktok', '') : '';
    $link_twitter = function_exists('get_setting') ? get_setting('twitter', '') : '';
    $link_threads = function_exists('get_setting') ? get_setting('threads', '') : '';
    
    $variables = [
        'link_whatsapp' => $link_whatsapp,
        'link_facebook' => $link_facebook, 
        'link_instagram' => $link_instagram,
        'link_youtube' => $link_youtube,
        'link_tiktok' => $link_tiktok,
        'link_twitter' => $link_twitter,
        'link_threads' => $link_threads
    ];
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Variable</th><th>Value</th><th>Will Show?</th></tr>";
    
    foreach ($variables as $var_name => $var_value) {
        $will_show = !empty($var_value) ? 'YES' : 'NO';
        echo "<tr>";
        echo "<td>" . htmlspecialchars($var_name) . "</td>";
        echo "<td>" . htmlspecialchars($var_value) . "</td>";
        echo "<td><strong>" . $will_show . "</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test HTML output
    echo "<h3>HTML Output Test:</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 10px;'>";
    
    if (!empty($link_youtube)) {
        echo '<p>YouTube button will show: <a href="' . htmlspecialchars($link_youtube) . '" class="social-float youtube-float" target="_blank"><i class="fab fa-youtube"></i></a></p>';
    } else {
        echo '<p><strong>YouTube button will NOT show</strong> - link_youtube is empty</p>';
    }
    
    if (!empty($link_threads)) {
        echo '<p>Threads button will show: <a href="' . htmlspecialchars($link_threads) . '" class="social-float threads-float" target="_blank"><i class="fab fa-threads"></i></a></p>';
    } else {
        echo '<p><strong>Threads button will NOT show</strong> - link_threads is empty</p>';
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>";
    echo "<h3>ERROR:</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "</div>";
}
?>