# 🔧 **PANDUAN MENGHILANGKAN /public DARI URL LARAVEL HOSTING**

---

## 🎯 **Masalah:**
URL Laravel di hosting menampilkan `/public` seperti:
- ❌ `https://domain.com/public/`
- ❌ `https://domain.com/public/admin`
- ❌ `https://domain.com/public/login`

**Target:**
- ✅ `https://domain.com/`
- ✅ `https://domain.com/admin`
- ✅ `https://domain.com/login`

---

## 🚀 **SOLUSI 1: DOCUMENT ROOT (RECOMMENDED)**

### **📁 Ubah Document Root ke Folder Public**

#### **Untuk cPanel/Shared Hosting:**
1. **Login ke cPanel** → File Manager
2. **Buka folder** `public_html` atau `www`
3. **Upload project Laravel** ke folder di atas `public_html` (misal: `/home/username/laravel_app/`)
4. **Hapus semua file** di `public_html`
5. **Copy semua file** dari `/laravel_app/public/` ke `public_html/`
6. **Edit file** `public_html/index.php`:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ubah path ini sesuai lokasi Laravel Anda
if (file_exists($maintenance = __DIR__.'/../laravel_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Ubah path ini sesuai lokasi Laravel Anda
require __DIR__.'/../laravel_app/vendor/autoload.php';

// Ubah path ini sesuai lokasi Laravel Anda
$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

#### **Untuk VPS/Dedicated Server:**

**Apache Virtual Host:**
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /path/to/your/laravel/public
    
    <Directory /path/to/your/laravel/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/laravel_error.log
    CustomLog ${APACHE_LOG_DIR}/laravel_access.log combined
</VirtualHost>
```

**Nginx Server Block:**
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/your/laravel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🔄 **SOLUSI 2: HTACCESS REDIRECT**

### **📝 Buat .htaccess di Root Domain**

Buat file `.htaccess` di folder root domain (di luar folder public):

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    
    # Redirect ke folder public jika file/folder tidak ada di root
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ public/$1 [L]
    
    # Redirect jika mengakses /public secara langsung
    RewriteCond %{THE_REQUEST} /public/([^\s?]*) [NC]
    RewriteRule ^ %1 [R=301,L]
    
    # Handle trailing slash
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [R=301,L]
</IfModule>
```

### **🔧 Update .htaccess di Folder Public**

Edit file `public/.htaccess` untuk menambahkan redirect:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Redirect /public ke root
    RewriteCond %{THE_REQUEST} /public/([^\s?]*) [NC]
    RewriteRule ^ /%1 [R=301,L]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 📂 **SOLUSI 3: SYMLINK (SHARED HOSTING)**

### **🔗 Buat Symbolic Link**

Jika hosting mendukung symlink:

```bash
# Hapus folder public_html yang ada
rm -rf public_html

# Buat symlink ke folder public Laravel
ln -s /path/to/laravel/public public_html
```

Atau via cPanel File Manager:
1. **Hapus folder** `public_html`
2. **Buat symlink** dari `public_html` ke `laravel_project/public`

---

## ⚙️ **SOLUSI 4: CUSTOM INDEX.PHP**

### **📝 Buat index.php di Root Domain**

Buat file `index.php` di root domain:

```php
<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Path ke Laravel project
$laravel_path = __DIR__ . '/laravel_project'; // Sesuaikan path

if (file_exists($maintenance = $laravel_path.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

require $laravel_path.'/vendor/autoload.php';

$app = require_once $laravel_path.'/bootstrap/app.php';

$app->handleRequest(Request::capture());
```

### **🔧 Update Asset URLs**

Update `config/app.php`:

```php
'url' => env('APP_URL', 'https://yourdomain.com'),
'asset_url' => env('ASSET_URL', 'https://yourdomain.com'),
```

---

## 🛠️ **KONFIGURASI TAMBAHAN**

### **📋 Update .env File**

```env
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com
SESSION_DOMAIN=yourdomain.com
```

### **🔄 Clear Cache**

Setelah perubahan, jalankan:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **🔗 Storage Link**

Pastikan storage link sudah dibuat:

```bash
php artisan storage:link
```

---

## ✅ **VERIFIKASI**

### **🧪 Test URLs:**
- ✅ `https://yourdomain.com/` → Home page
- ✅ `https://yourdomain.com/admin` → Admin panel
- ✅ `https://yourdomain.com/storage/...` → File assets
- ❌ `https://yourdomain.com/public/...` → Should redirect

### **🔍 Check Console:**
- Tidak ada error 404 untuk assets
- CSS dan JS ter-load dengan benar
- Images dan files dapat diakses

---

## 🚨 **TROUBLESHOOTING**

### **❌ Assets Tidak Load:**
```php
// Di blade template, gunakan:
{{ asset('css/app.css') }}
{{ asset('js/app.js') }}

// Bukan:
{{ url('public/css/app.css') }}
```

### **❌ Storage Files 404:**
```bash
# Pastikan storage link ada
ls -la public/storage

# Jika tidak ada, buat ulang
php artisan storage:link
```

### **❌ Admin Panel 404:**
```php
// Check routes
php artisan route:list | grep admin

// Clear route cache
php artisan route:clear
```

---

## 🎯 **REKOMENDASI**

### **✅ Best Practice:**
1. **Gunakan Solusi 1** (Document Root) untuk performa terbaik
2. **Backup project** sebelum melakukan perubahan
3. **Test di staging** sebelum production
4. **Monitor error logs** setelah deployment

### **⚠️ Catatan Penting:**
- Pastikan file `.env` tidak dapat diakses public
- Set permission yang benar untuk folder storage
- Gunakan HTTPS untuk production
- Enable gzip compression untuk performa

---

**🎉 URL Laravel Tanpa /public - Ready for Production! 🎉**
