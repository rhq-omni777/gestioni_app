# Despliegue manual (sin SSH CI)

Pasos para instalar y actualizar la app directamente en el servidor Ubuntu sin usar el workflow de GitHub Actions.

## 1. Requisitos en servidor
- Ubuntu 22.04+, Nginx, PHP 8.2-FPM, MySQL 8, Node 20, Composer.
- Usuario con acceso shell (local o SSH). Si solo tienes consola local, usa estos comandos directamente.

Instala paquetes base (ejemplo):
```
sudo apt update
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl unzip git curl
# Node (con nvm)
curl -fsSL https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
source ~/.bashrc
nvm install 20
npm install -g npm@latest
# Composer
cd /tmp && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

## 2. Base de datos
```
sudo mysql -e "CREATE DATABASE gestioni_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'gestioni'@'localhost' IDENTIFIED BY 'G3st!0n1-Prod-2026';"
sudo mysql -e "GRANT ALL PRIVILEGES ON gestioni_laravel.* TO 'gestioni'@'localhost'; FLUSH PRIVILEGES;"
```

## 3. Obtener código
```
cd /var/www
sudo git clone https://github.com/rhq-omni777/gestioni_app.git
sudo chown -R $USER:$USER gestioni_app
cd gestioni_app
cp .env.example .env
```

Edita `.env` con:
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio
DB_DATABASE=gestioni_laravel
DB_USERNAME=gestioni
DB_PASSWORD=G3st!0n1-Prod-2026
```
(Ajusta SMTP, etc.)

## 4. Dependencias y build
```
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan key:generate --force
php artisan migrate --force --seed
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5. Permisos
```
sudo chown -R www-data:www-data storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod 775 {} \;
sudo find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

## 6. Nginx
Archivo `/etc/nginx/sites-available/gestioni`:
```
server {
    listen 80;
    server_name tu-dominio;
    root /var/www/gestioni_app/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }
}
```
Habilita y recarga:
```
sudo ln -s /etc/nginx/sites-available/gestioni /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 7. Cron y colas
- Cron: `* * * * * php /var/www/gestioni_app/artisan schedule:run >> /dev/null 2>&1`
- Colas (Supervisor):
```
[program:gestioni-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/gestioni_app/artisan queue:work --sleep=3 --tries=3 --backoff=5
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/gestioni_app/storage/logs/worker.log
stopwaitsecs=3600
```

## 8. Verificación
- `php artisan test` (opcional en servidor).
- Navega a `https://tu-dominio` y verifica login.
- Credenciales seed: admin@gestioni.local / admin123 (cámbialas en producción).
- Sube un adjunto a una tarea y prueba descarga.

## 9. Actualizaciones futuras
```
cd /var/www/gestioni_app
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache
sudo systemctl reload php8.2-fpm
sudo systemctl reload nginx
```
