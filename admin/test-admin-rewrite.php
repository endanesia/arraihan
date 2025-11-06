<?php
// Test URL rewriting untuk admin panel
echo "<h2>Admin Panel URL Rewriting Test</h2>";
echo "<p><strong>Current URL:</strong> " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p><strong>PHP_SELF:</strong> " . $_SERVER['PHP_SELF'] . "</p>";

echo "<h3>Test Links - Admin Pages:</h3>";
$admin_pages = [
    'dashboard' => 'Dashboard',
    'posts' => 'Posts Management',
    'packages' => 'Packages Management',
    'partners' => 'Partners Management',
    'schedules' => 'Schedules Management',
    'gallery-images' => 'Gallery Images',
    'gallery-videos' => 'Gallery Videos',
    'social-links' => 'Social Links'
];

echo "<ul>";
foreach ($admin_pages as $page => $title) {
    echo "<li><a href='/dev/admin/$page'>$title</a> (tanpa .php)</li>";
}
echo "</ul>";

echo "<h3>URL Rewriting Status:</h3>";
if (strpos($_SERVER['REQUEST_URI'], '.php') === false) {
    echo "<p style='color: green;'>✓ URL rewriting bekerja! (tidak ada .php di URL)</p>";
} else {
    echo "<p style='color: orange;'>⚠ Masih menggunakan .php di URL</p>";
}

echo "<h3>Server Info:</h3>";
echo "<p><strong>Request Method:</strong> " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<p><strong>Query String:</strong> " . ($_SERVER['QUERY_STRING'] ?? 'none') . "</p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2 { color: #333; }
h3 { color: #666; margin-top: 20px; }
ul { margin-left: 20px; }
li { margin: 5px 0; }
a { color: #007cba; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>