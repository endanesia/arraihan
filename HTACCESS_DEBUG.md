# .htaccess Step-by-Step Debugging Guide
# Use these versions one by one to identify the problem

## Version 1: EMPTY (no .htaccess)
# Remove .htaccess completely and test

## Version 2: MINIMAL (basic only)
```
Options -Indexes
AddDefaultCharset UTF-8
```

## Version 3: WITH REWRITE (basic rewrite)
```
Options -Indexes
AddDefaultCharset UTF-8

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /dev/
</IfModule>
```

## Version 4: WITH SIMPLE RULES
```
Options -Indexes
AddDefaultCharset UTF-8

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /dev/
    
    # Simple rule: remove .php extension
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME}.php -f
    RewriteRule ^([^./]+)/?$ $1.php [L,QSA]
</IfModule>
```

## Testing Order:
1. Remove .htaccess → test hello.txt
2. Use Version 2 → test hello.txt and simple.php
3. Use Version 3 → test if rewrite works
4. Use Version 4 → test clean URLs
5. Add more rules gradually

## Files to Test:
- https://arraihantravelindo.com/dev/hello.txt
- https://arraihantravelindo.com/dev/simple.php
- https://arraihantravelindo.com/dev/test.html
- https://arraihantravelindo.com/dev/index.php