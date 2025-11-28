<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

// Get filter parameters
$filter_category = isset($_GET['category']) ? trim($_GET['category']) : '';
$filter_search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query with filters
$query = "SELECT * FROM packages WHERE 1=1";
$conditions = [];

if ($filter_category !== '') {
    $conditions[] = "category = '" . db()->real_escape_string($filter_category) . "'";
}

if ($filter_search !== '') {
    $search_term = db()->real_escape_string($filter_search);
    $conditions[] = "(title LIKE '%$search_term%' OR description LIKE '%$search_term%' OR hotel LIKE '%$search_term%' OR pesawat LIKE '%$search_term%')";
}

if (!empty($conditions)) {
    $query .= " AND " . implode(" AND ", $conditions);
}

$query .= " ORDER BY featured DESC, id DESC";

$rows = [];
if ($res = db()->query($query)) {
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

.bg-purple {
    background-color: #6f42c1 !important;
    color: white !important;
}

.filter-card {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}

.filter-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

@media (max-width: 768px) {
    .filter-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .filter-actions .btn {
        width: 100%;
    }
}
</style>

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Paket Perjalanan</h3>
    <a href="<?= e($base) ?>/admin/package-edit" class="btn btn-primary">Tambah Paket</a>
  </div>

  <!-- Filter Section -->
  <div class="filter-card">
    <form method="get" class="row g-3">
      <div class="col-md-4">
        <label class="form-label fw-bold"><i class="fas fa-filter me-1"></i>Filter Kategori</label>
        <select name="category" class="form-select">
          <option value="">Semua Kategori</option>
          <option value="Umroh" <?= $filter_category === 'Umroh' ? 'selected' : '' ?>>Umroh</option>
          <option value="Badal Umroh" <?= $filter_category === 'Badal Umroh' ? 'selected' : '' ?>>Badal Umroh</option>
          <option value="Badal Haji" <?= $filter_category === 'Badal Haji' ? 'selected' : '' ?>>Badal Haji</option>
          <option value="Halal Tour" <?= $filter_category === 'Halal Tour' ? 'selected' : '' ?>>Halal Tour</option>
          <option value="Ziarah" <?= $filter_category === 'Ziarah' ? 'selected' : '' ?>>Ziarah</option>
          <option value="Dana Talangan" <?= $filter_category === 'Dana Talangan' ? 'selected' : '' ?>>Dana Talangan</option>
          <option value="Tabungan Umroh" <?= $filter_category === 'Tabungan Umroh' ? 'selected' : '' ?>>Tabungan Umroh</option>
        </select>
      </div>
      <div class="col-md-5">
        <label class="form-label fw-bold"><i class="fas fa-search me-1"></i>Cari Paket</label>
        <input type="text" name="search" class="form-control" placeholder="Cari berdasarkan nama, hotel, atau pesawat..." value="<?= e($filter_search) ?>">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="filter-actions">
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-search me-1"></i>Filter
          </button>
          <a href="<?= e($base) ?>/admin/packages" class="btn btn-secondary">
            <i class="fas fa-redo me-1"></i>Reset
          </a>
        </div>
      </div>
    </form>
    
    <?php if ($filter_category || $filter_search): ?>
    <div class="mt-3">
      <small class="text-muted">
        <i class="fas fa-info-circle me-1"></i>
        Menampilkan <strong><?= count($rows) ?></strong> paket
        <?php if ($filter_category): ?>
          dari kategori <strong><?= e($filter_category) ?></strong>
        <?php endif; ?>
        <?php if ($filter_search): ?>
          dengan kata kunci "<strong><?= e($filter_search) ?></strong>"
        <?php endif; ?>
      </small>
    </div>
    <?php endif; ?>
  </div>

  <div class="card"><div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Poster</th>
            <th>Judul</th>
            <th>Kategori</th>
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
              <?php if (!empty($r['poster'])): ?>
                <img src="<?= e($base . '/images/packages/' . $r['poster']) ?>" alt="Poster" 
                     style="width: 60px; height: 45px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd;">
              <?php else: ?>
                <div style="width: 60px; height: 45px; background: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                  <i class="fas fa-image text-muted"></i>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div class="fw-bold"><?= e($r['title']) ?></div>
              <?php if (!empty($r['description'])): ?>
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
              <?php if (!empty($r['category'])): ?>
                <?php 
                  $badge_colors = [
                    'Umroh' => 'bg-success',
                    'Badal Umroh' => 'bg-info',
                    'Badal Haji' => 'bg-primary',
                    'Halal Tour' => 'bg-warning text-dark',
                    'Ziarah' => 'bg-secondary',
                    'Dana Talangan' => 'bg-danger',
                    'Tabungan Umroh' => 'bg-purple'
                  ];
                  $badge_color = $badge_colors[$r['category']] ?? 'bg-secondary';
                ?>
                <span class="badge <?= $badge_color ?>"><?= e($r['category']) ?></span>
              <?php else: ?>
                <span class="text-muted">-</span>
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
          <tr><td colspan="10" class="text-center text-muted py-4">
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
