# Guía de despliegue en Ubuntu Server

Probado con Ubuntu 22.04, Nginx, PHP 8.2, MySQL 8, Node 20.

## 1. Paquetes base
```
sudo apt update
sudo apt install -y nginx mysql-server php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl unzip git curl
# Node (usar nvm o repositorio oficial; ejemplo con nvm):
curl -fsSL https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
source ~/.bashrc
nvm install 20
npm install -g npm@latest
# Composer
cd /tmp && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php --install-dir=/usr/local/bin --filename=composer
```

## 2. Base de datos MySQL
```
sudo mysql -e "CREATE DATABASE gestioni_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'gestioni'@'localhost' IDENTIFIED BY 'cambiaEstaClave';"
sudo mysql -e "GRANT ALL PRIVILEGES ON gestioni_laravel.* TO 'gestioni'@'localhost'; FLUSH PRIVILEGES;"
```

## 3. Deploy del código
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
DB_PASSWORD=cambiaEstaClave
MAIL_FROM_ADDRESS=noreply@gestioni.local
# Ajusta SMTP real
```

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

## 6. Nginx (server block)
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
Habilitar y probar:
```
sudo ln -s /etc/nginx/sites-available/gestioni /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## 7. Servicios en background
- Cron (scheduler): `* * * * * php /var/www/gestioni_app/artisan schedule:run >> /dev/null 2>&1`
- Queues (si las usas): Supervisor `/etc/supervisor/conf.d/gestioni-worker.conf`:
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
Aplicar:
```
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl status
```

## 8. Firewall (ufw)
```
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

## 9. Verificación final
- `php artisan test` (opcional en servidor): debe pasar.
- Navega a `http://tu-dominio` y verifica pantalla de login.
- Usa credenciales seed (cámbialas luego):
  - admin@gestioni.local / admin123
  - manager@gestioni.local / admin123
  - user@gestioni.local / admin123
- Sube un adjunto en una tarea y comprueba descarga.

## 10. Mantenimiento
- Limpiar cachés en incidencias: `php artisan optimize:clear`
- Logs: `storage/logs/laravel.log` y `storage/logs/worker.log`
- Backups DB (ejemplo): `mysqldump gestioni_laravel > backup.sql`
