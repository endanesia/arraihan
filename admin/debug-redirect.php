<?php
echo "<h2>Debug .htaccess - Redirect Test</h2>";

echo "<h3>Current Request Info:</h3>";
echo "<p><strong>REQUEST_URI:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>THE_REQUEST:</strong> " . $_SERVER['REQUEST_METHOD'] . " " . $_SERVER['REQUEST_URI'] . " " . $_SERVER['SERVER_PROTOCOL'] . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . $_SERVER['PHP_SELF'] . "</p>";
echo "<p><strong>SCRIPT_NAME:</strong> " . $_SERVER['SCRIPT_NAME'] . "</p>";

// Test manual redirect dengan JavaScript untuk debugging
echo "<h3>Manual Redirect Test:</h3>";
echo "<p>Jika tidak redirect otomatis, coba klik: <a href='/dev/admin/test-admin-rewrite'>Link tanpa .php</a></p>";

echo "<script>";
echo "console.log('Current URL:', window.location.href);";
echo "console.log('Should redirect to:', window.location.href.replace('.php', ''));";
echo "</script>";

// Cek apakah ini hasil redirect atau akses langsung
if (isset($_SERVER['HTTP_REFERER'])) {
    echo "<p><strong>Referer:</strong> " . $_SERVER['HTTP_REFERER'] . "</p>";
}

echo "<h3>Test Links:</h3>";
echo "<p><a href='/dev/admin/dashboard.php'>Dashboard dengan .php</a></p>";
echo "<p><a href='/dev/admin/dashboard'>Dashboard tanpa .php</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
a { color: #007cba; }
</style>