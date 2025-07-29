<?php

echo "=== LARAVEL REMOVE PUBLIC URL SETUP ===\n\n";

// Function to create .htaccess for root
function createRootHtaccess() {
    $htaccess_content = '<IfModule mod_rewrite.c>
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
</IfModule>';

    file_put_contents('.htaccess', $htaccess_content);
    echo "✅ Root .htaccess created\n";
}

// Function to update public .htaccess
function updatePublicHtaccess() {
    $public_htaccess = 'public/.htaccess';
    
    if (!file_exists($public_htaccess)) {
        echo "❌ Public .htaccess not found\n";
        return;
    }
    
    $content = file_get_contents($public_htaccess);
    
    // Add redirect rule for /public
    $redirect_rule = '    # Redirect /public ke root
    RewriteCond %{THE_REQUEST} /public/([^\s?]*) [NC]
    RewriteRule ^ /%1 [R=301,L]

';
    
    // Insert after RewriteEngine On
    $content = str_replace(
        "RewriteEngine On\n",
        "RewriteEngine On\n\n" . $redirect_rule,
        $content
    );
    
    file_put_contents($public_htaccess, $content);
    echo "✅ Public .htaccess updated\n";
}

// Function to create custom index.php
function createCustomIndex($laravel_path = '.') {
    $index_content = '<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define(\'LARAVEL_START\', microtime(true));

// Path ke Laravel project
$laravel_path = __DIR__ . \'/\' . \'' . $laravel_path . '\';

if (file_exists($maintenance = $laravel_path.\'/storage/framework/maintenance.php\')) {
    require $maintenance;
}

require $laravel_path.\'/vendor/autoload.php\';

$app = require_once $laravel_path.\'/bootstrap/app.php\';

$app->handleRequest(Request::capture());';

    file_put_contents('index.php', $index_content);
    echo "✅ Custom index.php created\n";
}

// Function to generate server configs
function generateServerConfigs($domain, $path) {
    // Apache Virtual Host
    $apache_config = "<VirtualHost *:80>
    ServerName {$domain}
    DocumentRoot {$path}/public
    
    <Directory {$path}/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/{$domain}_error.log
    CustomLog \${APACHE_LOG_DIR}/{$domain}_access.log combined
</VirtualHost>

<VirtualHost *:443>
    ServerName {$domain}
    DocumentRoot {$path}/public
    
    <Directory {$path}/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    
    ErrorLog \${APACHE_LOG_DIR}/{$domain}_ssl_error.log
    CustomLog \${APACHE_LOG_DIR}/{$domain}_ssl_access.log combined
</VirtualHost>";

    // Nginx Server Block
    $nginx_config = "server {
    listen 80;
    server_name {$domain};
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    server_name {$domain};
    root {$path}/public;

    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;

    add_header X-Frame-Options \"SAMEORIGIN\";
    add_header X-Content-Type-Options \"nosniff\";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}";

    file_put_contents('apache_vhost.conf', $apache_config);
    file_put_contents('nginx_server.conf', $nginx_config);
    
    echo "✅ Server config files generated:\n";
    echo "   - apache_vhost.conf\n";
    echo "   - nginx_server.conf\n";
}

// Main menu
echo "Choose setup method:\n";
echo "1. Create root .htaccess (Shared hosting)\n";
echo "2. Update public .htaccess\n";
echo "3. Create custom index.php\n";
echo "4. Generate server configs\n";
echo "5. Full setup (1+2)\n";
echo "0. Exit\n\n";

$choice = readline("Enter your choice (0-5): ");

switch ($choice) {
    case '1':
        createRootHtaccess();
        break;
        
    case '2':
        updatePublicHtaccess();
        break;
        
    case '3':
        $path = readline("Enter Laravel project path (default: current): ") ?: '.';
        createCustomIndex($path);
        break;
        
    case '4':
        $domain = readline("Enter your domain: ");
        $path = readline("Enter full path to Laravel project: ");
        generateServerConfigs($domain, $path);
        break;
        
    case '5':
        createRootHtaccess();
        updatePublicHtaccess();
        echo "\n✅ Full setup completed!\n";
        break;
        
    case '0':
        echo "Goodbye!\n";
        exit;
        
    default:
        echo "Invalid choice!\n";
        break;
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Update your .env file:\n";
echo "   APP_URL=https://yourdomain.com\n";
echo "   ASSET_URL=https://yourdomain.com\n\n";
echo "2. Clear Laravel cache:\n";
echo "   php artisan config:clear\n";
echo "   php artisan route:clear\n";
echo "   php artisan view:clear\n\n";
echo "3. Create storage link:\n";
echo "   php artisan storage:link\n\n";
echo "4. Test your website!\n\n";

echo "=== SETUP COMPLETE ===\n";
