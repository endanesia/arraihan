<?php require_once __DIR__ . '/header.php'; ?>
<?php $base = rtrim($config['app']['base_url'] ?? '', '/'); ?>
<?php
$msg=''; $err='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $action = $_POST['action'] ?? 'create';
  if ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    if ($id>0) {
      $stmt = db()->prepare('UPDATE gallery_videos SET title=? WHERE id=?');
      $stmt->bind_param('si', $title, $id);
      $stmt->execute();
      $msg = 'Judul video diperbarui.';
    }
  } else {
    $title = trim($_POST['title'] ?? '');
    $url = trim($_POST['youtube'] ?? '');
    $vid = youtube_id_from_url($url);
    if (!$vid) { $err = 'URL/ID YouTube tidak valid.'; }
    else {
      $stmt = db()->prepare('INSERT INTO gallery_videos(title,youtube_id) VALUES(?,?)');
      $stmt->bind_param('ss', $title, $vid);
      $stmt->execute();
      $msg = 'Video berhasil ditambahkan.';
    }
  }
}
$res = db()->query('SELECT * FROM gallery_videos ORDER BY id DESC');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Galeri Video</h3>
</div>

<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card mb-4">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-5">
        <label class="form-label">Judul (opsional)</label>
        <input type="text" name="title" class="form-control">
      </div>
      <div class="col-md-5">
        <label class="form-label">URL/ID YouTube</label>
        <input type="text" name="youtube" class="form-control" placeholder="https://youtube.com/watch?v=..." required>
      </div>
      <div class="col-md-2 d-grid align-items-end">
        <button class="btn btn-success" type="submit">Tambah</button>
        <input type="hidden" name="action" value="create">
      </div>
    </form>
  </div>
</div>

<div class="row g-3">
  <?php while($row = $res->fetch_assoc()): $thumb = 'https://img.youtube.com/vi/'.e($row['youtube_id']).'/hqdefault.jpg'; ?>
  <div class="col-sm-6 col-md-4 col-lg-3">
    <div class="card h-100">
      <img src="<?= $thumb ?>" class="card-img-top" alt="<?= e($row['title']) ?>">
      <div class="card-body">
        <form method="post" class="d-flex gap-2 align-items-center mb-2">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
          <input type="text" name="title" class="form-control form-control-sm" value="<?= e($row['title']) ?>" placeholder="Judul video">
          <button class="btn btn-sm btn-outline-primary" type="submit">Simpan</button>
        </form>
  <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/gallery-videos/<?= (int)$row['id'] ?>/delete" onclick="return confirm('Hapus video ini?');">Hapus</a>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
