<?php 
require_once __DIR__ . '/inc/db.php';

// Fetch upcoming schedules (only future dates)
$schedules = [];
if (function_exists('db') && db()) {
    $today = date('Y-m-d');
    $res = db()->query("SELECT s.*, p.title as package_name, p.icon_class, p.price_value, p.price_unit
                        FROM schedules s 
                        LEFT JOIN packages p ON s.id_packages = p.id 
                        WHERE s.departure_date >= '$today' 
                        ORDER BY s.departure_date ASC");
    if ($res) {
        while ($row = $res->fetch_assoc()) { 
            $schedules[] = $row; 
        }
    }
}

// Page configuration for header template
$page_title = 'Jadwal Keberangkatan - Ar Raihan Travelindo';
$page_description = 'Jadwal Keberangkatan Umroh & Haji - Raihan Travelindo';
$current_page = 'jadwal';

// Extra head content for custom styles
$extra_head_content = '<link rel="stylesheet" href="css/jadwal.css?v=' . time() . '">';

// Include header template
require_once __DIR__ . '/inc/header.php';
?>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1 class="page-title">Jadwal Keberangkatan</h1>
            <p class="page-subtitle">Pilih jadwal keberangkatan yang sesuai dengan rencana perjalanan ibadah Anda</p>
            <div class="breadcrumb-nav">
                <a href="<?= $base2 ?>index.php">Home</a>
                <i class="fas fa-chevron-right"></i>
                <span>Jadwal</span>
            </div>
        </div>
    </section>

    <!-- Schedule Section -->
    <section class="schedule-section">
        <div class="container">
            <a href="<?= $base2 ?>index.php#jadwal" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Homepage
            </a>

            <?php if (!empty($schedules)): ?>
            <div class="schedule-grid">
                <?php foreach ($schedules as $schedule): ?>
                <div class="schedule-card">
                    <div class="schedule-header">
                        <div class="package-icon">
                            <i class="<?= e($schedule['icon_class'] ?? 'fas fa-kaaba') ?>"></i>
                        </div>
                        <div class="package-info">
                            <h3><?= e($schedule['package_name'] ?? $schedule['title']) ?></h3>
                            <p class="duration">
                                <i class="fas fa-clock"></i>
                                <?= (int)$schedule['jml_hari'] ?> Hari
                            </p>
                        </div>
                    </div>

                    <div class="schedule-body">
                        <div class="info-row">
                            <div class="info-item">
                                <i class="fas fa-calendar-alt"></i>
                                <div>
                                    <span class="label">Tanggal Keberangkatan</span>
                                    <span class="value"><?= date('d F Y', strtotime($schedule['departure_date'])) ?></span>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($schedule['description'])): ?>
                        <div class="info-row">
                            <div class="info-item">
                                <i class="fas fa-info-circle"></i>
                                <div>
                                    <span class="label">Keterangan</span>
                                    <span class="value"><?= nl2br(e($schedule['description'])) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($schedule['price_value'])): ?>
                        <div class="info-row price-row">
                            <div class="info-item">
                                <i class="fas fa-tag"></i>
                                <div>
                                    <span class="label">Harga Mulai Dari</span>
                                    <span class="value price-value"><?= e($schedule['price_value']) ?> <?= e($schedule['price_unit'] ?? '') ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="schedule-footer">
                        <?php 
                        $link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
                        if (!empty($link_whatsapp)): 
                        ?>
                        <a href="<?= e($link_whatsapp) ?>" class="btn-book" target="_blank">
                            <i class="fab fa-whatsapp"></i>
                            Daftar Sekarang
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($schedule['id_packages'])): ?>
                        <a href="paket-detail.php?id=<?= (int)$schedule['id_packages'] ?>" class="btn-detail">
                            <i class="fas fa-info-circle"></i>
                            Detail Paket
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-times"></i>
                <h3>Belum Ada Jadwal</h3>
                <p>Jadwal keberangkatan sedang dalam proses penyusunan. Silakan hubungi kami untuk informasi lebih lanjut.</p>
                <?php 
                $link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
                if (!empty($link_whatsapp)): 
                ?>
                <a href="<?= e($link_whatsapp) ?>" class="btn-contact" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    Hubungi Kami
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

<?php
// Include footer template
require_once __DIR__ . '/inc/footer.php';
?>
