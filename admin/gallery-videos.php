<?php require_once __DIR__ . '/header.php'; ?>
<?php $base = rtrim($config['app']['base_url'] ?? '', '/'); ?>
<?php
// Check if new columns exist in gallery_videos table
$video_table_columns = [];
try {
  $columns_result = db()->query("DESCRIBE gallery_videos");
  while ($col = $columns_result->fetch_assoc()) {
    $video_table_columns[] = $col['Field'];
  }
} catch (Exception $e) {
  // Handle error gracefully
}

$has_platform_column = in_array('platform', $video_table_columns);
$has_video_url_column = in_array('video_url', $video_table_columns);

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
    
    if ($has_platform_column && $has_video_url_column) {
      // Multi-platform support
      $platform = trim($_POST['platform'] ?? 'youtube');
      $video_input = trim($_POST['video_input'] ?? '');
      
      if (empty($video_input)) {
        $err = 'URL video tidak boleh kosong.';
      } else {
        if ($platform === 'youtube') {
          $vid = youtube_id_from_url($video_input);
          if (!$vid) { 
            $err = 'URL/ID YouTube tidak valid.'; 
          } else {
            $video_url = "https://www.youtube.com/embed/{$vid}?autoplay=1";
            $stmt = db()->prepare('INSERT INTO gallery_videos(title,youtube_id,platform,video_url) VALUES(?,?,?,?)');
            $stmt->bind_param('ssss', $title, $vid, $platform, $video_url);
            $stmt->execute();
            $msg = 'Video YouTube berhasil ditambahkan.';
          }
        } elseif ($platform === 'instagram' || $platform === 'tiktok') {
          // For Instagram and TikTok, just store the URL directly
          if (!filter_var($video_input, FILTER_VALIDATE_URL)) {
            $err = 'URL tidak valid.';
          } else {
            $stmt = db()->prepare('INSERT INTO gallery_videos(title,platform,video_url) VALUES(?,?,?)');
            $stmt->bind_param('sss', $title, $platform, $video_input);
            $stmt->execute();
            $msg = 'Video ' . ucfirst($platform) . ' berhasil ditambahkan.';
          }
        }
      }
    } else {
      // Legacy YouTube-only support
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
}

// Fetch videos with multi-platform support
if ($has_platform_column && $has_video_url_column) {
  $res = db()->query('SELECT * FROM gallery_videos ORDER BY id DESC');
} else {
  $res = db()->query('SELECT * FROM gallery_videos ORDER BY id DESC');
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Galeri Video</h3>
</div>

<?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
<?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>

<div class="card mb-4">
  <div class="card-body">
    <form method="post" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Judul (opsional)</label>
        <input type="text" name="title" class="form-control">
      </div>
      
      <?php if ($has_platform_column && $has_video_url_column): ?>
      <div class="col-md-2">
        <label class="form-label">Platform</label>
        <select name="platform" class="form-control" id="platformSelect" onchange="updatePlaceholder()">
          <option value="youtube">YouTube</option>
          <option value="instagram">Instagram</option>
          <option value="tiktok">TikTok</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">URL Video</label>
        <input type="text" name="video_input" class="form-control" id="videoInput" placeholder="https://youtube.com/watch?v=..." required>
      </div>
      <?php else: ?>
      <div class="col-md-6">
        <label class="form-label">URL/ID YouTube</label>
        <input type="text" name="youtube" class="form-control" placeholder="https://youtube.com/watch?v=..." required>
      </div>
      <?php endif; ?>
      
      <div class="col-md-2 d-grid align-items-end">
        <button class="btn btn-success" type="submit">Tambah</button>
        <input type="hidden" name="action" value="create">
      </div>
    </form>
  </div>
</div>

<?php if ($has_platform_column && $has_video_url_column): ?>
<script>
function updatePlaceholder() {
  const select = document.getElementById('platformSelect');
  const input = document.getElementById('videoInput');
  
  const placeholders = {
    'youtube': 'https://youtube.com/watch?v=...',
    'instagram': 'https://instagram.com/p/...',
    'tiktok': 'https://tiktok.com/@user/video/...'
  };
  
  input.placeholder = placeholders[select.value] || 'URL Video';
}
</script>
<?php endif; ?>

<div class="row g-3">
  <?php while($row = $res->fetch_assoc()): 
    // Determine platform and thumbnail
    $platform = $has_platform_column && !empty($row['platform']) ? $row['platform'] : 'youtube';
    
    if ($platform === 'youtube') {
      $thumb = 'https://img.youtube.com/vi/'.e($row['youtube_id']).'/hqdefault.jpg';
      $platform_icon = '<i class="fab fa-youtube text-danger"></i>';
    } elseif ($platform === 'instagram') {
      $thumb = 'https://via.placeholder.com/480x360/E4405F/white?text=Instagram+Video';
      $platform_icon = '<i class="fab fa-instagram text-primary"></i>';
    } elseif ($platform === 'tiktok') {
      $thumb = 'https://via.placeholder.com/480x360/000000/white?text=TikTok+Video';
      $platform_icon = '<i class="fab fa-tiktok text-dark"></i>';
    } else {
      $thumb = 'https://via.placeholder.com/480x360/6c757d/white?text=Video';
      $platform_icon = '<i class="fas fa-video text-secondary"></i>';
    }
  ?>
  <div class="col-sm-6 col-md-4 col-lg-3">
    <div class="card h-100">
      <div class="position-relative">
        <img src="<?= $thumb ?>" class="card-img-top" alt="<?= e($row['title']) ?>" style="height: 200px; object-fit: cover;">
        <div class="position-absolute top-0 end-0 m-2">
          <span class="badge bg-light text-dark"><?= $platform_icon ?> <?= ucfirst($platform) ?></span>
        </div>
      </div>
      <div class="card-body">
        <form method="post" class="d-flex gap-2 align-items-center mb-2">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
          <input type="text" name="title" class="form-control form-control-sm" value="<?= e($row['title']) ?>" placeholder="Judul video">
          <button class="btn btn-sm btn-outline-primary" type="submit">Simpan</button>
        </form>
        <div class="d-flex gap-2">
          <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/gallery-videos/<?= (int)$row['id'] ?>/delete" onclick="return confirm('Hapus video ini?');">Hapus</a>
          <?php if ($has_video_url_column && !empty($row['video_url'])): ?>
          <a class="btn btn-sm btn-outline-info" href="<?= e($row['video_url']) ?>" target="_blank" title="Lihat Video">
            <i class="fas fa-external-link-alt"></i>
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endwhile; ?>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
