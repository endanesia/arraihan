<?php
require_once __DIR__ . '/../inc/db.php';
require_login();
$base = rtrim($config['app']['base_url'] ?? '', '/');

$results = [];

function table_count($table){
  $res = db()->query("SELECT COUNT(*) c FROM {$table}");
  if ($res && ($row = $res->fetch_assoc())) return (int)$row['c'];
  return 0;
}

// Seed partners
$pc = table_count('partners');
if ($pc === 0) {
  $partners = [
    ['PT. Maju Sejahtera','fas fa-building'],
    ['CV. Berkah Jaya','fas fa-industry'],
    ['Toko Amanah','fas fa-store'],
    ['RS. Harapan Sehat','fas fa-hospital'],
    ['Universitas Islam','fas fa-university'],
    ['Bank Syariah','fas fa-landmark'],
  ];
  $stmt = db()->prepare("INSERT INTO partners(name, icon_class) VALUES(?,?)");
  foreach ($partners as $p){ $stmt->bind_param('ss', $p[0], $p[1]); $stmt->execute(); }
  $results[] = 'Partners: ditambahkan ' . count($partners) . ' data.';
} else {
  $results[] = 'Partners: sudah ada data (' . $pc . '). Tidak menambah.';
}

// Seed packages
$pkgc = table_count('packages');
if ($pkgc === 0) {
  $packages = [
    [
      'Paket Umroh','Mulai dari','Rp 24 Juta','/orang','fas fa-moon',
      "fas fa-check|Direct Flight\nfas fa-check|Hotel Bintang 4-5\nfas fa-check|Ring 1 Masjidil Haram\nfas fa-check|Muthawif Berpengalaman\nfas fa-check|City Tour\nfas fa-check|Ziarah Lengkap",
      0,'Lihat Detail','#kontak'
    ],
    [
      'Haji Khusus','Mulai dari','USD 11.500','/orang','fas fa-kaaba',
      "fas fa-check|Direct Flight Premium\nfas fa-check|Hotel Bintang 5\nfas fa-check|Dekat Masjidil Haram\nfas fa-check|Pembimbing Khusus\nfas fa-check|Handling Khusus\nfas fa-check|Layanan VIP",
      1,'Lihat Detail','#kontak'
    ],
    [
      'Badal Haji','Mulai dari','Rp 17 Juta','/orang','fas fa-user-plus',
      "fas fa-check|Badal Haji Terpercaya\nfas fa-check|Petugas Berpengalaman\nfas fa-check|Sertifikat Resmi\nfas fa-check|Laporan Lengkap\nfas fa-check|Dokumentasi\nfas fa-check|Amanah & Terpercaya",
      0,'Lihat Detail','#kontak'
    ],
    [
      'Badal Umroh','Mulai dari','Rp 2 Juta','/orang','fas fa-hands-praying',
      "fas fa-check|Badal Umroh Amanah\nfas fa-check|Petugas Profesional\nfas fa-check|Sertifikat Resmi\nfas fa-check|Laporan Detail\nfas fa-check|Foto & Video\nfas fa-check|Terpercaya",
      0,'Lihat Detail','#kontak'
    ],
  ];
  $stmt = db()->prepare("INSERT INTO packages(title, price_label, price_value, price_unit, icon_class, features, featured, button_text, button_link) VALUES(?,?,?,?,?,?,?,?,?)");
  foreach ($packages as $p){ $stmt->bind_param('ssssssiss', $p[0],$p[1],$p[2],$p[3],$p[4],$p[5],$p[6],$p[7],$p[8]); $stmt->execute(); }
  $results[] = 'Packages: ditambahkan ' . count($packages) . ' data.';
} else {
  $results[] = 'Packages: sudah ada data (' . $pkgc . '). Tidak menambah.';
}

// Seed schedules
$sc = table_count('schedules');
if ($sc === 0) {
  $today = new DateTime();
  $sched = [
    ['Keberangkatan Gelombang 1', $today->modify('+60 days')->format('Y-m-d'), 'Paket Umroh reguler gelombang 1'],
    ['Keberangkatan Gelombang 2', (new DateTime())->modify('+90 days')->format('Y-m-d'), 'Paket Umroh reguler gelombang 2'],
    ['Keberangkatan Haji Khusus', (new DateTime())->modify('+200 days')->format('Y-m-d'), 'Haji Khusus 1447 H'],
  ];
  $stmt = db()->prepare("INSERT INTO schedules(title, departure_date, description) VALUES(?,?,?)");
  foreach ($sched as $s){ $stmt->bind_param('sss', $s[0], $s[1], $s[2]); $stmt->execute(); }
  $results[] = 'Schedules: ditambahkan ' . count($sched) . ' data.';
} else {
  $results[] = 'Schedules: sudah ada data (' . $sc . '). Tidak menambah.';
}

include __DIR__ . '/header.php';
?>
<div class="container-fluid">
  <div class="d-flex align-items-center justify-content-between mb-3">
    <h3 class="mb-0">Seed Sample Data</h3>
    <a href="<?= e($base) ?>/admin/dashboard" class="btn btn-secondary">Kembali</a>
  </div>
  <div class="card"><div class="card-body">
    <ul class="mb-0">
      <?php foreach ($results as $line): ?>
        <li><?= e($line) ?></li>
      <?php endforeach; ?>
    </ul>
  </div></div>
  <div class="mt-3">
    <a class="btn btn-primary" href="<?= e($base) ?>">Lihat Homepage</a>
    <a class="btn btn-outline-primary" href="<?= e($base) ?>/admin/packages">Ke Paket</a>
    <a class="btn btn-outline-primary" href="<?= e($base) ?>/admin/partners">Ke Partner</a>
    <a class="btn btn-outline-primary" href="<?= e($base) ?>/admin/schedules">Ke Jadwal</a>
  </div>
</div>
<?php include __DIR__ . '/footer.php'; ?>
