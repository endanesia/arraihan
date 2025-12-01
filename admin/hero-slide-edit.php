<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;

// Default values
$title = '';
$subtitle = '';
$button_text = 'Lihat Program Umroh';
$button_link = '#paket';
$stat1_text = '';
$stat1_desc = '';
$stat2_text = '';
$stat2_desc = '';
$background_image = '';
$is_active = 1;
$sort_order = 0;
$err = null;
$ok = null;

if ($editing) {
    $stmt = db()->prepare("SELECT * FROM hero_slides WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($slide = $result->fetch_assoc()) {
        $title = $slide['title'];
        $subtitle = $slide['subtitle'];
        $button_text = $slide['button_text'];
        $button_link = $slide['button_link'];
        $stat1_text = $slide['stat1_text'];
        $stat1_desc = $slide['stat1_desc'];
        $stat2_text = $slide['stat2_text'];
        $stat2_desc = $slide['stat2_desc'];
        $background_image = $slide['background_image'];
        $is_active = $slide['is_active'];
        $sort_order = $slide['sort_order'];
    } else {
        $editing = false;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $button_text = trim($_POST['button_text'] ?? 'Lihat Program Umroh');
    $button_link = trim($_POST['button_link'] ?? '#paket');
    $stat1_text = trim($_POST['stat1_text'] ?? '');
    $stat1_desc = trim($_POST['stat1_desc'] ?? '');
    $stat2_text = trim($_POST['stat2_text'] ?? '');
    $stat2_desc = trim($_POST['stat2_desc'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)($_POST['sort_order'] ?? 0);

    if ($title === '' || $subtitle === '') {
        $err = 'Judul dan subtitle wajib diisi';
    }

    // Handle image upload
    $uploaded_image = '';
    if (isset($_FILES['background_image']) && $_FILES['background_image']['error'] === UPLOAD_ERR_OK) {
        $f = $_FILES['background_image'];
        
        // Validate file type
        $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp','image/jpg'=>'.jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detected_type = finfo_file($finfo, $f['tmp_name']);
        finfo_close($finfo);
        
        if (!isset($allowed[$detected_type])) {
            $err = 'Format gambar tidak didukung. Yang didukung: JPEG, PNG, WebP.';
        } else {
            // Create upload directory
            $upload_dir = __DIR__ . '/../images/hero';
            if (!is_dir($upload_dir)) {
                if (!@mkdir($upload_dir, 0755, true)) {
                    $err = 'Gagal membuat direktori upload';
                }
            }
            
            if (!$err) {
                $ext = $allowed[$detected_type];
                $filename = 'hero_slide_'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).$ext;
                $dest = $upload_dir.'/'.$filename;
                
                if (move_uploaded_file($f['tmp_name'], $dest)) {
                    $uploaded_image = '/images/hero/'.$filename;
                    
                    // Delete old image if editing
                    if ($editing && $background_image && $background_image !== $uploaded_image) {
                        $old_file = __DIR__ . '/..' . $background_image;
                        if (file_exists($old_file)) {
                            @unlink($old_file);
                        }
                    }
                } else {
                    $err = 'Gagal mengupload gambar';
                }
            }
        }
    }

    if (!$err) {
        if ($editing) {
            $update_image = $uploaded_image ? ", background_image='$uploaded_image'" : '';
            $stmt = db()->prepare("UPDATE hero_slides SET title=?, subtitle=?, button_text=?, button_link=?, stat1_text=?, stat1_desc=?, stat2_text=?, stat2_desc=?, is_active=?, sort_order=?" . ($uploaded_image ? ", background_image=?" : "") . " WHERE id=?");
            
            if ($uploaded_image) {
                $stmt->bind_param('sssssssssissi', $title, $subtitle, $button_text, $button_link, $stat1_text, $stat1_desc, $stat2_text, $stat2_desc, $is_active, $sort_order, $uploaded_image, $id);
            } else {
                $stmt->bind_param('ssssssssisi', $title, $subtitle, $button_text, $button_link, $stat1_text, $stat1_desc, $stat2_text, $stat2_desc, $is_active, $sort_order, $id);
            }
            
            $stmt->execute();
            $ok = 'Slide berhasil diperbarui';
        } else {
            $stmt = db()->prepare("INSERT INTO hero_slides(title, subtitle, button_text, button_link, stat1_text, stat1_desc, stat2_text, stat2_desc, background_image, is_active, sort_order) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('sssssssssii', $title, $subtitle, $button_text, $button_link, $stat1_text, $stat1_desc, $stat2_text, $stat2_desc, $uploaded_image, $is_active, $sort_order);
            $stmt->execute();
            header('Location: ' . $base . '/admin/hero-slides');
            exit;
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

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.image-preview {
    max-width: 300px;
    max-height: 200px;
    border-radius: 8px;
    border: 1px solid #ddd;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Hero Slide</h3>
    <a href="<?= e($base) ?>/admin/hero-slides" class="btn btn-secondary">Kembali</a>
  </div>
  
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

  <div class="card">
    <div class="card-body">
      <form method="post" enctype="multipart/form-data" class="row g-3">
        <!-- Basic Information -->
        <div class="col-12">
          <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Slide</h5>
        </div>
        
        <div class="col-md-8">
          <label class="form-label">Judul Slide *</label>
          <input name="title" class="form-control" value="<?= e($title) ?>" required>
        </div>
        
        <div class="col-md-2">
          <label class="form-label">Urutan</label>
          <input type="number" name="sort_order" class="form-control" value="<?= (int)$sort_order ?>" min="0">
        </div>
        
        <div class="col-md-2 d-flex align-items-end">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $is_active ? 'checked' : '' ?>>
            <label class="form-check-label" for="is_active">Aktif</label>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">Subtitle *</label>
          <textarea name="subtitle" class="form-control" rows="4" required><?= e($subtitle) ?></textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Background Image</label>
          <?php if ($editing && $background_image): ?>
          <div class="mb-2">
            <img src="<?= e($base . $background_image) ?>" alt="Current Image" class="image-preview">
            <div class="form-text">Gambar saat ini</div>
          </div>
          <?php endif; ?>
          <input type="file" name="background_image" accept="image/*" class="form-control">
          <div class="form-text">
            Format yang didukung: JPEG, PNG, WebP. Ukuran optimal: 1920x1080px
            <?php if ($editing): ?>
            <br>Kosongkan jika tidak ingin mengubah gambar
            <?php endif; ?>
          </div>
        </div>

        <!-- Button Section -->
        <div class="col-12 mt-4">
          <h5 class="text-primary mb-3"><i class="fas fa-mouse-pointer me-2"></i>Tombol CTA</h5>
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Teks Tombol</label>
          <input name="button_text" class="form-control" value="<?= e($button_text) ?>">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Link Tombol</label>
          <input name="button_link" class="form-control" value="<?= e($button_link) ?>">
        </div>

        <!-- Statistics Section -->
        <div class="col-12 mt-4">
          <h5 class="text-primary mb-3"><i class="fas fa-chart-bar me-2"></i>Statistik</h5>
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Statistik 1 - Teks</label>
          <input name="stat1_text" class="form-control" value="<?= e($stat1_text) ?>" placeholder="24 Januari 2026">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Statistik 1 - Deskripsi</label>
          <input name="stat1_desc" class="form-control" value="<?= e($stat1_desc) ?>" placeholder="Jadwal Berangkat">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Statistik 2 - Teks</label>
          <input name="stat2_text" class="form-control" value="<?= e($stat2_text) ?>" placeholder="Program Pembiayaan">
        </div>
        
        <div class="col-md-6">
          <label class="form-label">Statistik 2 - Deskripsi</label>
          <input name="stat2_desc" class="form-control" value="<?= e($stat2_desc) ?>" placeholder="Dana Talangan Umrah">
        </div>

        <div class="col-12 mt-4">
          <button class="btn btn-primary btn-lg" type="submit">
            <i class="fas fa-save me-2"></i>Simpan Slide
          </button>
          <a href="<?= e($base) ?>/admin/hero-slides" class="btn btn-secondary btn-lg ms-2">
            <i class="fas fa-times me-2"></i>Batal
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>