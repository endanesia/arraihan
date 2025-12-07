<?php
require_once __DIR__ . '/../inc/db.php';
require_once __DIR__ . '/auth.php';

// Handle delete video request
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['confirm'])) {
    // Get current video path
    $result = db()->query("SELECT video_path FROM hero_video WHERE id = 1");
    if ($result && $row = $result->fetch_assoc()) {
        if (!empty($row['video_path'])) {
            $filePath = __DIR__ . '/../' . $row['video_path'];
            
            // Delete file from server
            if (file_exists($filePath)) {
                if (unlink($filePath)) {
                    // Update database - clear video_path
                    db()->query("UPDATE hero_video SET video_path = '', is_active = 0 WHERE id = 1");
                    $success = 'Video berhasil dihapus dari server!';
                } else {
                    $error = 'Gagal menghapus file video dari server!';
                }
            } else {
                // File doesn't exist, just clear database
                db()->query("UPDATE hero_video SET video_path = '', is_active = 0 WHERE id = 1");
                $success = 'Data video berhasil dihapus!';
            }
            
            // Redirect to refresh page
            header("Location: hero-video.php?msg=" . urlencode($success ?: $error));
            exit;
        }
    }
}

// Handle form submission
$success = '';
$error = '';

// Check for message from redirect
if (isset($_GET['msg'])) {
    $success = $_GET['msg'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Validasi
    if (empty($title)) {
        $error = 'Judul harus diisi!';
    } else {
        // Check if uploading new video
        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['video_file'];
            
            // Get server upload limit
            $phpMaxUpload = ini_get('upload_max_filesize');
            $phpMaxUploadBytes = (int)$phpMaxUpload * 1024 * 1024; // Convert to bytes
            
            // Use the smaller of our limit or server limit
            $ourMaxSize = 2 * 1024 * 1024; // 2MB in bytes
            $maxSize = min($ourMaxSize, $phpMaxUploadBytes);
            $maxSizeMB = round($maxSize / (1024 * 1024), 1);
            
            if ($file['size'] > $maxSize) {
                $error = "Ukuran file video maksimal {$maxSizeMB}MB! File Anda: " . round($file['size'] / (1024 * 1024), 2) . "MB";
            } else {
                // Validasi tipe file
                $allowedTypes = ['video/mp4', 'video/webm', 'video/ogg'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);
                
                if (!in_array($mimeType, $allowedTypes)) {
                    $error = 'Hanya file video MP4, WebM, atau OGG yang diperbolehkan!';
                } else {
                    // Generate unique filename
                    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $newFileName = 'hero-video-' . time() . '.' . $extension;
                    $uploadDir = __DIR__ . '/../images/videos/';
                    
                    // Buat direktori jika belum ada dengan permission yang benar
                    if (!is_dir($uploadDir)) {
                        if (!mkdir($uploadDir, 0755, true)) {
                            $error = 'Gagal membuat direktori upload! Periksa permission folder images/';
                            error_log("Failed to create directory: $uploadDir");
                        }
                    }
                    
                    // Check if directory is writable
                    if (!is_writable($uploadDir)) {
                        $error = 'Direktori upload tidak dapat ditulis! Jalankan: chmod 755 images/videos/';
                        error_log("Directory not writable: $uploadDir");
                    }
                    
                    if (empty($error)) {
                        $uploadPath = $uploadDir . $newFileName;
                        
                        // Upload file
                        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                            // Verify file was actually uploaded
                            if (!file_exists($uploadPath)) {
                                $error = 'File gagal tersimpan di server! Path: ' . $uploadPath;
                                error_log("File not found after upload: $uploadPath");
                            } else {
                                $videoPath = 'images/videos/' . $newFileName;
                                
                                // Delete old video if exists
                                $oldVideo = db()->query("SELECT video_path FROM hero_video WHERE id = 1");
                                if ($oldVideo && $row = $oldVideo->fetch_assoc()) {
                                    if (!empty($row['video_path']) && file_exists(__DIR__ . '/../' . $row['video_path'])) {
                                        @unlink(__DIR__ . '/../' . $row['video_path']);
                                    }
                                }
                                
                                // Update database
                                $stmt = db()->prepare("UPDATE hero_video SET video_path = ?, title = ?, description = ?, is_active = ? WHERE id = 1");
                                $stmt->bind_param('sssi', $videoPath, $title, $description, $is_active);
                                
                                if ($stmt->execute()) {
                                    $success = 'Video hero berhasil diupload dan disimpan!';
                                } else {
                                    $error = 'Gagal menyimpan ke database! Error: ' . db()->error;
                                    error_log("Database error: " . db()->error);
                                    // Delete uploaded file if database update fails
                                    if (file_exists($uploadPath)) {
                                        @unlink($uploadPath);
                                    }
                                }
                            }
                        } else {
                            $error = 'Gagal mengupload file! Error code: ' . $_FILES['video_file']['error'];
                            error_log("Upload failed. Error: " . $_FILES['video_file']['error'] . ", Tmp: " . $file['tmp_name'] . ", Dest: " . $uploadPath);
                        }
                    }
                }
            }
        } else if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Handle upload errors
            $serverMax = ini_get('upload_max_filesize');
            $maxDisplay = min(2, (int)$serverMax);
            switch ($_FILES['video_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $error = "File terlalu besar! Maksimal {$maxDisplay}MB (Server limit: {$serverMax})";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $error = 'File hanya terupload sebagian. Coba lagi!';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $error = 'Temporary folder tidak ditemukan di server!';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $error = 'Gagal menulis file ke disk!';
                    break;
                default:
                    $error = 'Error upload: ' . $_FILES['video_file']['error'];
            }
        } else {
            // Update without changing video
            $stmt = db()->prepare("UPDATE hero_video SET title = ?, description = ?, is_active = ? WHERE id = 1");
            $stmt->bind_param('ssi', $title, $description, $is_active);
            
            if ($stmt->execute()) {
                $success = 'Informasi video berhasil diupdate!';
            } else {
                $error = 'Gagal menyimpan ke database!';
            }
        }
    }
}

