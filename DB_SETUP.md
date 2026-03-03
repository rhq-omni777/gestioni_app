# Creación rápida de base de datos MySQL

Ejecuta en tu servidor (cambia la contraseña si quieres otra):
```
sudo mysql -e "CREATE DATABASE gestioni_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
sudo mysql -e "CREATE USER 'gestioni'@'localhost' IDENTIFIED BY 'G3st!0n1-Prod-2026';"
sudo mysql -e "GRANT ALL PRIVILEGES ON gestioni_laravel.* TO 'gestioni'@'localhost'; FLUSH PRIVILEGES;"
```

Luego en `.env` usa los mismos valores:
```
DB_DATABASE=gestioni_laravel
DB_USERNAME=gestioni
DB_PASSWORD=G3st!0n1-Prod-2026
```

Después corre las migraciones con seed:
```
php artisan migrate --force --seed
```

Puedes cambiar la contraseña; solo asegúrate de usar la misma en `.env` y en el comando `CREATE USER`.
