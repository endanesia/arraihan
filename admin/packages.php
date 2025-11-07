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
<style>
.table th {
    background-color: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
    font-size: 0.875rem;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

.table-responsive {
    border-radius: 0.375rem;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.description-preview {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.4;
    max-height: 2.8em;
}
</style>

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Paket Perjalanan</h3>
    <a href="<?= e($base) ?>/admin/package-edit" class="btn btn-primary">Tambah Paket</a>
  </div>

  <div class="card"><div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Judul</th>
            <th>Harga Utama</th>
            <th>Harga Detail</th>
            <th>Hotel</th>
            <th>Pesawat</th>
            <th>Featured</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td>#<?= (int)$r['id'] ?></td>
            <td>
              <div class="fw-bold"><?= e($r['title']) ?></div>
              <?php if ($r['description']): ?>
                <div class="text-muted small mt-1 description-preview" style="max-width: 200px;" title="<?= e(strip_tags($r['description'])) ?>">
                  <?= strip_tags($r['description']) ?>
                </div>
              <?php endif; ?>
              <?php if ($r['icon_class']): ?>
                <small class="text-muted">
                  <i class="<?= e($r['icon_class']) ?> me-1"></i><?= e($r['icon_class']) ?>
                </small>
              <?php endif; ?>
            </td>
            <td>
              <div class="fw-bold"><?= e($r['price_label'] . ' ' . $r['price_value']) ?></div>
              <small class="text-muted"><?= e($r['price_unit']) ?></small>
            </td>
            <td>
              <?php if ($r['price_quad']): ?>
                <div><small><strong>Quad:</strong> <?= e($r['price_quad']) ?></small></div>
              <?php endif; ?>
              <?php if ($r['price_triple']): ?>
                <div><small><strong>Triple:</strong> <?= e($r['price_triple']) ?></small></div>
              <?php endif; ?>
              <?php if ($r['price_double']): ?>
                <div><small><strong>Double:</strong> <?= e($r['price_double']) ?></small></div>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($r['hotel']): ?>
                <small class="text-truncate d-inline-block" style="max-width: 150px;" title="<?= e($r['hotel']) ?>">
                  <?= e($r['hotel']) ?>
                </small>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($r['pesawat']): ?>
                <small class="text-truncate d-inline-block" style="max-width: 120px;" title="<?= e($r['pesawat']) ?>">
                  <?= e($r['pesawat']) ?>
                </small>
              <?php else: ?>
                <span class="text-muted">-</span>
              <?php endif; ?>
            </td>
            <td><?= $r['featured'] ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' ?></td>
            <td>
              <div class="btn-group" role="group">
                <a class="btn btn-sm btn-outline-primary" href="<?= e($base) ?>/admin/package-edit?id=<?= (int)$r['id'] ?>" title="Edit">
                  <i class="fas fa-edit"></i>
                </a>
                <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/package-delete?id=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus paket ini?')" title="Hapus">
                  <i class="fas fa-trash"></i>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($rows)): ?>
          <tr><td colspan="8" class="text-center text-muted py-4">
            <i class="fas fa-suitcase fa-2x mb-2 d-block"></i>
            Belum ada paket perjalanan
          </td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div></div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
