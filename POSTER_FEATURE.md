# Package Poster Upload Feature

## Overview
Fitur upload poster untuk packages telah ditambahkan ke admin panel. Fitur ini memungkinkan admin untuk mengupload gambar poster untuk setiap paket perjalanan.

## Features Added

### 1. Database Field
- Field `poster` (VARCHAR(255)) telah ditambahkan ke tabel `packages`
- Field ini menyimpan nama file poster yang diupload

### 2. Upload Functionality
- Upload gambar dengan format JPEG, PNG, WebP
- Auto resize jika ukuran lebih dari 1920x1080px atau 2MB
- File disimpan di folder `/images/packages/`
- Nama file otomatis dengan timestamp dan random string

### 3. Admin Interface
- Input file poster di form package-edit.php
- Preview poster saat ini jika dalam mode edit
- Kolom poster di tabel packages.php dengan thumbnail

### 4. Image Processing
- Automatic image resizing menggunakan PHP GD
- Preserve transparency untuk PNG/WebP
- Configurable quality (default: 85%)
- Informative success messages with resize info

## Usage

### Adding Poster to New Package
1. Go to Admin > Packages > Add Package
2. Fill in package details
3. Choose poster image file
4. Save package

### Updating Poster for Existing Package
1. Go to Admin > Packages
2. Click edit on desired package
3. Current poster will be shown if exists
4. Choose new poster file (leave empty to keep current)
5. Save changes

## File Structure
```
/images/packages/           # Poster storage directory
/admin/package-edit.php     # Form with poster upload
/admin/packages.php         # List with poster thumbnails
```

## Technical Details

### Upload Path
- Directory: `/images/packages/`
- Filename format: `poster_YYYYMMDD_HHMMSS_[random].ext`

### Image Constraints
- Max dimensions: 1920x1080px
- Max file size: 2MB
- Supported formats: JPEG, PNG, WebP
- Quality: 85% for JPEG

### Database Migration
Run `/check-packages-table.php` to automatically add the poster field to existing installations.

## Files Modified
- `admin/package-edit.php` - Added poster upload functionality
- `admin/packages.php` - Added poster column in table view
- `check-packages-table.php` - Added poster field migration

## CSS Classes Added
- `.poster-preview` - Styling for poster preview images
- `.file-input-wrapper` - Wrapper for file input styling
- `.file-input-label` - Label styling for file inputs