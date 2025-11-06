<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

// Handle inline create
$err = null; $ok = null;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['create'])) {
    $name = trim($_POST['name'] ?? '');
    $icon = trim($_POST['icon_class'] ?? '');
    if ($name === '') {
        $err = 'Nama partner wajib diisi';
    } else {
        $stmt = db()->prepare("INSERT INTO partners(name, icon_class) VALUES(?, ?)");
        $stmt->bind_param('ss', $name, $icon);
        $stmt->execute();
        $ok = 'Partner ditambahkan';
    }
}

// Fetch
$rows = [];
if ($res = db()->query("SELECT * FROM partners ORDER BY id DESC")) {
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
}

include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Partner</h3>
    <a href="<?= e($base) ?>/admin/partner-edit" class="btn btn-primary">Tambah Partner</a>
  </div>

  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

  <div class="card mb-3">
    <div class="card-body">
      <form method="post" class="row g-2">
        <input type="hidden" name="create" value="1">
        <div class="col-md-5"><input name="name" class="form-control" placeholder="Nama Partner"></div>
        <div class="col-md-5"><input name="icon_class" class="form-control" placeholder="Font Awesome icon (mis: fas fa-building)"></div>
        <div class="col-md-2"><button class="btn btn-success w-100">Tambah Cepat</button></div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead><tr><th>ID</th><th>Nama</th><th>Icon</th><th>Aksi</th></tr></thead>
          <tbody>
            <?php foreach ($rows as $r): ?>
            <tr>
              <td>#<?= (int)$r['id'] ?></td>
              <td><?= e($r['name']) ?></td>
              <td><i class="<?= e($r['icon_class'] ?: 'fas fa-building') ?>"></i> <code><?= e($r['icon_class'] ?: 'fas fa-building') ?></code></td>
              <td>
                <a class="btn btn-sm btn-outline-primary" href="<?= e($base) ?>/admin/partner-edit?id=<?= (int)$r['id'] ?>">Edit</a>
                <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/partners/<?= (int)$r['id'] ?>/delete" onclick="return confirm('Hapus partner ini?')">Hapus</a>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?>
            <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
