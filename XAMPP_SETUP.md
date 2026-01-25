# XAMPP Configuration Guide

Quick setup instructions for Apache with the new e-commerce structure.

## 🚀 Quick Setup (5 Minutes)

### Option 1: Using Virtual Host (Recommended)

1. **Edit Virtual Hosts File**
   - Open: `C:\xampp\apache\conf\extra\httpd-vhosts.conf`
   - Add at the end:

```apache
<VirtualHost *:80>
    ServerName ecommerce.local
    DocumentRoot "C:\xampp\htdocs\Project_e-com\public"
    
    <Directory "C:\xampp\htdocs\Project_e-com\public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

2. **Enable Virtual Hosts**
   - Open: `C:\xampp\apache\conf\httpd.conf`
   - Find line: `#Include conf/extra/httpd-vhosts.conf`
   - Remove the `#` to uncomment it
   - Save the file

3. **Edit Hosts File**
   - Open: `C:\Windows\System32\drivers\etc\hosts` (as Administrator)
   - Add: `127.0.0.1       ecommerce.local`
   - Save

4. **Restart Apache**
   - Open XAMPP Control Panel
   - Click "Stop" next to Apache
   - Click "Start" next to Apache
   - Wait 2-3 seconds

5. **Test**
   - Open browser
   - Visit: `http://ecommerce.local/`
   - Should show login page ✅

### Option 2: Change DocumentRoot Directly

1. **Edit httpd.conf**
   - Open: `C:\xampp\apache\conf\httpd.conf`
   - Find (around line 240): `DocumentRoot "C:/xampp/htdocs"`
   - Change to: `DocumentRoot "C:/xampp/htdocs/Project_e-com/public"`
   - Find (around line 241): `<Directory "C:/xampp/htdocs">`
   - Change to: `<Directory "C:/xampp/htdocs/Project_e-com/public">`
   - Find (around line 265): `AllowOverride None`
   - Change to: `AllowOverride All`
   - Save

2. **Restart Apache**
   - Open XAMPP Control Panel
   - Click "Stop" then "Start" on Apache

3. **Test**
   - Visit: `http://localhost/`
   - Should show login page ✅

### Option 3: Keep Current Setup

If you want to keep accessing at `http://localhost/Project_e-com/`:

1. Ensure `.htaccess` files exist:
   - `C:\xampp\htdocs\Project_e-com\.htaccess` ✓
   - `C:\xampp\htdocs\Project_e-com\public\.htaccess` ✓

2. Enable mod_rewrite:
   - Open: `C:\xampp\apache\conf\httpd.conf`
   - Find: `#LoadModule rewrite_module modules/mod_rewrite.so`
   - Remove the `#` to uncomment
   - Save
   - Restart Apache

3. Test:
   - Visit: `http://localhost/Project_e-com/`
   - Should show login page ✅

## ✅ Verify Setup

### Check Apache Modules
1. Open XAMPP Control Panel
2. Click "Config" button next to Apache
3. Click "Apache (httpd.conf)"
4. Verify these are uncommented:
   - `LoadModule rewrite_module modules/mod_rewrite.so`
   - `LoadModule php8_module modules/php_handler.so`
   - `Include conf/extra/httpd-vhosts.conf` (if using virtual hosts)

### Check PHP Configuration
1. Create `C:\xampp\htdocs\Project_e-com\public\phpinfo.php`:
```php
<?php phpinfo(); ?>
```

2. Visit the file in browser
3. Verify:
   - PHP version is 7.4+
   - PDO extension is enabled
   - mod_rewrite is enabled
   - Sessions are enabled

4. **Delete** `phpinfo.php` after testing (security risk)

### Check Permissions
1. Right-click `C:\xampp\htdocs\Project_e-com`
2. Properties → Security
3. Ensure XAMPP/Apache user has "Modify" permissions
4. If not, grant permissions

## 📂 Folder Permissions (Windows)

Open PowerShell as Administrator:

```powershell
# Set read/write for logs directory
icacls "C:\xampp\htdocs\Project_e-com\logs" /grant Everyone:F /T

# Set read/write for uploads directory
icacls "C:\xampp\htdocs\Project_e-com\public\uploads" /grant Everyone:F /T

# Set read-only for everything else
icacls "C:\xampp\htdocs\Project_e-com" /reset /T
```

## 🆘 Common Issues

### Issue 1: "Cannot find config" Error

