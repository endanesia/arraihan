<?php require_once __DIR__ . '/header.php'; ?>
<?php $base = rtrim($config['app']['base_url'] ?? '', '/'); ?>
<?php
$msg=''; $err='';
if ($_SERVER['REQUEST_METHOD']==='POST'){
  $action = $_POST['action'] ?? 'upload';
  if ($action === 'update') {
    $id = (int)($_POST['id'] ?? 0);
    $title = trim($_POST['title'] ?? '');
    if ($id>0) {
      $stmt = db()->prepare('UPDATE gallery_images SET title=? WHERE id=?');
      $stmt->bind_param('si', $title, $id);
      $stmt->execute();
      $msg = 'Judul gambar diperbarui.';
    }
  } else {
    $title = trim($_POST['title'] ?? '');
    if (isset($_FILES['image']) && $_FILES['image']['error']===UPLOAD_ERR_OK){
      $f = $_FILES['image'];
      $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp'];
      if (!isset($allowed[$f['type']])) {
        $err = 'Format gambar tidak didukung.';
      } else {
        if (!is_dir($config['app']['uploads_dir'])) @mkdir($config['app']['uploads_dir'], 0777, true);
        $ext = $allowed[$f['type']];
        $filename = 'img_'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).$ext;
        $dest = rtrim($config['app']['uploads_dir'],'/\\').DIRECTORY_SEPARATOR.$filename;
        if (move_uploaded_file($f['tmp_name'], $dest)){
          $url = rtrim($config['app']['uploads_url'],'/').'/'.$filename;
          $stmt = db()->prepare('INSERT INTO gallery_images(title,file_path) VALUES(?,?)');
          $stmt->bind_param('ss', $title, $url);
          $stmt->execute();
          $msg = 'Gambar berhasil diupload.';
        } else {
          $err = 'Gagal upload file.';
        }
      }
    } else {
      $err = 'Pilih file gambar terlebih dahulu.';
    }
  }
}

$res = db()->query('SELECT * FROM gallery_images ORDER BY id DESC');
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Galeri Gambar</h3>
</div>

<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card mb-4">
  <div class="card-body">
    <form method="post" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Judul (opsional)</label>
        <input type="text" name="title" class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Pilih Gambar</label>
        <input type="file" name="image" accept="image/*" class="form-control" required>
      </div>
      <div class="col-md-2 d-grid align-items-end">
        <button class="btn btn-success" type="submit">Upload</button>
        <input type="hidden" name="action" value="upload">
      </div>
    </form>
  </div>
  </div>

<div class="row g-3">
  <?php while($row = $res->fetch_assoc()): ?>
  <div class="col-sm-6 col-md-4 col-lg-3">
    <div class="card h-100">
      <img src="<?= e($row['file_path']) ?>" class="card-img-top" alt="<?= e($row['title']) ?>">
      <div class="card-body">
        <form method="post" class="d-flex gap-2 align-items-center">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
          <input type="text" name="title" class="form-control form-control-sm" value="<?= e($row['title']) ?>" placeholder="Judul gambar">
          <button class="btn btn-sm btn-outline-primary" type="submit">Simpan</button>
        </form>
  <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/gallery-images/<?= (int)$row['id'] ?>/delete" onclick="return confirm('Hapus gambar ini?');">Hapus</a>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
