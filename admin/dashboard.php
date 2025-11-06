<?php require_once __DIR__ . '/header.php'; ?>
<?php
$counts = [
  'posts' => 0,
  'images' => 0,
  'videos' => 0,
];
if (db()) {
  foreach ([
    'posts' => 'SELECT COUNT(*) c FROM posts',
    'images' => 'SELECT COUNT(*) c FROM gallery_images',
    'videos' => 'SELECT COUNT(*) c FROM gallery_videos',
  ] as $key => $sql) {
    if ($res = db()->query($sql)) { $row = $res->fetch_assoc(); $counts[$key] = (int)$row['c']; }
  }
}
?>
<div class="row g-4">
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted">Total Posts</div>
            <div class="h3 mb-0"><?= $counts['posts'] ?></div>
          </div>
          <a class="btn btn-sm btn-success" href="posts.php">Kelola</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted">Galeri Gambar</div>
            <div class="h3 mb-0"><?= $counts['images'] ?></div>
          </div>
          <a class="btn btn-sm btn-success" href="gallery-images.php">Kelola</a>
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted">Galeri Video</div>
            <div class="h3 mb-0"><?= $counts['videos'] ?></div>
          </div>
          <a class="btn btn-sm btn-success" href="gallery-videos.php">Kelola</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/footer.php'; ?>
