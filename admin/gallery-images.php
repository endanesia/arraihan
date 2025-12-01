<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
$msg=''; $err='';

// Image resize function
function resizeImage($source, $destination, $maxWidth = 1920, $maxHeight = 1080, $quality = 85) {
    // Get image info
    $imageInfo = getimagesize($source);
    if (!$imageInfo) return false;
    
    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $imageType = $imageInfo[2];
    
    // Check if resize is needed
    if ($originalWidth <= $maxWidth && $originalHeight <= $maxHeight) {
        // No resize needed, just copy
        return copy($source, $destination);
    }
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
    $newWidth = (int)($originalWidth * $ratio);
    $newHeight = (int)($originalHeight * $ratio);
    
    // Create image resource from source
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($source);
            break;
        case IMAGETYPE_WEBP:
            $sourceImage = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) return false;
    
    // Create new image
    $newImage = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency for PNG and WebP
    if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_WEBP) {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    
    // Save resized image
    $result = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($newImage, $destination, $quality);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($newImage, $destination, (int)(9 - ($quality / 10)));
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($newImage, $destination, $quality);
            break;
    }
    
    // Clean up
    imagedestroy($sourceImage);
    imagedestroy($newImage);
    
    return $result;
}
if ($_SERVER['REQUEST_METHOD']==='POST'){
      $action = $_POST['action'] ?? 'upload';
  if ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    $album_name = trim($_POST['album_name'] ?? 'Umum');
    if ($id>0) {
      $stmt = db()->prepare('UPDATE gallery_images SET title=?, album_name=? WHERE id=?');
      $stmt->bind_param('ssi', $title, $album_name, $id);
      $stmt->execute();
      $msg = 'Gambar diperbarui.';
    }
  } else {
    $title = trim($_POST['title'] ?? '');
    $album_name = trim($_POST['album_name'] ?? 'Umum');
    
    // Debug upload
    if (!isset($_FILES['image'])) {
      $err = 'Tidak ada file yang dikirim. Pastikan form menggunakan enctype="multipart/form-data".';
    } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      $upload_errors = [
        UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
        UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
        UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
        UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih',
        UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
        UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
        UPLOAD_ERR_EXTENSION => 'Upload dibatalkan oleh extension PHP'
      ];
      $err = $upload_errors[$_FILES['image']['error']] ?? 'Error upload tidak diketahui: ' . $_FILES['image']['error'];
    } else {
      $f = $_FILES['image'];
      
      // Validate file type
      $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp','image/jpg'=>'.jpg'];
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $detected_type = finfo_file($finfo, $f['tmp_name']);
      finfo_close($finfo);
      
      if (!isset($allowed[$detected_type])) {
        $err = 'Format gambar tidak didukung. Terdeteksi: ' . $detected_type . '. Yang didukung: JPEG, PNG, WebP.';
      } else {
        // Create directory if not exists
        $upload_dir = $config['app']['uploads_dir'];
        if (!is_dir($upload_dir)) {
          if (!@mkdir($upload_dir, 0755, true)) {
            $err = 'Gagal membuat direktori upload: ' . $upload_dir;
          }
        }
        
        if (!$err) {
          $ext = $allowed[$detected_type];
          $filename = 'img_'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).$ext;
          $dest = rtrim($upload_dir,'/\\').DIRECTORY_SEPARATOR.$filename;
          
          // Get image dimensions for info
          $imageInfo = getimagesize($f['tmp_name']);
          $originalWidth = $imageInfo[0] ?? 0;
          $originalHeight = $imageInfo[1] ?? 0;
          $originalSize = $f['size'];
          
          // Configuration for resize
          $maxWidth = 1920;   // Maximum width in pixels
          $maxHeight = 1080;  // Maximum height in pixels  
          $maxFileSize = 2 * 1024 * 1024; // 2MB in bytes
          $quality = 85; // JPEG quality (1-100)
          
          $resized = false;
          $resizeReason = '';
          
          // Check if resize is needed
          $needsResize = ($originalWidth > $maxWidth) || 
                        ($originalHeight > $maxHeight) || 
                        ($originalSize > $maxFileSize);
          
          if ($needsResize) {
            // Try to resize image
            if (resizeImage($f['tmp_name'], $dest, $maxWidth, $maxHeight, $quality)) {
              $resized = true;
              
              // Get new file size and dimensions
              $newSize = filesize($dest);
              $newImageInfo = getimagesize($dest);
              $newWidth = $newImageInfo[0] ?? 0;
              $newHeight = $newImageInfo[1] ?? 0;
              
              if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                $resizeReason .= "diperkecil dari {$originalWidth}x{$originalHeight} ke {$newWidth}x{$newHeight}px";
              }
              if ($originalSize > $maxFileSize) {
                $resizeReason .= ($resizeReason ? ', ' : '') . "dikompres dari " . number_format($originalSize/1024, 1) . "KB ke " . number_format($newSize/1024, 1) . "KB";
              }
              
            } else {
              $err = 'Gagal mengubah ukuran gambar. Pastikan PHP GD extension aktif.';
            }
          } else {
            // No resize needed, just move file
            if (!move_uploaded_file($f['tmp_name'], $dest)) {
              $err = 'Gagal memindahkan file ke direktori upload.';
            }
          }
          
          // Save to database if upload successful
          if (!$err) {
            $url = rtrim($config['app']['uploads_url'],'/').'/'.$filename;
            $stmt = db()->prepare('INSERT INTO gallery_images(title,file_path,album_name) VALUES(?,?,?)');
            $stmt->bind_param('sss', $title, $url, $album_name);
            if ($stmt->execute()) {
              if ($resized) {
                $msg = "Gambar berhasil diupload dan otomatis $resizeReason: $filename";
              } else {
                $msg = 'Gambar berhasil diupload: ' . $filename;
              }
            } else {
              $err = 'Gambar terupload tapi gagal disimpan ke database.';
              @unlink($dest); // cleanup file
            }
          }
        }
      }
    }
  }
}

