# Panduan Deploy ke Production

## Langkah-langkah Deploy:

### 1. Persiapan File
- Ganti nama `.htaccess-production` menjadi `.htaccess`
- Ganti nama `config-production.php` menjadi `config.php`
- Pastikan database sudah dibuat di hosting

### 2. Konfigurasi Database
Edit file `inc/config.php`:
```php
'db' => [
    'host' => 'localhost', // atau IP server database
    'user' => 'username_database_hosting',
    'pass' => 'password_database_hosting', 
    'name' => 'nama_database_production',
    'charset' => 'utf8mb4'
]
```

### 3. Upload Files
- Upload semua file ke root directory hosting (public_html atau www)
- Pastikan struktur folder tetap sama
- Set permission untuk folder `images/gallery` menjadi 755 atau 777

### 4. Import Database
- Export database dari development: `umroh_cms`
- Import ke database production
- Atau jalankan file `install/install.php` untuk setup database baru

### 5. Test Website
- Akses domain Anda
- Test semua fungsi (admin, upload gambar, dll)
- Pastikan tidak ada error 404

## Troubleshooting Error 404:

### Cek Apache mod_rewrite
Pastikan hosting support mod_rewrite. Jika tidak:
1. Hapus semua RewriteRule dari .htaccess
2. Gunakan URL dengan ekstensi .php (index.php, admin/login.php)

### Cek File Permissions
- Folder: 755
- File PHP: 644
- File .htaccess: 644

### Cek Path Configuration
Pastikan semua path di config.php sudah benar untuk production

### Error Database
Jika ada error koneksi database:
1. Cek kredensial database di config.php
2. Pastikan database sudah dibuat
3. Import tabel dari file SQL

## Kontak
Jika masih ada masalah, silakan hubungi developer.