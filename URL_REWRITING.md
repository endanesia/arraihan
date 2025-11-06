# URL Rewriting Documentation - Panduan Menghilangkan Ekstensi .php

## Masalah yang Diselesaikan
Project ini menggunakan URL rewriting untuk menghilangkan ekstensi `.php` dari URL, sehingga:
- `http://localhost/dev/index.php` → `http://localhost/dev/index`
- `http://localhost/dev/test-db.php` → `http://localhost/dev/test-db`

## Konfigurasi .htaccess

### 1. URL Rewriting untuk Root Directory
```apache
# External redirect from /file.php to /file (but not for admin folder)
RewriteCond %{THE_REQUEST} /dev/([^./\s]+)\.php [NC]
RewriteCond %{REQUEST_URI} !^/dev/admin/ [NC]
RewriteRule ^ /dev/%1? [NC,L,R=301]

# Internal rewrite from /file to /file.php (but not for admin folder)
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME}.php -f
RewriteCond %{REQUEST_URI} !^/dev/admin/ [NC]
RewriteRule ^([^./]+)/?$ $1.php [L,QSA]
```

### 2. URL Rewriting untuk Admin Panel
Admin panel menggunakan aturan khusus untuk pretty URLs:

**External Redirect untuk Admin:**
```apache
# Admin: External redirect from /admin/file.php to /admin/file
RewriteCond %{THE_REQUEST} /dev/admin/([^./\s]+)\.php [NC]
RewriteRule ^ /dev/admin/%1? [NC,L,R=301]
```

**Specific Admin Routes:**
```apache
RewriteRule ^admin/login/?$ admin/login.php [L,QSA]
RewriteRule ^admin/dashboard/?$ admin/dashboard.php [L,QSA]
RewriteRule ^admin/posts/?$ admin/posts.php [L,QSA]
# ... dan seterusnya
```

**Fallback untuk Admin:**
```apache
# Fallback: /admin/page -> /admin/page.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^admin/([A-Za-z0-9_-]+)$ admin/$1.php [L,QSA]
```

## Cara Kerja

### External Redirect (301)
- Ketika user mengakses `file.php`, server akan redirect ke `file`
- Status HTTP 301 (Permanent Redirect) memberitahu search engine bahwa URL tanpa ekstensi adalah URL yang benar

### Internal Rewrite
- Ketika user mengakses `file`, server secara internal mencari `file.php`
- Jika file tersebut ada, maka request akan dilanjutkan ke `file.php`
- User tetap melihat URL tanpa ekstensi di browser

## Persyaratan

### 1. Apache mod_rewrite
Pastikan mod_rewrite aktif di Apache:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

### 2. AllowOverride
Pastikan AllowOverride diset ke All:
```apache
<Directory "C:/xampp82/htdocs">
    AllowOverride All
</Directory>
```

## Testing

Untuk test apakah URL rewriting berfungsi:

1. **Akses file test:**
   - `http://localhost/dev/test-rewrite.php` (dengan ekstensi)
   - `http://localhost/dev/test-rewrite` (tanpa ekstensi)

2. **File-file utama:**
   - `http://localhost/dev/index` ✓
   - `http://localhost/dev/test-db` ✓

3. **Admin panel tetap menggunakan pretty URLs:**
   - `http://localhost/dev/admin/dashboard` ✓
   - `http://localhost/dev/admin/posts` ✓

## Troubleshooting

### Error 404 ketika mengakses tanpa .php
1. Periksa mod_rewrite aktif: `php -m | grep rewrite`
2. Periksa AllowOverride di httpd.conf
3. Periksa file .htaccess ada dan readable
4. Restart Apache setelah perubahan konfigurasi

### Redirect loop
- Pastikan RewriteBase sesuai dengan folder project
- Pastikan tidak ada aturan yang bertentangan

### Admin URLs tidak berfungsi
- Aturan admin harus diletakkan SEBELUM aturan general
- Pastikan urutan aturan benar dalam .htaccess
- Pastikan ada aturan external redirect untuk admin: `RewriteCond %{THE_REQUEST} /dev/admin/([^./\s]+)\.php [NC]`
- Test dengan file: `/admin/test-admin-rewrite`

### Masalah Khusus pada Admin Panel
Jika masih harus menggunakan `.php` di URL admin:
1. Periksa aturan external redirect untuk admin sudah ada
2. Pastikan aturan admin diletakkan sebelum aturan root directory
3. Test dengan mengakses: `http://localhost/dev/admin/dashboard.php` → harus redirect ke `http://localhost/dev/admin/dashboard`

## File yang Terpengaruh
- `/.htaccess` - Konfigurasi URL rewriting
- `/test-rewrite.php` - File testing (bisa dihapus setelah testing)

## Note Penting
- Semua link internal dalam aplikasi harus menggunakan URL tanpa ekstensi .php
- Search engine akan mengindex URL tanpa ekstensi
- Performa tidak terpengaruh karena rewriting dilakukan di level Apache