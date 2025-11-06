<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$name = '';
$icon_class = '';
$err = null; $ok = null;

if ($editing) {
    $stmt = db()->prepare("SELECT name, icon_class FROM partners WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($name, $icon_class);
    if (!$stmt->fetch()) { $editing = false; }
    $stmt->close();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $icon_class = trim($_POST['icon_class'] ?? '');
    if ($name === '') { $err = 'Nama wajib diisi'; }
    if (!$err) {
        if ($editing) {
            $stmt = db()->prepare("UPDATE partners SET name=?, icon_class=? WHERE id=?");
            $stmt->bind_param('ssi', $name, $icon_class, $id);
            $stmt->execute();
            $ok = 'Partner diperbarui';
        } else {
            $stmt = db()->prepare("INSERT INTO partners(name, icon_class) VALUES(?, ?)");
            $stmt->bind_param('ss', $name, $icon_class);
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
    <form method="post" class="row g-3">
      <div class="col-12 col-md-6">
        <label class="form-label">Nama</label>
        <input name="name" class="form-control" value="<?= e($name) ?>" required>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Font Awesome Icon</label>
        <input name="icon_class" class="form-control" placeholder="fas fa-building" value="<?= e($icon_class) ?>">
        <div class="form-text">Contoh: fas fa-building, fas fa-industry, fas fa-store</div>
      </div>
      <div class="col-12">
        <button class="btn btn-primary" type="submit">Simpan</button>
      </div>
    </form>
  </div></div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
