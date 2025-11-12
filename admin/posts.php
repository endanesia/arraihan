<?php require_once __DIR__ . '/header.php'; ?>
<?php
// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$published_filter = isset($_GET['published']) ? $_GET['published'] : '';

// Build query with filters
$where = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where[] = "(title LIKE ? OR content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if ($type_filter !== '') {
    $where[] = "type = ?";
    $params[] = $type_filter;
    $types .= 's';
}

if ($published_filter !== '') {
    $where[] = "published = ?";
    $params[] = (int)$published_filter;
    $types .= 'i';
}

$whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
$query = "SELECT id, type, title, slug, published, created_at FROM posts $whereClause ORDER BY created_at DESC, id DESC";

// Execute query
if (!empty($params)) {
    $stmt = db()->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = db()->query($query);
}

$total_posts = $res->num_rows;
?>
<?php $base = rtrim($config['app']['base_url'] ?? '', '/'); ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3 class="mb-0">Artikel & Berita</h3>
  <a class="btn btn-success" href="<?= e($base) ?>/admin/posts/new">
    <i class="fas fa-plus"></i> Tambah Artikel
  </a>
</div>

<!-- Filter Section -->
<div class="card mb-3">
  <div class="card-body">
    <form method="GET" class="row g-3">
      <div class="col-md-4">
        <label class="form-label">Pencarian</label>
        <input type="text" name="search" class="form-control" placeholder="Cari judul atau konten..." value="<?= e($search) ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Jenis</label>
        <select name="type" class="form-select">
          <option value="">Semua Jenis</option>
          <option value="news" <?= $type_filter === 'news' ? 'selected' : '' ?>>News</option>
          <option value="article" <?= $type_filter === 'article' ? 'selected' : '' ?>>Article</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Status Publish</label>
        <select name="published" class="form-select">
          <option value="">Semua Status</option>
          <option value="1" <?= $published_filter === '1' ? 'selected' : '' ?>>Published</option>
          <option value="0" <?= $published_filter === '0' ? 'selected' : '' ?>>Draft</option>
        </select>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">
          <i class="fas fa-search"></i> Filter
        </button>
      </div>
    </form>
    <?php if (!empty($search) || $type_filter !== '' || $published_filter !== ''): ?>
    <div class="mt-3">
      <a href="posts.php" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-times"></i> Reset Filter
      </a>
      <span class="text-muted ms-2">Ditemukan <?= $total_posts ?> artikel</span>
    </div>
    <?php endif; ?>
  </div>
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
      <?php if ($total_posts > 0): ?>
        <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
          <td><?= (int)$row['id'] ?></td>
          <td><?= e($row['title']) ?></td>
          <td><span class="badge bg-primary"><?= e($row['type']) ?></span></td>
          <td><?= e($row['slug']) ?></td>
          <td>
            <?php if ($row['published']): ?>
              <span class="badge bg-success">Published</span>
            <?php else: ?>
              <span class="badge bg-secondary">Draft</span>
            <?php endif; ?>
          </td>
          <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
          <td>
            <a class="btn btn-sm btn-outline-primary" href="<?= e($base) ?>/admin/posts/<?= (int)$row['id'] ?>/edit">
              <i class="fas fa-edit"></i> Edit
            </a>
            <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/posts/<?= (int)$row['id'] ?>/delete" onclick="return confirm('Hapus artikel ini?');">
              <i class="fas fa-trash"></i> Hapus
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr>
          <td colspan="7" class="text-center py-4">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <p class="text-muted">Tidak ada artikel ditemukan</p>
            <?php if (!empty($search) || $type_filter !== '' || $published_filter !== ''): ?>
              <a href="posts.php" class="btn btn-sm btn-primary">Reset Filter</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
