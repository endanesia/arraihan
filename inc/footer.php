<?php
/**
 * Footer Template
 * Include this file in all pages to show consistent footer
 * 
 * Required variables (should be loaded before including this file):
 * - Social media links: $link_whatsapp, $link_facebook, $link_instagram, etc.
 */

// Load social links if not already loaded
if (!function_exists('get_setting')) {
    require_once __DIR__ . '/db.php';
}

if (!isset($link_whatsapp)) {
    $link_whatsapp = get_setting('whatsapp', '');
    $link_facebook = get_setting('facebook', '');
    $link_instagram = get_setting('instagram', '');
    $link_youtube = get_setting('youtube', '');
    $link_twitter = get_setting('twitter', '');
    $link_tiktok = get_setting('tiktok', '');
    $link_threads = get_setting('threads', '');
}
?>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <div class="footer-logo">
                        <img src="<?= $base ?? '' ?>images/logo.png" alt="Raihan Travelindo" style="height: 40px;">
                        <span>Arraihan Travelindo</span>
                    </div>
                    <p>Jl. Laksda Adi Sucipto No.176B, Blimbing, Kec. Blimbing, Kota Malang, Jawa Timur 65124</p>
                    <div class="footer-social">
                        <?php if (!empty($link_whatsapp)): ?><a href="<?= e($link_whatsapp) ?>" target="_blank"><i class="fab fa-whatsapp"></i></a><?php endif; ?>
                        <?php if (!empty($link_facebook)): ?><a href="<?= e($link_facebook) ?>" target="_blank"><i class="fab fa-facebook"></i></a><?php endif; ?>
                        <?php if (!empty($link_instagram)): ?><a href="<?= e($link_instagram) ?>" target="_blank"><i class="fab fa-instagram"></i></a><?php endif; ?>
                        <?php if (!empty($link_youtube)): ?><a href="<?= e($link_youtube) ?>" target="_blank"><i class="fab fa-youtube"></i></a><?php endif; ?>
                        <?php if (!empty($link_twitter)): ?><a href="<?= e($link_twitter) ?>" target="_blank"><i class="fab fa-twitter"></i></a><?php endif; ?>
                        <?php if (!empty($link_tiktok)): ?><a href="<?= e($link_tiktok) ?>" target="_blank"><i class="fab fa-tiktok"></i></a><?php endif; ?>
                        <?php if (!empty($link_threads)): ?><a href="<?= e($link_threads) ?>" target="_blank"><i class="fas fa-at"></i></a><?php endif; ?>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Menu</h4>
                    <ul>
                        <li><a href="<?= $base ?? '' ?>index.php#home">Home</a></li>
                        <li><a href="<?= $base ?? '' ?>index.php#paket">Paket Umroh</a></li>
                        <li><a href="<?= $base ?? '' ?>index.php#keunggulan">Keunggulan</a></li>
                        <li><a href="<?= $base ?? '' ?>index.php#jadwal">Jadwal</a></li>
                        <li><a href="<?= $base ?? '' ?>index.php#galeri">Galeri</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Layanan</h4>
                    <ul>
                        <li><a href="<?= $base ?? '' ?>index.php#paket">Paket Umroh</a></li>
                        <li><a href="<?= $base ?? '' ?>index.php#paket">Haji Khusus</a></li>
                        <li><a href="<?= $base ?? '' ?>index.php#paket">Badal Haji</a></li>
                        <li><a href="<?= $base ?? '' ?>index.php#paket">Badal Umroh</a></li>
                        <li><a href="<?= $base ?? '' ?>index.php#tentang">Tentang Kami</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Legalitas</h4>
                    <ul>
                        <li><i class="fas fa-check-circle"></i> Izin PPIU Kemenag RI</li>
                        <li><i class="fas fa-check-circle"></i> Tergabung AMPHURI</li>
                        <li><i class="fas fa-check-circle"></i> Anggota IATA</li>
                        <li><i class="fas fa-check-circle"></i> Anggota AITTA</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Raihan Travelindo. All Rights Reserved.</p>
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float Button -->
    <?php if (!empty($link_whatsapp)): ?>
    <a href="<?= e($link_whatsapp) ?>" class="whatsapp-float" target="_blank"><i class="fab fa-whatsapp"></i></a>
    <?php endif; ?>

    <!-- Social Media Float Buttons -->
    <?php if (!empty($link_facebook)): ?>
    <a href="<?= e($link_facebook) ?>" class="social-float facebook-float" target="_blank">
        <i class="fab fa-facebook-f"></i>
    </a>
    <?php endif; ?>
    
    <?php if (!empty($link_youtube)): ?>
    <a href="<?= e($link_youtube) ?>" class="social-float youtube-float" target="_blank" 
       style="position: fixed; bottom: 175px; right: 30px; width: 50px; height: 50px; background: #FF0000 !important; z-index: 1000 !important; display: flex !important;">
        <i class="fab fa-youtube"></i>
    </a>
    <?php endif; ?>
    
    <?php if (!empty($link_instagram)): ?>
    <a href="<?= e($link_instagram) ?>" class="social-float instagram-float" target="_blank">
        <i class="fab fa-instagram"></i>
    </a>
    <?php endif; ?>
    
    <?php if (!empty($link_threads)): ?>
    <a href="<?= e($link_threads) ?>" class="social-float threads-float" target="_blank"
       style="position: fixed; bottom: 240px; right: 30px; width: 50px; height: 50px; background: #000000 !important; z-index: 1000 !important; display: flex !important;">
        <i class="fas fa-at"></i>
    </a>
    <?php endif; ?>
    
    <?php if (!empty($link_tiktok)): ?>
    <a href="<?= e($link_tiktok) ?>" class="social-float tiktok-float" target="_blank">
        <i class="fab fa-tiktok"></i>
    </a>
    <?php endif; ?>

    <!-- Scroll to Top Button -->
    <button class="scroll-top" id="scrollTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <?php if (isset($include_swiper) && $include_swiper): ?>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- Swiper Initialization -->
    <script>
        const packageSwiper = new Swiper('.packageSwiper', {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
                dynamicBullets: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                640: {
                    slidesPerView: 2,
                    spaceBetween: 20,
                },
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                },
            },
        });
    </script>
    <?php endif; ?>
    
    <?php if (isset($include_bootstrap) && $include_bootstrap): ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php endif; ?>
    
    <!-- Main JavaScript -->
    <script src="<?= $base ?? '' ?>js/script.js"></script>
    
    <?php if (isset($extra_footer_scripts)): ?>
    <!-- Extra footer scripts -->
    <?= $extra_footer_scripts ?>
    <?php endif; ?>
</body>
</html>
