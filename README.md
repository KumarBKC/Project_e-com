# E-Commerce Platform - Professional Structure Guide

## 📁 Directory Structure

```
Project_e-com/
├── public/                          ⭐ WEB ROOT (Apache DocumentRoot)
│   ├── index.php                    Entry point / Home page
│   ├── .htaccess                    URL rewriting & security
│   ├── init.php                     Bootstrap & configuration loader
│   ├── login.php                    Login page
│   ├── register.php                 Registration page
│   ├── dashboard.php                User dashboard
│   ├── logout.php                   Logout handler
│   ├── product_list.php             Display all products
│   ├── search.php                   Product search
│   ├── cart.php                     Shopping cart
│   ├── add_product.php              Admin: add products
│   ├── css/                         Stylesheets
│   ├── js/                          JavaScript files
│   ├── images/                      Static images
│   └── uploads/                     User-uploaded files (non-web accessible)
│
├── app/                             🔒 APPLICATION LOGIC (Not accessible via web)
│   ├── config.php                   Configuration constants & paths
│   └── Database.php                 PDO database connection class
│
├── src/                             🔒 BUSINESS LOGIC (Classes for models)
│   ├── User.php                     User model/class
│   ├── Product.php                  Product model/class
│   └── Cart.php                     Cart model/class
│
├── views/                           🔒 REUSABLE TEMPLATES
│   ├── header.php                   Common header
│   ├── footer.php                   Common footer
│   ├── login.php                    Login form
│   ├── register.php                 Registration form
│   ├── dashboard.php                Dashboard template
│   ├── product_list.php             Products list template
│   ├── cart.php                     Cart template
│   ├── search.php                   Search results template
│   └── add_product.php              Add product form
│
├── includes/                        🔒 HELPER FUNCTIONS
│   └── functions.php                Common utility functions
│
├── sql/                             🔒 DATABASE FILES
│   └── usersystemdb.sql             Database schema
│
├── logs/                            🔒 APPLICATION LOGS
│   └── error.log                    Error logging
│
├── .htaccess                        Root-level security rules
└── README.md                        This file

Legend:
⭐ = Web accessible (set as DocumentRoot)
🔒 = Protected from direct web access
```

## 🔧 Apache Configuration for XAMPP

### Option 1: Virtual Host Configuration (Recommended for Production)

Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    ServerName ecommerce.local
    ServerAlias www.ecommerce.local
    DocumentRoot "C:\xampp\htdocs\Project_e-com\public"
    
    <Directory "C:\xampp\htdocs\Project_e-com\public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
        
        # URL Rewriting
        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteBase /
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ index.php?request=$1 [QSA,L]
        </IfModule>
    </Directory>
    
    <Directory "C:\xampp\htdocs\Project_e-com">
        Deny from all
    </Directory>
    
    ErrorLog "logs/ecommerce_error.log"
    CustomLog "logs/ecommerce_access.log" combined
</VirtualHost>
```

Then add to `C:\xampp\apache\conf\httpd.conf`:
```
NameVirtualHost *:80
Include conf/extra/httpd-vhosts.conf
```

Edit `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1       ecommerce.local
127.0.0.1       www.ecommerce.local
```

Access at: `http://ecommerce.local/`

### Option 2: Direct DocumentRoot Change (Simpler)

Edit `C:\xampp\apache\conf\httpd.conf`:

Find this line (around line 240):
```apache
DocumentRoot "C:/xampp/htdocs"
```

Replace with:
```apache
DocumentRoot "C:/xampp/htdocs/Project_e-com/public"
```

Find this line:
```apache
<Directory "C:/xampp/htdocs">
```

Replace with:
```apache
<Directory "C:/xampp/htdocs/Project_e-com/public">
```

Then restart Apache.

Access at: `http://localhost/`

### Option 3: Project Subdirectory (Current Setup)

If you want to keep the default DocumentRoot at `C:\xampp\htdocs`, your project is already set up for this.

Access at: `http://localhost/Project_e-com/`

Make sure the `.htaccess` files are in place for proper routing.

## 📋 How It Works

### 1. **File Structure & Security**

