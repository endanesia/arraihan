<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
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
    
    // Debug upload
    if (!isset($_FILES['image'])) {
      $err = 'Tidak ada file yang dikirim. Pastikan form menggunakan enctype="multipart/form-data".';
    } elseif ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
      $upload_errors = [
        UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
        UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
        UPLOAD_ERR_PARTIAL => 'File hanya terupload sebagian',
        UPLOAD_ERR_NO_FILE => 'Tidak ada file yang dipilih',
        UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
        UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
        UPLOAD_ERR_EXTENSION => 'Upload dibatalkan oleh extension PHP'
      ];
      $err = $upload_errors[$_FILES['image']['error']] ?? 'Error upload tidak diketahui: ' . $_FILES['image']['error'];
    } else {
      $f = $_FILES['image'];
      
      // Validate file type
      $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp','image/jpg'=>'.jpg'];
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $detected_type = finfo_file($finfo, $f['tmp_name']);
      finfo_close($finfo);
      
      if (!isset($allowed[$detected_type])) {
        $err = 'Format gambar tidak didukung. Terdeteksi: ' . $detected_type . '. Yang didukung: JPEG, PNG, WebP.';
      } else {
        // Create directory if not exists
        $upload_dir = $config['app']['uploads_dir'];
        if (!is_dir($upload_dir)) {
          if (!@mkdir($upload_dir, 0755, true)) {
            $err = 'Gagal membuat direktori upload: ' . $upload_dir;
          }
        }
        
        if (!$err) {
          $ext = $allowed[$detected_type];
          $filename = 'img_'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).$ext;
          $dest = rtrim($upload_dir,'/\\').DIRECTORY_SEPARATOR.$filename;
          
          if (move_uploaded_file($f['tmp_name'], $dest)){
            $url = rtrim($config['app']['uploads_url'],'/').'/'.$filename;
            $stmt = db()->prepare('INSERT INTO gallery_images(title,file_path) VALUES(?,?)');
            $stmt->bind_param('ss', $title, $url);
            if ($stmt->execute()) {
              $msg = 'Gambar berhasil diupload: ' . $filename;
            } else {
              $err = 'Gambar terupload tapi gagal disimpan ke database.';
              @unlink($dest); // cleanup file
            }
          } else {
            $err = 'Gagal memindahkan file ke direktori upload. Periksa permission direktori.';
          }
        }
      }
    }
  }
}

$res = db()->query('SELECT * FROM gallery_images ORDER BY id DESC');
include __DIR__ . '/header.php';
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
