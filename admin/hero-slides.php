<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$rows = [];
if ($res = db()->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, id ASC")) {
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

.slide-preview {
    max-width: 100px;
    max-height: 60px;
    border-radius: 4px;
    border: 1px solid #ddd;
    object-fit: cover;
}

.text-truncate-custom {
    max-width: 200px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.status-toggle {
    cursor: pointer;
}
</style>

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Hero Slideshow</h3>
    <div class="btn-group">
      <a href="<?= e($base) ?>/admin/hero-slide-edit" class="btn btn-primary">
        <i class="fas fa-plus"></i> Tambah Slide
      </a>
      <a href="<?= e($base) ?>/" target="_blank" class="btn btn-outline-secondary">
        <i class="fas fa-eye"></i> Lihat Website
      </a>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-striped align-middle">
          <thead>
            <tr>
              <th>Urutan</th>
              <th>Preview</th>
              <th>Judul</th>
              <th>Subtitle</th>
              <th>Statistik</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td>
                <span class="badge bg-secondary">#<?= (int)$r['sort_order'] ?></span>
              </td>
              <td>
                <?php if (!empty($r['background_image'])): ?>
                  <img src="<?= e($base . $r['background_image']) ?>" alt="Slide" class="slide-preview">
                <?php else: ?>
                  <div class="bg-light border d-flex align-items-center justify-content-center" style="width: 100px; height: 60px; border-radius: 4px;">
                    <i class="fas fa-image text-muted"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <div class="fw-bold text-truncate-custom" title="<?= e($r['title']) ?>">
                  <?= e($r['title']) ?>
                </div>
                <?php if ($r['button_text']): ?>
                <small class="text-muted">
                  <i class="fas fa-mouse-pointer me-1"></i><?= e($r['button_text']) ?>
                </small>
                <?php endif; ?>
              </td>
              <td>
                <div class="text-truncate-custom text-muted small" title="<?= e($r['subtitle']) ?>">
                  <?= e($r['subtitle']) ?>
                </div>
              </td>
              <td>
                <?php if ($r['stat1_text'] || $r['stat2_text']): ?>
                  <div class="small">
                    <?php if ($r['stat1_text']): ?>
                      <div><strong><?= e($r['stat1_text']) ?></strong></div>
                      <div class="text-muted"><?= e($r['stat1_desc']) ?></div>
                    <?php endif; ?>
                    <?php if ($r['stat2_text']): ?>
                      <div class="mt-1"><strong><?= e($r['stat2_text']) ?></strong></div>
                      <div class="text-muted"><?= e($r['stat2_desc']) ?></div>
                    <?php endif; ?>
                  </div>
                <?php else: ?>
                  <span class="text-muted">-</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="badge <?= $r['is_active'] ? 'bg-success' : 'bg-secondary' ?> status-toggle" 
                      data-id="<?= (int)$r['id'] ?>" 
                      title="Klik untuk toggle status">
                  <?= $r['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                </span>
              </td>
              <td>
                <div class="btn-group" role="group">
                  <a class="btn btn-sm btn-outline-primary" href="<?= e($base) ?>/admin/hero-slide-edit?id=<?= (int)$r['id'] ?>" title="Edit">
                    <i class="fas fa-edit"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-danger" href="<?= e($base) ?>/admin/hero-slide-delete?id=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus slide ini?')" title="Hapus">
                    <i class="fas fa-trash"></i>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($rows)): ?>
            <tr><td colspan="7" class="text-center text-muted py-4">
              <i class="fas fa-images fa-2x mb-2 d-block"></i>
              Belum ada slide hero. <a href="<?= e($base) ?>/admin/hero-slide-edit">Tambah slide pertama</a>
            </td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle status
    document.querySelectorAll('.status-toggle').forEach(function(element) {
        element.addEventListener('click', function() {
            const slideId = this.getAttribute('data-id');
            const currentStatus = this.textContent.trim() === 'Aktif';
            
            fetch('<?= e($base) ?>/admin/ajax/toggle-hero-slide-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    id: slideId,
                    status: !currentStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.textContent = data.is_active ? 'Aktif' : 'Nonaktif';
                    this.className = data.is_active ? 
                        'badge bg-success status-toggle' : 
                        'badge bg-secondary status-toggle';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi error saat mengubah status');
            });
        });
    });
});
</script>

<?php include __DIR__ . '/footer.php'; ?>