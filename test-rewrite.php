<?php
// Test file untuk memeriksa konfigurasi Apache
echo "<h2>Apache Configuration Test</h2>";

// Cek mod_rewrite
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "<p style='color: green;'>✓ mod_rewrite is ENABLED</p>";
    } else {
        echo "<p style='color: red;'>✗ mod_rewrite is DISABLED</p>";
    }
    
    echo "<h3>Loaded Apache Modules:</h3>";
    echo "<ul>";
    foreach ($modules as $module) {
        echo "<li>$module</li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠ Cannot check Apache modules (not running under Apache or function not available)</p>";
}

// Test SERVER variables
echo "<h3>Server Information:</h3>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Request URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";

// Test .htaccess
echo "<h3>.htaccess Test:</h3>";
if (file_exists('.htaccess')) {
    echo "<p style='color: green;'>✓ .htaccess file exists</p>";
    echo "<p><strong>File size:</strong> " . filesize('.htaccess') . " bytes</p>";
} else {
    echo "<p style='color: red;'>✗ .htaccess file not found</p>";
}

// Test URL rewriting
echo "<h3>URL Rewrite Test:</h3>";
echo "<p>Try accessing this page without .php extension:</p>";
echo "<p><a href='test-rewrite'>test-rewrite (without .php)</a></p>";
echo "<p>If the link above works, URL rewriting is working correctly!</p>";
?>