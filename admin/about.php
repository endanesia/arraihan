<?php
require_once __DIR__ . '/header.php';

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content_p1 = trim($_POST['content_p1'] ?? '');
    $content_p2 = trim($_POST['content_p2'] ?? '');
    $content_p3 = trim($_POST['content_p3'] ?? '');
    $badge_number = trim($_POST['badge_number'] ?? '');
    $badge_text = trim($_POST['badge_text'] ?? '');

    if ($title && $content_p1) {
        // Save to database or file (implement based on your needs)
        $message = '<div class="alert alert-success">Tentang Kami berhasil disimpan!</div>';
    } else {
        $message = '<div class="alert alert-danger">Title dan konten paragraf 1 wajib diisi!</div>';
    }
}

// Get current about data (implement this based on your storage method)
$about_data = [
    'title' => 'Tentang Kami',
    'content_p1' => 'Raihan Travelindo resmi didirikan pada tahun 2005 oleh para pendiri yang memiliki visi mulia untuk membantu umat Islam menunaikan ibadah ke Tanah Suci. Pada awal berdirinya, perusahaan ini bergerak di bidang pariwisata, mulai dari tiket domestik dan penerbangan internasional.',
    'content_p2' => 'Setelah itu, kami mulai merambah ke bisnis layanan jasa travel umroh dan haji khusus. Nama Raihan Travelindo terinspirasi dari Ka\'bah yang merupakan rumah Allah SWT yang suci dan penuh berkah, dengan harapan dapat memberikan pelayanan terbaik yang penuh keberkahan kepada setiap jamaah.',
    'content_p3' => 'Kami telah mengantongi berbagai izin resmi dari Pemerintah RI. Hal ini merupakan bukti nyata keseriusan kami dalam memberikan layanan terbaik untuk para calon Tamu Allah SWT. Dengan pengalaman lebih dari 18 tahun dan telah melayani lebih dari 15.000 jamaah, kami berkomitmen untuk terus meningkatkan kualitas pelayanan.',
    'badge_number' => '15.000+',
    'badge_text' => 'Jamaah Terlayani'
];
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Tentang Kami</h1>
                <a href="<?= e($base) ?>/#tentang" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Website
                </a>
            </div>

            <?= $message ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Edit Tentang Kami
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Section *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= e($about_data['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="content_p1" class="form-label">Paragraf 1 *</label>
                            <textarea class="form-control" id="content_p1" name="content_p1" rows="4" required><?= e($about_data['content_p1']) ?></textarea>
                            <div class="form-text">Paragraf pembuka tentang sejarah perusahaan</div>
                        </div>

                        <div class="mb-3">
                            <label for="content_p2" class="form-label">Paragraf 2</label>
                            <textarea class="form-control" id="content_p2" name="content_p2" rows="4"><?= e($about_data['content_p2']) ?></textarea>
                            <div class="form-text">Paragraf tentang perkembangan dan filosofi nama</div>
                        </div>

                        <div class="mb-3">
                            <label for="content_p3" class="form-label">Paragraf 3</label>
                            <textarea class="form-control" id="content_p3" name="content_p3" rows="4"><?= e($about_data['content_p3']) ?></textarea>
                            <div class="form-text">Paragraf tentang legalitas dan pengalaman</div>
                        </div>

                        <hr>
                        <h6 class="mb-3">Badge Statistik</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="badge_number" class="form-label">Angka Badge</label>
                                    <input type="text" class="form-control" id="badge_number" name="badge_number" 
                                           value="<?= e($about_data['badge_number']) ?>">
                                    <div class="form-text">Contoh: 15.000+</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="badge_text" class="form-label">Teks Badge</label>
                                    <input type="text" class="form-control" id="badge_text" name="badge_text" 
                                           value="<?= e($about_data['badge_text']) ?>">
                                    <div class="form-text">Contoh: Jamaah Terlayani</div>
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
                        <i class="fas fa-eye text-info me-2"></i>
                        Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="bg-light p-4 rounded">
                        <h2 class="text-primary mb-4"><?= e($about_data['title']) ?></h2>
                        
                        <?php if ($about_data['content_p1']): ?>
                        <p class="mb-3"><?= nl2br(e($about_data['content_p1'])) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($about_data['content_p2']): ?>
                        <p class="mb-3"><?= nl2br(e($about_data['content_p2'])) ?></p>
                        <?php endif; ?>
                        
                        <?php if ($about_data['content_p3']): ?>
                        <p class="mb-4"><?= nl2br(e($about_data['content_p3'])) ?></p>
                        <?php endif; ?>

                        <?php if ($about_data['badge_number'] || $about_data['badge_text']): ?>
                        <div class="d-flex align-items-center gap-3 bg-white p-3 rounded border">
                            <i class="fas fa-users text-primary fs-3"></i>
                            <div>
                                <h4 class="text-primary mb-0"><?= e($about_data['badge_number']) ?></h4>
                                <small class="text-muted"><?= e($about_data['badge_text']) ?></small>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-certificate text-warning me-2"></i>
                        Fitur Keunggulan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-certificate text-success"></i>
                                <div>
                                    <strong>Izin PPIU Resmi</strong>
                                    <div class="small text-muted">Terdaftar Kementerian Agama RI</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-award text-success"></i>
                                <div>
                                    <strong>Izin PIHK Resmi</strong>
                                    <div class="small text-muted">Penyelenggara Ibadah Haji Khusus</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-shield-alt text-success"></i>
                                <div>
                                    <strong>Sertifikat ISO 9001:2015</strong>
                                    <div class="small text-muted">Sistem Manajemen Mutu Terjamin</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Fitur keunggulan ini ditampilkan secara otomatis di bagian Tentang Kami
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>