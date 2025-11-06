<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$title = '';
$departure_date = '';
$description = '';
$err = null; $ok = null;

if ($editing) {
    $stmt = db()->prepare("SELECT title, departure_date, description FROM schedules WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($title, $departure_date, $description);
    if (!$stmt->fetch()) { $editing = false; }
    $stmt->close();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $departure_date = trim($_POST['departure_date'] ?? '');
    $description = trim($_POST['description'] ?? '');
    if ($title === '') { $err = 'Judul wajib diisi'; }
    if (!$err) {
        if ($editing) {
            $stmt = db()->prepare("UPDATE schedules SET title=?, departure_date=?, description=? WHERE id=?");
            $stmt->bind_param('sssi', $title, $departure_date, $description, $id);
            $stmt->execute();
            $ok = 'Jadwal diperbarui';
        } else {
            $stmt = db()->prepare("INSERT INTO schedules(title, departure_date, description) VALUES(?,?,?)");
            $stmt->bind_param('sss', $title, $departure_date, $description);
            $stmt->execute();
            header('Location: ' . $base . '/admin/schedules'); exit;
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