// Get existing albums for dropdown
$albums = [];
if ($albumRes = db()->query("SELECT DISTINCT album_name FROM gallery_images WHERE album_name IS NOT NULL ORDER BY album_name ASC")) {
    while ($albumRow = $albumRes->fetch_assoc()) {
        $albums[] = $albumRow['album_name'];
    }
}

$res = db()->query('SELECT * FROM gallery_images ORDER BY album_name ASC, id DESC');
include __DIR__ . '/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Galeri Gambar</h3>
</div>

<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card mb-4">
  <div class="card-body">
    <form method="post" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Judul (opsional)</label>
        <input type="text" name="title" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Album</label>
        <div class="input-group">
          <input type="text" name="album_name" class="form-control" list="album-list" value="Umum" required>
          <datalist id="album-list">
            <option value="Umum">
            <?php foreach ($albums as $album): ?>
            <option value="<?= e($album) ?>">
            <?php endforeach; ?>
          </datalist>
        </div>
        <div class="form-text">Ketik nama album baru atau pilih yang sudah ada</div>
      </div>
      <div class="col-md-4">
        <label class="form-label">Pilih Gambar</label>
        <input type="file" name="image" accept="image/*" class="form-control" required>
        <div class="form-text">
          Format: JPEG, PNG, WebP. Gambar akan otomatis diperkecil jika lebih dari 1920x1080px atau 2MB.
        </div>
      </div>
      <div class="col-md-2 d-grid align-items-end">
        <button class="btn btn-success" type="submit">Upload</button>
        <input type="hidden" name="action" value="upload">
      </div>
    </form>
  </div>
  </div>

<div class="row g-3">
  <?php while($row = $res->fetch_assoc()): ?>
  <div class="col-sm-6 col-md-4 col-lg-3">
    <div class="card h-100">
      <img src="<?= e($row['file_path']) ?>" class="card-img-top" alt="<?= e($row['title']) ?>">
      <div class="card-body">
        <form method="post">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
          
          <div class="mb-2">
            <label class="form-label small">Judul:</label>
            <input type="text" name="title" class="form-control form-control-sm" value="<?= e($row['title']) ?>" placeholder="Judul gambar">
          </div>
          
          <div class="mb-2">
            <label class="form-label small">Album:</label>
            <div class="input-group input-group-sm">
              <input type="text" name="album_name" class="form-control" list="album-list-<?= $row['id'] ?>" value="<?= e($row['album_name'] ?? 'Umum') ?>" required>
              <datalist id="album-list-<?= $row['id'] ?>">
                <option value="Umum">
                <?php foreach ($albums as $album): ?>
                <option value="<?= e($album) ?>">
                <?php endforeach; ?>
              </datalist>
            </div>
          </div>
          
          <div class="d-flex gap-1">
            <button class="btn btn-sm btn-outline-primary flex-fill" type="submit">Simpan</button>
            <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/gallery-images/<?= (int)$row['id'] ?>/delete" onclick="return confirm('Hapus gambar ini?');">Hapus</a>
          </div>
        </form>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
