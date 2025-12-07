<?php
/**
 * Script untuk mengecek dan memperbaiki permission direktori video
 * Jalankan file ini di browser untuk debug masalah upload
 */

$baseDir = __DIR__ . '/../images/videos/';

echo "<h2>Video Upload Directory Check</h2>";
echo "<hr>";

// Check if directory exists
echo "<h3>1. Directory Existence</h3>";
if (is_dir($baseDir)) {
    echo "✅ Directory exists: <code>$baseDir</code><br>";
} else {
    echo "❌ Directory NOT exists: <code>$baseDir</code><br>";
    echo "Attempting to create...<br>";
    if (mkdir($baseDir, 0755, true)) {
        echo "✅ Directory created successfully!<br>";
    } else {
        echo "❌ Failed to create directory!<br>";
    }
}

// Check permissions
echo "<h3>2. Directory Permissions</h3>";
if (is_dir($baseDir)) {
    $perms = fileperms($baseDir);
    $info = substr(sprintf('%o', $perms), -4);
    echo "Current permissions: <code>$info</code><br>";
    
    if (is_writable($baseDir)) {
        echo "✅ Directory is writable<br>";
    } else {
        echo "❌ Directory is NOT writable<br>";
        echo "Run this command on server:<br>";
        echo "<code>chmod 755 $baseDir</code><br>";
    }
    
    if (is_readable($baseDir)) {
        echo "✅ Directory is readable<br>";
    } else {
        echo "❌ Directory is NOT readable<br>";
    }
} else {
    echo "⚠️ Cannot check permissions - directory doesn't exist<br>";
}

// Check parent directory
echo "<h3>3. Parent Directory (images/)</h3>";
$parentDir = __DIR__ . '/../images/';
if (is_dir($parentDir)) {
    $perms = fileperms($parentDir);
    $info = substr(sprintf('%o', $perms), -4);
    echo "Current permissions: <code>$info</code><br>";
    
    if (is_writable($parentDir)) {
        echo "✅ Parent directory is writable<br>";
    } else {
        echo "❌ Parent directory is NOT writable<br>";
        echo "Run this command on server:<br>";
        echo "<code>chmod 755 $parentDir</code><br>";
    }
}

// Check PHP upload settings
echo "<h3>4. PHP Upload Settings</h3>";
echo "upload_max_filesize: <code>" . ini_get('upload_max_filesize') . "</code><br>";
echo "post_max_size: <code>" . ini_get('post_max_size') . "</code><br>";
echo "max_file_uploads: <code>" . ini_get('max_file_uploads') . "</code><br>";
echo "upload_tmp_dir: <code>" . (ini_get('upload_tmp_dir') ?: 'Default system temp dir') . "</code><br>";

// Test file creation
echo "<h3>5. Test File Creation</h3>";
$testFile = $baseDir . 'test-' . time() . '.txt';
if (file_put_contents($testFile, 'test')) {
    echo "✅ Successfully created test file: <code>" . basename($testFile) . "</code><br>";
    if (unlink($testFile)) {
        echo "✅ Successfully deleted test file<br>";
    }
} else {
    echo "❌ Failed to create test file!<br>";
    echo "This is the problem - PHP cannot write to this directory.<br>";
}

// List existing files
echo "<h3>6. Existing Files</h3>";
if (is_dir($baseDir)) {
    $files = glob($baseDir . '*');
    if (empty($files)) {
        echo "No files found in directory<br>";
    } else {
        echo "<ul>";
        foreach ($files as $file) {
            $size = filesize($file);
            $sizeMB = number_format($size / (1024 * 1024), 2);
            echo "<li>" . basename($file) . " ({$sizeMB} MB)</li>";
        }
        echo "</ul>";
    }
}

echo "<hr>";
echo "<p><a href='hero-video.php'>← Back to Hero Video</a></p>";
?>
