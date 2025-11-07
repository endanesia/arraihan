<?php
require_once __DIR__ . '/header.php';

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $db = db();
        $id = (int)$_GET['delete'];
        $stmt = $db->prepare("DELETE FROM keunggulan WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        header('Location: keunggulan.php?success=deleted');
        exit;
    } catch (Exception $e) {
        $error = 'Error deleting keunggulan: ' . $e->getMessage();
    }
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $order_num = (int)($_POST['order_num'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($title && $description && $icon) {
        try {
            $db = db();
            
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                // Update existing
                $id = (int)$_POST['id'];
                $stmt = $db->prepare("UPDATE keunggulan SET title = ?, description = ?, icon = ?, order_num = ?, is_active = ? WHERE id = ?");
                $stmt->bind_param('sssiii', $title, $description, $icon, $order_num, $is_active, $id);
                $stmt->execute();
                $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Keunggulan berhasil diupdate!</div>';
            } else {
                // Insert new
                $stmt = $db->prepare("INSERT INTO keunggulan (title, description, icon, order_num, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssii', $title, $description, $icon, $order_num, $is_active);
                $stmt->execute();
                $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Keunggulan berhasil ditambahkan!</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error: ' . e($e->getMessage()) . '</div>';
        }
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Semua field wajib diisi!</div>';
    }
}

// Get keunggulan for editing
$edit_keunggulan = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    try {
        $db = db();
        $id = (int)$_GET['edit'];
        $stmt = $db->prepare("SELECT * FROM keunggulan WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_keunggulan = $result->fetch_assoc();
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error loading keunggulan: ' . e($e->getMessage()) . '</div>';
    }
}

// Get all keunggulan
$keunggulan_list = [];
try {
    $db = db();
    $result = $db->query("SELECT * FROM keunggulan ORDER BY order_num ASC, id ASC");
    while ($row = $result->fetch_assoc()) {
        $keunggulan_list[] = $row;
    }
} catch (Exception $e) {
    $message = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Error loading data: ' . e($e->getMessage()) . '</div>';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Keunggulan Kami</h1>
                <a href="<?= e($base) ?>/" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Website
                </a>
            </div>

            <?= $message ?>

            <!-- Form Add/Edit -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star text-primary me-2"></i>
                        <?= $edit_keunggulan ? 'Edit Keunggulan' : 'Tambah Keunggulan Baru' ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <?php if ($edit_keunggulan): ?>
                        <input type="hidden" name="id" value="<?= e($edit_keunggulan['id']) ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Keunggulan *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= e($edit_keunggulan['title'] ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi *</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required><?= e($edit_keunggulan['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon Font Awesome *</label>
                                    <input type="text" class="form-control" id="icon" name="icon" 
                                           value="<?= e($edit_keunggulan['icon'] ?? '') ?>" required 
                                           placeholder="fas fa-star">
                                    <div class="form-text">Contoh: fas fa-star, fas fa-check, fas fa-heart</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="order_num" class="form-label">Urutan</label>
                                    <input type="number" class="form-control" id="order_num" name="order_num" 
                                           value="<?= e($edit_keunggulan['order_num'] ?? '0') ?>" min="0">
                                    <div class="form-text">Semakin kecil semakin di atas</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               <?= ($edit_keunggulan['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            Aktif / Tampilkan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $edit_keunggulan ? 'Update' : 'Simpan' ?>
                            </button>
                            <?php if ($edit_keunggulan): ?>
                            <a href="keunggulan" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal Edit
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Keunggulan -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list text-info me-2"></i>
                        Daftar Keunggulan (<?= count($keunggulan_list) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($keunggulan_list)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada keunggulan. Tambahkan keunggulan pertama Anda!</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">Urutan</th>
                                    <th width="60">Icon</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th width="80">Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($keunggulan_list as $item): ?>
                                <tr class="<?= $item['is_active'] ? '' : 'table-secondary' ?>">
                                    <td><?= e($item['order_num']) ?></td>
                                    <td>
                                        <i class="<?= e($item['icon']) ?> fa-lg text-primary"></i>
                                    </td>
                                    <td>
                                        <strong><?= e($item['title']) ?></strong>
                                    </td>
                                    <td>
                                        <small><?= e(substr($item['description'], 0, 100)) ?><?= strlen($item['description']) > 100 ? '...' : '' ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $item['is_active'] ? 'success' : 'secondary' ?>">
                                            <?= $item['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="keunggulan?edit=<?= $item['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="keunggulan?delete=<?= $item['id'] ?>" class="btn btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin menghapus keunggulan ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
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
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>