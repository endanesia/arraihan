# Panduan Kustomisasi Website
# Raihan Travelindo

## 📝 Daftar Isi
1. [Mengganti Informasi Perusahaan](#1-mengganti-informasi-perusahaan)
2. [Mengganti Nomor WhatsApp](#2-mengganti-nomor-whatsapp)
3. [Mengganti Warna Theme](#3-mengganti-warna-theme)
4. [Mengganti Logo](#4-mengganti-logo)
5. [Mengganti Gambar](#5-mengganti-gambar)
6. [Mengedit Paket & Harga](#6-mengedit-paket--harga)
7. [Menambah/Menghapus Section](#7-menambahmenghapus-section)
8. [Optimasi SEO](#8-optimasi-seo)

---

## 1. Mengganti Informasi Perusahaan

### Nama Perusahaan
**File**: `index.html`
- Cari: `Raihan Travelindo`
- Ganti dengan nama perusahaan Anda

### Alamat
**File**: `index.html` (Section Kontak)
```html
<p>Jl. Raya Tanah Kusir No. 123<br>
Jakarta Selatan, DKI Jakarta 12345<br>
Indonesia</p>
```

### Email
**File**: `index.html` (Section Kontak)
```html
<p>info@baitulmabrur.com<br>
customer@baitulmabrur.com</p>
```

### Nomor Telepon
**File**: `index.html` (Section Kontak)
```html
<p>+62 812-3456-7890<br>
+62 21-1234-5678</p>
```

---

## 2. Mengganti Nomor WhatsApp

### Di HTML
**File**: `index.html`
Cari semua: `6281234567890`
Ganti dengan: Nomor WhatsApp Anda (format: 628xxxxxxxxxx)

Lokasi nomor WhatsApp di HTML:
1. Header - Button WhatsApp
2. Hero Section - Button Konsultasi
3. CTA Section - Button WhatsApp
4. Footer - Link WhatsApp
5. Floating WhatsApp Button

### Di JavaScript
**File**: `js/script.js`
```javascript
// Line 120
const whatsappNumber = '6281234567890'; // Ganti dengan nomor Anda
```

---

## 3. Mengganti Warna Theme

**File**: `css/style.css`

### Edit CSS Variables (Line 8-17)
```css
:root {
    --primary-color: #0a7e3e;      /* Hijau utama - ganti sesuai keinginan */
    --secondary-color: #f39c12;     /* Emas - ganti sesuai keinginan */
    --dark-color: #1a1a1a;          /* Warna gelap */
    --light-color: #ffffff;         /* Warna terang */
    --gray-color: #6c757d;          /* Abu-abu */
    --light-gray: #f8f9fa;          /* Abu-abu terang */
}
```

### Contoh Kombinasi Warna:
```css
/* Biru & Emas */
--primary-color: #003d82;
--secondary-color: #ffa500;

/* Merah Maroon & Emas */
--primary-color: #800020;
--secondary-color: #ffd700;

/* Hijau Tosca & Oranye */
--primary-color: #00a896;
--secondary-color: #ff6b35;
```

---

## 4. Mengganti Logo

### Opsi 1: Upload Logo Gambar
1. Upload file logo ke folder `/images/`
2. Edit `index.html` (Line 14-17)
```html
<div class="logo">
    <img src="images/logo.png" alt="Logo" style="height: 40px;">
    <span>Nama Perusahaan</span>
</div>
```

### Opsi 2: Ganti Icon
**File**: `index.html`
```html
<div class="logo">
    <i class="fas fa-kaaba"></i> <!-- Ganti dengan icon lain -->
    <span>Raihan Travelindo</span>
</div>
```

Icon alternatif dari Font Awesome:
- `<i class="fas fa-mosque"></i>` - Masjid
- `<i class="fas fa-plane-departure"></i>` - Pesawat
- `<i class="fas fa-praying-hands"></i>` - Tangan berdoa
- `<i class="fas fa-star-and-crescent"></i>` - Bulan bintang

---

## 5. Mengganti Gambar

### Hero Background
**File**: `css/style.css` (Line 221)
```css
.hero {
    background: url('images/hero-bg.jpg') center/cover;
    /* atau gunakan URL eksternal */
}
```

### Galeri
**File**: `index.html` (Section Galeri)
```html
<img src="images/galeri-1.jpg" alt="Deskripsi gambar">
```

### Tips Upload Gambar:
1. Buat folder `/images/` jika belum ada
2. Upload gambar dengan resolusi optimal:
   - Hero: 1920x1080px
   - Galeri: 800x600px
   - Thumbnail: 400x300px
3. Compress gambar untuk loading cepat
4. Format rekomendasi: JPG atau WebP

---

## 6. Mengedit Paket & Harga

**File**: `index.html` (Section Paket)

### Edit Paket Umroh
```html
<div class="paket-card">
    <div class="paket-price">
        <span class="price-value">Rp 24 Juta</span> <!-- Edit harga -->
    </div>
    <ul class="paket-features">
        <li><i class="fas fa-check"></i> Direct Flight</li>
        <!-- Tambah/hapus fitur sesuai kebutuhan -->
    </ul>
</div>
```

### Menambah Paket Baru
Copy salah satu `<div class="paket-card">` dan edit kontennya.

---

## 7. Menambah/Menghapus Section

### Menambah Section Baru
```html
<!-- Tambahkan setelah section yang ada -->
<section class="nama-section" id="nama-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Judul Section</h2>
            <p class="section-desc">Deskripsi section</p>
        </div>
        <!-- Konten section -->
    </div>
</section>
```

### Menghapus Section
Cari section yang ingin dihapus dan delete seluruh blok `<section>...</section>`

### Update Navigasi
Jangan lupa update menu navigasi di header:
```html
<li><a href="#nama-section" class="nav-link">Menu Baru</a></li>
```

---

## 8. Optimasi SEO

### Meta Tags
**File**: `index.html` (Line 4-6)
```html
<meta name="description" content="Deskripsi website Anda (150-160 karakter)">
<title>Judul Website - Travel Umroh & Haji Terpercaya</title>

<!-- Tambahkan meta tags tambahan -->
<meta name="keywords" content="umroh, haji, travel umroh, travel haji">
<meta name="author" content="Nama Perusahaan">
<meta property="og:title" content="Judul untuk Social Media">
<meta property="og:description" content="Deskripsi untuk Social Media">
<meta property="og:image" content="URL gambar untuk thumbnail">
```

### Update Sitemap
**File**: `sitemap.xml`
- Update `<loc>` dengan URL aktual website
- Update `<lastmod>` saat ada perubahan
- Tambah halaman baru jika ada

### Google Analytics (Opsional)
Tambahkan sebelum `</head>` di `index.html`:
```html
<!-- Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', 'GA_MEASUREMENT_ID');
</script>
```

---

## 🔧 Tips Tambahan

### Testing di Berbagai Device
1. Desktop: Chrome, Firefox, Safari
2. Mobile: Gunakan Chrome DevTools (F12 > Toggle Device Toolbar)
3. Test di device fisik jika memungkinkan

### Backup
Selalu backup file sebelum melakukan perubahan besar:
```
backup/
├── index.html.bak
├── style.css.bak
└── script.js.bak
```

### Performance
1. Compress gambar: https://tinypng.com
2. Minify CSS & JS untuk production
3. Enable caching (sudah di .htaccess)
4. Use CDN untuk library eksternal

### Browser Cache
Setelah update, clear browser cache:
- Chrome: Ctrl+Shift+Del
- Firefox: Ctrl+Shift+Del
- Hard Reload: Ctrl+F5

---

## 📞 Butuh Bantuan?

Jika mengalami kesulitan, dokumentasikan:
1. Screenshot error
2. File yang diubah
3. Langkah yang sudah dilakukan

Hubungi developer untuk bantuan lebih lanjut.

---

**Happy Customizing! 🎨**