- **public/** - Only this directory is web-accessible
- **app/**, **src/**, **views/**, **includes/** - Protected from direct web access
- **sql/** - Database files stored safely outside web root
- **.htaccess** files prevent direct access to PHP files and directories

### 2. **Bootstrap Process**

Every public PHP file includes `init.php`:

```php
<?php
require_once 'init.php';
// Your code here
?>
```

`init.php` loads:
1. `app/config.php` - All configuration constants
2. `app/Database.php` - Database connection
3. Session initialization
4. Security headers

### 3. **Database Access**

Centralized through `Database.php` class:

```php
// Method 1: Using Database class
$db = Database::getInstance();
$products = $db->fetchAll("SELECT * FROM products");

// Method 2: Global $pdo variable (for backward compatibility)
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([1]);
$product = $stmt->fetch();
```

### 4. **Path Constants**

Use these constants for consistent paths:

```php
BASE_PATH        // C:\xampp\htdocs\Project_e-com
PUBLIC_PATH      // C:\xampp\htdocs\Project_e-com\public
APP_PATH         // C:\xampp\htdocs\Project_e-com\app
VIEWS_PATH       // C:\xampp\htdocs\Project_e-com\views
INCLUDES_PATH    // C:\xampp\htdocs\Project_e-com\includes

// Example usage
include VIEWS_PATH . '/header.php';
$image_path = PUBLIC_PATH . '/images/product.jpg';
```

## 🔒 Security Features

### .htaccess Rules

**Root `.htaccess`:**
- Denies direct access to all PHP files outside public/
- Blocks access to .sql files
- Hides .htaccess itself
- Prevents directory listing

**public/.htaccess:**
- URL rewriting (clean URLs without index.php)
- Security headers (X-Frame-Options, X-Content-Type-Options, etc.)
- Gzip compression
- Browser caching for static assets
- Blocks access to config files

### PHP Security

`init.php` sets:
- Security headers (XSS Protection, Clickjacking, MIME-sniffing)
- HTTPOnly & Secure cookies
- SameSite cookie policy
- Session timeout

## 📝 Important Notes

### Database Configuration
Edit `app/config.php`:
```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'usersystemdb');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Move SQL File
Move `usersystemdb.sql` to `sql/` directory:
```bash
move usersystemdb.sql sql/
```

### Image Uploads
- Static images go in: `public/images/`
- User uploads go in: `public/uploads/`
- Configure upload directory in `app/config.php`

### Logging
Errors are logged to `logs/error.log` (created automatically).

## 🚀 Production Deployment

### Before Going Live

1. **Environment Variables** - Move sensitive credentials to `.env` file:
   ```php
   // .env (never commit to git)
   DB_HOST=your-production-host
   DB_USER=prod_user
   DB_PASS=secure_password
   ```

2. **Error Handling** - Set `display_errors = 0` in `app/config.php`:
   ```php
   ini_set('display_errors', 0); // Never show errors to users
   ```

3. **.gitignore** - Create and exclude:
   ```
   logs/
   .env
   uploads/
   ```

4. **HTTPS** - Always use HTTPS in production:
   ```apache
   RewriteCond %{HTTPS} off
   RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

5. **Database Backups** - Regular backups of database and files

6. **File Permissions**:
   ```bash
   chmod 755 public/
   chmod 755 app/
   chmod 755 views/
   chmod 755 src/
   chmod 755 includes/
   chmod 755 sql/
   chmod 755 logs/
   chmod 644 .htaccess
   chmod 644 public/.htaccess
   ```

## 📚 Usage Examples

### Creating a New Page

1. Create `public/newpage.php`:
```php
<?php
require_once 'init.php';

// Your page logic here

// Include template
include VIEWS_PATH . '/newpage.php';
?>
```

2. Create `views/newpage.php`:
```php
<!DOCTYPE html>
<html>
<head>
    <title>New Page</title>
</head>
<body>
    <!-- Your HTML here -->
</body>
</html>
```

### Creating a Model Class

Create `src/Product.php`:
```php
<?php
class Product {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM products");
        return $stmt->fetchAll();
    }
    
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}
?>
```

Usage in `public/product_list.php`:
```php
<?php
require_once 'init.php';
require_once SRC_PATH . '/Product.php';

$product = new Product($pdo);
$products = $product->getAll();
?>
```

## 🆘 Troubleshooting

| Problem | Solution |
|---------|----------|
| 404 Not Found | Ensure `public/.htaccess` exists and mod_rewrite is enabled |
| Cannot find config | Check `init.php` is at `public/init.php` |
| Database connection fails | Check credentials in `app/config.php` |
| Uploads fail | Ensure `public/uploads/` directory exists & is writable |
| Sessions not working | Check `session_start()` in `init.php` |
| Include path errors | Use defined constants (VIEWS_PATH, SRC_PATH, etc.) |

## 📞 Support

For issues:
1. Check `logs/error.log` for detailed error messages
2. Verify database credentials
3. Ensure all directories have proper permissions
4. Test locally before deploying

---

**Version**: 1.0  
**Last Updated**: January 5, 2026
