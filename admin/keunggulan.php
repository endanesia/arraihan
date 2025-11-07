<?php
require_once __DIR__ . '/header.php';

// Handle delete action
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    try {
        $db = db();
        $id = (int)$_GET['delete'];
        $stmt = $db->prepare("DELETE FROM keunggulan WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        header('Location: keunggulan.php?success=deleted');
        exit;
    } catch (Exception $e) {
        $error = 'Error deleting keunggulan: ' . $e->getMessage();
    }
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '');
    $order_num = (int)($_POST['order_num'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($title && $description && $icon) {
        try {
            $db = db();
            
            if (isset($_POST['id']) && is_numeric($_POST['id'])) {
                // Update existing
                $id = (int)$_POST['id'];
                $stmt = $db->prepare("UPDATE keunggulan SET title = ?, description = ?, icon = ?, order_num = ?, is_active = ? WHERE id = ?");
                $stmt->bind_param('sssiii', $title, $description, $icon, $order_num, $is_active, $id);
                $stmt->execute();
                $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Keunggulan berhasil diupdate!</div>';
            } else {
                // Insert new
                $stmt = $db->prepare("INSERT INTO keunggulan (title, description, icon, order_num, is_active) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sssii', $title, $description, $icon, $order_num, $is_active);
                $stmt->execute();
                $message = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> Keunggulan berhasil ditambahkan!</div>';
            }
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error: ' . e($e->getMessage()) . '</div>';
        }
    } else {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Semua field wajib diisi!</div>';
    }
}

// Get keunggulan for editing
$edit_keunggulan = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    try {
        $db = db();
        $id = (int)$_GET['edit'];
        $stmt = $db->prepare("SELECT * FROM keunggulan WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $edit_keunggulan = $result->fetch_assoc();
    } catch (Exception $e) {
        $message = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error loading keunggulan: ' . e($e->getMessage()) . '</div>';
    }
}

// Get all keunggulan
$keunggulan_list = [];
try {
    $db = db();
    $result = $db->query("SELECT * FROM keunggulan ORDER BY order_num ASC, id ASC");
    while ($row = $result->fetch_assoc()) {
        $keunggulan_list[] = $row;
    }
} catch (Exception $e) {
    $message = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Error loading data: ' . e($e->getMessage()) . '</div>';
}
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Keunggulan Kami</h1>
                <a href="<?= e($base) ?>/" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-eye"></i> Lihat Website
                </a>
            </div>

            <?= $message ?>

            <!-- Form Add/Edit -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star text-primary me-2"></i>
                        <?= $edit_keunggulan ? 'Edit Keunggulan' : 'Tambah Keunggulan Baru' ?>
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post">
                        <?php if ($edit_keunggulan): ?>
                        <input type="hidden" name="id" value="<?= e($edit_keunggulan['id']) ?>">
                        <?php endif; ?>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Judul Keunggulan *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?= e($edit_keunggulan['title'] ?? '') ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Deskripsi *</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required><?= e($edit_keunggulan['description'] ?? '') ?></textarea>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="icon" class="form-label">Icon Font Awesome *</label>
                                    
                                    <!-- Hidden input for form submission -->
                                    <input type="hidden" id="icon" name="icon" value="<?= e($edit_keunggulan['icon'] ?? '') ?>" required>
                                    
                                    <!-- Custom dropdown trigger -->
                                    <div class="custom-icon-select" id="iconSelectContainer">
                                        <div class="icon-select-trigger" id="iconSelectTrigger">
                                            <span class="selected-icon">
                                                <?php if ($edit_keunggulan['icon'] ?? ''): 
                                                    $current_icon = $edit_keunggulan['icon'];
                                                    $icons = [
                                                        'fas fa-award' => 'Award / Penghargaan',
                                                        'fas fa-star' => 'Star / Bintang',
                                                        'fas fa-medal' => 'Medal / Medali',
                                                        'fas fa-trophy' => 'Trophy / Piala',
                                                        'fas fa-certificate' => 'Certificate / Sertifikat',
                                                        'fas fa-shield-alt' => 'Shield / Perisai',
                                                        'fas fa-check-circle' => 'Check Circle / Centang Bulat',
                                                        'fas fa-thumbs-up' => 'Thumbs Up / Jempol',
                                                        'fas fa-heart' => 'Heart / Hati',
                                                        'fas fa-calendar-check' => 'Calendar Check / Kalender Centang',
                                                        'fas fa-clock' => 'Clock / Jam',
                                                        'fas fa-calendar-alt' => 'Calendar / Kalender',
                                                        'fas fa-plane' => 'Plane / Pesawat',
                                                        'fas fa-map-marker-alt' => 'Location / Lokasi',
                                                        'fas fa-globe' => 'Globe / Dunia',
                                                        'fas fa-hotel' => 'Hotel',
                                                        'fas fa-bed' => 'Bed / Tempat Tidur',
                                                        'fas fa-building' => 'Building / Gedung',
                                                        'fas fa-user-tie' => 'Professional / Profesional',
                                                        'fas fa-users' => 'Users / Pengguna',
                                                        'fas fa-user-graduate' => 'Graduate / Lulusan',
                                                        'fas fa-headset' => 'Headset',
                                                        'fas fa-phone' => 'Phone / Telepon',
                                                        'fas fa-comments' => 'Comments / Komentar',
                                                        'fas fa-money-bill-wave' => 'Money / Uang',
                                                        'fas fa-credit-card' => 'Credit Card / Kartu Kredit',
                                                        'fas fa-wallet' => 'Wallet / Dompet',
                                                        'fas fa-hand-holding-usd' => 'Payment / Pembayaran',
                                                        'fas fa-handshake' => 'Handshake / Jabat Tangan',
                                                        'fas fa-gem' => 'Gem / Permata',
                                                        'fas fa-crown' => 'Crown / Mahkota',
                                                        'fas fa-fire' => 'Fire / Api',
                                                        'fas fa-bolt' => 'Lightning / Petir',
                                                        'fas fa-rocket' => 'Rocket / Roket',
                                                        'fas fa-magic' => 'Magic / Sihir'
                                                    ];
                                                ?>
                                                <i class="<?= e($current_icon) ?> me-2"></i>
                                                <?= e($icons[$current_icon] ?? 'Unknown Icon') ?>
                                                <?php else: ?>
                                                <i class="fas fa-question-circle me-2 text-muted"></i>
                                                -- Pilih Icon --
                                                <?php endif; ?>
                                            </span>
                                            <i class="fas fa-chevron-down ms-auto"></i>
                                        </div>
                                        
                                        <!-- Dropdown options -->
                                        <div class="icon-select-dropdown" id="iconSelectDropdown">
                                            <div class="icon-search-box">
                                                <input type="text" placeholder="Cari icon..." id="iconSearch" class="form-control form-control-sm">
                                            </div>
                                            <div class="icon-options" id="iconOptions">
                                                <?php
                                                $icons = [
                                                    'fas fa-award' => 'Award / Penghargaan',
                                                    'fas fa-star' => 'Star / Bintang',
                                                    'fas fa-medal' => 'Medal / Medali',
                                                    'fas fa-trophy' => 'Trophy / Piala',
                                                    'fas fa-certificate' => 'Certificate / Sertifikat',
                                                    'fas fa-shield-alt' => 'Shield / Perisai',
                                                    'fas fa-check-circle' => 'Check Circle / Centang Bulat',
                                                    'fas fa-thumbs-up' => 'Thumbs Up / Jempol',
                                                    'fas fa-heart' => 'Heart / Hati',
                                                    'fas fa-calendar-check' => 'Calendar Check / Kalender Centang',
                                                    'fas fa-clock' => 'Clock / Jam',
                                                    'fas fa-calendar-alt' => 'Calendar / Kalender',
                                                    'fas fa-plane' => 'Plane / Pesawat',
                                                    'fas fa-map-marker-alt' => 'Location / Lokasi',
                                                    'fas fa-globe' => 'Globe / Dunia',
                                                    'fas fa-hotel' => 'Hotel',
                                                    'fas fa-bed' => 'Bed / Tempat Tidur',
                                                    'fas fa-building' => 'Building / Gedung',
                                                    'fas fa-user-tie' => 'Professional / Profesional',
                                                    'fas fa-users' => 'Users / Pengguna',
                                                    'fas fa-user-graduate' => 'Graduate / Lulusan',
                                                    'fas fa-headset' => 'Headset',
                                                    'fas fa-phone' => 'Phone / Telepon',
                                                    'fas fa-comments' => 'Comments / Komentar',
                                                    'fas fa-money-bill-wave' => 'Money / Uang',
                                                    'fas fa-credit-card' => 'Credit Card / Kartu Kredit',
                                                    'fas fa-wallet' => 'Wallet / Dompet',
                                                    'fas fa-hand-holding-usd' => 'Payment / Pembayaran',
                                                    'fas fa-handshake' => 'Handshake / Jabat Tangan',
                                                    'fas fa-gem' => 'Gem / Permata',
                                                    'fas fa-crown' => 'Crown / Mahkota',
                                                    'fas fa-fire' => 'Fire / Api',
                                                    'fas fa-bolt' => 'Lightning / Petir',
                                                    'fas fa-rocket' => 'Rocket / Roket',
                                                    'fas fa-magic' => 'Magic / Sihir'
                                                ];
                                                
                                                foreach ($icons as $icon_class => $icon_name):
                                                    $active = ($edit_keunggulan['icon'] ?? '') === $icon_class ? 'active' : '';
                                                ?>
                                                <div class="icon-option <?= $active ?>" data-value="<?= e($icon_class) ?>">
                                                    <i class="<?= e($icon_class) ?> me-2"></i>
                                                    <span><?= e($icon_name) ?></span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-text">Pilih icon yang sesuai dengan keunggulan</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="order_num" class="form-label">Urutan</label>
                                    <input type="number" class="form-control" id="order_num" name="order_num" 
                                           value="<?= e($edit_keunggulan['order_num'] ?? '0') ?>" min="0">
                                    <div class="form-text">Semakin kecil semakin di atas</div>
                                </div>
                                
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                               <?= ($edit_keunggulan['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            Aktif / Tampilkan
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $edit_keunggulan ? 'Update' : 'Simpan' ?>
                            </button>
                            <?php if ($edit_keunggulan): ?>
                            <a href="keunggulan" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Batal Edit
                            </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Keunggulan -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list text-info me-2"></i>
                        Daftar Keunggulan (<?= count($keunggulan_list) ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($keunggulan_list)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-star fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Belum ada keunggulan. Tambahkan keunggulan pertama Anda!</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">Urutan</th>
                                    <th width="60">Icon</th>
                                    <th>Judul</th>
                                    <th>Deskripsi</th>
                                    <th width="80">Status</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($keunggulan_list as $item): ?>
                                <tr class="<?= $item['is_active'] ? '' : 'table-secondary' ?>">
                                    <td><?= e($item['order_num']) ?></td>
                                    <td>
                                        <i class="<?= e($item['icon']) ?> fa-lg text-primary"></i>
                                    </td>
                                    <td>
                                        <strong><?= e($item['title']) ?></strong>
                                    </td>
                                    <td>
                                        <small><?= e(substr($item['description'], 0, 100)) ?><?= strlen($item['description']) > 100 ? '...' : '' ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $item['is_active'] ? 'success' : 'secondary' ?>">
                                            <?= $item['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="keunggulan?edit=<?= $item['id'] ?>" class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="keunggulan?delete=<?= $item['id'] ?>" class="btn btn-outline-danger" 
                                               onclick="return confirm('Yakin ingin menghapus keunggulan ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Custom Icon Select functionality
function initCustomIconSelect() {
    const trigger = document.getElementById('iconSelectTrigger');
    const dropdown = document.getElementById('iconSelectDropdown');
    const container = document.getElementById('iconSelectContainer');
    const hiddenInput = document.getElementById('icon');
    const searchInput = document.getElementById('iconSearch');
    const options = document.querySelectorAll('.icon-option');
    
    if (!trigger || !dropdown || !container) return;
    
    // Toggle dropdown
    trigger.addEventListener('click', function(e) {
        e.stopPropagation();
        const isOpen = dropdown.style.display === 'block';
        
        if (isOpen) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });
    
    // Open dropdown
    function openDropdown() {
        dropdown.style.display = 'block';
        dropdown.classList.add('show');
        trigger.classList.add('active');
        searchInput.focus();
        
        // Clear search
        searchInput.value = '';
        options.forEach(option => {
            option.style.display = 'flex';
        });
    }
    
    // Close dropdown
    function closeDropdown() {
        dropdown.style.display = 'none';
        dropdown.classList.remove('show');
        trigger.classList.remove('active');
    }
    
    // Handle option selection
    options.forEach(option => {
        option.addEventListener('click', function(e) {
            e.stopPropagation();
            
            const value = this.dataset.value;
            const iconClass = this.querySelector('i').className;
            const text = this.querySelector('span').textContent;
            
            // Update hidden input
            hiddenInput.value = value;
            
            // Update trigger display
            const selectedIcon = trigger.querySelector('.selected-icon');
            selectedIcon.innerHTML = `<i class="${iconClass} me-2"></i>${text}`;
            
            // Update active state
            options.forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
            
            closeDropdown();
        });
    });
    
    // Search functionality
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        options.forEach(option => {
            const text = option.querySelector('span').textContent.toLowerCase();
            const iconClass = option.dataset.value.toLowerCase();
            
            if (text.includes(searchTerm) || iconClass.includes(searchTerm)) {
                option.style.display = 'flex';
            } else {
                option.style.display = 'none';
            }
        });
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!container.contains(e.target)) {
            closeDropdown();
        }
    });
    
    // Prevent dropdown close when clicking inside search
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    
    // Keyboard navigation
    container.addEventListener('keydown', function(e) {
        const visibleOptions = Array.from(options).filter(opt => opt.style.display !== 'none');
        let currentIndex = visibleOptions.findIndex(opt => opt.classList.contains('keyboard-focus'));
        
        switch(e.key) {
            case 'ArrowDown':
                e.preventDefault();
                if (dropdown.style.display !== 'block') {
                    openDropdown();
                } else {
                    currentIndex = Math.min(currentIndex + 1, visibleOptions.length - 1);
                    updateKeyboardFocus(visibleOptions, currentIndex);
                }
                break;
                
            case 'ArrowUp':
                e.preventDefault();
                currentIndex = Math.max(currentIndex - 1, 0);
                updateKeyboardFocus(visibleOptions, currentIndex);
                break;
                
            case 'Enter':
                e.preventDefault();
                if (currentIndex >= 0 && visibleOptions[currentIndex]) {
                    visibleOptions[currentIndex].click();
                }
                break;
                
            case 'Escape':
                e.preventDefault();
                closeDropdown();
                trigger.focus();
                break;
        }
    });
    
    function updateKeyboardFocus(visibleOptions, index) {
        visibleOptions.forEach((opt, i) => {
            opt.classList.toggle('keyboard-focus', i === index);
        });
        
        if (visibleOptions[index]) {
            visibleOptions[index].scrollIntoView({ block: 'nearest' });
        }
    }
    
    // Form validation - ensure icon is selected
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!hiddenInput.value) {
                e.preventDefault();
                alert('Silakan pilih icon terlebih dahulu!');
                openDropdown();
                return false;
            }
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', initCustomIconSelect);
</script>

<style>
/* Custom Icon Select Styles */
.custom-icon-select {
    position: relative;
}

.icon-select-trigger {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
    min-height: 48px;
}

.icon-select-trigger:hover {
    border-color: #667eea;
}

.icon-select-trigger.active {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.selected-icon {
    flex: 1;
    display: flex;
    align-items: center;
    font-size: 0.95em;
}

.selected-icon i {
    width: 20px;
    text-align: center;
    font-size: 1.1em;
    color: #667eea;
}

.icon-select-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    z-index: 1000;
    max-height: 350px;
    overflow: hidden;
    display: none;
    margin-top: 5px;
}

.icon-search-box {
    padding: 0.75rem;
    border-bottom: 1px solid #f0f0f0;
    background: #f8f9fa;
}

.icon-search-box input {
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 0.9em;
}

.icon-options {
    max-height: 250px;
    overflow-y: auto;
}

.icon-option {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    border-bottom: 1px solid #f8f9fa;
}

.icon-option:hover {
    background-color: #f8f9fa;
}

.icon-option.active {
    background-color: #667eea;
    color: white;
}

.icon-option i {
    width: 20px;
    text-align: center;
    font-size: 1.1em;
    margin-right: 0.5rem;
    color: #667eea;
}

.icon-option.active i {
    color: white;
}

.icon-option span {
    font-size: 0.9em;
    flex: 1;
}

/* Scrollbar styling */
.icon-options::-webkit-scrollbar {
    width: 6px;
}

.icon-options::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.icon-options::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.icon-options::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Animation for dropdown */
.icon-select-dropdown.show {
    display: block;
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Keyboard focus styling */
.icon-option.keyboard-focus {
    background-color: #e9ecef;
    outline: 2px solid #667eea;
    outline-offset: -2px;
}

.icon-option.keyboard-focus.active {
    background-color: #5a6fd8;
    outline-color: white;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .icon-select-dropdown {
        max-height: 280px;
    }
    
    .icon-options {
        max-height: 200px;
    }
    
    .icon-option {
        padding: 0.6rem;
    }
    
    .icon-option span {
        font-size: 0.85em;
    }
}
</style>

<?php require_once __DIR__ . '/footer.php'; ?>