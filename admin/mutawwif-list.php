<?php
require_once __DIR__ . '/header.php';

$mutawwif = [];
$res = db()->query("SELECT * FROM mutawwif ORDER BY urutan ASC, id ASC");
while ($row = $res->fetch_assoc()) {
    $mutawwif[] = $row;
}
?>

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mutawwif & Tour Leader</h2>
            <p class="text-muted mb-0">Kelola data mutawwif dan tour leader profesional</p>
        </div>
        <a href="mutawwif-edit.php" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Mutawwif
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php if ($_GET['success'] == 'add'): ?>
            Mutawwif berhasil ditambahkan!
        <?php elseif ($_GET['success'] == 'edit'): ?>
            Mutawwif berhasil diperbarui!
        <?php elseif ($_GET['success'] == 'delete'): ?>
            Mutawwif berhasil dihapus!
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($mutawwif)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data mutawwif. Silakan tambahkan data baru.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Urutan</th>
                                <th width="100">Foto</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                                <th width="100" class="text-center">Status</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($mutawwif as $mw): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary"><?= (int)$mw['urutan'] ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($mw['foto'])): ?>
                                        <img src="../images/mutawwif/<?= e($mw['foto']) ?>" 
                                             alt="<?= e($mw['nama']) ?>" 
                                             class="rounded"
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 60px;">
                                            <i class="fas fa-user-tie text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= e($mw['nama']) ?></strong>
                                </td>
                                <td>
                                    <span class="text-muted"><?= e($mw['jabatan']) ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($mw['is_active']): ?>
                                        <span class="badge bg-success">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="mutawwif-edit.php?id=<?= (int)$mw['id'] ?>" 
                                           class="btn btn-outline-primary" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="mutawwif-delete.php?id=<?= (int)$mw['id'] ?>" 
                                           class="btn btn-outline-danger" 
                                           onclick="return confirm('Yakin ingin menghapus mutawwif ini?')"
                                           title="Hapus">
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

<?php require_once __DIR__ . '/footer.php'; ?>
