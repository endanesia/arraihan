<?php
require_once __DIR__ . '/../inc/db.php';
require_login();

echo "<h2>Gallery Images Upload Debug</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Debug:</h3>";
    echo "<pre>";
    echo "POST data:\n";
    print_r($_POST);
    echo "\nFILES data:\n";
    print_r($_FILES);
    echo "</pre>";
    
    echo "<h3>Upload Analysis:</h3>";
    
    if (isset($_FILES['image'])) {
        $f = $_FILES['image'];
        echo "<p>File submitted: ✅ YES</p>";
        echo "<p>File name: " . htmlspecialchars($f['name']) . "</p>";
        echo "<p>File type: " . htmlspecialchars($f['type']) . "</p>";
        echo "<p>File size: " . $f['size'] . " bytes</p>";
        echo "<p>File error code: " . $f['error'] . "</p>";
        
        $error_messages = [
            UPLOAD_ERR_OK => 'No error',
            UPLOAD_ERR_INI_SIZE => 'File too large (exceeds upload_max_filesize)',
            UPLOAD_ERR_FORM_SIZE => 'File too large (exceeds MAX_FILE_SIZE)',
            UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload'
        ];
        
        echo "<p>Error meaning: " . ($error_messages[$f['error']] ?? 'Unknown error') . "</p>";
        
        if ($f['error'] === UPLOAD_ERR_OK) {
            echo "<p>✅ File upload successful!</p>";
            
            $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp'];
            if (isset($allowed[$f['type']])) {
                echo "<p>✅ File type allowed</p>";
            } else {
                echo "<p>❌ File type NOT allowed. Detected: " . htmlspecialchars($f['type']) . "</p>";
            }
            
            // Check upload directory
            echo "<h4>Directory Check:</h4>";
            $uploads_dir = $config['app']['uploads_dir'] ?? './uploads';
            echo "<p>Upload directory: " . htmlspecialchars($uploads_dir) . "</p>";
            echo "<p>Directory exists: " . (is_dir($uploads_dir) ? "✅ YES" : "❌ NO") . "</p>";
            echo "<p>Directory writable: " . (is_writable($uploads_dir) ? "✅ YES" : "❌ NO") . "</p>";
            
        } else {
            echo "<p>❌ File upload failed</p>";
        }
    } else {
        echo "<p>❌ No file submitted in FILES array</p>";
    }
    
    echo "<h3>PHP Settings:</h3>";
    echo "<p>upload_max_filesize: " . ini_get('upload_max_filesize') . "</p>";
    echo "<p>post_max_size: " . ini_get('post_max_size') . "</p>";
    echo "<p>max_file_uploads: " . ini_get('max_file_uploads') . "</p>";
    echo "<p>file_uploads: " . (ini_get('file_uploads') ? 'ON' : 'OFF') . "</p>";
    
} else {
    echo "<p>Submit the form below to test upload:</p>";
}
?>

<div class="card mt-4">
<div class="card-body">
<h4>Test Upload Form</h4>
<form method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label class="form-label">Choose Image:</label>
    <input type="file" name="image" accept="image/*" class="form-control" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Title (optional):</label>
    <input type="text" name="title" class="form-control">
  </div>
  <button type="submit" class="btn btn-primary">Test Upload</button>
</form>
</div>
</div>

<hr>
<p><a href="gallery-images.php">← Back to Gallery Images</a></p>