<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check which form was submitted
    $is_keunggulan_form = isset($_POST['submit_keunggulan']);
    
    if ($is_keunggulan_form) {
        // Handle Keunggulan form submission
        $keunggulan_1_icon = trim($_POST['keunggulan_1_icon'] ?? '');
        $keunggulan_1_title = trim($_POST['keunggulan_1_title'] ?? '');
        $keunggulan_1_desc = trim($_POST['keunggulan_1_desc'] ?? '');
        
        $keunggulan_2_icon = trim($_POST['keunggulan_2_icon'] ?? '');
        $keunggulan_2_title = trim($_POST['keunggulan_2_title'] ?? '');
        $keunggulan_2_desc = trim($_POST['keunggulan_2_desc'] ?? '');
        
        $keunggulan_3_icon = trim($_POST['keunggulan_3_icon'] ?? '');
        $keunggulan_3_title = trim($_POST['keunggulan_3_title'] ?? '');
        $keunggulan_3_desc = trim($_POST['keunggulan_3_desc'] ?? '');
        
        // Save keunggulan data
        set_setting('about_keunggulan_1_icon', $keunggulan_1_icon);
        set_setting('about_keunggulan_1_title', $keunggulan_1_title);
        set_setting('about_keunggulan_1_desc', $keunggulan_1_desc);
        
        set_setting('about_keunggulan_2_icon', $keunggulan_2_icon);
        set_setting('about_keunggulan_2_title', $keunggulan_2_title);
        set_setting('about_keunggulan_2_desc', $keunggulan_2_desc);
        
        set_setting('about_keunggulan_3_icon', $keunggulan_3_icon);
        set_setting('about_keunggulan_3_title', $keunggulan_3_title);
        set_setting('about_keunggulan_3_desc', $keunggulan_3_desc);
        
        $message = '<div class="alert alert-success">Fitur Keunggulan berhasil disimpan!</div>';
    } else {
        // Handle About form submission
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
}

