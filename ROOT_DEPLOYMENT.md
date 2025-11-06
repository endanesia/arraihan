# Deploy ke ROOT Domain - Solusi Subdirektori 404

## Masalah Identified:
- ✅ Root domain accessible: https://arraihantravelindo.com
- ❌ Subdirectories not working: https://arraihantravelindo.com/dev/
- ❌ New subdirectories also fail: https://arraihantravelindo.com/dev2/

## Root Cause:
**Hosting server tidak mendukung subdirektori atau ada konfigurasi yang memblokir akses subdirektori**

## Solution: Deploy ke Root Domain

### 1. Backup existing under-construction
```bash
# Di cPanel File Manager:
# Rename index.html menjadi index-backup.html (jika ada)
```

### 2. Upload files ke ROOT
Upload semua files dari GitHub ke `/public_html/` langsung (BUKAN `/public_html/dev/`)

### 3. Struktur yang benar:
```
/public_html/
├── .htaccess (sudah diupdate untuk root)
├── index.php (website utama)
├── admin/
├── css/
├── js/
├── images/
├── inc/
│   └── config.php (sudah diupdate untuk root)
└── install/
```

### 4. Update Configuration:
- ✅ config.php sudah diupdate: base_url = ''
- ✅ .htaccess sudah diupdate: RewriteBase /
- ✅ uploads_url sudah diupdate: /images/gallery

### 5. Test URLs setelah deploy:
- https://arraihantravelindo.com/ (website utama)
- https://arraihantravelindo.com/admin/login (admin panel)

## Notes:
- Website akan menggantikan halaman under-construction
- Database tetap menggunakan kredensial yang sama
- Semua assets (CSS/JS/Images) akan diakses dari root

## Fallback:
Jika tidak ingin menggantikan under-construction, bisa deploy dengan nama domain/subdomain berbeda.