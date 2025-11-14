<?php
require_once __DIR__ . '/header.php';

// Handle filter
$filter = $_GET['filter'] ?? 'all';
$search = $_GET['search'] ?? '';

// Build query
$where = [];
$params = [];
$types = '';

if ($filter === 'approved') {
    $where[] = "is_approved = 1";
} elseif ($filter === 'pending') {
    $where[] = "is_approved = 0";
}

if (!empty($search)) {
    $where[] = "(nama LIKE ? OR judul LIKE ? OR pesan LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'sss';
}

$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$testimonials = [];
$query = "SELECT * FROM testimonials $whereClause ORDER BY created_at DESC";

if (!empty($params)) {
    $stmt = db()->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    $res = db()->query($query);
}

while ($row = $res->fetch_assoc()) {
    $testimonials[] = $row;
}

// Count stats
$total = db()->query("SELECT COUNT(*) as count FROM testimonials")->fetch_assoc()['count'];
$approved = db()->query("SELECT COUNT(*) as count FROM testimonials WHERE is_approved = 1")->fetch_assoc()['count'];
$pending = db()->query("SELECT COUNT(*) as count FROM testimonials WHERE is_approved = 0")->fetch_assoc()['count'];
?>

<div class="p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Testimonial</h2>
            <p class="text-muted mb-0">Kelola testimonial jamaah</p>
        </div>
        <a href="testimonial-edit" class="btn btn-success">
            <i class="fas fa-plus"></i> Tambah Testimonial
        </a>
    </div>

    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php if ($_GET['success'] == 'add'): ?>
            Testimonial berhasil ditambahkan!
        <?php elseif ($_GET['success'] == 'edit'): ?>
            Testimonial berhasil diperbarui!
        <?php elseif ($_GET['success'] == 'delete'): ?>
            Testimonial berhasil dihapus!
        <?php elseif ($_GET['success'] == 'approve'): ?>
            Testimonial berhasil disetujui!
        <?php elseif ($_GET['success'] == 'unapprove'): ?>
            Testimonial berhasil dibatalkan persetujuannya!
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-comments fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Testimonial</h6>
                            <h3 class="mb-0"><?= $total ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Disetujui</h6>
                            <h3 class="mb-0"><?= $approved ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Menunggu Persetujuan</h6>
                            <h3 class="mb-0"><?= $pending ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter and Search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Filter Status</label>
                    <select name="filter" class="form-select">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua</option>
                        <option value="approved" <?= $filter === 'approved' ? 'selected' : '' ?>>Disetujui</option>
                        <option value="pending" <?= $filter === 'pending' ? 'selected' : '' ?>>Menunggu</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cari</label>
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, judul, atau pesan..." value="<?= e($search) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </form>
            <?php if ($filter !== 'all' || !empty($search)): ?>
            <div class="mt-3">
                <a href="testimonials.php" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-times"></i> Reset Filter
                </a>
                <span class="text-muted ms-2"><?= count($testimonials) ?> hasil ditemukan</span>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Testimonials List -->
    <div class="card shadow-sm">
        <div class="card-body">
            <?php if (empty($testimonials)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Tidak ada testimonial.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Nama</th>
                                <th>Judul</th>
                                <th>Pesan</th>
                                <th width="100" class="text-center">Status</th>
                                <th width="150" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testimonials as $testi): ?>
                            <tr>
                                <td>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($testi['created_at'])) ?></small>
                                </td>
                                <td>
                                    <strong><?= e($testi['nama']) ?></strong>
                                </td>
                                <td>
                                    <?= e($testi['judul']) ?>
                                </td>
                                <td>
                                    <small><?= e(substr($testi['pesan'], 0, 100)) . (strlen($testi['pesan']) > 100 ? '...' : '') ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if ($testi['is_approved']): ?>
                                        <span class="badge bg-success">Disetujui</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <?php if (!$testi['is_approved']): ?>
                                        <a href="testimonial-approve?id=<?= (int)$testi['id'] ?>&action=approve" 
                                           class="btn btn-outline-success" 
                                           title="Setujui">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <?php else: ?>
                                        <a href="testimonial-approve?id=<?= (int)$testi['id'] ?>&action=unapprove" 
                                           class="btn btn-outline-warning" 
                                           title="Batalkan">
                                            <i class="fas fa-times"></i>
                                        </a>
                                        <?php endif; ?>
                                        <a href="testimonial-edit?id=<?= (int)$testi['id'] ?>" 
                                           class="btn btn-outline-primary" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="testimonial-delete?id=<?= (int)$testi['id'] ?>" 
                                           class="btn btn-outline-danger" 
                                           onclick="return confirm('Yakin ingin menghapus testimonial ini?')"
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
