<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$rows = [];
if ($res = db()->query("SELECT s.*, p.title as package_title FROM schedules s LEFT JOIN packages p ON s.id_packages = p.id ORDER BY (s.departure_date IS NULL), s.departure_date ASC, s.id DESC")) {
    while ($r = $res->fetch_assoc()) { $rows[] = $r; }
}
include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Jadwal Keberangkatan</h3>
    <a href="<?= e($base) ?>/admin/schedule-edit" class="btn btn-primary">Tambah Jadwal</a>
  </div>

  <div class="card"><div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead><tr><th>ID</th><th>Tanggal</th><th>Judul</th><th>Paket</th><th>Jumlah Hari</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td>#<?= (int)$r['id'] ?></td>
            <td><?= e($r['departure_date'] ?: '-') ?></td>
            <td><?= e($r['title']) ?></td>
            <td><?= e($r['package_title'] ?: '-') ?></td>
            <td><?= e($r['jml_hari'] ? $r['jml_hari'] . ' hari' : '-') ?></td>
            <td>
              <a class="btn btn-sm btn-outline-primary" href="<?= e($base) ?>/admin/schedule-edit?id=<?= (int)$r['id'] ?>">Edit</a>
              <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/schedules/<?= (int)$r['id'] ?>/delete" onclick="return confirm('Hapus jadwal ini?')">Hapus</a>
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
