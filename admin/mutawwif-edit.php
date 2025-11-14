<?php
require_once __DIR__ . '/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $id > 0;

$mutawwif = [
    'nama' => '',
    'jabatan' => 'Mutawwif Indonesia',
    'foto' => '',
    'urutan' => 0,
    'is_active' => 1
];

if ($isEdit) {
    $stmt = db()->prepare("SELECT * FROM mutawwif WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $mutawwif = $row;
    } else {
        header('Location: mutawwif-list.php');
        exit;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $jabatan = trim($_POST['jabatan'] ?? '');
    $urutan = (int)($_POST['urutan'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $foto = $mutawwif['foto']; // Keep existing photo by default

    // Handle file upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['foto']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed)) {
            $newname = 'mutawwif_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
            $uploadPath = __DIR__ . '/../images/mutawwif/' . $newname;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                // Delete old photo if exists
                if (!empty($mutawwif['foto']) && file_exists(__DIR__ . '/../images/mutawwif/' . $mutawwif['foto'])) {
                    unlink(__DIR__ . '/../images/mutawwif/' . $mutawwif['foto']);
                }
                $foto = $newname;
            }
        }
    }

    // Validate
    if (empty($nama)) {
        $error = 'Nama mutawwif harus diisi!';
    } else {
        if ($isEdit) {
            // Update
            $stmt = db()->prepare("UPDATE mutawwif SET nama=?, jabatan=?, foto=?, urutan=?, is_active=? WHERE id=?");
            $stmt->bind_param('sssiii', $nama, $jabatan, $foto, $urutan, $is_active, $id);
            $stmt->execute();
            header('Location: mutawwif-list.php?success=edit');
            exit;
        } else {
            // Insert
            $stmt = db()->prepare("INSERT INTO mutawwif (nama, jabatan, foto, urutan, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param('sssii', $nama, $jabatan, $foto, $urutan, $is_active);
            $stmt->execute();
            header('Location: mutawwif-list.php?success=add');
            exit;
        }
    }
}
?>

<div class="p-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="mutawwif-list.php">Mutawwif</a></li>
                <li class="breadcrumb-item active"><?= $isEdit ? 'Edit' : 'Tambah' ?> Mutawwif</li>
            </ol>
        </nav>
        <h2><?= $isEdit ? 'Edit' : 'Tambah' ?> Mutawwif</h2>
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
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   name="nama" 
                                   value="<?= e($mutawwif['nama']) ?>" 
                                   required 
                                   placeholder="Contoh: Uztad Mochammad Munir Djamil">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jabatan</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="jabatan" 
                                   value="<?= e($mutawwif['jabatan']) ?>" 
                                   placeholder="Contoh: Mutawwif Indonesia">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Foto</label>
                            <input type="file" 
                                   class="form-control" 
                                   name="foto" 
                                   accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Maksimal 2MB.</small>
                            
                            <?php if (!empty($mutawwif['foto'])): ?>
                            <div class="mt-3">
                                <p class="mb-2"><strong>Foto saat ini:</strong></p>
                                <img src="../images/mutawwif/<?= e($mutawwif['foto']) ?>" 
                                     alt="<?= e($mutawwif['nama']) ?>" 
                                     class="img-thumbnail"
                                     style="max-width: 200px;">
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Urutan Tampilan</label>
                            <input type="number" 
                                   class="form-control" 
                                   name="urutan" 
                                   value="<?= (int)$mutawwif['urutan'] ?>" 
                                   min="0"
                                   placeholder="0">
                            <small class="text-muted">Semakin kecil angka, semakin awal posisinya</small>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       name="is_active" 
                                       id="is_active" 
                                       <?= $mutawwif['is_active'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Tampilkan di website
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <a href="mutawwif-list.php" class="btn btn-secondary">
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
                        <li>Nama harus diisi dengan lengkap</li>
                        <li>Jabatan otomatis diisi "Mutawwif Indonesia"</li>
                        <li>Upload foto dengan latar belakang merah sesuai contoh</li>
                        <li>Foto akan ditampilkan di homepage dengan rasio 3:4</li>
                        <li>Urutan: 0 = paling awal, semakin besar semakin akhir</li>
                        <li>Non-aktifkan jika ingin menyembunyikan dari website</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
