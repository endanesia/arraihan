<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$title = '';
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
    $stmt = db()->prepare("SELECT title, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double FROM packages WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($title, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $hotel, $pesawat, $price_quad, $price_triple, $price_double);
    if (!$stmt->fetch()) { $editing = false; }
    $stmt->close();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $price_label = trim($_POST['price_label'] ?? 'Mulai dari');
    $price_value = trim($_POST['price_value'] ?? '');
    $price_unit = trim($_POST['price_unit'] ?? '/orang');
    $icon_class = trim($_POST['icon_class'] ?? 'fas fa-moon');
    $features = trim($_POST['features'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $button_text = trim($_POST['button_text'] ?? 'Lihat Detail');
    $button_link = trim($_POST['button_link'] ?? '#kontak');
    $hotel = trim($_POST['hotel'] ?? '');
    $pesawat = trim($_POST['pesawat'] ?? '');
    $price_quad = trim($_POST['price_quad'] ?? '');
    $price_triple = trim($_POST['price_triple'] ?? '');
    $price_double = trim($_POST['price_double'] ?? '');

    if ($title === '' || $price_value === '') { $err = 'Judul dan nilai harga wajib diisi'; }

    if (!$err) {
        if ($editing) {
            $stmt = db()->prepare("UPDATE packages SET title=?, price_label=?, price_value=?, price_unit=?, icon_class=?, features=?, featured=?, button_text=?, button_link=?, hotel=?, pesawat=?, price_quad=?, price_triple=?, price_double=? WHERE id=?");
            $stmt->bind_param('ssssssisssssssi', $title, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $hotel, $pesawat, $price_quad, $price_triple, $price_double, $id);
            $stmt->execute();
            $ok = 'Paket diperbarui';
        } else {
            $stmt = db()->prepare("INSERT INTO packages(title, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('ssssssisssssss', $title, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $hotel, $pesawat, $price_quad, $price_triple, $price_double);
            $stmt->execute();
            header('Location: ' . $base . '/admin/packages'); exit;
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
</style>

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Paket</h3>
    <a href="<?= e($base) ?>/admin/packages" class="btn btn-secondary">Kembali</a>
  </div>
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

  <div class="card"><div class="card-body">
    <form method="post" class="row g-3">
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
        <label class="form-label">Fitur Paket</label>
        <textarea name="features" class="form-control" rows="6" placeholder="fas fa-check|Direct Flight&#10;fas fa-check|Hotel Bintang 4-5&#10;fas fa-check|Makan 3x Sehari&#10;fas fa-plane|Penerbangan Langsung"><?= e($features) ?></textarea>
        <div class="form-text">
          <strong>Format:</strong> Satu fitur per baris. Opsional dengan icon: <code>icon_class|teks fitur</code><br>
          <strong>Contoh:</strong> <code>fas fa-check|Direct Flight</code> atau <code>Hotel Bintang 5</code>
        </div>
      </div>

      <!-- Call to Action -->
      <div class="col-12 mt-4">
        <h5 class="text-primary mb-3"><i class="fas fa-mouse-pointer me-2"></i>Call to Action</h5>
      </div>
      
      <div class="col-md-6">
        <label class="form-label">Teks Tombol</label>
        <input name="button_text" class="form-control" value="<?= e($button_text) ?>" placeholder="Lihat Detail">
      </div>
      <div class="col-md-6">
        <label class="form-label">Link Tombol</label>
        <input name="button_link" class="form-control" value="<?= e($button_link) ?>" placeholder="#kontak">
        <div class="form-text">Link tujuan tombol (misal: #kontak, /paket-detail, dll)</div>
      </div>

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
<?php include __DIR__ . '/footer.php'; ?>
