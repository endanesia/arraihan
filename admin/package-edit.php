<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

// Image resize function for poster
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
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$title = '';
$poster = '';
$price_label = 'Mulai dari';
$price_value = '';
$price_unit = '/orang';
$icon_class = 'fas fa-moon';
$features = '';
$featured = 0;
$button_text = 'Lihat Detail';
$button_link = '#kontak';
$hotel = '';
$pesawat = '';
$price_quad = '';
$price_triple = '';
$price_double = '';
$err = null; $ok = null;

if ($editing) {
    
    // Check if poster column exists
    $has_poster = false;
    try {
        $check_result = db()->query("SHOW COLUMNS FROM packages LIKE 'poster'");
        $has_poster = $check_result && $check_result->num_rows > 0;
    } catch (Exception $e) {
        $has_poster = false;
    }

    if ($has_poster) {
        $stmt = db()->prepare("SELECT title, poster, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double FROM packages WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($title, $poster, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $hotel, $pesawat, $price_quad, $price_triple, $price_double);
        if (!$stmt->fetch()) { $editing = false; }
        $stmt->close();
    } else {
        // Fallback when poster column doesn't exist
        $stmt = db()->prepare("SELECT title, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double FROM packages WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($title, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $hotel, $pesawat, $price_quad, $price_triple, $price_double);
        if (!$stmt->fetch()) { $editing = false; }
        $stmt->close();
        $poster = ''; // Default empty poster
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $price_label = trim($_POST['price_label'] ?? 'Mulai dari');
    $price_value = trim($_POST['price_value'] ?? '');
    $price_unit = trim($_POST['price_unit'] ?? '/orang');
    $icon_class = trim($_POST['icon_class'] ?? 'fas fa-moon');
    $features = trim($_POST['features'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    // Call-to-action fields removed for simplification
    $button_text = 'Lihat Detail';
    $button_link = '#kontak';
    $hotel = trim($_POST['hotel'] ?? '');
    $pesawat = trim($_POST['pesawat'] ?? '');
    $price_quad = trim($_POST['price_quad'] ?? '');
    $price_triple = trim($_POST['price_triple'] ?? '');
    $price_double = trim($_POST['price_double'] ?? '');

    if ($title === '' || $price_value === '') { $err = 'Judul dan nilai harga wajib diisi'; }
    
    // Handle poster upload
    $uploaded_poster = '';
    if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
        $f = $_FILES['poster'];
        
        // File size validation (2MB limit)
        if ($f['size'] > 2 * 1024 * 1024) {
            $err = 'Ukuran file poster terlalu besar. Maksimal 2MB. Ukuran file Anda: ' . number_format($f['size']/1024/1024, 2) . 'MB';
        } else {
            // Validate file type
            $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp','image/jpg'=>'.jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected_type = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);
            
            if (!isset($allowed[$detected_type])) {
                $err = 'Format poster tidak didukung. Terdeteksi: ' . $detected_type . '. Yang didukung: JPEG, PNG, WebP.';
            } else {
            // Create upload directory for posters
            $upload_dir = __DIR__ . '/../images/packages';
            if (!is_dir($upload_dir)) {
                if (!@mkdir($upload_dir, 0755, true)) {
                    $err = 'Gagal membuat direktori upload: ' . $upload_dir;
                }
            }
            
            if (!$err) {
                $ext = $allowed[$detected_type];
                $filename = 'poster_'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).$ext;
                $dest = rtrim($upload_dir,'/\\').DIRECTORY_SEPARATOR.$filename;
                
                // Configuration for resize
                $maxWidth = 1920;   // Maximum width in pixels
                $maxHeight = 1080;  // Maximum height in pixels  
                $quality = 85; // JPEG quality (1-100)
                
                // Get original image info for resize message
                $imageInfo = getimagesize($f['tmp_name']);
                $originalWidth = $imageInfo[0] ?? 0;
                $originalHeight = $imageInfo[1] ?? 0; 
                $originalSize = $f['size'];

                // Resize and save poster
                if (resizeImage($f['tmp_name'], $dest, $maxWidth, $maxHeight, $quality)) {
                    $uploaded_poster = $filename;
                    
                    // Check if file was actually resized
                    $newSize = filesize($dest);
                    $newImageInfo = getimagesize($dest);
                    $newWidth = $newImageInfo[0] ?? 0;
                    $newHeight = $newImageInfo[1] ?? 0;
                    
                    $resize_info = '';
                    if ($originalWidth > $maxWidth || $originalHeight > $maxHeight) {
                        $resize_info .= " diperkecil dari {$originalWidth}x{$originalHeight} ke {$newWidth}x{$newHeight}px";
                    }
                    if ($originalSize > (2 * 1024 * 1024)) { // 2MB
                        $resize_info .= ($resize_info ? ',' : '') . " dikompres dari " . number_format($originalSize/1024, 1) . "KB ke " . number_format($newSize/1024, 1) . "KB";
                    }
                    
                    $upload_success_msg = $resize_info ? "Poster berhasil diupload dan" . $resize_info : "Poster berhasil diupload";
                } else {
                    $err = 'Gagal mengubah ukuran poster. Pastikan PHP GD extension aktif.';
                }
            }
        }
    }
    
    // Handle upload errors if file was attempted but failed
    if (!$err && isset($_FILES['poster']) && $_FILES['poster']['error'] !== UPLOAD_ERR_NO_FILE && $_FILES['poster']['error'] !== UPLOAD_ERR_OK) {
        $upload_errors = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
            UPLOAD_ERR_PARTIAL => 'File tidak terupload sempurna',
            UPLOAD_ERR_NO_TMP_DIR => 'Direktori temporary tidak ditemukan',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension'
        ];
        $err = $upload_errors[$_FILES['poster']['error']] ?? 'Error upload tidak dikenal';
    }
    }

    if (!$err) {
        // Check if description column exists before trying to save
        $has_description = false;
        try {
            $check_result = db()->query("SHOW COLUMNS FROM packages LIKE 'description'");
            $has_description = $check_result && $check_result->num_rows > 0;
        } catch (Exception $e) {
            $has_description = false;
        }
        
        // Check if poster column exists
        $has_poster = false;
        try {
            $check_result = db()->query("SHOW COLUMNS FROM packages LIKE 'poster'");
            $has_poster = $check_result && $check_result->num_rows > 0;
        } catch (Exception $e) {
            $has_poster = false;
        }

        if ($has_poster) {
            if ($editing) {
                if ($uploaded_poster) {
                    // Update with new poster
                    $stmt = db()->prepare("UPDATE packages SET title='$title', poster='$uploaded_poster', price_label='$price_label', price_value='$price_value', price_unit='$price_unit', icon_class='$icon_class', features='$features', featured='$featured', button_text='$button_text', button_link='$button_link', hotel='$hotel', pesawat='$pesawat', price_quad='$price_quad', price_triple='$price_triple', price_double='$price_double' WHERE id=$id");
                } else {
                    // Update without changing poster
                    $stmt = db()->prepare("UPDATE packages SET title='$title', price_label='$price_label', price_value='$price_value', price_unit='$price_unit', icon_class='$icon_class', features='$features', featured='$featured', button_text='$button_text', button_link='$button_link', hotel='$hotel', pesawat='$pesawat', price_quad='$price_quad', price_triple='$price_triple', price_double='$price_double' WHERE id=$id");
                }
                $stmt->execute();
                $ok = 'Paket diperbarui' . (isset($upload_success_msg) ? '. ' . $upload_success_msg : '');
            } else {
                // INSERT with poster
                $stmt = db()->prepare("INSERT INTO packages(title, poster, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double) VALUES('$title', '$uploaded_poster', '$price_label', '$price_value', '$price_unit', '$icon_class', '$features', $featured, '$button_text', '$button_link', '$hotel', '$pesawat', '$price_quad', '$price_triple', '$price_double')");
                $stmt->execute();
                header('Location: ' . $base . '/admin/packages'); exit;
            }
        } else {
            // Fallback when poster column doesn't exist
            if ($editing) {
                // UPDATE without poster
                $stmt = db()->prepare("UPDATE packages SET title='$title', price_label='$price_label', price_value='$price_value', price_unit='$price_unit', icon_class='$icon_class', features='$features', featured='$featured', button_text='$button_text', button_link='$button_link', hotel='$hotel', pesawat='$pesawat', price_quad='$price_quad', price_triple='$price_triple', price_double='$price_double' WHERE id=$id");
                $stmt->execute();
                $ok = 'Paket diperbarui';
            } else {
                // INSERT without poster
                $stmt = db()->prepare("INSERT INTO packages(title, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double) VALUES('$title', '$price_label', '$price_value', '$price_unit', '$icon_class', '$features', $featured, '$button_text', '$button_link', '$hotel', '$pesawat', '$price_quad', '$price_triple', '$price_double')");
                $stmt->execute();
                header('Location: ' . $base . '/admin/packages'); exit;
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<style>
.form-section-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

.form-section-header i {
    color: #0d6efd;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-text {
    font-size: 0.875em;
    color: #6c757d;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

/* CKEditor Styling */
.ck-editor__editable {
    min-height: 200px !important;
}

.ck-editor {
    margin-bottom: 1rem;
}

.ck.ck-editor {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
}

.ck.ck-editor__main > .ck-editor__editable {
    border-radius: 0 0 10px 10px;
}

.ck.ck-toolbar {
    border-radius: 10px 10px 0 0;
    border-bottom: 1px solid #e0e0e0;
}

.ck-content {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 14px;
    line-height: 1.6;
}

.ck-content h1, .ck-content h2, .ck-content h3 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.ck-content p {
    margin-bottom: 0.75rem;
}

/* Poster Preview Styling */
.poster-preview {
    max-width: 200px;
    max-height: 150px;
    border-radius: 8px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.poster-preview:hover {
    transform: scale(1.05);
    cursor: pointer;
}

.file-input-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
}

.file-input-wrapper input[type=file] {
    position: absolute;
    left: -9999px;
}

.file-input-label {
    cursor: pointer;
    display: inline-block;
    padding: 8px 16px;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.file-input-label:hover {
    background: #e9ecef;
    border-color: #adb5bd;
}
</style>

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Paket</h3>
    <a href="<?= e($base) ?>/admin/packages" class="btn btn-secondary">Kembali</a>
  </div>
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

  <div class="card"><div class="card-body">
    <form method="post" enctype="multipart/form-data" class="row g-3">
      <!-- Basic Information -->
      <div class="col-12">
        <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Dasar</h5>
      </div>
      
      <div class="col-md-6">
        <label class="form-label">Judul Paket *</label>
        <input name="title" class="form-control" value="<?= e($title) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Icon (Font Awesome)</label>
        <input name="icon_class" class="form-control" placeholder="fas fa-moon" value="<?= e($icon_class) ?>">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="featured" id="featured" <?= $featured ? 'checked' : '' ?>>
          <label class="form-check-label" for="featured">Tandai Populer</label>
        </div>
      </div>

      <?php
      // Check if poster column exists
      $has_poster_field = false;
      try {
          $check_result = db()->query("SHOW COLUMNS FROM packages LIKE 'poster'");
          $has_poster_field = $check_result && $check_result->num_rows > 0;
      } catch (Exception $e) {
          $has_poster_field = false;
      }
      
      if ($has_poster_field): ?>
      <div class="col-12">
        <label class="form-label">Poster Paket</label>
        <?php if ($editing && $poster): ?>
        <div class="mb-2">
          <img src="<?= e($base . '/images/packages/' . $poster) ?>" alt="Current Poster" class="poster-preview">
          <div class="form-text">Poster saat ini</div>
        </div>
        <?php endif; ?>
        <input type="file" name="poster" accept="image/jpeg,image/png,image/webp" class="form-control">
        <div class="form-text">
          <div class="text-info mb-1">
            <i class="fas fa-info-circle me-1"></i>
            <strong>Format:</strong> JPEG, PNG, WebP • 
            <strong>Ukuran Max:</strong> 2MB • 
            <strong>Resolusi Optimal:</strong> 1920x1080px
          </div>
          <div class="text-warning">
            <i class="fas fa-exclamation-triangle me-1"></i>
            File akan otomatis dikompres dan diubah ukurannya jika melebihi batas
          </div>
          <?php if ($editing && $poster): ?>
          <div class="text-muted mt-1">Kosongkan jika tidak ingin mengubah poster saat ini</div>
          <?php endif; ?>
        </div>
      </div>
      <?php else: ?>
      <div class="col-12">
        <div class="alert alert-info">
          <i class="fas fa-info-circle me-2"></i>
          <strong>Database Migration Required:</strong> 
          Poster field is not available. Please run the database migration to enable poster uploads.
          <a href="<?= e($base) ?>/check-packages-table.php" target="_blank" class="btn btn-sm btn-outline-info ms-2">
            <i class="fas fa-database me-1"></i>Check & Migrate
          </a>
        </div>
      </div>
      <?php endif; ?>

      <!-- Pricing Section -->
      <div class="col-12 mt-4">
        <h5 class="text-primary mb-3"><i class="fas fa-tag me-2"></i>Informasi Harga</h5>
      </div>
      
      <div class="col-md-3">
        <label class="form-label">Label Harga</label>
        <input name="price_label" class="form-control" value="<?= e($price_label) ?>" placeholder="Mulai dari">
      </div>
      <div class="col-md-3">
        <label class="form-label">Nilai Harga Utama *</label>
        <input name="price_value" class="form-control" placeholder="Rp 24 Juta" value="<?= e($price_value) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Unit Harga</label>
        <input name="price_unit" class="form-control" value="<?= e($price_unit) ?>" placeholder="/orang">
      </div>
      
      <div class="col-md-3">
        <label class="form-label">Harga Quad</label>
        <input name="price_quad" class="form-control" value="<?= e($price_quad) ?>" placeholder="Rp 20 Juta">
        <div class="form-text">Harga untuk 4 orang</div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Harga Triple</label>
        <input name="price_triple" class="form-control" value="<?= e($price_triple) ?>" placeholder="Rp 22 Juta">
        <div class="form-text">Harga untuk 3 orang</div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Harga Double</label>
        <input name="price_double" class="form-control" value="<?= e($price_double) ?>" placeholder="Rp 24 Juta">
        <div class="form-text">Harga untuk 2 orang</div>
      </div>

      <!-- Package Details -->
      <div class="col-12 mt-4">
        <h5 class="text-primary mb-3"><i class="fas fa-suitcase me-2"></i>Detail Paket</h5>
      </div>
      
      <div class="col-md-6">
        <label class="form-label">Hotel</label>
        <input name="hotel" class="form-control" value="<?= e($hotel) ?>" placeholder="Hotel Bintang 4-5, Dekat Haram">
        <div class="form-text">Informasi hotel yang disediakan</div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Pesawat</label>
        <input name="pesawat" class="form-control" value="<?= e($pesawat) ?>" placeholder="Garuda Indonesia, Direct Flight">
        <div class="form-text">Informasi maskapai dan penerbangan</div>
      </div>

      <div class="col-12">
        <label class="form-label">Fitur & Fasilitas Paket</label>
        <textarea name="features" id="features" class="form-control ckeditor" rows="8"><?= e($features) ?></textarea>
        <div class="form-text">
          Daftar fitur dan fasilitas yang disediakan dalam paket ini. Gunakan format HTML untuk styling yang lebih baik.
        </div>
      </div>

      <!-- Call to Action fields removed for simplification -->

      <div class="col-12 mt-4">
        <button class="btn btn-primary btn-lg" type="submit">
          <i class="fas fa-save me-2"></i>Simpan Paket
        </button>
        <a href="<?= e($base) ?>/admin/packages" class="btn btn-secondary btn-lg ms-2">
          <i class="fas fa-times me-2"></i>Batal
        </a>
      </div>
    </form>
  </div></div>
</div>

<!-- CKEditor 5 -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for Features
    const featuresField = document.querySelector('#features');
    if (featuresField) {
        ClassicEditor
            .create(featuresField, {
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'blockQuote', 'insertTable', '|',
                    'link', '|',
                    'undo', 'redo'
                ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                        { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                    ]
                },
                table: {
                    contentToolbar: [
                        'tableColumn',
                        'tableRow',
                        'mergeTableCells'
                    ]
                }
            })
            .then(editor => {
                window.featuresEditor = editor;
            })
            .catch(error => {
                console.error('CKEditor Features initialization failed:', error);
            });
    }

    // Handle form submission to ensure CKEditor content is saved
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Update textareas with CKEditor content before submission
            if (window.featuresEditor && document.querySelector('#features')) {
                document.querySelector('#features').value = window.featuresEditor.getData();
            }
        });
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
