<?php
require_once __DIR__ . '/header.php';

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $greeting_title = trim($_POST['greeting_title'] ?? '');
    $greeting_subtitle = trim($_POST['greeting_subtitle'] ?? '');
    $greeting_text = trim($_POST['greeting_text'] ?? '');
    $greeting_stats_title = trim($_POST['greeting_stats_title'] ?? '');
    $greeting_button_text = trim($_POST['greeting_button_text'] ?? '');
    $greeting_button_link = trim($_POST['greeting_button_link'] ?? '');
    
    // Handle background image upload
    $greeting_background = get_setting('greeting_background', '');
    if (isset($_FILES['greeting_background']) && $_FILES['greeting_background']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../images/';
        $file = $_FILES['greeting_background'];
        $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (in_array($file_extension, $allowed_types)) {
            $filename = 'greeting-bg-' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            // Delete old background image if exists
            if ($greeting_background && file_exists('../' . $greeting_background)) {
                unlink('../' . $greeting_background);
            }
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $greeting_background = 'images/' . $filename;
            }
        }
    }

    if ($greeting_title && $greeting_subtitle) {
        try {
            // Ensure settings table exists
            ensure_settings_table();
            
            // Save all greeting settings
            set_setting('greeting_title', $greeting_title);
            set_setting('greeting_subtitle', $greeting_subtitle);
            set_setting('greeting_text', $greeting_text);
            set_setting('greeting_stats_title', $greeting_stats_title);
            set_setting('greeting_button_text', $greeting_button_text);
            set_setting('greeting_button_link', $greeting_button_link);
            
            if ($greeting_background) {
                set_setting('greeting_background', $greeting_background);
            }
            
            $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Greeting section berhasil disimpan!</div>';
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error saving: ' . e($e->getMessage()) . '</div>';
        }
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Title dan subtitle wajib diisi!</div>';
    }
}

// Get current greeting data from database
try {
    ensure_settings_table();
    
    $greeting_data = [
        'title' => get_setting('greeting_title', 'Selamat Datang di Raihan Travelindo'),
        'subtitle' => get_setting('greeting_subtitle', 'Travel Haji & Umroh Terpercaya'),
        'text' => get_setting('greeting_text', 'Kami adalah travel haji dan umroh yang telah berpengalaman melayani jamaah sejak tahun 2005. Dengan layanan profesional dan paket yang terjangkau, kami siap mengantarkan Anda menuju tanah suci.'),
        'stats_title' => get_setting('greeting_stats_title', 'Kepercayaan Jamaah'),
        'button_text' => get_setting('greeting_button_text', 'Pelajari Lebih Lanjut'),
        'button_link' => get_setting('greeting_button_link', '#paket'),
        'background' => get_setting('greeting_background', '/images/greeting-bg.jpg')
    ];
} catch (Exception $e) {
    // Fallback to default values if database error
    $greeting_data = [
        'title' => 'Selamat Datang di Raihan Travelindo',
        'subtitle' => 'Travel Haji & Umroh Terpercaya',
        'text' => 'Kami adalah travel haji dan umroh yang telah berpengalaman melayani jamaah sejak tahun 2005. Dengan layanan profesional dan paket yang terjangkau, kami siap mengantarkan Anda menuju tanah suci.',
        'stats_title' => 'Kepercayaan Jamaah',
        'button_text' => 'Pelajari Lebih Lanjut',
        'button_link' => '#paket',
        'background' => '/images/greeting-bg.jpg'
    ];
    $message = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Database error: ' . e($e->getMessage()) . '. Using default values.</div>';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Greeting Section</h1>
                <a href="<?= e($base) ?>/" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Website
                </a>
            </div>

            <?= $message ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-handshake text-primary me-2"></i>
                        Edit Greeting Section
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="greeting_background" class="form-label">Background Image</label>
                            <input type="file" class="form-control" id="greeting_background" name="greeting_background" accept="image/*">
                            <div class="form-text">Upload gambar background greeting (JPG, PNG, WEBP). Ukuran yang disarankan: 1920x1080px</div>
                            <?php if ($greeting_data['background']): ?>
                            <div class="mt-2">
                                <small class="text-muted">Background saat ini:</small><br>
                                <img src="<?= e($greeting_data['background']) ?>" alt="Current background" class="img-thumbnail" style="max-width: 200px; max-height: 100px;">
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="greeting_title" class="form-label">Judul Utama *</label>
                            <input type="text" class="form-control" id="greeting_title" name="greeting_title" 
                                   value="<?= e($greeting_data['title']) ?>" required>
                            <div class="form-text">Judul utama greeting section</div>
                        </div>

                        <div class="mb-3">
                            <label for="greeting_subtitle" class="form-label">Subtitle *</label>
                            <input type="text" class="form-control" id="greeting_subtitle" name="greeting_subtitle" 
                                   value="<?= e($greeting_data['subtitle']) ?>" required>
                            <div class="form-text">Subtitle di bawah judul utama</div>
                        </div>

                        <div class="mb-3">
                            <label for="greeting_text" class="form-label">Teks Deskripsi</label>
                            <textarea class="form-control" id="greeting_text" name="greeting_text" rows="4"><?= e($greeting_data['text']) ?></textarea>
                            <div class="form-text">Deskripsi panjang tentang perusahaan</div>
                        </div>

                        <div class="mb-3">
                            <label for="greeting_stats_title" class="form-label">Judul Statistik</label>
                            <input type="text" class="form-control" id="greeting_stats_title" name="greeting_stats_title" 
                                   value="<?= e($greeting_data['stats_title']) ?>">
                            <div class="form-text">Judul untuk section statistik (opsional)</div>
                        </div>

                        <hr>
                        <h6 class="mb-3">Tombol Call-to-Action</h6>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="greeting_button_text" class="form-label">Teks Tombol</label>
                                    <input type="text" class="form-control" id="greeting_button_text" name="greeting_button_text" 
                                           value="<?= e($greeting_data['button_text']) ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="greeting_button_link" class="form-label">Link Tombol</label>
                                    <input type="text" class="form-control" id="greeting_button_link" name="greeting_button_link" 
                                           value="<?= e($greeting_data['button_link']) ?>" placeholder="#paket">
                                    <div class="form-text">Link tujuan tombol (misal: #paket, /kontak, dll)</div>
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
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>