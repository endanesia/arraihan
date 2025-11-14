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
        <div class="row">
            <!-- Testimonial List -->
            <div class="col-lg-8">
                <h2 class="section-title mb-4">Testimoni Jamaah</h2>
                
                <?php if (!empty($testimonials)): ?>
                <div class="testimonial-list">
                    <?php foreach ($testimonials as $testi): ?>
                    <div class="testimonial-item">
                        <div class="testimonial-header">
                            <div class="author-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="author-info">
                                <h4><?= e($testi['nama']) ?></h4>
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
            
            <!-- Testimonial Form -->
            <div class="col-lg-4">
                <div class="testimonial-form-wrapper">
                    <h3>Kirim Testimonial</h3>
                    <p class="text-muted mb-4">Silakan bagikan kesan, cerita, atau saran Anda selama mengikuti perjalanan ibadah bersama kami</p>
                    
                    <div id="formMessage"></div>
                    
                    <form id="testimonialForm">
                        <div class="form-group">
                            <label>Nama <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nama" 
                                   name="nama" 
                                   placeholder="Masukkan Nama Anda" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label>Judul <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control" 
                                   id="judul" 
                                   name="judul" 
                                   placeholder="Masukan Judul" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label>Pengalaman atau kesan Anda <span class="text-danger">*</span></label>
                            <textarea class="form-control" 
                                      id="pesan" 
                                      name="pesan" 
                                      rows="5" 
                                      placeholder="Ceritakan pengalaman atau kesan Anda" 
                                      required></textarea>
                        </div>
                        
                        <!-- Cloudflare Turnstile -->
                        <div class="form-group">
                            <div class="cf-turnstile" data-sitekey="0x4AAAAAACAl8S6dya4dFd3k"></div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane"></i> Kirim Testimonial
                        </button>
                        
                        <p class="small text-muted mt-3 mb-0">
                            <i class="fas fa-info-circle"></i> Testimonial Anda akan ditampilkan setelah disetujui oleh admin.
                        </p>
                    </form>
                </div>
            </div>
        </div>
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
}

.testimonial-list {
    display: flex;
    flex-direction: column;
    gap: 30px;
}

.testimonial-item {
    background: var(--light-color);
    padding: 30px;
    border-radius: 15px;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.testimonial-item:hover {
    box-shadow: var(--shadow-lg);
    transform: translateY(-5px);
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

.author-info h4 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: var(--dark-color);
}

.author-info p {
    margin: 0;
    font-size: 14px;
}

.testimonial-item .testimonial-title {
    font-size: 20px;
    font-weight: 600;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.testimonial-item .testimonial-text {
    font-size: 16px;
    line-height: 1.8;
    color: var(--gray-color);
    margin: 0;
}

.testimonial-form-wrapper {
    background: var(--light-gray);
    padding: 30px;
    border-radius: 15px;
    position: sticky;
    top: 100px;
}

.testimonial-form-wrapper h3 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 10px;
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

.btn-block {
    width: 100%;
    padding: 12px 20px;
    font-size: 16px;
    font-weight: 600;
}

@media (max-width: 991px) {
    .testimonial-form-wrapper {
        position: static;
        margin-top: 40px;
    }
}
</style>

<script>
document.getElementById('testimonialForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const turnstileResponse = document.querySelector('[name="cf-turnstile-response"]').value;
    formData.append('cf-turnstile-response', turnstileResponse);
    
    const messageDiv = document.getElementById('formMessage');
    messageDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Mengirim testimonial...</div>';
    
    try {
        const response = await fetch('testimonial-submit.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            messageDiv.innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + result.message + '</div>';
            this.reset();
            turnstile.reset();
        } else {
            messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> ' + result.message + '</div>';
            turnstile.reset();
        }
    } catch (error) {
        messageDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan. Silakan coba lagi.</div>';
        turnstile.reset();
    }
});
</script>

<?php
// Include footer template
require_once __DIR__ . '/inc/footer.php';
?>
