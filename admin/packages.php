<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$rows = [];
if ($res = db()->query("SELECT * FROM packages ORDER BY featured DESC, id DESC")) {
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
}
include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Paket Perjalanan</h3>
    <a href="<?= e($base) ?>/admin/package-edit" class="btn btn-primary">Tambah Paket</a>
  </div>

  <div class="card"><div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Judul</th><th>Harga</th><th>Unit</th><th>Featured</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td>#<?= (int)$r['id'] ?></td>
            <td><?= e($r['title']) ?></td>
            <td><?= e($r['price_label'] . ' ' . $r['price_value']) ?></td>
            <td><?= e($r['price_unit']) ?></td>
            <td><?= $r['featured'] ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' ?></td>
            <td>
              <a class="btn btn-sm btn-outline-primary" href="<?= e($base) ?>/admin/package-edit?id=<?= (int)$r['id'] ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/packages/<?= (int)$r['id'] ?>/delete" onclick="return confirm('Hapus paket ini?')">Hapus</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="6" class="text-center text-muted">Belum ada data</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div></div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
