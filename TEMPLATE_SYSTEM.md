# Template System - Panduan Penggunaan

Sistem template telah dibuat untuk mempermudah pembuatan halaman baru tanpa perlu menulis ulang header dan footer di setiap halaman.

## Struktur Template

### File Template
1. **`inc/header.php`** - Header dan navigation menu
2. **`inc/footer.php`** - Footer, social media buttons, dan JavaScript
3. **`css/paket-detail.css`** - CSS khusus untuk halaman detail paket

## Cara Membuat Halaman Baru

### Contoh 1: Halaman Sederhana

```php
<?php 
require_once __DIR__ . '/inc/db.php';

// Konfigurasi halaman
$page_title = 'Judul Halaman - Ar Raihan Travelindo';
$page_description = 'Deskripsi halaman untuk SEO';
$current_page = 'home'; // atau 'paket', 'artikel', 'galeri', dll

// Optional: Load social links jika belum
$link_whatsapp = get_setting('whatsapp', '');

// Include header
require_once __DIR__ . '/inc/header.php';
?>

    <!-- Konten halaman Anda di sini -->
    <section class="my-section">
        <div class="container">
            <h1>Konten Halaman</h1>
            <p>Isi konten...</p>
        </div>
    </section>

<?php
// Include footer
require_once __DIR__ . '/inc/footer.php';
?>
```

### Contoh 2: Halaman dengan Bootstrap

```php
<?php 
require_once __DIR__ . '/inc/db.php';

// Konfigurasi halaman
$page_title = 'Halaman dengan Bootstrap';
$page_description = 'Contoh halaman menggunakan Bootstrap';
$current_page = 'paket';
$include_bootstrap = true; // Enable Bootstrap CSS & JS

// Include header
require_once __DIR__ . '/inc/header.php';
?>

    <section class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <h2>Column 1</h2>
            </div>
            <div class="col-md-6">
                <h2>Column 2</h2>
            </div>
        </div>
    </section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
```

### Contoh 3: Halaman dengan Swiper Slider

```php
<?php 
require_once __DIR__ . '/inc/db.php';

// Konfigurasi halaman
$page_title = 'Galeri Foto';
$page_description = 'Galeri foto perjalanan umroh';
$current_page = 'galeri';
$include_swiper = true; // Enable Swiper CSS & JS

// Include header
require_once __DIR__ . '/inc/header.php';
?>

    <!-- Swiper Slider -->
    <div class="swiper mySwiper">
        <div class="swiper-wrapper">
            <div class="swiper-slide">Slide 1</div>
            <div class="swiper-slide">Slide 2</div>
        </div>
        <div class="swiper-pagination"></div>
    </div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
```

### Contoh 4: Halaman dengan Custom CSS

```php
<?php 
require_once __DIR__ . '/inc/db.php';

// Konfigurasi halaman
$page_title = 'Custom Page';
$page_description = 'Halaman dengan custom CSS';
$current_page = 'home';

// Custom CSS di head
$extra_head_content = '<link rel="stylesheet" href="css/custom-page.css?v=' . time() . '">';

// Include header
require_once __DIR__ . '/inc/header.php';
?>

    <section class="custom-section">
        <h1>Custom Styled Content</h1>
    </section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
```

### Contoh 5: Halaman dengan Custom JavaScript

```php
<?php 
require_once __DIR__ . '/inc/db.php';

// Konfigurasi halaman
$page_title = 'Interactive Page';
$current_page = 'home';

// Custom JavaScript di footer
$extra_footer_scripts = '<script src="js/custom-script.js"></script>';

// Include header
require_once __DIR__ . '/inc/header.php';
?>

    <section>
        <button id="myButton">Click Me</button>
    </section>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
```

## Variabel yang Tersedia

### Variabel Wajib
```php
$page_title = 'Judul Halaman';           // Untuk <title> tag
$page_description = 'Deskripsi';         // Untuk meta description
$current_page = 'home';                  // Untuk active menu (home, paket, artikel, jadwal, galeri, tentang, kontak)
```

### Variabel Optional
```php
$include_bootstrap = true;               // Load Bootstrap CSS & JS
$include_swiper = true;                  // Load Swiper CSS & JS
$extra_head_content = '<link...>';       // Custom CSS atau meta tags
$extra_footer_scripts = '<script...>';   // Custom JavaScript
$base = '';                              // Base URL (otomatis terdeteksi)
```

### Variabel Social Media (Auto-loaded dari database)
```php
$link_whatsapp
$link_facebook
$link_instagram
$link_youtube
$link_twitter
$link_tiktok
$link_threads
$phone_number
```

## Struktur File

```
dev/
├── inc/
│   ├── header.php          # Template header & navigation
│   ├── footer.php          # Template footer & scripts
│   ├── db.php              # Database connection & functions
│   └── config.php          # Configuration
├── css/
│   ├── style.css           # Main stylesheet
│   └── paket-detail.css    # Detail page stylesheet
├── js/
│   └── script.js           # Main JavaScript
├── index.php               # Homepage (menggunakan template)
├── paket-detail.php        # Package detail (menggunakan template)
└── artikel.php             # Articles page (bisa diupdate)
```

## Keuntungan Sistem Template

✅ **DRY (Don't Repeat Yourself)** - Tidak perlu copy-paste header/footer
✅ **Maintenance Mudah** - Update menu cukup di 1 file (`inc/header.php`)
✅ **Konsistensi** - Semua halaman punya struktur yang sama
✅ **Flexibel** - Bisa custom CSS/JS per halaman
✅ **SEO Friendly** - Setiap halaman bisa punya title dan description sendiri

## Update Menu Navigasi

Untuk mengubah menu navigasi, edit file `inc/header.php` pada bagian:

```php
<ul class="nav-menu" id="navMenu">
    <li><a href="..." class="nav-link">Menu Item</a></li>
</ul>
```

## Update Footer

Untuk mengubah footer, edit file `inc/footer.php` pada bagian yang diinginkan.

## Contoh Lengkap: Halaman "Tentang Kami"

Lihat file `paket-detail.php` sebagai referensi implementasi lengkap.

---

**Catatan:** File lama (yang belum menggunakan template) tersimpan dengan suffix `-old.php` sebagai backup.
