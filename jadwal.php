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

// Convert schedules to FullCalendar events format
$calendar_events = [];
foreach ($schedules as $schedule) {
    $calendar_events[] = [
        'id' => $schedule['id'],
        'title' => $schedule['package_name'] ?? $schedule['title'],
        'start' => $schedule['departure_date'],
        'description' => $schedule['description'] ?? '',
        'packageId' => $schedule['id_packages'],
        'price' => $schedule['price_value'] ?? '',
        'priceUnit' => $schedule['price_unit'] ?? '',
        'duration' => $schedule['jml_hari'] ?? '',
        'icon' => $schedule['icon_class'] ?? 'fas fa-kaaba'
    ];
}

// Page configuration for header template
$page_title = 'Jadwal Keberangkatan - Ar Raihan Travelindo';
$page_description = 'Jadwal Keberangkatan Umroh & Haji - Raihan Travelindo';
$current_page = 'jadwal';

// Extra head content for FullCalendar
$extra_head_content = '
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/jadwal.css?v=' . time() . '">
<style>
.fc {
    font-family: "Poppins", sans-serif;
}
.fc .fc-event {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border: none;
    border-radius: 8px;
    padding: 2px 6px;
    cursor: pointer;
    transition: all 0.3s ease;
}
.fc .fc-event:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}
.fc .fc-event-title {
    font-weight: 600;
    font-size: 12px;
}
.fc-daygrid-event {
    margin: 1px 0;
}
.fc-h-event {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)) !important;
    border: none !important;
}
.calendar-container {
    background: white;
    border-radius: 15px;
    box-shadow: var(--shadow-md);
    padding: 30px;
    margin: 30px 0;
}
</style>';

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

    <!-- Calendar Section -->
    <section class="schedule-section">
        <div class="container">
            <a href="<?= $base2 ?>index.php#jadwal" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Homepage
            </a>

            <div class="calendar-container">
                <div id="calendar"></div>
            </div>

            <?php if (empty($schedules)): ?>
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

    <!-- Event Detail Modal -->
    <div id="eventModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <div class="package-icon">
                    <i id="modalIcon" class="fas fa-kaaba"></i>
                </div>
                <div class="package-info">
                    <h3 id="modalTitle">Paket Umroh</h3>
                    <p id="modalDuration" class="duration">
                        <i class="fas fa-clock"></i>
                        12 Hari
                    </p>
                </div>
            </div>
            <div class="modal-body">
                <div class="info-row">
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <span class="label">Tanggal Keberangkatan</span>
                            <span id="modalDate" class="value">1 Januari 2024</span>
                        </div>
                    </div>
                </div>
                <div id="modalDescriptionRow" class="info-row" style="display: none;">
                    <div class="info-item">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <span class="label">Keterangan</span>
                            <span id="modalDescription" class="value"></span>
                        </div>
                    </div>
                </div>
                <div id="modalPriceRow" class="info-row price-row" style="display: none;">
                    <div class="info-item">
                        <i class="fas fa-tag"></i>
                        <div>
                            <span class="label">Harga Mulai Dari</span>
                            <span id="modalPrice" class="value price-value"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <?php 
                $link_whatsapp = function_exists('get_setting') ? get_setting('whatsapp', '') : '';
                if (!empty($link_whatsapp)): 
                ?>
                <a href="<?= e($link_whatsapp) ?>" id="modalBookBtn" class="btn-book" target="_blank">
                    <i class="fab fa-whatsapp"></i>
                    Daftar Sekarang
                </a>
                <?php endif; ?>
                <a href="#" id="modalDetailBtn" class="btn-detail">
                    <i class="fas fa-info-circle"></i>
                    Detail Paket
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        // Calendar events from PHP
        var events = <?= json_encode($calendar_events) ?>;
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth'
            },
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                list: 'Daftar'
            },
            events: events,
            eventClick: function(info) {
                showEventModal(info.event);
            },
            eventMouseEnter: function(info) {
                info.el.style.transform = 'translateY(-2px)';
                info.el.style.boxShadow = '0 4px 15px rgba(0,0,0,0.2)';
            },
            eventMouseLeave: function(info) {
                info.el.style.transform = '';
                info.el.style.boxShadow = '';
            },
            height: 'auto',
            aspectRatio: 1.8
        });
        
        calendar.render();
        
        // Modal functionality
        function showEventModal(event) {
            const modal = document.getElementById('eventModal');
            const extendedProps = event.extendedProps;
            
            // Set modal content
            document.getElementById('modalIcon').className = extendedProps.icon || 'fas fa-kaaba';
            document.getElementById('modalTitle').textContent = event.title;
            document.getElementById('modalDuration').innerHTML = `<i class="fas fa-clock"></i> ${extendedProps.duration || '-'} Hari`;
            document.getElementById('modalDate').textContent = formatDate(event.start);
            
            // Description
            const descRow = document.getElementById('modalDescriptionRow');
            if (extendedProps.description) {
                document.getElementById('modalDescription').textContent = extendedProps.description;
                descRow.style.display = 'block';
            } else {
                descRow.style.display = 'none';
            }
            
            // Price
            const priceRow = document.getElementById('modalPriceRow');
            if (extendedProps.price) {
                document.getElementById('modalPrice').textContent = `${extendedProps.price} ${extendedProps.priceUnit || ''}`;
                priceRow.style.display = 'block';
            } else {
                priceRow.style.display = 'none';
            }
            
            // Detail button link
            if (extendedProps.packageId) {
                document.getElementById('modalDetailBtn').href = `paket-detail.php?id=${extendedProps.packageId}`;
            }
            
            // Show modal
            modal.style.display = 'block';
        }
        
        function formatDate(date) {
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return date.toLocaleDateString('id-ID', options);
        }
        
        // Close modal
        document.querySelector('.close').onclick = function() {
            document.getElementById('eventModal').style.display = 'none';
        }
        
        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    });
    </script>

<?php
// Include footer template
require_once __DIR__ . '/inc/footer.php';
?>
