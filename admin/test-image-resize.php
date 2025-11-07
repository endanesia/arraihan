<?php
require_once __DIR__ . '/../inc/db.php';
require_login();

echo "<h2>Image Resize Test</h2>";

// Test if GD extension is loaded
if (!extension_loaded('gd')) {
    echo "<p>❌ PHP GD extension is NOT loaded!</p>";
    exit;
}

echo "<p>✅ PHP GD extension is loaded</p>";

// Show GD info
$gdInfo = gd_info();
echo "<h3>GD Information:</h3>";
echo "<ul>";
foreach ($gdInfo as $key => $value) {
    echo "<li><strong>$key:</strong> " . (is_bool($value) ? ($value ? 'Yes' : 'No') : $value) . "</li>";
}
echo "</ul>";

// Test resize function (copy from gallery-images.php)
function testResizeImage($source, $destination, $maxWidth = 1920, $maxHeight = 1080, $quality = 85) {
    // Get image info
    $imageInfo = getimagesize($source);
    if (!$imageInfo) return false;
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $imageType = $imageInfo[2];
    
    echo "<p><strong>Original image:</strong> {$originalWidth}x{$originalHeight}px, Type: $imageType</p>";
    
    // Check if resize is needed
    if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
        echo "<p>ℹ️ No resize needed - image is within limits</p>";
        return copy($source, $destination);
    }
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = (int)($originalWidth * $ratio);
    $newHeight = (int)($originalHeight * $ratio);
    
    echo "<p><strong>New dimensions:</strong> {$newWidth}x{$newHeight}px (ratio: " . number_format($ratio, 3) . ")</p>";
    
    // Create image resource from source
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($source);
            echo "<p>✅ Created JPEG resource</p>";
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($source);
            echo "<p>✅ Created PNG resource</p>";
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagecreatefromwebp')) {
                $sourceImage = imagecreatefromwebp($source);
                echo "<p>✅ Created WebP resource</p>";
            } else {
                echo "<p>❌ WebP support not available</p>";
                return false;
            }
            break;
        default:
            echo "<p>❌ Unsupported image type</p>";
            return false;
    }
    
    if (!$sourceImage) {
        echo "<p>❌ Failed to create source image resource</p>";
        return false;
    }
    
    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and WebP
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_WEBP) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        echo "<p>✅ Transparency preserved</p>";
    }
    
    // Resize image
    if (imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight)) {
        echo "<p>✅ Image resampled successfully</p>";
    } else {
        echo "<p>❌ Failed to resample image</p>";
        imagedestroy($sourceImage);
        imagedestroy($newImage);
        return false;
    }
    
    // Save resized image
    $result = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($newImage, $destination, $quality);
            echo "<p>✅ Saved as JPEG (quality: $quality)</p>";
            break;
        case IMAGETYPE_PNG:
            $pngQuality = (int)(9 - ($quality / 10));
            $result = imagepng($newImage, $destination, $pngQuality);
            echo "<p>✅ Saved as PNG (compression: $pngQuality)</p>";
            break;
        case IMAGETYPE_WEBP:
            if (function_exists('imagewebp')) {
                $result = imagewebp($newImage, $destination, $quality);
                echo "<p>✅ Saved as WebP (quality: $quality)</p>";
            } else {
                echo "<p>❌ WebP save not supported</p>";
            }
            break;
    }
    
    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    if ($result) {
        $newSize = filesize($destination);
        echo "<p>✅ Final file size: " . number_format($newSize / 1024, 1) . " KB</p>";
    } else {
        echo "<p>❌ Failed to save resized image</p>";
    }
    
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    $f = $_FILES['test_image'];
    if ($f['error'] === UPLOAD_ERR_OK) {
        echo "<h3>Testing Image Resize:</h3>";
        
        $tempDest = sys_get_temp_dir() . '/test_resized_' . time() . '.jpg';
        
        echo "<p><strong>Original file:</strong> " . htmlspecialchars($f['name']) . "</p>";
        echo "<p><strong>File size:</strong> " . number_format($f['size'] / 1024, 1) . " KB</p>";
        
        if (testResizeImage($f['tmp_name'], $tempDest)) {
            echo "<p>🎉 <strong>Resize test SUCCESSFUL!</strong></p>";
            @unlink($tempDest); // cleanup
        } else {
            echo "<p>❌ <strong>Resize test FAILED!</strong></p>";
        }
    } else {
        echo "<p>❌ Upload error: " . $f['error'] . "</p>";
    }
}
?>

<div class="card mt-4">
<div class="card-body">
<h4>Test Image Resize</h4>
<form method="post" enctype="multipart/form-data">
  <div class="mb-3">
    <label class="form-label">Choose a large image to test resize:</label>
    <input type="file" name="test_image" accept="image/*" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Test Resize</button>
</form>
<div class="mt-3">
  <small class="text-muted">
    This will test the resize function without saving to gallery. 
    Try uploading a large image (&gt;1920px width or &gt;2MB) to see the resize in action.
  </small>
</div>
</div>
</div>

<hr>
<p><a href="gallery-images.php">← Back to Gallery Images</a></p>