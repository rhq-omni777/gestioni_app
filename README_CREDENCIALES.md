# Accesos rápidos

Credenciales seed (migración + seeder):
- Admin: admin@gestioni.local / admin123
- Manager: manager@gestioni.local / admin123
- Usuario: user@gestioni.local / admin123

## Comandos básicos

Instalar dependencias PHP:
```
composer install
```

Instalar dependencias JS y build:
```
npm install
npm run build
```

Migrar + seed (crea usuarios anteriores y ajustes por defecto):
```
php artisan migrate --seed
```

Enlace de storage público (para adjuntos y uploads):
```
php artisan storage:link
```

Correr pruebas:
```
php artisan test
```

## Variables de entorno mínimas
- APP_URL=http://localhost
- APP_NAME=GESTIONI
- DB_* según tu base MySQL
- MAIL_FROM_ADDRESS=noreply@gestioni.local

## Notas
- Cambia las contraseñas de los usuarios seed en entornos reales.
- Ajusta APP_URL/APP_NAME y correo remitente según despliegue.