// Get about images for slideshow
$about_images = [];
if (function_exists('db') && db()) {
    try {
        $table_check = db()->query("SHOW TABLES LIKE 'about_images'");
        if ($table_check && $table_check->num_rows > 0) {
            if ($res = db()->query("SELECT * FROM about_images WHERE is_active = 1 ORDER BY sort_order ASC, id ASC")) {
                while ($row = $res->fetch_assoc()) {
                    $about_images[] = $row;
                }
            }
        }
    } catch (Exception $e) {
        $about_images = [];
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
    'image' => get_setting('about_image', ''),
    'keunggulan_1_icon' => get_setting('about_keunggulan_1_icon', 'fas fa-certificate'),
    'keunggulan_1_title' => get_setting('about_keunggulan_1_title', 'Izin PPIU Resmi'),
    'keunggulan_1_desc' => get_setting('about_keunggulan_1_desc', 'Terdaftar Kementerian Agama RI'),
    'keunggulan_2_icon' => get_setting('about_keunggulan_2_icon', 'fas fa-award'),
    'keunggulan_2_title' => get_setting('about_keunggulan_2_title', 'Izin PIHK Resmi'),
    'keunggulan_2_desc' => get_setting('about_keunggulan_2_desc', 'Penyelenggara Ibadah Haji Khusus'),
    'keunggulan_3_icon' => get_setting('about_keunggulan_3_icon', 'fas fa-shield-alt'),
    'keunggulan_3_title' => get_setting('about_keunggulan_3_title', 'Sertifikat ISO 9001:2015'),
    'keunggulan_3_desc' => get_setting('about_keunggulan_3_desc', 'Sistem Manajemen Mutu Terjamin')
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
                            <label class="form-label">Gambar Tentang Kami</label>
                            
                            <!-- Multi-Image Slideshow Management -->
                            <div class="alert alert-info d-flex align-items-center">
                                <i class="fas fa-images me-2"></i>
                                <div class="flex-grow-1">
                                    <strong>Slideshow Multiple Gambar</strong>
                                    <div class="small">Kelola multiple gambar untuk slideshow di section Tentang Kami</div>
                                </div>
                                <div>
                                    <a href="<?= e($base) ?>/admin/about-images" class="btn btn-sm btn-primary">
                                        <i class="fas fa-cog"></i> Kelola Slideshow
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Legacy Single Image (Fallback) -->
                            <div class="border rounded p-3 bg-light">
                                <label for="about_image" class="form-label">
                                    <small>Gambar Tunggal (Fallback)</small>
                                </label>
                                <input type="file" class="form-control" id="about_image" name="about_image" accept="image/*">
                                <div class="form-text">
                                    <small>
                                        <i class="fas fa-info-circle"></i>
                                        Gambar ini hanya digunakan jika tidak ada gambar slideshow. 
                                        Format: JPEG, PNG, WebP.
                                    </small>
                                </div>
                                <?php if (!empty($about_data['image'])): ?>
                                <div class="mt-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= e($about_data['image']) ?>" alt="Current fallback image" class="img-thumbnail" style="max-width: 100px; max-height: 70px;">
                                        <div>
                                            <small class="text-muted">Gambar fallback saat ini</small><br>
                                            <small class="text-info">Upload gambar baru untuk mengganti</small>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
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
                            
                            <div class="col-md-4">
                                <?php if (!empty($about_images)): ?>
                                <!-- Slideshow Preview -->
                                <div class="text-center">
                                    <div class="bg-primary text-white p-2 rounded-top">
                                        <i class="fas fa-images"></i> Slideshow (<?= count($about_images) ?> gambar)
                                    </div>
                                    <div class="border border-top-0 p-3 rounded-bottom">
                                        <div class="row g-2">
                                            <?php foreach (array_slice($about_images, 0, 4) as $img): ?>
                                            <div class="col-6">
                                                <img src="<?= $base ?>images/<?= e($img['image_path']) ?>" 
                                                     alt="<?= e($img['title']) ?>" 
                                                     class="img-thumbnail w-100" 
                                                     style="height: 60px; object-fit: cover;">
                                            </div>
                                            <?php endforeach; ?>
                                            <?php if (count($about_images) > 4): ?>
                                            <div class="col-12">
                                                <small class="text-muted">+<?= count($about_images) - 4 ?> gambar lainnya</small>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mt-2">
                                            <a href="<?= e($base) ?>/admin/about-images" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-cog"></i> Kelola
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php elseif (!empty($about_data['image'])): ?>
                                <!-- Single Image Fallback -->
                                <div class="text-center">
                                    <div class="bg-warning text-dark p-2 rounded-top">
                                        <i class="fas fa-image"></i> Gambar Tunggal (Fallback)
                                    </div>
                                    <img src="<?= e($about_data['image']) ?>" alt="<?= e($about_data['title']) ?>" 
                                         class="img-fluid rounded-bottom shadow" style="max-height: 300px;">
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle"></i>
                                            Tambah gambar slideshow untuk tampilan yang lebih menarik
                                        </small>
                                    </div>
                                </div>
                                <?php else: ?>
                                <!-- No Images -->
                                <div class="text-center">
                                    <div class="bg-secondary text-white p-3 rounded">
                                        <i class="fas fa-image fa-2x mb-2"></i>
                                        <div>Belum ada gambar</div>
                                        <small>Upload gambar slideshow atau gambar fallback</small>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
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
                    <p class="text-muted mb-4">
                        <i class="fas fa-info-circle"></i>
                        Kelola 3 fitur keunggulan yang akan ditampilkan di bagian Tentang Kami di homepage
                    </p>
                    
                    <form method="post">
                        <input type="hidden" name="submit_keunggulan" value="1">
                        <!-- Keunggulan 1 -->
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-primary mb-3">Keunggulan 1</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="keunggulan_1_icon" class="form-label">Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i id="icon_preview_1" class="<?= e($about_data['keunggulan_1_icon']) ?>"></i></span>
                                            <select class="form-select" id="keunggulan_1_icon" name="keunggulan_1_icon" onchange="document.getElementById('icon_preview_1').className=this.value">
                                                <option value="fas fa-certificate" <?= $about_data['keunggulan_1_icon'] == 'fas fa-certificate' ? 'selected' : '' ?>>Sertifikat</option>
                                                <option value="fas fa-award" <?= $about_data['keunggulan_1_icon'] == 'fas fa-award' ? 'selected' : '' ?>>Penghargaan</option>
                                                <option value="fas fa-shield-alt" <?= $about_data['keunggulan_1_icon'] == 'fas fa-shield-alt' ? 'selected' : '' ?>>Perlindungan</option>
                                                <option value="fas fa-check-circle" <?= $about_data['keunggulan_1_icon'] == 'fas fa-check-circle' ? 'selected' : '' ?>>Centang</option>
                                                <option value="fas fa-star" <?= $about_data['keunggulan_1_icon'] == 'fas fa-star' ? 'selected' : '' ?>>Bintang</option>
                                                <option value="fas fa-thumbs-up" <?= $about_data['keunggulan_1_icon'] == 'fas fa-thumbs-up' ? 'selected' : '' ?>>Jempol</option>
                                                <option value="fas fa-medal" <?= $about_data['keunggulan_1_icon'] == 'fas fa-medal' ? 'selected' : '' ?>>Medali</option>
                                                <option value="fas fa-trophy" <?= $about_data['keunggulan_1_icon'] == 'fas fa-trophy' ? 'selected' : '' ?>>Trofi</option>
                                                <option value="fas fa-crown" <?= $about_data['keunggulan_1_icon'] == 'fas fa-crown' ? 'selected' : '' ?>>Mahkota</option>
                                                <option value="fas fa-gem" <?= $about_data['keunggulan_1_icon'] == 'fas fa-gem' ? 'selected' : '' ?>>Permata</option>
                                                <option value="fas fa-heart" <?= $about_data['keunggulan_1_icon'] == 'fas fa-heart' ? 'selected' : '' ?>>Hati</option>
                                                <option value="fas fa-handshake" <?= $about_data['keunggulan_1_icon'] == 'fas fa-handshake' ? 'selected' : '' ?>>Jabat Tangan</option>
                                                <option value="fas fa-user-shield" <?= $about_data['keunggulan_1_icon'] == 'fas fa-user-shield' ? 'selected' : '' ?>>Perlindungan User</option>
                                                <option value="fas fa-globe" <?= $about_data['keunggulan_1_icon'] == 'fas fa-globe' ? 'selected' : '' ?>>Dunia</option>
                                                <option value="fas fa-plane" <?= $about_data['keunggulan_1_icon'] == 'fas fa-plane' ? 'selected' : '' ?>>Pesawat</option>
                                                <option value="fas fa-kaaba" <?= $about_data['keunggulan_1_icon'] == 'fas fa-kaaba' ? 'selected' : '' ?>>Ka'bah</option>
                                                <option value="fas fa-mosque" <?= $about_data['keunggulan_1_icon'] == 'fas fa-mosque' ? 'selected' : '' ?>>Masjid</option>
                                                <option value="fas fa-praying-hands" <?= $about_data['keunggulan_1_icon'] == 'fas fa-praying-hands' ? 'selected' : '' ?>>Berdoa</option>
                                                <option value="fas fa-book-quran" <?= $about_data['keunggulan_1_icon'] == 'fas fa-book-quran' ? 'selected' : '' ?>>Al-Quran</option>
                                                <option value="fas fa-users" <?= $about_data['keunggulan_1_icon'] == 'fas fa-users' ? 'selected' : '' ?>>Pengguna</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="keunggulan_1_title" class="form-label">Judul</label>
                                        <input type="text" class="form-control" id="keunggulan_1_title" name="keunggulan_1_title" 
                                               value="<?= e($about_data['keunggulan_1_title']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="keunggulan_1_desc" class="form-label">Deskripsi</label>
                                        <input type="text" class="form-control" id="keunggulan_1_desc" name="keunggulan_1_desc" 
                                               value="<?= e($about_data['keunggulan_1_desc']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keunggulan 2 -->
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-primary mb-3">Keunggulan 2</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="keunggulan_2_icon" class="form-label">Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i id="icon_preview_2" class="<?= e($about_data['keunggulan_2_icon']) ?>"></i></span>
                                            <select class="form-select" id="keunggulan_2_icon" name="keunggulan_2_icon" onchange="document.getElementById('icon_preview_2').className=this.value">
                                                <option value="fas fa-certificate" <?= $about_data['keunggulan_2_icon'] == 'fas fa-certificate' ? 'selected' : '' ?>>Sertifikat</option>
                                                <option value="fas fa-award" <?= $about_data['keunggulan_2_icon'] == 'fas fa-award' ? 'selected' : '' ?>>Penghargaan</option>
                                                <option value="fas fa-shield-alt" <?= $about_data['keunggulan_2_icon'] == 'fas fa-shield-alt' ? 'selected' : '' ?>>Perlindungan</option>
                                                <option value="fas fa-check-circle" <?= $about_data['keunggulan_2_icon'] == 'fas fa-check-circle' ? 'selected' : '' ?>>Centang</option>
                                                <option value="fas fa-star" <?= $about_data['keunggulan_2_icon'] == 'fas fa-star' ? 'selected' : '' ?>>Bintang</option>
                                                <option value="fas fa-thumbs-up" <?= $about_data['keunggulan_2_icon'] == 'fas fa-thumbs-up' ? 'selected' : '' ?>>Jempol</option>
                                                <option value="fas fa-medal" <?= $about_data['keunggulan_2_icon'] == 'fas fa-medal' ? 'selected' : '' ?>>Medali</option>
                                                <option value="fas fa-trophy" <?= $about_data['keunggulan_2_icon'] == 'fas fa-trophy' ? 'selected' : '' ?>>Trofi</option>
                                                <option value="fas fa-crown" <?= $about_data['keunggulan_2_icon'] == 'fas fa-crown' ? 'selected' : '' ?>>Mahkota</option>
                                                <option value="fas fa-gem" <?= $about_data['keunggulan_2_icon'] == 'fas fa-gem' ? 'selected' : '' ?>>Permata</option>
                                                <option value="fas fa-heart" <?= $about_data['keunggulan_2_icon'] == 'fas fa-heart' ? 'selected' : '' ?>>Hati</option>
                                                <option value="fas fa-handshake" <?= $about_data['keunggulan_2_icon'] == 'fas fa-handshake' ? 'selected' : '' ?>>Jabat Tangan</option>
                                                <option value="fas fa-user-shield" <?= $about_data['keunggulan_2_icon'] == 'fas fa-user-shield' ? 'selected' : '' ?>>Perlindungan User</option>
                                                <option value="fas fa-globe" <?= $about_data['keunggulan_2_icon'] == 'fas fa-globe' ? 'selected' : '' ?>>Dunia</option>
                                                <option value="fas fa-plane" <?= $about_data['keunggulan_2_icon'] == 'fas fa-plane' ? 'selected' : '' ?>>Pesawat</option>
                                                <option value="fas fa-kaaba" <?= $about_data['keunggulan_2_icon'] == 'fas fa-kaaba' ? 'selected' : '' ?>>Ka'bah</option>
                                                <option value="fas fa-mosque" <?= $about_data['keunggulan_2_icon'] == 'fas fa-mosque' ? 'selected' : '' ?>>Masjid</option>
                                                <option value="fas fa-praying-hands" <?= $about_data['keunggulan_2_icon'] == 'fas fa-praying-hands' ? 'selected' : '' ?>>Berdoa</option>
                                                <option value="fas fa-book-quran" <?= $about_data['keunggulan_2_icon'] == 'fas fa-book-quran' ? 'selected' : '' ?>>Al-Quran</option>
                                                <option value="fas fa-users" <?= $about_data['keunggulan_2_icon'] == 'fas fa-users' ? 'selected' : '' ?>>Pengguna</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="keunggulan_2_title" class="form-label">Judul</label>
                                        <input type="text" class="form-control" id="keunggulan_2_title" name="keunggulan_2_title" 
                                               value="<?= e($about_data['keunggulan_2_title']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="keunggulan_2_desc" class="form-label">Deskripsi</label>
                                        <input type="text" class="form-control" id="keunggulan_2_desc" name="keunggulan_2_desc" 
                                               value="<?= e($about_data['keunggulan_2_desc']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Keunggulan 3 -->
                        <div class="border rounded p-3 mb-3">
                            <h6 class="text-primary mb-3">Keunggulan 3</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="keunggulan_3_icon" class="form-label">Icon</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i id="icon_preview_3" class="<?= e($about_data['keunggulan_3_icon']) ?>"></i></span>
                                            <select class="form-select" id="keunggulan_3_icon" name="keunggulan_3_icon" onchange="document.getElementById('icon_preview_3').className=this.value">
                                                <option value="fas fa-certificate" <?= $about_data['keunggulan_3_icon'] == 'fas fa-certificate' ? 'selected' : '' ?>>Sertifikat</option>
                                                <option value="fas fa-award" <?= $about_data['keunggulan_3_icon'] == 'fas fa-award' ? 'selected' : '' ?>>Penghargaan</option>
                                                <option value="fas fa-shield-alt" <?= $about_data['keunggulan_3_icon'] == 'fas fa-shield-alt' ? 'selected' : '' ?>>Perlindungan</option>
                                                <option value="fas fa-check-circle" <?= $about_data['keunggulan_3_icon'] == 'fas fa-check-circle' ? 'selected' : '' ?>>Centang</option>
                                                <option value="fas fa-star" <?= $about_data['keunggulan_3_icon'] == 'fas fa-star' ? 'selected' : '' ?>>Bintang</option>
                                                <option value="fas fa-thumbs-up" <?= $about_data['keunggulan_3_icon'] == 'fas fa-thumbs-up' ? 'selected' : '' ?>>Jempol</option>
                                                <option value="fas fa-medal" <?= $about_data['keunggulan_3_icon'] == 'fas fa-medal' ? 'selected' : '' ?>>Medali</option>
                                                <option value="fas fa-trophy" <?= $about_data['keunggulan_3_icon'] == 'fas fa-trophy' ? 'selected' : '' ?>>Trofi</option>
                                                <option value="fas fa-crown" <?= $about_data['keunggulan_3_icon'] == 'fas fa-crown' ? 'selected' : '' ?>>Mahkota</option>
                                                <option value="fas fa-gem" <?= $about_data['keunggulan_3_icon'] == 'fas fa-gem' ? 'selected' : '' ?>>Permata</option>
                                                <option value="fas fa-heart" <?= $about_data['keunggulan_3_icon'] == 'fas fa-heart' ? 'selected' : '' ?>>Hati</option>
                                                <option value="fas fa-handshake" <?= $about_data['keunggulan_3_icon'] == 'fas fa-handshake' ? 'selected' : '' ?>>Jabat Tangan</option>
                                                <option value="fas fa-user-shield" <?= $about_data['keunggulan_3_icon'] == 'fas fa-user-shield' ? 'selected' : '' ?>>Perlindungan User</option>
                                                <option value="fas fa-globe" <?= $about_data['keunggulan_3_icon'] == 'fas fa-globe' ? 'selected' : '' ?>>Dunia</option>
                                                <option value="fas fa-plane" <?= $about_data['keunggulan_3_icon'] == 'fas fa-plane' ? 'selected' : '' ?>>Pesawat</option>
                                                <option value="fas fa-kaaba" <?= $about_data['keunggulan_3_icon'] == 'fas fa-kaaba' ? 'selected' : '' ?>>Ka'bah</option>
                                                <option value="fas fa-mosque" <?= $about_data['keunggulan_3_icon'] == 'fas fa-mosque' ? 'selected' : '' ?>>Masjid</option>
                                                <option value="fas fa-praying-hands" <?= $about_data['keunggulan_3_icon'] == 'fas fa-praying-hands' ? 'selected' : '' ?>>Berdoa</option>
                                                <option value="fas fa-book-quran" <?= $about_data['keunggulan_3_icon'] == 'fas fa-book-quran' ? 'selected' : '' ?>>Al-Quran</option>
                                                <option value="fas fa-users" <?= $about_data['keunggulan_3_icon'] == 'fas fa-users' ? 'selected' : '' ?>>Pengguna</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="keunggulan_3_title" class="form-label">Judul</label>
                                        <input type="text" class="form-control" id="keunggulan_3_title" name="keunggulan_3_title" 
                                               value="<?= e($about_data['keunggulan_3_title']) ?>">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="mb-3">
                                        <label for="keunggulan_3_desc" class="form-label">Deskripsi</label>
                                        <input type="text" class="form-control" id="keunggulan_3_desc" name="keunggulan_3_desc" 
                                               value="<?= e($about_data['keunggulan_3_desc']) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Keunggulan
                            </button>
                        </div>
                    </form>
                    
                    <hr class="my-4">
                    
                    <h6 class="mb-3">Preview Keunggulan</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="<?= e($about_data['keunggulan_1_icon']) ?> text-success"></i>
                                <div>
                                    <strong><?= e($about_data['keunggulan_1_title']) ?></strong>
                                    <div class="small text-muted"><?= e($about_data['keunggulan_1_desc']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="<?= e($about_data['keunggulan_2_icon']) ?> text-success"></i>
                                <div>
                                    <strong><?= e($about_data['keunggulan_2_title']) ?></strong>
                                    <div class="small text-muted"><?= e($about_data['keunggulan_2_desc']) ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center gap-2">
                                <i class="<?= e($about_data['keunggulan_3_icon']) ?> text-success"></i>
                                <div>
                                    <strong><?= e($about_data['keunggulan_3_title']) ?></strong>
                                    <div class="small text-muted"><?= e($about_data['keunggulan_3_desc']) ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>      </div>
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