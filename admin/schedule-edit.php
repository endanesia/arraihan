<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$title = '';
$departure_date = '';
$description = '';
$jml_hari = '';
$id_packages = '';
$err = null; $ok = null;

// Get packages for dropdown
$packages = [];
if ($res = db()->query("SELECT id, title FROM packages ORDER BY title ASC")) {
    while ($r = $res->fetch_assoc()) { $packages[] = $r; }
}

if ($editing) {
    // Check if new columns exist
    $has_new_fields = false;
    if ($res = db()->query("SHOW COLUMNS FROM schedules LIKE 'jml_hari'")) {
        $has_new_fields = $res->num_rows > 0;
    }
    
    if ($has_new_fields) {
        $stmt = db()->prepare("SELECT title, departure_date, description, jml_hari, id_packages FROM schedules WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($title, $departure_date, $description, $jml_hari, $id_packages);
        if (!$stmt->fetch()) { $editing = false; }
        $stmt->close();
    } else {
        // Fallback for old schema
        $stmt = db()->prepare("SELECT title, departure_date, description FROM schedules WHERE id=? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->bind_result($title, $departure_date, $description);
        if (!$stmt->fetch()) { $editing = false; }
        $stmt->close();
    }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $departure_date = trim($_POST['departure_date'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $jml_hari = trim($_POST['jml_hari'] ?? '');
    $id_packages = isset($_POST['id_packages']) && $_POST['id_packages'] !== '' ? (int)$_POST['id_packages'] : null;
    
    if ($title === '') { $err = 'Judul wajib diisi'; }
    if ($jml_hari !== '' && !is_numeric($jml_hari)) { $err = 'Jumlah hari harus berupa angka'; }
    
    if (!$err) {
        // Check if new columns exist
        $has_new_fields = false;
        if ($res = db()->query("SHOW COLUMNS FROM schedules LIKE 'jml_hari'")) {
            $has_new_fields = $res->num_rows > 0;
        }
        
        if ($has_new_fields) {
            if ($editing) {
                // UPDATE with new fields: title, departure_date, description, jml_hari, id_packages, id
                $stmt = db()->prepare("UPDATE schedules SET title=?, departure_date=?, description=?, jml_hari=?, id_packages=? WHERE id=?");
                $stmt->bind_param('sssiii', $title, $departure_date, $description, $jml_hari, $id_packages, $id);
                $stmt->execute();
                $ok = 'Jadwal diperbarui';
            } else {
                // INSERT with new fields: title, departure_date, description, jml_hari, id_packages
                $stmt = db()->prepare("INSERT INTO schedules(title, departure_date, description, jml_hari, id_packages) VALUES(?,?,?,?,?)");
                $stmt->bind_param('sssii', $title, $departure_date, $description, $jml_hari, $id_packages);
                $stmt->execute();
                header('Location: ' . $base . '/admin/schedules'); exit;
            }
        } else {
            // Fallback for old schema
            if ($editing) {
                $stmt = db()->prepare("UPDATE schedules SET title=?, departure_date=?, description=? WHERE id=?");
                $stmt->bind_param('sssi', $title, $departure_date, $description, $id);
                $stmt->execute();
                $ok = 'Jadwal diperbarui (new fields not available - please run database migration)';
            } else {
                $stmt = db()->prepare("INSERT INTO schedules(title, departure_date, description) VALUES(?,?,?)");
                $stmt->bind_param('sss', $title, $departure_date, $description);
                $stmt->execute();
                header('Location: ' . $base . '/admin/schedules'); exit;
            }
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Jadwal</h3>
    <a href="<?= e($base) ?>/admin/schedules" class="btn btn-secondary">Kembali</a>
  </div>
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

  <div class="card"><div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Judul</label>
        <input name="title" class="form-control" value="<?= e($title) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Tanggal Keberangkatan</label>
        <input type="date" name="departure_date" class="form-control" value="<?= e($departure_date) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Jumlah Hari</label>
        <input type="number" name="jml_hari" class="form-control" value="<?= e($jml_hari) ?>" min="1" placeholder="contoh: 9">
      </div>
      
      <div class="col-md-6">
        <label class="form-label">Paket Wisata</label>
        <select name="id_packages" class="form-select">
          <option value="">-- Pilih Paket --</option>
          <?php foreach ($packages as $pkg): ?>
            <option value="<?= (int)$pkg['id'] ?>"<?= $id_packages == $pkg['id'] ? ' selected' : '' ?>>
              <?= e($pkg['title']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-text">Pilih paket wisata yang terkait dengan jadwal ini</div>
      </div>
      
      <div class="col-12">
        <label class="form-label">Deskripsi</label>
        <textarea name="description" class="form-control" rows="6" placeholder="Detail itinerary, maskapai, dll."><?= e($description) ?></textarea>
      </div>
      <div class="col-12">
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
