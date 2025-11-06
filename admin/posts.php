<?php require_once __DIR__ . '/header.php'; ?>
<?php
// Fetch posts
$res = db()->query("SELECT id, type, title, slug, published, created_at FROM posts ORDER BY id DESC");
?>
<?php $base = rtrim($config['app']['base_url'] ?? '', '/'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Posts</h3>
  <a class="btn btn-success" href="<?= e($base) ?>/admin/posts/new">Tambah Post</a>
  </div>

<div class="table-responsive">
  <table class="table table-striped align-middle">
    <thead>
      <tr>
        <th>ID</th>
        <th>Judul</th>
        <th>Jenis</th>
        <th>Slug</th>
        <th>Publish</th>
        <th>Dibuat</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $res->fetch_assoc()): ?>
      <tr>
        <td><?= (int)$row['id'] ?></td>
        <td><?= e($row['title']) ?></td>
        <td><span class="badge bg-primary"><?= e($row['type']) ?></span></td>
        <td><?= e($row['slug']) ?></td>
        <td><?= $row['published'] ? 'Ya' : 'Tidak' ?></td>
        <td><?= e($row['created_at']) ?></td>
        <td>
          <a class="btn btn-sm btn-outline-primary" href="<?= e($base) ?>/admin/posts/<?= (int)$row['id'] ?>/edit">Edit</a>
          <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/posts/<?= (int)$row['id'] ?>/delete" onclick="return confirm('Hapus post ini?');">Hapus</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
