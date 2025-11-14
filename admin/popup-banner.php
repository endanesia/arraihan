<?php
require_once __DIR__ . '/header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'save') {
        $id = $_POST['id'] ?? null;
        $title = $_POST['title'] ?? '';
        $link_url = $_POST['link_url'] ?? '';
        $is_active = isset($_POST['is_active']) ? 1 : 0;
        
        // Handle image upload
        $image_url = $_POST['existing_image'] ?? '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = __DIR__ . '/../images/popup/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = 'popup_' . time() . '.' . $ext;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                // Delete old image if exists
                if (!empty($image_url) && file_exists(__DIR__ . '/../' . $image_url)) {
                    unlink(__DIR__ . '/../' . $image_url);
                }
                $image_url = 'images/popup/' . $filename;
            }
        }
        
        if ($id) {
            // Update
            $stmt = db()->prepare("UPDATE popup_banner SET title=?, image_url=?, link_url=?, is_active=? WHERE id=?");
            $stmt->bind_param('sssii', $title, $image_url, $link_url, $is_active, $id);
        } else {
            // Insert
            $stmt = db()->prepare("INSERT INTO popup_banner (title, image_url, link_url, is_active) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('sssi', $title, $image_url, $link_url, $is_active);
        }
        
        if ($stmt->execute()) {
            header('Location: popup-banner?success=' . ($id ? 'edit' : 'add'));
            exit;
        }
        $stmt->close();
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? 0;
        
        // Get image path before deleting
        $row = db()->query("SELECT image_url FROM popup_banner WHERE id=$id")->fetch_assoc();
        if ($row && !empty($row['image_url'])) {
            $image_path = __DIR__ . '/../' . $row['image_url'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        db()->query("DELETE FROM popup_banner WHERE id=$id");
        header('Location: popup-banner?success=delete');
        exit;
    }
}

// Get all popup banners
$popups = [];
$res = db()->query("SELECT * FROM popup_banner ORDER BY created_at DESC");
while ($row = $res->fetch_assoc()) {
    $popups[] = $row;
}
?>

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Popup Banner</h2>
            <p class="text-muted mb-0">Kelola popup banner homepage</p>
        </div>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#popupModal" onclick="resetForm()">
            <i class="fas fa-plus"></i> Tambah Popup Banner
        </button>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php if ($_GET['success'] == 'add'): ?>
            Popup banner berhasil ditambahkan!
        <?php elseif ($_GET['success'] == 'edit'): ?>
            Popup banner berhasil diperbarui!
        <?php elseif ($_GET['success'] == 'delete'): ?>
            Popup banner berhasil dihapus!
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($popups)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-image fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada popup banner. Klik tombol "Tambah Popup Banner" untuk membuat popup banner baru.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th style="width: 80px;">Preview</th>
                                <th>Judul</th>
                                <th>Link URL</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                                <th style="width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($popups as $popup): ?>
                            <tr>
                                <td>
                                    <img src="../<?= e($popup['image_url']) ?>" alt="Preview" class="img-thumbnail" style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td><?= e($popup['title']) ?></td>
                                <td>
                                    <?php if (!empty($popup['link_url'])): ?>
                                        <a href="<?= e($popup['link_url']) ?>" target="_blank" class="text-primary">
                                            <i class="fas fa-external-link-alt"></i> Link
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($popup['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($popup['created_at'])) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" onclick="editPopup(<?= htmlspecialchars(json_encode($popup), ENT_QUOTES) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus popup banner ini?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $popup['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Form -->
<div class="modal fade" id="popupModal" tabindex="-1" aria-labelledby="popupModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="popupModalLabel">Tambah Popup Banner</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="save">
                    <input type="hidden" name="id" id="popup_id">
                    <input type="hidden" name="existing_image" id="existing_image">
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="title" name="title" required>
                        <small class="text-muted">Nama internal untuk identifikasi popup</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Popup <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Gambar akan ditampilkan di popup. Ukuran disarankan: 800x600px</small>
                        <div id="current_image_preview" style="display: none;" class="mt-2">
                            <img id="current_image" src="" alt="Current" class="img-thumbnail" style="max-width: 200px;">
                            <p class="text-muted small mb-0">Gambar saat ini (upload gambar baru untuk mengubah)</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="link_url" class="form-label">Link URL</label>
                        <input type="url" class="form-control" id="link_url" name="link_url" placeholder="https://example.com">
                        <small class="text-muted">URL tujuan ketika popup diklik (opsional)</small>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                            <small class="d-block text-muted">Hanya popup aktif yang akan ditampilkan di homepage</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('popupModalLabel').textContent = 'Tambah Popup Banner';
    document.querySelector('#popupModal form').reset();
    document.getElementById('popup_id').value = '';
    document.getElementById('existing_image').value = '';
    document.getElementById('current_image_preview').style.display = 'none';
    document.getElementById('image').required = true;
}

function editPopup(popup) {
    document.getElementById('popupModalLabel').textContent = 'Edit Popup Banner';
    document.getElementById('popup_id').value = popup.id;
    document.getElementById('title').value = popup.title;
    document.getElementById('link_url').value = popup.link_url || '';
    document.getElementById('is_active').checked = popup.is_active == 1;
    document.getElementById('existing_image').value = popup.image_url;
    
    // Show current image
    document.getElementById('current_image').src = '../' + popup.image_url;
    document.getElementById('current_image_preview').style.display = 'block';
    document.getElementById('image').required = false;
    
    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('popupModal'));
    modal.show();
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
