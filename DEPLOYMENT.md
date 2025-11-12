# Deployment Guide

## Important: .htaccess Configuration

This project has different `.htaccess` configurations for **local development** and **production deployment**.

### Files:
- `.htaccess` - **FOR PRODUCTION** (RewriteBase `/`)
- `.htaccess.local` - **FOR LOCAL DEVELOPMENT** (RewriteBase `/dev/`)
- `.htaccess.production` - Backup of production config

### Local Development (XAMPP /dev/ subfolder)

When working locally in `http://localhost/dev/`:

```bash
# Use local .htaccess with /dev/ base
cp .htaccess.local .htaccess
```

### Production Deployment (Root domain)

Before deploying to production (arraihantravelindo.com):

```bash
# Use production .htaccess with / base
cp .htaccess.production .htaccess
```

Or simply ensure `.htaccess` has:
```apache
RewriteBase /
```

## Common Issues

### Homepage berantakan di production
**Cause:** `.htaccess` still has `RewriteBase /dev/` instead of `RewriteBase /`

**Fix:** Upload `.htaccess` with `RewriteBase /`

### 404 errors on local development
**Cause:** `.htaccess` has `RewriteBase /` but you're accessing `http://localhost/dev/`

**Fix:** Use `.htaccess.local` with `RewriteBase /dev/`

## Deployment Checklist

Before pushing to production:

1. ✅ Ensure `.htaccess` has `RewriteBase /`
2. ✅ Test all links and navigation
3. ✅ Verify CSS/JS files load correctly
4. ✅ Check database credentials in `inc/config.php` (should auto-detect production)
5. ✅ Clear browser cache after deployment

## Database Configuration

The system automatically detects environment:
- **Local:** Uses `inc/config-local.php` (localhost, root user, no password)
- **Production:** Uses `inc/config-production.php` (arraihan_db, arraihan_users)

No manual changes needed - detection is automatic based on `$_SERVER['HTTP_HOST']`.
