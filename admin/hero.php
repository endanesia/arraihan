<?php
require_once __DIR__ . '/header.php';

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $button_text = trim($_POST['button_text'] ?? '');
    $stat1_text = trim($_POST['stat1_text'] ?? '');
    $stat1_desc = trim($_POST['stat1_desc'] ?? '');
    $stat2_text = trim($_POST['stat2_text'] ?? '');
    $stat2_desc = trim($_POST['stat2_desc'] ?? '');

    if ($title && $subtitle) {
        // Save to database or file (implement based on your needs)
        $message = '<div class="alert alert-success">Hero section berhasil disimpan!</div>';
    } else {
        $message = '<div class="alert alert-danger">Title dan subtitle wajib diisi!</div>';
    }
}

// Get current hero data (implement this based on your storage method)
$hero_data = [
    'title' => 'Perjalanan Suci Berkualitas, Biaya Bersahabat',
    'subtitle' => 'Jangan biarkan biaya menunda niat suci Anda. Paket Umroh terjangkau dengan tetap berkualitas layanan terbaik, mencakup akomodasi dan bimbingan yang profesional. Wujudkan ibadah khusyuk dan nyaman Anda, karena Umroh berkualitas kini bisa diakses oleh semua.',
    'button_text' => 'Lihat Paket Umroh',
    'stat1_text' => '24 Januri 2026',
    'stat1_desc' => 'Jadwal Berangkat',
    'stat2_text' => 'Program Pembiayaan',
    'stat2_desc' => 'Pembiayaan dana talangan Umrah'
];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Hero Section</h1>
                <a href="<?= e($base) ?>/" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Website
                </a>
            </div>

            <?= $message ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-image text-primary me-2"></i>
                        Edit Hero Section
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Utama *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= e($hero_data['title']) ?>" required>
                            <div class="form-text">Judul besar yang ditampilkan di hero section</div>
                        </div>

                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Subtitle *</label>
                            <textarea class="form-control" id="subtitle" name="subtitle" rows="4" required><?= e($hero_data['subtitle']) ?></textarea>
                            <div class="form-text">Deskripsi di bawah judul utama</div>
                        </div>

                        <div class="mb-3">
                            <label for="button_text" class="form-label">Teks Tombol</label>
                            <input type="text" class="form-control" id="button_text" name="button_text" 
                                   value="<?= e($hero_data['button_text']) ?>">
                            <div class="form-text">Teks pada tombol utama</div>
                        </div>

                        <hr>
                        <h6 class="mb-3">Statistik Hero</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stat1_text" class="form-label">Statistik 1 - Teks</label>
                                    <input type="text" class="form-control" id="stat1_text" name="stat1_text" 
                                           value="<?= e($hero_data['stat1_text']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="stat1_desc" class="form-label">Statistik 1 - Deskripsi</label>
                                    <input type="text" class="form-control" id="stat1_desc" name="stat1_desc" 
                                           value="<?= e($hero_data['stat1_desc']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="stat2_text" class="form-label">Statistik 2 - Teks</label>
                                    <input type="text" class="form-control" id="stat2_text" name="stat2_text" 
                                           value="<?= e($hero_data['stat2_text']) ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="stat2_desc" class="form-label">Statistik 2 - Deskripsi</label>
                                    <input type="text" class="form-control" id="stat2_desc" name="stat2_desc" 
                                           value="<?= e($hero_data['stat2_desc']) ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                            <a href="<?= e($base) ?>/admin/dashboard" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info me-2"></i>
                        Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="bg-light p-4 rounded">
                        <h2 class="text-primary"><?= e($hero_data['title']) ?></h2>
                        <p class="text-muted mb-3"><?= nl2br(e($hero_data['subtitle'])) ?></p>
                        <button class="btn btn-primary btn-sm mb-3" disabled>
                            <i class="fas fa-calendar-check"></i> <?= e($hero_data['button_text']) ?>
                        </button>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h5 class="text-success"><?= e($hero_data['stat1_text']) ?></h5>
                                    <small class="text-muted"><?= e($hero_data['stat1_desc']) ?></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="text-center">
                                    <h5 class="text-success"><?= e($hero_data['stat2_text']) ?></h5>
                                    <small class="text-muted"><?= e($hero_data['stat2_desc']) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>