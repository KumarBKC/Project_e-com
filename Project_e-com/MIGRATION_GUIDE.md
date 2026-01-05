# Migration Guide - From Old to New Structure

This guide explains how to migrate your existing files to the new professional structure.

## 📋 What Changed

### Old Structure
```
Project_e-com/
├── db.php                    (Root level)
├── login.php                 (Root level)
├── register.php              (Root level)
├── dashboard.php             (Root level)
├── logout.php                (Root level)
├── product_list.php          (Root level)
├── search.php                (Root level)
├── cart.php                  (Root level)
├── add_product.php           (Root level)
├── test_db.php               (Root level)
├── login.html                (Root level)
├── register.html             (Root level)
├── images/                   (Root level)
├── public/                   (Subdirectory)
└── usersystemdb.sql          (Root level)
```

### New Structure
```
Project_e-com/
├── public/                   ⭐ WEB ROOT
│   ├── index.php
│   ├── init.php
│   ├── login.php
│   ├── register.php
│   ├── dashboard.php
│   ├── logout.php
│   ├── product_list.php
│   ├── search.php
│   ├── cart.php
│   ├── add_product.php
│   ├── images/
│   ├── uploads/
│   └── .htaccess
├── app/
│   ├── config.php
│   └── Database.php
├── src/
│   ├── User.php
│   ├── Product.php
│   └── Cart.php
├── views/
│   ├── header.php
│   ├── footer.php
│   └── [template files]
├── includes/
│   └── functions.php
├── sql/
│   └── usersystemdb.sql
├── logs/
└── .htaccess
```

## 🔄 Migration Steps

### Step 1: Files Already Updated ✅

The following files have been updated automatically:
- ✅ `public/init.php` - New bootstrap file
- ✅ `public/login.php` - Updated with new path
- ✅ `public/register.php` - Updated with new path
- ✅ `public/dashboard.php` - Updated with new path
- ✅ `public/logout.php` - Updated with new path
- ✅ `public/product_list.php` - Updated with new path
- ✅ `public/search.php` - Updated with new path
- ✅ `public/cart.php` - Updated with new path
- ✅ `public/add_product.php` - Updated with new path
- ✅ `app/config.php` - New configuration file
- ✅ `app/Database.php` - New database class
- ✅ `src/User.php` - New user model
- ✅ `src/Product.php` - New product model
- ✅ `includes/functions.php` - New helper functions
- ✅ `.htaccess` (root) - New security file
- ✅ `public/.htaccess` - New routing & security file

### Step 2: Files to Move Manually

#### Move to `public/images/`
```bash
# Copy existing images (Windows PowerShell)
Copy-Item "C:\xampp\htdocs\Project_e-com\images\*" `
          "C:\xampp\htdocs\Project_e-com\public\images\" -Recurse
```

#### Move Database File to `sql/`
```bash
# Copy SQL file (Windows PowerShell)
Copy-Item "C:\xampp\htdocs\Project_e-com\usersystemdb.sql" `
          "C:\xampp\htdocs\Project_e-com\sql\usersystemdb.sql"
```

### Step 3: Files to Update in HTML Forms

If you have HTML forms (login.html, register.html) that POST to PHP files, ensure the action attribute is correct:

**Old:**
```html
<form method="POST" action="login.php">
```

**New (stays the same because files are in same directory):**
```html
<form method="POST" action="login.php">
```

No changes needed - they're still in the `public/` directory.

### Step 4: Update Custom Pages

For any custom PHP files you've created, update the includes:

**Old:**
```php
<?php
session_start();
require 'db.php';
// Your code
?>
```

**New:**
```php
<?php
require_once 'init.php';
// Your code
// init.php handles session_start() and database connection
?>
```

### Step 5: Move Views (Templates)

If you have pure HTML/template files with PHP, move them to `views/`:

```bash
# Example: Move custom template files
Move-Item "public/my_template.php" "views/my_template.php"
```

Then include them in the public file:

```php
<?php
require_once 'init.php';
// Your logic
include VIEWS_PATH . '/my_template.php';
?>
```

## 📝 Important: Update Include Paths in Custom Files

### For Files Using Include/Require

**Old way:**
```php
require '../db.php';
require 'functions.php';
include 'header.html';
```