// Fetch current video data
$video = null;
$result = db()->query("SELECT * FROM hero_video WHERE id = 1");
if ($result && $result->num_rows > 0) {
    $video = $result->fetch_assoc();
}

// Check directory permissions
$uploadDir = __DIR__ . '/../images/videos/';
$dirExists = is_dir($uploadDir);
$dirWritable = $dirExists && is_writable($uploadDir);

$page_title = 'Hero Video';
$current_page = 'hero-video';
require_once __DIR__ . '/header.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1><i class="fas fa-video"></i> Hero Video</h1>
        <?php
        $serverMaxUpload = ini_get('upload_max_filesize');
        $serverMaxMB = (int)$serverMaxUpload;
        $displayMaxMB = min(2, $serverMaxMB);
        ?>
        <p>Upload dan kelola video yang ditampilkan setelah hero slideshow (Maksimal <?= $displayMaxMB ?>MB)</p>
        <?php if ($serverMaxMB < 2): ?>
        <div class="alert alert-info" style="margin-top: 10px;">
            <i class="fas fa-info-circle"></i> 
            <strong>Info:</strong> Server limit upload: <?= $serverMaxUpload ?>. 
            Hubungi hosting untuk menaikkan jika perlu file lebih besar.
        </div>
        <?php endif; ?>
    </div>

    <?php if (!$dirExists): ?>
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> 
        <strong>Perhatian:</strong> Direktori <code>images/videos/</code> belum ada. 
        Akan dibuat otomatis saat upload pertama.
    </div>
    <?php elseif (!$dirWritable): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> 
        <strong>Error:</strong> Direktori <code>images/videos/</code> tidak dapat ditulis!<br>
        Jalankan command: <code>chmod 755 <?= $uploadDir ?></code>
    </div>
    <?php endif; ?>

    <?php if ($success): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-edit"></i> Kelola Hero Video</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Judul Video *</label>
                    <input type="text" 
                           class="form-control" 
                           id="title" 
                           name="title" 
                           value="<?= htmlspecialchars($video['title'] ?? '') ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label for="description">Deskripsi</label>
                    <textarea class="form-control" 
                              id="description" 
                              name="description" 
                              rows="3"><?= htmlspecialchars($video['description'] ?? '') ?></textarea>
                </div>

                <?php if ($video && !empty($video['video_path'])): ?>
                <div class="form-group">
                    <label>Video Saat Ini</label>
                    <div class="current-video">
                        <video controls style="max-width: 100%; max-height: 400px; border-radius: 8px;">
                            <source src="../<?= htmlspecialchars($video['video_path']) ?>" type="video/<?= pathinfo($video['video_path'], PATHINFO_EXTENSION) ?>">
                            Browser Anda tidak mendukung video HTML5.
                        </video>
                        <p class="text-muted mt-2">
                            <i class="fas fa-file-video"></i> 
                            File: <?= basename($video['video_path']) ?>
                            <?php
                            $filePath = __DIR__ . '/../' . $video['video_path'];
                            if (file_exists($filePath)) {
                                $fileSize = filesize($filePath);
                                $fileSizeMB = number_format($fileSize / (1024 * 1024), 2);
                                echo " ({$fileSizeMB} MB)";
                            }
                            ?>
                        </p>
                        <button type="button" class="btn btn-danger btn-sm mt-2" onclick="confirmDeleteVideo()">
                            <i class="fas fa-trash"></i> Hapus Video dari Server
                        </button>
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="video_file">
                        <?= ($video && !empty($video['video_path'])) ? 'Ganti Video (Opsional)' : 'Upload Video *' ?>
                    </label>
                    <input type="file" 
                           class="form-control" 
                           id="video_file" 
                           name="video_file" 
                           accept="video/mp4,video/webm,video/ogg"
                           <?= (!$video || empty($video['video_path'])) ? 'required' : '' ?>>
                    <small class="form-text text-muted">
                        <?php
                        $serverMax = ini_get('upload_max_filesize');
                        $maxDisplay = min(2, (int)$serverMax);
                        ?>
                        Format: MP4, WebM, atau OGG. Maksimal ukuran: <?= $maxDisplay ?>MB (Server limit: <?= $serverMax ?>)
                    </small>
                </div>

                <div class="form-group">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" 
                               class="custom-control-input" 
                               id="is_active" 
                               name="is_active" 
                               <?= ($video && $video['is_active']) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="is_active">
                            Aktifkan video (tampilkan di halaman utama)
                        </label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.current-video {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 15px;
}

.form-actions {
    display: flex;
    gap: 10px;
    margin-top: 20px;
}
</style>

<script>
function confirmDeleteVideo() {
    if (confirm('⚠️ PERHATIAN!\n\nAnda yakin ingin menghapus video ini?\n\nFile video akan DIHAPUS PERMANEN dari server untuk menghemat storage.\n\nProses ini TIDAK DAPAT dibatalkan!')) {
        window.location.href = 'hero-video.php?action=delete&confirm=1';
    }
}
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
