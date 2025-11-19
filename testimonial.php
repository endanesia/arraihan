<?php
require_once __DIR__ . '/inc/db.php';

// Base URL configuration
$base = '';
if (isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
     $_SERVER['HTTP_HOST'] === '127.0.0.1')) {
    $base = '/dev/';
} else {
    $base = '';
}

// Fetch all approved testimonials
$testimonials = [];
if (function_exists('db') && db()) {
    if ($res = db()->query("SELECT * FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC")) {
        while ($row = $res->fetch_assoc()) { $testimonials[] = $row; }
    }
}

// Page configuration for header template
$page_title = 'Testimonial Jamaah - Raihan Travelindo';
$page_description = 'Testimoni dan pengalaman jamaah yang telah menunaikan ibadah bersama Raihan Travelindo';
$current_page = 'testimonial';

// Extra head content for Cloudflare Turnstile
$extra_head_content = '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';

// Include header template
require_once __DIR__ . '/inc/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Testimonial Jamaah</h1>
        <p>Bagikan pengalaman atau cerita Anda selama mengikuti perjalanan ibadah bersama kami</p>
    </div>
</section>

<!-- Testimonial Content -->
<section class="testimonial-page">
    <div class="container">
        <!-- Testimonial Form -->
        <div class="testimonial-form-card mb-5">
            <h3><i class="fas fa-pen"></i> Kirim Testimonial Anda</h3>
            <p class="text-muted mb-4">Silakan bagikan kesan, cerita, atau saran Anda selama mengikuti perjalanan ibadah bersama kami</p>
            
            <div id="formMessage"></div>
            
            <form id="testimonialForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nama" 
                                   name="nama" 
                                   placeholder="Masukkan Nama Anda" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Judul <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="judul" 
                                   name="judul" 
                                   placeholder="Masukan Judul" 
                                   required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Pengalaman atau kesan Anda <span class="text-danger">*</span></label>
                    <textarea class="form-control" 
                              id="pesan" 
                              name="pesan" 
                              rows="4" 
                              placeholder="Ceritakan pengalaman atau kesan Anda" 
                              required></textarea>
                </div>
                
                <!-- Cloudflare Turnstile -->
                <div class="form-group">
                    <div class="cf-turnstile" data-sitekey="0x4AAAAAACAl8S6dya4dFd3k"></div>
                </div>
                
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Kirim Testimonial
                    </button>
                </div>
            </form>
        </div>

        <!-- Testimonial List -->
        <h2 class="section-title mb-4"><i class="fas fa-comments"></i> Testimoni Jamaah</h2>
        
        <?php if (!empty($testimonials)): ?>
        <div class="testimonial-grid">
            <?php foreach ($testimonials as $testi): ?>
            <div class="testimonial-card">
                <div class="testimonial-icon">
                    <i class="fas fa-quote-left"></i>
                </div>
                <div class="testimonial-header">
                    <div class="author-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="author-info">
                        <h5><?= e($testi['nama']) ?></h5>
                        <p class="text-muted"><?= date('d F Y', strtotime($testi['created_at'])) ?></p>
                    </div>
                </div>
                <h5 class="testimonial-title"><?= e($testi['judul']) ?></h5>
                <p class="testimonial-text"><?= nl2br(e($testi['pesan'])) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
            <p class="text-muted">Belum ada testimonial yang disetujui.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<style>
.page-header {
    background: var(--gradient-primary);
    color: var(--light-color);
    padding: 80px 0 60px;
    text-align: center;
}

.page-header h1 {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 15px;
}

.page-header p {
    font-size: 18px;
    opacity: 0.9;
}

.testimonial-page {
    padding: 80px 0;
    background: var(--light-gray);
}

.testimonial-form-card {
    background: var(--light-color);
    padding: 40px;
    border-radius: 15px;
    box-shadow: var(--shadow-md);
}

.testimonial-form-card h3 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
    color: var(--primary-color);
}

.testimonial-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
}

.testimonial-card {
    background: var(--light-color);
    padding: 30px;
    border-radius: 15px;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.testimonial-card:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
}

.testimonial-icon {
    color: var(--primary-color);
    font-size: 36px;
    margin-bottom: 15px;
    opacity: 0.3;
}

.testimonial-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--light-color);
    font-size: 20px;
    flex-shrink: 0;
}

.author-info h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--dark-color);
}

.author-info p {
    margin: 0;
    font-size: 13px;
}

.testimonial-card .testimonial-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.testimonial-card .testimonial-text {
    font-size: 15px;
    line-height: 1.8;
    color: var(--gray-color);
    margin: 0;
    flex: 1;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: var(--dark-color);
}

.form-control {
    width: 100%;
    padding: 12px 15px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(10, 126, 62, 0.1);
}

.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-info {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.btn-block {
    width: 100%;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: 600;
}

@media (max-width: 991px) {
    .testimonial-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .testimonial-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
document.getElementById('testimonialForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const nama = document.getElementById('nama').value.trim();
    const judul = document.getElementById('judul').value.trim();
    const pesan = document.getElementById('pesan').value.trim();
    const turnstileElement = document.querySelector('[name="cf-turnstile-response"]');
    const messageDiv = document.getElementById('formMessage');
    
    // Validate Turnstile
    if (!turnstileElement || !turnstileElement.value) {
        messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Mohon selesaikan verifikasi keamanan terlebih dahulu.</div>';
        return;
    }
    
    // Validate required fields
    if (!nama || !judul || !pesan) {
        messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Semua field harus diisi.</div>';
        return;
    }
    
    // Show loading
    messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Mengirim testimonial...</div>';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('nama', nama);
    formData.append('judul', judul);
    formData.append('pesan', pesan);
    formData.append('cf-turnstile-response', turnstileElement.value);
    
    try {
        const response = await fetch('testimonial-submit.php', {
            method: 'POST',
            body: formData
        });
        
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        
        const result = await response.json();
        
        if (result.success) {
            messageDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + result.message + '</div>';
            this.reset();
            if (window.turnstile) {
                window.turnstile.reset();
            }
            
            // Scroll to message
            messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        } else {
            messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + (result.message || 'Terjadi kesalahan. Silakan coba lagi.') + '</div>';
            if (window.turnstile) {
                window.turnstile.reset();
            }
        }
    } catch (error) {
        console.error('Error:', error);
        messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan koneksi. Silakan coba lagi. Error: ' + error.message + '</div>';
        if (window.turnstile) {
            window.turnstile.reset();
        }
    }
});
</script>

<?php
// Include footer template
require_once __DIR__ . '/inc/footer.php';
?>
