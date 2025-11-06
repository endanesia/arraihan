<?php
require_once __DIR__ . '/../inc/db.php';
require_login();

$base = rtrim($config['app']['base_url'] ?? '', '/');
$logs = [];
$error = null;
$success = null;

// Current schema version stored in settings (default 0)
$curr = (int) get_setting('schema_version', '0');

// Define migrations: incremental, idempotent
// v1: ensure settings table exists (for older installs)
$latest = 2;

function run_migration($version, &$logs, &$error) {
    try {
        switch ($version) {
            case 1:
                $ok = ensure_settings_table();
                if ($ok) {
                    $logs[] = 'v1: Tabel settings dipastikan ada (CREATE TABLE IF NOT EXISTS).';
                } else {
                    $logs[] = 'v1: Gagal memastikan tabel settings, namun proses akan tetap lanjut jika sudah ada.';
                }
                break;
      case 2:
        // Create partners, packages, schedules
        $db = db();
        if ($db) {
          $db->query("CREATE TABLE IF NOT EXISTS partners (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  name VARCHAR(255) NOT NULL,\n  icon_class VARCHAR(100) NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
          $logs[] = $db->errno ? 'v2: partners - ERROR: ' . $db->error : 'v2: Tabel partners dibuat/dipastikan ada.';

          $db->query("CREATE TABLE IF NOT EXISTS packages (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  title VARCHAR(255) NOT NULL,\n  price_label VARCHAR(100) DEFAULT 'Mulai dari',\n  price_value VARCHAR(100) NOT NULL,\n  price_unit VARCHAR(50) DEFAULT '/orang',\n  icon_class VARCHAR(100) DEFAULT 'fas fa-moon',\n  features TEXT NULL,\n  featured TINYINT(1) NOT NULL DEFAULT 0,\n  button_text VARCHAR(100) DEFAULT 'Lihat Detail',\n  button_link VARCHAR(255) DEFAULT '#kontak',\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
          $logs[] = $db->errno ? 'v2: packages - ERROR: ' . $db->error : 'v2: Tabel packages dibuat/dipastikan ada.';

          $db->query("CREATE TABLE IF NOT EXISTS schedules (\n  id INT AUTO_INCREMENT PRIMARY KEY,\n  title VARCHAR(255) NOT NULL,\n  departure_date DATE NULL,\n  description TEXT NULL,\n  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
          $logs[] = $db->errno ? 'v2: schedules - ERROR: ' . $db->error : 'v2: Tabel schedules dibuat/dipastikan ada.';
        }
        break;
        }
        return true;
    } catch (Throwable $e) {
        $error = 'Error pada migrasi v' . $version . ': ' . $e->getMessage();
        return false;
    }
}

$didRun = false;
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    if ($curr >= $latest) {
        $success = 'Skema sudah terbaru. Tidak ada migrasi yang perlu dijalankan.';
    } else {
        for ($v = $curr + 1; $v <= $latest; $v++) {
            if (!run_migration($v, $logs, $error)) {
                break;
            }
            set_setting('schema_version', (string)$v);
            $didRun = true;
        }
        if (!$error) {
            $success = 'Upgrade selesai. Versi skema sekarang: v' . e(get_setting('schema_version', (string)$latest));
        }
    }
}

include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Upgrade Database</h3>
    <span class="badge bg-secondary">Versi Saat Ini: v<?= e(get_setting('schema_version', (string)$curr)) ?></span>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
  <?php elseif ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
  <?php endif; ?>

  <?php if (!empty($logs)): ?>
    <div class="card mb-3"><div class="card-body">
      <h6 class="mb-2">Log Migrasi</h6>
      <ul class="mb-0">
        <?php foreach ($logs as $line): ?>
          <li><?= e($line) ?></li>
        <?php endforeach; ?>
      </ul>
    </div></div>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
      <p class="text-muted mb-3">Klik tombol di bawah untuk menjalankan upgrade skema database (idempotent). Jika skema sudah terbaru, tidak ada perubahan yang dilakukan.</p>
      <form method="post">
        <button type="submit" class="btn btn-primary">Jalankan Upgrade</button>
      </form>
    </div>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
