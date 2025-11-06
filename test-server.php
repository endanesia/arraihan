<?php
// Simple test file to check if PHP is working
echo "PHP is working!<br>";
echo "Server Time: " . date('Y-m-d H:i:s') . "<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "Script Name: " . $_SERVER['SCRIPT_NAME'] . "<br>";

// Test file existence
$files_to_check = [
    'index.php',
    '.htaccess', 
    'inc/config.php',
    'css/style.css',
    'js/script.js'
];

echo "<h3>File Check:</h3>";
foreach($files_to_check as $file) {
    $exists = file_exists($file) ? "✅ EXISTS" : "❌ NOT FOUND";
    echo "$file: $exists<br>";
}

// Test mod_rewrite
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $rewrite_enabled = in_array('mod_rewrite', $modules) ? "✅ ENABLED" : "❌ DISABLED";
    echo "<h3>Apache mod_rewrite: $rewrite_enabled</h3>";
} else {
    echo "<h3>Cannot check mod_rewrite status</h3>";
}
?>