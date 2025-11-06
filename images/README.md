# Folder Images

Folder ini digunakan untuk menyimpan semua gambar website.

## Struktur Rekomendasi:

```
images/
├── logo.png                 # Logo perusahaan
├── favicon.ico              # Favicon website
├── hero/
│   └── hero-bg.jpg         # Background hero section
├── galeri/
│   ├── galeri-1.jpg        # Foto jamaah 1
│   ├── galeri-2.jpg        # Foto jamaah 2
│   └── ...
├── testimonial/
│   ├── video-thumb-1.jpg   # Thumbnail video 1
│   └── ...
├── partners/
│   ├── partner-1.png       # Logo partner 1
│   └── ...
└── about/
    └── kantor.jpg          # Foto kantor
```

## Spesifikasi Gambar:

### Logo
- Format: PNG dengan background transparan
- Ukuran: 200x80px
- Max size: 50KB

### Hero Background
- Format: JPG
- Resolusi: 1920x1080px
- Max size: 200KB (compressed)

### Galeri
- Format: JPG
- Resolusi: 800x600px
- Max size: 100KB per gambar

### Thumbnail Video
- Format: JPG
- Resolusi: 640x480px
- Max size: 80KB

### Partner Logos
- Format: PNG dengan background transparan
- Ukuran: 200x100px
- Max size: 30KB

## Tools Rekomendasi:

### Compress Gambar
- TinyPNG: https://tinypng.com
- Squoosh: https://squoosh.app
- ImageOptim (Mac)

### Resize Gambar
- Photoshop
- GIMP (Free)
- Online: https://www.iloveimg.com/resize-image

### Convert Format
- CloudConvert: https://cloudconvert.com
- Online-Convert: https://www.online-convert.com

## Sumber Gambar Gratis:

### Stock Photos
- Unsplash: https://unsplash.com
- Pexels: https://www.pexels.com
- Pixabay: https://pixabay.com

### Islamic Images
- Freepik: https://www.freepik.com (search: mosque, kaaba, mecca)
- Islamic Vector: https://www.vecteezy.com

## Tips:

1. **Optimasi**: Selalu compress gambar sebelum upload
2. **Naming**: Gunakan nama file yang deskriptif (contoh: `jamaah-masjidil-haram-2024.jpg`)
3. **Alt Text**: Jangan lupa tambahkan alt text untuk SEO
4. **WebP**: Pertimbangkan format WebP untuk performa lebih baik
5. **Lazy Loading**: Gambar akan di-lazy load otomatis oleh JavaScript

## Contoh Penggunaan di HTML:

```html
<!-- Regular Image -->
<img src="images/galeri/galeri-1.jpg" alt="Jamaah di Masjidil Haram">

<!-- With Lazy Loading -->
<img data-src="images/galeri/galeri-1.jpg" alt="Jamaah di Masjidil Haram" class="lazy">

<!-- Background Image via CSS -->
<div class="hero" style="background-image: url('images/hero/hero-bg.jpg')"></div>
```

---

**Note**: Folder ini saat ini kosong. Upload gambar Anda sesuai kebutuhan.
