# 🚀 PANDUAN INSTALASI JADWALKU

## 📋 **SYSTEM REQUIREMENTS**

### **Server Requirements:**
- **PHP**: 8.1 atau lebih tinggi
- **Database**: MySQL 8.0+ atau MariaDB 10.3+
- **Web Server**: Apache 2.4+ atau Nginx 1.18+
- **Memory**: Minimum 512MB RAM (Recommended 1GB+)
- **Storage**: Minimum 1GB free space

### **PHP Extensions Required:**
```bash
- BCMath PHP Extension
- Ctype PHP Extension
- cURL PHP Extension
- DOM PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PCRE PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- GD PHP Extension (untuk image processing)
- ZIP PHP Extension (untuk file handling)
```

### **Development Tools:**
- **Composer**: 2.0+
- **Node.js**: 16.0+ 
- **NPM**: 8.0+
- **Git**: 2.0+

---

## 🛠️ **INSTALASI STEP-BY-STEP**

### **Step 1: Clone Repository**
```bash
# Clone dari repository
git clone https://github.com/your-username/jadwalku.git
cd jadwalku

# Atau download ZIP dan extract
wget https://github.com/your-username/jadwalku/archive/main.zip
unzip main.zip
cd jadwalku-main
```

### **Step 2: Install Dependencies**
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Untuk production (optional)
composer install --optimize-autoloader --no-dev
npm ci --only=production
```

### **Step 3: Environment Configuration**
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### **Step 4: Database Setup**
```bash
# Edit .env file dengan database credentials
nano .env

# Database configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jadwalku
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Create database (jika belum ada)
mysql -u root -p
CREATE DATABASE jadwalku CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### **Step 5: Run Migrations & Seeders**
```bash
# Run database migrations
php artisan migrate

# Seed initial data
php artisan db:seed

# Atau run specific seeders
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=TahunAjaranSeeder
```

### **Step 6: Storage Setup**
```bash
# Create storage link
php artisan storage:link

# Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# For Linux/Mac
sudo chown -R www-data:www-data storage bootstrap/cache
```

### **Step 7: Build Assets**
```bash
# Development build
npm run dev

# Production build
npm run build

# Watch for changes (development)
npm run dev -- --watch
```

### **Step 8: Final Configuration**
```bash
# Clear and cache configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# For development, clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 🔧 **ENVIRONMENT CONFIGURATION**

### **Complete .env Example:**
```env
# Application
APP_NAME=JadwalKu
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jadwalku
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password

# Cache
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@jadwalku.com
MAIL_FROM_NAME="${APP_NAME}"

# File Storage
FILESYSTEM_DISK=local

# Logging
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Broadcasting (optional)
BROADCAST_DRIVER=log
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

# Redis (optional)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# AWS S3 (optional)
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```

---

## 🌐 **WEB SERVER CONFIGURATION**

### **Apache Configuration:**
```apache
# .htaccess (sudah included di Laravel)
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

# Virtual Host Configuration
<VirtualHost *:80>
    ServerName jadwalku.local
    DocumentRoot /path/to/jadwalku/public
    
    <Directory /path/to/jadwalku/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/jadwalku_error.log
    CustomLog ${APACHE_LOG_DIR}/jadwalku_access.log combined
</VirtualHost>
```

### **Nginx Configuration:**
```nginx
server {
    listen 80;
    server_name jadwalku.local;
    root /path/to/jadwalku/public;

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

## 🔐 **SECURITY SETUP**

### **File Permissions:**
```bash
# Set proper ownership
sudo chown -R www-data:www-data /path/to/jadwalku

# Set directory permissions
find /path/to/jadwalku -type d -exec chmod 755 {} \;

# Set file permissions
find /path/to/jadwalku -type f -exec chmod 644 {} \;

# Special permissions for storage and cache
chmod -R 775 storage bootstrap/cache
```

### **SSL Certificate (Production):**
```bash
# Using Let's Encrypt with Certbot
sudo apt install certbot python3-certbot-apache

# Generate certificate
sudo certbot --apache -d your-domain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### **Firewall Configuration:**
```bash
# UFW (Ubuntu)
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable

# iptables (CentOS/RHEL)
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
```

---

## 📊 **DATABASE OPTIMIZATION**

### **MySQL Configuration:**
```sql
-- Create optimized database
CREATE DATABASE jadwalku 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Create dedicated user
CREATE USER 'jadwalku_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON jadwalku.* TO 'jadwalku_user'@'localhost';
FLUSH PRIVILEGES;

-- Optimize MySQL settings (my.cnf)
[mysqld]
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
max_connections = 100
query_cache_size = 32M
query_cache_type = 1
```

### **Database Backup Setup:**
```bash
# Create backup script
cat > /usr/local/bin/backup-jadwalku.sh << 'EOF'
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/jadwalku"
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u jadwalku_user -p'password' jadwalku > $BACKUP_DIR/db_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /path/to/jadwalku/storage

# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
EOF

chmod +x /usr/local/bin/backup-jadwalku.sh

# Add to crontab
echo "0 2 * * * /usr/local/bin/backup-jadwalku.sh" | crontab -
```

---

## 🚀 **PRODUCTION DEPLOYMENT**

### **Deployment Script:**
```bash
#!/bin/bash
# deploy.sh

echo "🚀 Starting JadwalKu deployment..."

# Pull latest changes
git pull origin main

# Install/update dependencies
composer install --optimize-autoloader --no-dev
npm ci --only=production

# Build assets
npm run build

# Database migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Restart services
sudo systemctl restart php8.1-fpm
sudo systemctl restart nginx

echo "✅ Deployment completed successfully!"
```

### **Process Management (Supervisor):**
```ini
# /etc/supervisor/conf.d/jadwalku-worker.conf
[program:jadwalku-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/jadwalku/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/jadwalku/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 🔍 **TROUBLESHOOTING**

### **Common Issues:**

#### **Permission Errors:**
```bash
# Fix storage permissions
sudo chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

#### **Composer Issues:**
```bash
# Clear composer cache
composer clear-cache

# Update composer
composer self-update

# Install with verbose output
composer install -vvv
```

#### **Database Connection:**
```bash
# Test database connection
php artisan tinker
DB::connection()->getPdo();

# Check MySQL service
sudo systemctl status mysql
sudo systemctl restart mysql
```

#### **Asset Build Issues:**
```bash
# Clear npm cache
npm cache clean --force

# Remove node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Build with verbose output
npm run build -- --verbose
```

---

## ✅ **POST-INSTALLATION CHECKLIST**

- [ ] Application loads without errors
- [ ] Database connection working
- [ ] Admin panel accessible (/admin)
- [ ] Student portal accessible (/student)
- [ ] File uploads working
- [ ] Email configuration tested
- [ ] SSL certificate installed (production)
- [ ] Backup system configured
- [ ] Monitoring setup
- [ ] Performance optimization applied

---

## 📞 **SUPPORT**

### **Getting Help:**
- **Documentation**: Check RANGKUMAN_APLIKASI_JADWALKU.md
- **Technical Docs**: Check TECHNICAL_DOCUMENTATION.md
- **Issues**: Create GitHub issue
- **Email**: support@jadwalku.com

### **Useful Commands:**
```bash
# Check Laravel version
php artisan --version

# List all routes
php artisan route:list

# Check system status
php artisan about

# Clear all caches
php artisan optimize:clear

# Run tests
php artisan test
```

---

**🎉 Installation complete! Your JadwalKu application is ready to use! 🎉**
