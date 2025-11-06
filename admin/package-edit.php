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
$err = null; $ok = null;

if ($editing) {
    $stmt = db()->prepare("SELECT title, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link FROM packages WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($title, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link);
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

    if ($title === '' || $price_value === '') { $err = 'Judul dan nilai harga wajib diisi'; }

    if (!$err) {
        if ($editing) {
            $stmt = db()->prepare("UPDATE packages SET title=?, price_label=?, price_value=?, price_unit=?, icon_class=?, features=?, featured=?, button_text=?, button_link=? WHERE id=?");
            $stmt->bind_param('ssssssissi', $title, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $id);
            $stmt->execute();
            $ok = 'Paket diperbarui';
        } else {
            $stmt = db()->prepare("INSERT INTO packages(title, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link) VALUES(?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('ssssssiss', $title, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link);
            $stmt->execute();
            header('Location: ' . $base . '/admin/packages'); exit;
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Paket</h3>
    <a href="<?= e($base) ?>/admin/packages" class="btn btn-secondary">Kembali</a>
  </div>
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

  <div class="card"><div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Judul Paket</label>
        <input name="title" class="form-control" value="<?= e($title) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Label Harga</label>
        <input name="price_label" class="form-control" value="<?= e($price_label) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Nilai Harga</label>
        <input name="price_value" class="form-control" placeholder="Rp 24 Juta / USD 11.500" value="<?= e($price_value) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Unit Harga</label>
        <input name="price_unit" class="form-control" value="<?= e($price_unit) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Icon (Font Awesome)</label>
        <input name="icon_class" class="form-control" placeholder="fas fa-moon" value="<?= e($icon_class) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Fitur (satu per baris)</label>
        <textarea name="features" class="form-control" rows="6" placeholder="fas fa-check|Direct Flight\nfas fa-check|Hotel Bintang 4-5\n..."><?= e($features) ?></textarea>
        <div class="form-text">Format baris opsional: icon_class|teks. Contoh: <code>fas fa-check|Direct Flight</code></div>
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="featured" id="featured" <?= $featured ? 'checked' : '' ?>>
          <label class="form-check-label" for="featured">Tandai Populer</label>
        </div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Teks Tombol</label>
        <input name="button_text" class="form-control" value="<?= e($button_text) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Link Tombol</label>
        <input name="button_link" class="form-control" value="<?= e($button_link) ?>">
      </div>
      <div class="col-12">
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