**Problem**: `Fatal error: require_once(...): Failed opening required 'C:\xampp\htdocs\Project_e-com\app\config.php'`

**Solution**:
- Check `C:\xampp\htdocs\Project_e-com\public\init.php` exists
- Verify path in init.php: `require_once dirname(__DIR__) . '/app/config.php';`
- Check spelling and capitalization

### Issue 2: Database Connection Fails

**Problem**: `Database connection failed`

**Solution**:
1. Check database credentials in `app/config.php`
2. Verify MySQL is running (green light in XAMPP Control Panel)
3. Test connection:
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Verify database exists: `usersystemdb`
   - Check table: `users`

### Issue 3: 404 Not Found on Subpages

**Problem**: Login page works but product_list.php shows 404

**Solution**:
1. Verify `.htaccess` files exist in both locations
2. Enable mod_rewrite (see above)
3. Check `public/.htaccess` has correct RewriteBase:
   - If using virtual host: `RewriteBase /`
   - If using subdirectory: `RewriteBase /Project_e-com/`

### Issue 4: Sessions Not Working

**Problem**: Can't log in, session variables lost

**Solution**:
1. Ensure `init.php` is included in every public file
2. Check session.save_path in php.ini
3. Verify `C:\xampp\tmp` directory exists and is writable
4. Restart Apache

### Issue 5: Uploads Not Working

**Problem**: Image upload fails

**Solution**:
1. Create `C:\xampp\htdocs\Project_e-com\public\uploads` directory
2. Right-click → Properties → Security
3. Grant "Modify" permission to IUSR and NETWORK SERVICE
4. Verify in PHP: `is_writable(PUBLIC_PATH . '/uploads/')`

## 📊 Diagnostic Script

Create `C:\xampp\htdocs\Project_e-com\public\diagnose.php`:

```php
<?php
echo "<h2>✅ System Diagnostics</h2>";

// Check if init.php is loading
if (file_exists('init.php')) {
    echo "<p>✅ init.php found</p>";
} else {
    echo "<p>❌ init.php not found!</p>";
}

// Check config file
if (file_exists(dirname(__DIR__) . '/app/config.php')) {
    echo "<p>✅ config.php found</p>";
} else {
    echo "<p>❌ config.php not found!</p>";
}

// Check database
try {
    require_once 'init.php';
    $stmt = $pdo->query("SELECT 1");
    echo "<p>✅ Database connected</p>";
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

// Check directories
$dirs = ['logs', 'public/uploads', 'views', 'src', 'includes'];
foreach ($dirs as $dir) {
    $path = dirname(__DIR__) . '/' . $dir;
    if (is_dir($path)) {
        echo "<p>✅ $dir/ exists</p>";
    } else {
        echo "<p>❌ $dir/ missing!</p>";
    }
}

// Check writable directories
if (is_writable(dirname(__DIR__) . '/logs')) {
    echo "<p>✅ logs/ is writable</p>";
} else {
    echo "<p>⚠️ logs/ not writable (errors won't be logged)</p>";
}

if (is_writable(dirname(__DIR__) . '/public/uploads')) {
    echo "<p>✅ public/uploads/ is writable</p>";
} else {
    echo "<p>⚠️ public/uploads/ not writable (uploads will fail)</p>";
}

// Check mod_rewrite
if (function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules())) {
    echo "<p>✅ mod_rewrite enabled</p>";
} else {
    echo "<p>⚠️ mod_rewrite not detected (may not be enabled)</p>";
}

?>
```

Visit: `http://localhost/Project_e-com/diagnose.php`

Then delete it.

## 🔒 Security Checklist

Before going live:

- [ ] All sensitive files are outside `public/`
- [ ] `.htaccess` files are in place
- [ ] `public/uploads/` permissions set correctly
- [ ] `logs/` directory is not web-accessible
- [ ] Database credentials are secure
- [ ] SQL files are not in web root
- [ ] Error display is disabled (display_errors = 0)
- [ ] HTTPS is configured
- [ ] Regular backups are scheduled

## 📞 Support Resources

- Apache Documentation: https://httpd.apache.org/
- XAMPP Documentation: https://www.apachefriends.org/
- PHP Manual: https://www.php.net/manual/
- MySQL Documentation: https://dev.mysql.com/doc/

---

**Version**: 1.0  
**Last Updated**: January 5, 2026
