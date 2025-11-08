<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content_p1 = trim($_POST['content_p1'] ?? '');
    $content_p2 = trim($_POST['content_p2'] ?? '');
    $content_p3 = trim($_POST['content_p3'] ?? '');
    $badge_number = trim($_POST['badge_number'] ?? '');
    $badge_text = trim($_POST['badge_text'] ?? '');
    $image_path = '';

    if ($title && $content_p1) {
        // Handle image upload
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
            $f = $_FILES['about_image'];
            
            // Validate file type
            $allowed = ['image/jpeg'=>'.jpg','image/png'=>'.png','image/webp'=>'.webp','image/jpg'=>'.jpg'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detected_type = finfo_file($finfo, $f['tmp_name']);
            finfo_close($finfo);
            
            if (isset($allowed[$detected_type])) {
                $upload_dir = $config['app']['uploads_dir'];
                if (!is_dir($upload_dir)) {
                    @mkdir($upload_dir, 0755, true);
                }
                
                $ext = $allowed[$detected_type];
                $filename = 'about_'.date('Ymd_His').'_'.bin2hex(random_bytes(4)).$ext;
                $dest = rtrim($upload_dir,'/\\').DIRECTORY_SEPARATOR.$filename;
                
                // Include image resize utility
                require_once __DIR__ . '/../inc/image-utils.php';
                
                // Get image dimensions
                $imageInfo = getimagesize($f['tmp_name']);
                $originalWidth = $imageInfo[0] ?? 0;
                $originalHeight = $imageInfo[1] ?? 0;
                $originalSize = $f['size'];
                
                $maxWidth = 800;   // Smaller for about section
                $maxHeight = 600;  
                $maxFileSize = 1 * 1024 * 1024; // 1MB
                
                $needsResize = ($originalWidth > $maxWidth) || 
                              ($originalHeight > $maxHeight) || 
                              ($originalSize > $maxFileSize);
                
                if ($needsResize) {
                    if (resizeImage($f['tmp_name'], $dest, $maxWidth, $maxHeight, 85)) {
                        $image_path = rtrim($config['app']['uploads_url'],'/').'/'.$filename;
                    }
                } else {
                    if (move_uploaded_file($f['tmp_name'], $dest)) {
                        $image_path = rtrim($config['app']['uploads_url'],'/').'/'.$filename;
                    }
                }
            }
        }
        
        // Save to database using settings system
        set_setting('about_title', $title);
        set_setting('about_content_p1', $content_p1);
        set_setting('about_content_p2', $content_p2);
        set_setting('about_content_p3', $content_p3);
        set_setting('about_badge_number', $badge_number);
        set_setting('about_badge_text', $badge_text);
        
        // Only update image if new one was uploaded
        if ($image_path) {
            set_setting('about_image', $image_path);
        }
        
        $message = '<div class="alert alert-success">Tentang Kami berhasil disimpan!</div>';
    } else {
        $message = '<div class="alert alert-danger">Title dan konten paragraf 1 wajib diisi!</div>';
    }
}

// Get current about data from database
$about_data = [
    'title' => get_setting('about_title', 'Tentang Kami'),
    'content_p1' => get_setting('about_content_p1', 'Raihan Travelindo resmi didirikan pada tahun 2005 oleh para pendiri yang memiliki visi mulia untuk membantu umat Islam menunaikan ibadah ke Tanah Suci. Pada awal berdirinya, perusahaan ini bergerak di bidang pariwisata, mulai dari tiket domestik dan penerbangan internasional.'),
    'content_p2' => get_setting('about_content_p2', 'Setelah itu, kami mulai merambah ke bisnis layanan jasa travel umroh dan haji khusus. Nama Raihan Travelindo terinspirasi dari Ka\'bah yang merupakan rumah Allah SWT yang suci dan penuh berkah, dengan harapan dapat memberikan pelayanan terbaik yang penuh keberkahan kepada setiap jamaah.'),
    'content_p3' => get_setting('about_content_p3', 'Kami telah mengantongi berbagai izin resmi dari Pemerintah RI. Hal ini merupakan bukti nyata keseriusan kami dalam memberikan layanan terbaik untuk para calon Tamu Allah SWT. Dengan pengalaman lebih dari 18 tahun dan telah melayani lebih dari 15.000 jamaah, kami berkomitmen untuk terus meningkatkan kualitas pelayanan.'),
    'badge_number' => get_setting('about_badge_number', '15.000+'),
    'badge_text' => get_setting('about_badge_text', 'Jamaah Terlayani'),
    'image' => get_setting('about_image', '')
];

include __DIR__ . '/header.php';
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
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Section *</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= e($about_data['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="about_image" class="form-label">Gambar Tentang Kami</label>
                            <input type="file" class="form-control" id="about_image" name="about_image" accept="image/*">
                            <div class="form-text">
                                Format: JPEG, PNG, WebP. Gambar akan otomatis diperkecil jika lebih dari 800x600px.
                            </div>
                            <?php if (!empty($about_data['image'])): ?>
                            <div class="mt-2">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= e($about_data['image']) ?>" alt="Current image" class="img-thumbnail" style="max-width: 150px; max-height: 100px;">
                                    <div>
                                        <small class="text-muted">Gambar saat ini</small><br>
                                        <small class="text-info">Upload gambar baru untuk mengganti</small>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
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
                        <div class="row">
                            <div class="col-md-8">
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
                            
                            <?php if (!empty($about_data['image'])): ?>
                            <div class="col-md-4">
                                <div class="text-center">
                                    <img src="<?= e($about_data['image']) ?>" alt="<?= e($about_data['title']) ?>" 
                                         class="img-fluid rounded shadow" style="max-height: 300px;">
                                    <div class="mt-2">
                                        <small class="text-muted">Gambar Tentang Kami</small>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
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