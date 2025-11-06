<?php
require_once __DIR__ . '/../inc/db.php';
require_login();

$base = rtrim($config['app']['base_url'] ?? '', '/');
$success = null; $error = null;

$fields = [
  // Sosial Media
  'whatsapp' => 'WhatsApp URL (wa.me) or chat link',
  'facebook' => 'Facebook Page URL',
  'instagram' => 'Instagram URL',
  'youtube' => 'YouTube Channel/URL',
  'tiktok' => 'TikTok URL',
  'twitter' => 'Twitter/X URL',
  // Informasi Kantor
  'address' => 'Alamat Kantor (multiline)',
  'phone' => 'Telepon (boleh lebih dari satu, pisahkan koma)',
  'email' => 'Email (boleh lebih dari satu, pisahkan koma)',
  'hours' => 'Jam Operasional (multiline)'
];

// Handle submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  try {
    // Hanya simpan field yang disubmit agar form lain tidak ikut terhapus
    foreach ($fields as $key => $label) {
      if (!array_key_exists($key, $_POST)) continue; // skip yang tidak dikirim
      $val = trim($_POST[$key]);
      // Basic normalization
      if ($key === 'phone') {
        // izinkan +, digit, spasi, koma, dan tanda hubung
        $val = preg_replace('/[^+,\d\s-]/', '', $val);
      }
      set_setting($key, $val);
    }
    $success = 'Pengaturan berhasil disimpan.';
  } catch (Throwable $e) {
    $error = 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage();
  }
}

// Load current values
$values = [];
foreach ($fields as $key => $label) {
    $values[$key] = get_setting($key, '');
}

include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Pengaturan Situs</h3>
  </div>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
  <?php elseif ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
  <?php endif; ?>

  <div class="card mb-3">
    <div class="card-body">
      <h5 class="mb-3">Link Sosial</h5>
      <form method="post" class="row g-3">
        <div class="col-12 col-md-6">
          <label class="form-label">WhatsApp</label>
          <input type="url" name="whatsapp" class="form-control" placeholder="https://wa.me/62812..." value="<?= e($values['whatsapp']) ?>">
          <div class="form-text">Masukkan link lengkap chat WhatsApp (mis: https://wa.me/62812...?...)</div>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Facebook</label>
          <input type="url" name="facebook" class="form-control" placeholder="https://facebook.com/username" value="<?= e($values['facebook']) ?>">
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Instagram</label>
          <input type="url" name="instagram" class="form-control" placeholder="https://instagram.com/username" value="<?= e($values['instagram']) ?>">
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">YouTube</label>
          <input type="url" name="youtube" class="form-control" placeholder="https://youtube.com/@channel" value="<?= e($values['youtube']) ?>">
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">TikTok</label>
          <input type="url" name="tiktok" class="form-control" placeholder="https://tiktok.com/@username" value="<?= e($values['tiktok']) ?>">
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Twitter/X</label>
          <input type="url" name="twitter" class="form-control" placeholder="https://x.com/username" value="<?= e($values['twitter']) ?>">
        </div>
      <div class="col-12">
        <button class="btn btn-success" type="submit">Simpan</button>
      </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-body">
      <h5 class="mb-3">Informasi Kantor</h5>
      <form method="post" class="row g-3">
        <div class="col-12">
          <label class="form-label">Alamat Kantor</label>
          <textarea name="address" class="form-control" rows="3" placeholder="Jl. ..."><?= e($values['address']) ?></textarea>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Telepon</label>
          <input type="text" name="phone" class="form-control" placeholder="+62812..., 021-...." value="<?= e($values['phone']) ?>">
          <div class="form-text">Boleh lebih dari satu, pisahkan dengan koma. Nomor pertama dipakai untuk tombol tel di beranda.</div>
        </div>
        <div class="col-12 col-md-6">
          <label class="form-label">Email</label>
          <input type="text" name="email" class="form-control" placeholder="info@domain.com, cs@domain.com" value="<?= e($values['email']) ?>">
          <div class="form-text">Boleh lebih dari satu, pisahkan dengan koma.</div>
        </div>
        <div class="col-12">
          <label class="form-label">Jam Operasional</label>
          <textarea name="hours" class="form-control" rows="3" placeholder="Senin - Jumat: 08:00 - 17:00&#10;Sabtu: 08:00 - 14:00&#10;Minggu: Tutup"><?= e($values['hours']) ?></textarea>
        </div>
        <div class="col-12">
          <button class="btn btn-success" type="submit">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
