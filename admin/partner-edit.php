<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$name = '';
$icon_class = '';
$logo_url = '';
$img_url = '';
$err = null; $ok = null;

if ($editing) {
    $stmt = db()->prepare("SELECT name, icon_class, logo_url, img_url FROM partners WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($name, $icon_class, $logo_url, $img_url);
    if (!$stmt->fetch()) { $editing = false; }
    $stmt->close();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $icon_class = trim($_POST['icon_class'] ?? '');
    $new_logo_url = $_POST['existing_logo'] ?? '';
    $new_img_url = $_POST['existing_img'] ?? '';
    
    if ($name === '') { $err = 'Nama wajib diisi'; }
    
    // Handle logo upload
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/partners/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $filename = 'partner_' . time() . '_' . uniqid() . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $filepath)) {
            // Delete old logo if exists
            if (!empty($new_logo_url) && file_exists(__DIR__ . '/../' . $new_logo_url)) {
                unlink(__DIR__ . '/../' . $new_logo_url);
            }
            $new_logo_url = 'images/partners/' . $filename;
        }
    }
    
    // Handle certificate image upload
    if (isset($_FILES['certificate']) && $_FILES['certificate']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../images/partners/certificates/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $ext = pathinfo($_FILES['certificate']['name'], PATHINFO_EXTENSION);
        $filename = 'cert_' . time() . '_' . uniqid() . '.' . $ext;
        $filepath = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['certificate']['tmp_name'], $filepath)) {
            // Delete old certificate if exists
            if (!empty($new_img_url) && file_exists(__DIR__ . '/../' . $new_img_url)) {
                unlink(__DIR__ . '/../' . $new_img_url);
            }
            $new_img_url = 'images/partners/certificates/' . $filename;
        }
    }
    
    if (!$err) {
        if ($editing) {
            $stmt = db()->prepare("UPDATE partners SET name=?, icon_class=?, logo_url=?, img_url=? WHERE id=?");
            $stmt->bind_param('ssssi', $name, $icon_class, $new_logo_url, $new_img_url, $id);
            $stmt->execute();
            $logo_url = $new_logo_url; // Update display
            $img_url = $new_img_url; // Update display
            $ok = 'Partner diperbarui';
        } else {
            $stmt = db()->prepare("INSERT INTO partners(name, icon_class, logo_url, img_url) VALUES(?, ?, ?, ?)");
            $stmt->bind_param('ssss', $name, $icon_class, $new_logo_url, $new_img_url);
            $stmt->execute();
            header('Location: ' . $base . '/admin/partners'); exit;
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Partner</h3>
    <a href="<?= e($base) ?>/admin/partners" class="btn btn-secondary">Kembali</a>
  </div>
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

  <div class="card"><div class="card-body">
    <form method="post" enctype="multipart/form-data" class="row g-3">
      <input type="hidden" name="existing_logo" value="<?= e($logo_url) ?>">
      <input type="hidden" name="existing_img" value="<?= e($img_url) ?>">
      <div class="col-12 col-md-6">
        <label class="form-label">Nama Partner <span class="text-danger">*</span></label>
        <input name="name" class="form-control" value="<?= e($name) ?>" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Logo Partner</label>
        <input type="file" name="logo" class="form-control" accept="image/*">
        <div class="form-text">Upload logo partner (PNG/JPG, max 2MB). Ukuran disarankan: 200x100px</div>
        <?php if (!empty($logo_url)): ?>
        <div class="mt-2">
          <img src="<?= e($base . '/' . $logo_url) ?>" alt="Current logo" class="img-thumbnail" style="max-height: 80px;">
          <p class="text-muted small mb-0">Logo saat ini</p>
        </div>
        <?php endif; ?>
      </div>
      <div class="col-12">
        <label class="form-label">Sertifikat/Penghargaan (Opsional)</label>
        <input type="file" name="certificate" class="form-control" accept="image/*">
        <div class="form-text">Upload gambar sertifikat atau penghargaan dari partner ini (PNG/JPG, max 5MB)</div>
        <?php if (!empty($img_url)): ?>
        <div class="mt-2">
          <img src="<?= e($base . '/' . $img_url) ?>" alt="Current certificate" class="img-thumbnail" style="max-height: 150px;">
          <p class="text-muted small mb-0">Sertifikat saat ini</p>
        </div>
        <?php endif; ?>
      </div>
      <div class="col-12">
        <label class="form-label">Font Awesome Icon (Opsional)</label>
        <input name="icon_class" class="form-control" placeholder="fas fa-building" value="<?= e($icon_class) ?>">
        <div class="form-text">Icon akan digunakan jika logo tidak diupload. Contoh: fas fa-building, fas fa-industry</div>
      </div>
      <div class="col-12">
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
