# GESTIONI - Guía rápida

Aplicación Laravel 12 (Breeze) para gestión de proyectos y tareas con roles admin/manager/user.

## Requisitos
- PHP 8.2+
- Composer
- Node 20+ y npm
- MySQL 8+ (o MariaDB equivalente)

## Pasos al clonar
1) Instalar dependencias PHP:
```
composer install
```
2) Instalar dependencias JS y build de assets:
```

npm run build
```
3) Copiar env y ajustar credenciales:
```
cp .env.example .env
```
	- APP_URL=http://localhost (ajusta dominio)
	- DB_DATABASE=gestioni_laravel, DB_USERNAME/DB_PASSWORD según tu base
	- MAIL_* según tu SMTP
4) Generar key y migrar con seeds (crea usuarios demo):
```
php artisan key:generate
php artisan migrate --seed
```
5) Crear symlink de storage para adjuntos:
```
php artisan storage:link
```

## Usuarios seed (ambiente local)
- Admin: admin@gestioni.local / admin123
- Manager: manager@gestioni.local / admin123
- Usuario: user@gestioni.local / admin123

## Comandos útiles
- Servir local: `php artisan serve`
- Pruebas: `php artisan test`
- Exportar assets en prod: `npm run build`

## Notas de producción
- Cambia contraseñas seed y MAIL_FROM_ADDRESS.
- Configura APP_ENV=production, APP_DEBUG=false.
- Revisa permisos de `storage/` y `bootstrap/cache/` para el usuario del servidor web.

## CI/CD (GitHub Actions)
- Flujo básico en `.github/workflows/ci.yml`:
	- Levanta MySQL 8 en servicio.
	- Instala PHP deps, copia `.env.example`, genera key, ajusta DB a gestioni_test.
	- Corre migraciones, instala npm deps, build de assets y ejecuta `php artisan test`.
	- Triggers en push/PR a main/master.
- Deploy opcional a servidor Ubuntu por SSH en `.github/workflows/deploy.yml` (requiere secrets: SERVER_HOST, SERVER_USER, SERVER_SSH_KEY, APP_URL, DB_PASS, DB_ROOT_PASSWORD).

## Guía Ubuntu Server
- Consulta [DEPLOY_UBUNTU.md](DEPLOY_UBUNTU.md) para pasos completos en Ubuntu (paquetes, DB, Nginx, PHP-FPM, permisos, cron/queues y verificación).
- Consulta [DEPLOY_MANUAL.md](DEPLOY_MANUAL.md) si harás el despliegue manual (git clone/pull + composer/npm + migraciones, sin CI/SSH).

## Despliegue rápido (producción)
1) Dependencias y build (en el servidor o CI):
```
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```
2) Copia tu `.env` con credenciales productivas y genera key:
```
php artisan key:generate --force
```
3) Migraciones y seeds mínimos si los necesitas:
```
php artisan migrate --force
```
4) Cachés optimizadas:
```
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
5) Storage link (una sola vez):
```
php artisan storage:link
```
6) Servir con PHP-FPM/Nginx y apuntar el docroot a `public/`.

## Tareas de segundo plano
- Si usas colas: `php artisan queue:work --tries=3 --backoff=5` supervisado (Supervisor/systemd).
- Programador (cron): `* * * * * php /ruta/a/artisan schedule:run >> /dev/null 2>&1`.

## Salud y mantenimiento
- Logs en `storage/logs/laravel.log` (configurable por canal).
- Para limpiar cachés en incidencias: `php artisan optimize:clear`.
- Verifica que `storage/` y `bootstrap/cache/` tengan permisos de escritura por el usuario del servidor web.

