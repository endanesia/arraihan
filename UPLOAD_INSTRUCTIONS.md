# Manual Upload Instructions

## Cara Upload ke Hosting:

### Opsi 1: Download dari GitHub
1. Buka: https://github.com/endanesia/arraihan
2. Klik tombol hijau "Code" > "Download ZIP"
3. Extract file ZIP
4. Upload semua file ke hosting via cPanel File Manager atau FTP

### Opsi 2: Git Clone di Hosting (jika support)
```bash
cd /public_html
git clone https://github.com/endanesia/arraihan.git dev
```

### Opsi 3: FTP/SFTP Upload
1. Gunakan FileZilla atau WinSCP
2. Connect ke hosting FTP
3. Upload semua file ke folder yang benar

## Struktur yang Benar di Hosting:

```
/public_html/dev/
├── .htaccess
├── index.php
├── test-server.php
├── test.html
├── admin/
├── css/
├── js/
├── images/
├── inc/
│   ├── config.php
│   └── db.php
└── install/
```

## Test Setelah Upload:
1. https://arraihantravelindo.com/dev/test.html
2. https://arraihantravelindo.com/dev/test-server.php
3. https://arraihantravelindo.com/dev/index.php

## Catatan Penting:
- Set file permission ke 644
- Set folder permission ke 755
- Pastikan .htaccess ter-upload
- Update database credentials di inc/config.php