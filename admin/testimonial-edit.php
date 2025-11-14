<?php
require_once __DIR__ . '/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

$testimonial = [
    'nama' => '',
    'judul' => '',
    'pesan' => '',
    'is_approved' => 0,
    'is_featured' => 0
];

if ($isEdit) {
    $stmt = db()->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $testimonial = $row;
    } else {
        header('Location: testimonials.php');
        exit;
    }
}

// Debug: aktifkan untuk troubleshooting
echo '<!-- DEBUG INFO:';
echo ' ID: ' . $id;
echo ' IsEdit: ' . ($isEdit ? 'true' : 'false');
echo ' Data: ';
print_r($testimonial);
echo '-->';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $judul = trim($_POST['judul'] ?? '');
    $pesan = trim($_POST['pesan'] ?? '');
    $is_approved = isset($_POST['is_approved']) ? 1 : 0;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Validate
    if (empty($nama) || empty($judul) || empty($pesan)) {
        $error = 'Semua field harus diisi!';
    } else {
        if ($isEdit) {
            // Update
            $stmt = db()->prepare("UPDATE testimonials SET nama=?, judul=?, pesan=?, is_approved=?, is_featured=? WHERE id=?");
            $stmt->bind_param('sssiii', $nama, $judul, $pesan, $is_approved, $is_featured, $id);
            $stmt->execute();
            header('Location: testimonials?success=edit');
            exit;
        } else {
            // Insert
            $stmt = db()->prepare("INSERT INTO testimonials (nama, judul, pesan, is_approved, is_featured) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssii', $nama, $judul, $pesan, $is_approved, $is_featured);
            $stmt->execute();
            header('Location: testimonials?success=add');
            exit;
        }
    }
}
?>

<div class="p-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="testimonials.php">Testimonial</a></li>
                <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'Tambah' ?> Testimonial</li>
            </ol>
        </nav>
        <h2><?= $isEdit ? 'Edit' : 'Tambah' ?> Testimonial</h2>
    </div>

    <?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= e($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   name="nama" 
                                   value="<?= isset($testimonial['nama']) ? e($testimonial['nama']) : '' ?>" 
                                   required 
                                   placeholder="Nama jamaah">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   name="judul" 
                                   value="<?= isset($testimonial['judul']) ? e($testimonial['judul']) : '' ?>" 
                                   required 
                                   placeholder="Judul testimonial">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pesan / Testimonial <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      name="pesan" 
                                      rows="8" 
                                      required 
                                      placeholder="Isi testimonial"><?= isset($testimonial['pesan']) ? e($testimonial['pesan']) : '' ?></textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_approved" 
                                       id="is_approved" 
                                       <?= !empty($testimonial['is_approved']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_approved">
                                    <strong>Setujui dan tampilkan di website</strong>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_featured" 
                                       id="is_featured" 
                                       <?= !empty($testimonial['is_featured']) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_featured">
                                    <strong>Jadikan testimonial unggulan</strong>
                                    <small class="d-block text-muted">Testimonial unggulan akan diprioritaskan untuk ditampilkan</small>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="testimonials.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-info-circle"></i> Panduan</h5>
                    <ul class="small mb-0">
                        <li>Semua field harus diisi</li>
                        <li>Nama: Nama lengkap jamaah yang memberikan testimonial</li>
                        <li>Judul: Ringkasan singkat dari testimonial</li>
                        <li>Pesan: Isi lengkap testimonial/pengalaman jamaah</li>
                        <li>Centang "Setujui" untuk menampilkan di website</li>
                        <li>Testimonial unggulan akan muncul lebih sering di slider</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
