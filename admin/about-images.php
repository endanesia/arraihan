<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../inc/db.php';

// Handle form submissions
if ($_POST) {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_image':
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../images/about/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file = $_FILES['image'];
                    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $file['name']);
                    $filepath = $upload_dir . $filename;
                    
                    if (move_uploaded_file($file['tmp_name'], $filepath)) {
                        $title = $_POST['title'] ?? '';
                        $alt_text = $_POST['alt_text'] ?? '';
                        $sort_order = (int)($_POST['sort_order'] ?? 0);
                        
                        $stmt = db()->prepare("INSERT INTO about_images (image_path, title, alt_text, sort_order) VALUES (?, ?, ?, ?)");
                        $image_path = 'about/' . $filename;
                        $stmt->bind_param('sssi', $image_path, $title, $alt_text, $sort_order);
                        
                        if ($stmt->execute()) {
                            $_SESSION['success'] = 'Image berhasil ditambahkan!';
                        } else {
                            $_SESSION['error'] = 'Gagal menyimpan ke database!';
                        }
                    } else {
                        $_SESSION['error'] = 'Gagal upload file!';
                    }
                }
                break;
                
            case 'update_image':
                $id = (int)$_POST['id'];
                $title = $_POST['title'] ?? '';
                $alt_text = $_POST['alt_text'] ?? '';
                $sort_order = (int)($_POST['sort_order'] ?? 0);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                $stmt = db()->prepare("UPDATE about_images SET title = ?, alt_text = ?, sort_order = ?, is_active = ? WHERE id = ?");
                $stmt->bind_param('ssiii', $title, $alt_text, $sort_order, $is_active, $id);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'Image berhasil diupdate!';
                } else {
                    $_SESSION['error'] = 'Gagal update image!';
                }
                break;
                
            case 'delete_image':
                $id = (int)$_POST['id'];
                
                // Get image path first
                $stmt = db()->prepare("SELECT image_path FROM about_images WHERE id = ?");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $image = $result->fetch_assoc();
                
                if ($image) {
                    // Delete from database
                    $stmt = db()->prepare("DELETE FROM about_images WHERE id = ?");
                    $stmt->bind_param('i', $id);
                    
                    if ($stmt->execute()) {
                        // Delete physical file
                        $file_path = __DIR__ . '/../images/' . $image['image_path'];
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                        $_SESSION['success'] = 'Image berhasil dihapus!';
                    } else {
                        $_SESSION['error'] = 'Gagal hapus image!';
                    }
                }
                break;
        }
    }
    
    header('Location: about-images.php');
    exit;
}

// Get all images
$images = [];
$result = db()->query("SELECT * FROM about_images ORDER BY sort_order ASC, id ASC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $images[] = $row;
    }
}

$page_title = 'Kelola Gambar Tentang Kami';
require_once __DIR__ . '/header.php';
?>

