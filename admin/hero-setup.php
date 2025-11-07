<?php
require_once __DIR__ . '/header.php';

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['setup_hero'])) {
    try {
        $db = db();
        
        // Create settings table
        $createTable = "
            CREATE TABLE IF NOT EXISTS `settings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `setting_key` varchar(100) NOT NULL,
              `setting_value` text,
              `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              UNIQUE KEY `setting_key` (`setting_key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ";
        
        $db->query($createTable);
        
        // Insert default hero settings
        $defaultSettings = [
            'hero_title' => 'Perjalanan Suci Berkualitas, Biaya Bersahabat',
            'hero_subtitle' => 'Jangan biarkan biaya menunda niat suci Anda. Paket Umroh terjangkau dengan tetap berkualitas layanan terbaik, mencakup akomodasi dan bimbingan yang profesional. Wujudkan ibadah khusyuk dan nyaman Anda, karena Umroh berkualitas kini bisa diakses oleh semua.',
            'hero_button_text' => 'Lihat Paket Umroh',
            'hero_stat1_text' => '24 Januri 2026',  
            'hero_stat1_desc' => 'Jadwal Berangkat',
            'hero_stat2_text' => 'Program Pembiayaan',
            'hero_stat2_desc' => 'Pembiayaan dana talangan Umrah',
            'hero_background' => ''
        ];
        
        foreach ($defaultSettings as $key => $value) {
            $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
            $stmt->bind_param('ss', $key, $value);
            $stmt->execute();
        }
        
        $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Hero settings berhasil di-setup!</div>';
        $status = 'success';
        
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error: ' . e($e->getMessage()) . '</div>';
        $status = 'error';
    }
}

// Check if settings table exists
$tableExists = false;
$settingsCount = 0;
try {
    $db = db();
    $result = $db->query("SHOW TABLES LIKE 'settings'");
    $tableExists = $result->num_rows > 0;
    
    if ($tableExists) {
        $result = $db->query("SELECT COUNT(*) as count FROM settings WHERE setting_key LIKE 'hero_%'");
        $settingsCount = $result->fetch_assoc()['count'];
    }
} catch (Exception $e) {
    // Database not available
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Setup Hero Settings</h1>
                <a href="<?= e($base) ?>/admin/hero" class="btn btn-primary btn-sm">
                    <i class="fas fa-edit"></i> Edit Hero
                </a>
            </div>

            <?= $message ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-database text-primary me-2"></i>
                        Database Setup untuk Hero Section
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>Status Database:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-<?= $tableExists ? 'check-circle text-success' : 'times-circle text-danger' ?>"></i>
                                    Tabel Settings: <?= $tableExists ? 'Sudah ada' : 'Belum ada' ?>
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-<?= $settingsCount > 0 ? 'check-circle text-success' : 'times-circle text-warning' ?>"></i>
                                    Hero Settings: <?= $settingsCount ?> items
                                </li>
                            </ul>

                            <?php if (!$tableExists || $settingsCount == 0): ?>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Setup Diperlukan:</strong> Klik tombol di bawah untuk membuat tabel dan data default.
                            </div>
                            <?php endif; ?>

                            <form method="post">
                                <button type="submit" name="setup_hero" class="btn btn-primary">
                                    <i class="fas fa-cogs"></i> Setup Hero Settings
                                </button>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-light p-3 rounded">
                                <h6>Yang akan dibuat:</h6>
                                <ul class="small mb-0">
                                    <li>Tabel `settings`</li>
                                    <li>Default hero title</li>
                                    <li>Default hero subtitle</li>
                                    <li>Default button text</li>
                                    <li>Default statistics</li>
                                    <li>Background image field</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($settingsCount > 0): ?>
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-list text-info me-2"></i>
                        Current Hero Settings
                    </h6>
                </div>
                <div class="card-body">
                    <?php
                    try {
                        $db = db();
                        $result = $db->query("SELECT setting_key, setting_value FROM settings WHERE setting_key LIKE 'hero_%' ORDER BY setting_key");
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-sm">';
                        echo '<thead><tr><th>Key</th><th>Value</th></tr></thead>';
                        echo '<tbody>';
                        while ($row = $result->fetch_assoc()) {
                            $value = strlen($row['setting_value']) > 50 ? substr($row['setting_value'], 0, 50) . '...' : $row['setting_value'];
                            echo '<tr>';
                            echo '<td><code>' . e($row['setting_key']) . '</code></td>';
                            echo '<td>' . e($value) . '</td>';
                            echo '</tr>';
                        }
                        echo '</tbody></table>';
                        echo '</div>';
                    } catch (Exception $e) {
                        echo '<div class="alert alert-warning">Tidak dapat memuat data: ' . e($e->getMessage()) . '</div>';
                    }
                    ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>