**New way:**
```php
require_once 'init.php';                          // Loads config & database
require_once INCLUDES_PATH . '/functions.php';   // Helper functions
include VIEWS_PATH . '/header.php';               // Template files
require_once SRC_PATH . '/User.php';              // Model classes
```

### Using Absolute Paths

All files in `public/` should use:

```php
<?php
require_once 'init.php';  // This is all you need at the top!

// Then use constants defined in config.php:
// BASE_PATH, PUBLIC_PATH, APP_PATH, SRC_PATH, VIEWS_PATH, INCLUDES_PATH
?>
```

## 🗑️ Files to Delete

After migration, delete these old files from root:

```bash
# These are no longer needed at root level
del "C:\xampp\htdocs\Project_e-com\db.php"
del "C:\xampp\htdocs\Project_e-com\test_db.php"
del "C:\xampp\htdocs\Project_e-com\usersystemdb.sql"  # (moved to sql/)

# The old images directory is kept but you can consolidate into public/images/
# del /S "C:\xampp\htdocs\Project_e-com\images"

# The old public/ subdirectory is now the web root
# You may have duplicate files - keep only public/ version
```

## 🔍 Verification Checklist

- [ ] All directories created (app, src, views, includes, sql, logs)
- [ ] Configuration constants accessible (BASE_PATH, VIEWS_PATH, etc.)
- [ ] Database connection works (test by logging in)
- [ ] Images display correctly (from public/images/)
- [ ] URL rewriting works (.htaccess rules)
- [ ] Sessions work (can log in and stay logged in)
- [ ] No direct access to config files (try accessing /app/config.php)
- [ ] Error logging works (check logs/error.log)

## 🧪 Testing

### 1. Test Bootstrap
Create `public/test.php`:
```php
<?php
require_once 'init.php';
echo "✅ Config loaded successfully!<br>";
echo "Base Path: " . BASE_PATH . "<br>";
echo "Database: " . DB_NAME . "<br>";
echo "Session ID: " . session_id() . "<br>";
?>
```

Visit: `http://localhost/Project_e-com/test.php`

### 2. Test Database
```php
<?php
require_once 'init.php';
try {
    $stmt = $pdo->query("SELECT 1");
    echo "✅ Database connection successful!";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage();
}
?>
```

### 3. Test Security
Try accessing protected files:
- `http://localhost/Project_e-com/app/config.php` - Should show 403 Forbidden
- `http://localhost/Project_e-com/src/User.php` - Should show 403 Forbidden
- `http://localhost/Project_e-com/.htaccess` - Should show 403 Forbidden

## 🆘 Troubleshooting

| Issue | Solution |
|-------|----------|
| "Fatal error: Call to undefined function" | Make sure you included `init.php` at the top of the file |
| "Cannot find config" | Verify `public/init.php` exists and has correct path to `app/config.php` |
| Database connection fails | Check credentials in `app/config.php` |
| Images don't show | Verify they're in `public/images/` and use full path: `<?php echo IMAGES_DIR ?>/image.jpg` |
| Sessions lost between pages | Ensure `init.php` is included in every file with session data |
| .htaccess not working | Enable mod_rewrite in Apache: `a2enmod rewrite` |

## 📚 Next Steps

1. **Create Model Classes** - Example User.php and Product.php provided in `src/`
2. **Create Template Files** - Move HTML to `views/` directory
3. **Extract Helper Functions** - Move common functions to `includes/functions.php`
4. **Implement Error Handling** - Use try/catch blocks
5. **Add Input Validation** - Sanitize and validate all user input
6. **Implement CSRF Protection** - Use `generateCSRFToken()` in forms

## 💡 Pro Tips

### Use Constants Instead of Relative Paths
```php
// ❌ Bad
include __DIR__ . '/../includes/functions.php';

// ✅ Good
include INCLUDES_PATH . '/functions.php';
```

### Autoload Classes
Eventually upgrade to PSR-4 autoloading:
```php
spl_autoload_register(function ($class) {
    $file = SRC_PATH . '/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});
```

### Use Model Objects
```php
// Old way
$stmt = $pdo->query("SELECT * FROM users");

// New way
$user = new User($pdo);
$users = $user->getAll();
```

---

**Status**: Migration guide complete  
**Version**: 1.0  
**Last Updated**: January 5, 2026