<div class="main-content">
    <div class="content-header">
        <h1><i class="fas fa-images"></i> Kelola Gambar Tentang Kami</h1>
        <p>Kelola gambar slideshow untuk section Tentang Kami</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= e($_SESSION['success']) ?>
        <?php unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <?= e($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>

    <!-- Add New Image Form -->
    <div class="content-box">
        <h2><i class="fas fa-plus"></i> Tambah Gambar Baru</h2>
        <form method="POST" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="action" value="add_image">
            
            <div class="form-group">
                <label>Gambar *</label>
                <input type="file" name="image" accept="image/*" required>
                <small>Format: JPG, JPEG, PNG, WebP (Max: 2MB)</small>
            </div>
            
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" placeholder="Judul gambar">
            </div>
            
            <div class="form-group">
                <label>Alt Text</label>
                <input type="text" name="alt_text" placeholder="Deskripsi gambar untuk SEO">
            </div>
            
            <div class="form-group">
                <label>Urutan</label>
                <input type="number" name="sort_order" value="<?= count($images) + 1 ?>" min="1">
            </div>
            
            <div class="form-group form-group-full">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Gambar
                </button>
            </div>
        </form>
    </div>

    <!-- Images List -->
    <div class="content-box">
        <h2><i class="fas fa-list"></i> Daftar Gambar (<?= count($images) ?>)</h2>
        
        <?php if (empty($images)): ?>
        <div class="empty-state">
            <i class="fas fa-images fa-3x"></i>
            <h3>Belum ada gambar</h3>
            <p>Tambahkan gambar pertama untuk slideshow Tentang Kami</p>
        </div>
        <?php else: ?>
        <div class="images-grid">
            <?php foreach ($images as $img): ?>
            <div class="image-card">
                <div class="image-preview">
                    <img src="../images/<?= e($img['image_path']) ?>" alt="<?= e($img['alt_text']) ?>">
                    <?php if (!$img['is_active']): ?>
                    <div class="image-inactive-overlay">
                        <i class="fas fa-eye-slash"></i>
                        <span>Tidak Aktif</span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="image-info">
                    <h4><?= e($img['title']) ?: 'Tanpa Judul' ?></h4>
                    <p class="image-meta">
                        Urutan: <?= $img['sort_order'] ?> | 
                        <?= $img['is_active'] ? '<span class="status-active">Aktif</span>' : '<span class="status-inactive">Tidak Aktif</span>' ?>
                    </p>
                    <p class="image-path"><?= e($img['image_path']) ?></p>
                </div>
                
                <div class="image-actions">
                    <button type="button" class="btn btn-sm btn-primary" onclick="editImage(<?= $img['id'] ?>)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteImage(<?= $img['id'] ?>, '<?= e($img['title']) ?>')">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Image Modal -->
<div id="edit-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-edit"></i> Edit Gambar</h3>
            <button type="button" class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="edit-form" method="POST">
            <input type="hidden" name="action" value="update_image">
            <input type="hidden" name="id" id="edit-id">
            
            <div class="modal-body">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" id="edit-title">
                </div>
                
                <div class="form-group">
                    <label>Alt Text</label>
                    <input type="text" name="alt_text" id="edit-alt-text">
                </div>
                
                <div class="form-group">
                    <label>Urutan</label>
                    <input type="number" name="sort_order" id="edit-sort-order" min="1">
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" id="edit-is-active" value="1">
                        <span class="checkmark"></span>
                        Aktif
                    </label>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-trash"></i> Konfirmasi Hapus</h3>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Apakah Anda yakin ingin menghapus gambar <strong id="delete-title"></strong>?</p>
            <p class="text-warning"><i class="fas fa-exclamation-triangle"></i> File gambar juga akan dihapus dari server!</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Batal</button>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="action" value="delete_image">
                <input type="hidden" name="id" id="delete-id">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Hapus
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.image-card {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    overflow: hidden;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.image-preview {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.image-inactive-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
}

.image-info {
    padding: 15px;
}

.image-info h4 {
    margin: 0 0 8px 0;
    font-size: 16px;
}

.image-meta {
    margin: 5px 0;
    font-size: 14px;
    color: #666;
}

.image-path {
    margin: 5px 0 0 0;
    font-size: 12px;
    color: #999;
    font-family: monospace;
}

.status-active {
    color: #28a745;
    font-weight: bold;
}

.status-inactive {
    color: #dc3545;
    font-weight: bold;
}

.image-actions {
    padding: 15px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    gap: 8px;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.form-group-full {
    grid-column: 1 / -1;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state i {
    color: #ccc;
    margin-bottom: 20px;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
}

.modal-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 8px;
}
</style>

<script>
const images = <?= json_encode($images) ?>;

function editImage(id) {
    const image = images.find(img => img.id == id);
    if (!image) return;
    
    document.getElementById('edit-id').value = image.id;
    document.getElementById('edit-title').value = image.title || '';
    document.getElementById('edit-alt-text').value = image.alt_text || '';
    document.getElementById('edit-sort-order').value = image.sort_order;
    document.getElementById('edit-is-active').checked = image.is_active == 1;
    
    document.getElementById('edit-modal').style.display = 'flex';
}

function closeEditModal() {
    document.getElementById('edit-modal').style.display = 'none';
}

function deleteImage(id, title) {
    document.getElementById('delete-id').value = id;
    document.getElementById('delete-title').textContent = title || 'Tanpa Judul';
    document.getElementById('delete-modal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('delete-modal').style.display = 'none';
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
        e.target.style.display = 'none';
    }
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>