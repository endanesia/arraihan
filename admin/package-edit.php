<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$title = '';
$description = '';
$price_label = 'Mulai dari';
$price_value = '';
$price_unit = '/orang';
$icon_class = 'fas fa-moon';
$features = '';
$featured = 0;
$button_text = 'Lihat Detail';
$button_link = '#kontak';
$hotel = '';
$pesawat = '';
$price_quad = '';
$price_triple = '';
$price_double = '';
$err = null; $ok = null;

if ($editing) {
    $stmt = db()->prepare("SELECT title, description, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double FROM packages WHERE id=? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->bind_result($title, $description, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $hotel, $pesawat, $price_quad, $price_triple, $price_double);
    if (!$stmt->fetch()) { $editing = false; }
    $stmt->close();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price_label = trim($_POST['price_label'] ?? 'Mulai dari');
    $price_value = trim($_POST['price_value'] ?? '');
    $price_unit = trim($_POST['price_unit'] ?? '/orang');
    $icon_class = trim($_POST['icon_class'] ?? 'fas fa-moon');
    $features = trim($_POST['features'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $button_text = trim($_POST['button_text'] ?? 'Lihat Detail');
    $button_link = trim($_POST['button_link'] ?? '#kontak');
    $hotel = trim($_POST['hotel'] ?? '');
    $pesawat = trim($_POST['pesawat'] ?? '');
    $price_quad = trim($_POST['price_quad'] ?? '');
    $price_triple = trim($_POST['price_triple'] ?? '');
    $price_double = trim($_POST['price_double'] ?? '');

    if ($title === '' || $price_value === '') { $err = 'Judul dan nilai harga wajib diisi'; }

    if (!$err) {
        if ($editing) {
            $stmt = db()->prepare("UPDATE packages SET title=?, description=?, price_label=?, price_value=?, price_unit=?, icon_class=?, features=?, featured=?, button_text=?, button_link=?, hotel=?, pesawat=?, price_quad=?, price_triple=?, price_double=? WHERE id=?");
            $stmt->bind_param('sssssssissssssi', $title, $description, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $hotel, $pesawat, $price_quad, $price_triple, $price_double, $id);
            $stmt->execute();
            $ok = 'Paket diperbarui';
        } else {
            $stmt = db()->prepare("INSERT INTO packages(title, description, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link, hotel, pesawat, price_quad, price_triple, price_double) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('ssssssissssssss', $title, $description, $price_label, $price_value, $price_unit, $icon_class, $features, $featured, $button_text, $button_link, $hotel, $pesawat, $price_quad, $price_triple, $price_double);
            $stmt->execute();
            header('Location: ' . $base . '/admin/packages'); exit;
        }
    }
}

include __DIR__ . '/header.php';
?>
<style>
.form-section-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 8px;
    margin-bottom: 20px;
}

.form-section-header i {
    color: #0d6efd;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.form-text {
    font-size: 0.875em;
    color: #6c757d;
}

.btn-group .btn {
    border-radius: 0.375rem !important;
}

/* CKEditor Styling */
.ck-editor__editable {
    min-height: 200px !important;
}

.ck-editor {
    margin-bottom: 1rem;
}

.ck.ck-editor {
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
}

.ck.ck-editor__main > .ck-editor__editable {
    border-radius: 0 0 10px 10px;
}

.ck.ck-toolbar {
    border-radius: 10px 10px 0 0;
    border-bottom: 1px solid #e0e0e0;
}

.ck-content {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    font-size: 14px;
    line-height: 1.6;
}

.ck-content h1, .ck-content h2, .ck-content h3 {
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}

.ck-content p {
    margin-bottom: 0.75rem;
}
</style>

<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0"><?= $editing ? 'Edit' : 'Tambah' ?> Paket</h3>
    <a href="<?= e($base) ?>/admin/packages" class="btn btn-secondary">Kembali</a>
  </div>
  <?php if ($err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endif; ?>
  <?php if ($ok): ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

  <div class="card"><div class="card-body">
    <form method="post" class="row g-3">
      <!-- Basic Information -->
      <div class="col-12">
        <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Informasi Dasar</h5>
      </div>
      
      <div class="col-md-6">
        <label class="form-label">Judul Paket *</label>
        <input name="title" class="form-control" value="<?= e($title) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Icon (Font Awesome)</label>
        <input name="icon_class" class="form-control" placeholder="fas fa-moon" value="<?= e($icon_class) ?>">
      </div>
      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="featured" id="featured" <?= $featured ? 'checked' : '' ?>>
          <label class="form-check-label" for="featured">Tandai Populer</label>
        </div>
      </div>

      <div class="col-12">
        <label class="form-label">Deskripsi Paket</label>
        <textarea name="description" id="description" class="form-control ckeditor" rows="6"><?= e($description) ?></textarea>
        <div class="form-text">Deskripsi lengkap tentang paket perjalanan ini</div>
      </div>

      <!-- Pricing Section -->
      <div class="col-12 mt-4">
        <h5 class="text-primary mb-3"><i class="fas fa-tag me-2"></i>Informasi Harga</h5>
      </div>
      
      <div class="col-md-3">
        <label class="form-label">Label Harga</label>
        <input name="price_label" class="form-control" value="<?= e($price_label) ?>" placeholder="Mulai dari">
      </div>
      <div class="col-md-3">
        <label class="form-label">Nilai Harga Utama *</label>
        <input name="price_value" class="form-control" placeholder="Rp 24 Juta" value="<?= e($price_value) ?>" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Unit Harga</label>
        <input name="price_unit" class="form-control" value="<?= e($price_unit) ?>" placeholder="/orang">
      </div>
      
      <div class="col-md-3">
        <label class="form-label">Harga Quad</label>
        <input name="price_quad" class="form-control" value="<?= e($price_quad) ?>" placeholder="Rp 20 Juta">
        <div class="form-text">Harga untuk 4 orang</div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Harga Triple</label>
        <input name="price_triple" class="form-control" value="<?= e($price_triple) ?>" placeholder="Rp 22 Juta">
        <div class="form-text">Harga untuk 3 orang</div>
      </div>
      <div class="col-md-3">
        <label class="form-label">Harga Double</label>
        <input name="price_double" class="form-control" value="<?= e($price_double) ?>" placeholder="Rp 24 Juta">
        <div class="form-text">Harga untuk 2 orang</div>
      </div>

      <!-- Package Details -->
      <div class="col-12 mt-4">
        <h5 class="text-primary mb-3"><i class="fas fa-suitcase me-2"></i>Detail Paket</h5>
      </div>
      
      <div class="col-md-6">
        <label class="form-label">Hotel</label>
        <input name="hotel" class="form-control" value="<?= e($hotel) ?>" placeholder="Hotel Bintang 4-5, Dekat Haram">
        <div class="form-text">Informasi hotel yang disediakan</div>
      </div>
      <div class="col-md-6">
        <label class="form-label">Pesawat</label>
        <input name="pesawat" class="form-control" value="<?= e($pesawat) ?>" placeholder="Garuda Indonesia, Direct Flight">
        <div class="form-text">Informasi maskapai dan penerbangan</div>
      </div>

      <div class="col-12">
        <label class="form-label">Fitur & Fasilitas Paket</label>
        <textarea name="features" id="features" class="form-control ckeditor" rows="8"><?= e($features) ?></textarea>
        <div class="form-text">
          Daftar fitur dan fasilitas yang disediakan dalam paket ini. Gunakan format HTML untuk styling yang lebih baik.
        </div>
      </div>

      <!-- Call to Action -->
      <div class="col-12 mt-4">
        <h5 class="text-primary mb-3"><i class="fas fa-mouse-pointer me-2"></i>Call to Action</h5>
      </div>
      
      <div class="col-md-6">
        <label class="form-label">Teks Tombol</label>
        <input name="button_text" class="form-control" value="<?= e($button_text) ?>" placeholder="Lihat Detail">
      </div>
      <div class="col-md-6">
        <label class="form-label">Link Tombol</label>
        <input name="button_link" class="form-control" value="<?= e($button_link) ?>" placeholder="#kontak">
        <div class="form-text">Link tujuan tombol (misal: #kontak, /paket-detail, dll)</div>
      </div>

      <div class="col-12 mt-4">
        <button class="btn btn-primary btn-lg" type="submit">
          <i class="fas fa-save me-2"></i>Simpan Paket
        </button>
        <a href="<?= e($base) ?>/admin/packages" class="btn btn-secondary btn-lg ms-2">
          <i class="fas fa-times me-2"></i>Batal
        </a>
      </div>
    </form>
  </div></div>
</div>

<!-- CKEditor 5 -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor for Description
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'blockQuote', 'insertTable', '|',
                'link', '|',
                'undo', 'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            }
        })
        .then(editor => {
            window.descriptionEditor = editor;
        })
        .catch(error => {
            console.error('CKEditor initialization failed:', error);
        });

    // Initialize CKEditor for Features
    ClassicEditor
        .create(document.querySelector('#features'), {
            toolbar: [
                'heading', '|',
                'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|',
                'outdent', 'indent', '|',
                'blockQuote', 'insertTable', '|',
                'link', '|',
                'undo', 'redo'
            ],
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                ]
            },
            table: {
                contentToolbar: [
                    'tableColumn',
                    'tableRow',
                    'mergeTableCells'
                ]
            }
        })
        .then(editor => {
            window.featuresEditor = editor;
        })
        .catch(error => {
            console.error('CKEditor initialization failed:', error);
        });

    // Handle form submission to ensure CKEditor content is saved
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Update textareas with CKEditor content before submission
            if (window.descriptionEditor) {
                document.querySelector('#description').value = window.descriptionEditor.getData();
            }
            if (window.featuresEditor) {
                document.querySelector('#features').value = window.featuresEditor.getData();
            }
        });
    }
});
</script>

<?php include __DIR__ . '/footer.php'; ?>
