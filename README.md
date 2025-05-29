# Aplicacion API Criptomonedas con laravel 11
# Autor: Ing. Victor Gonzalez
# Fecha: 2023-02-20

## Requisitos
- PHP 8.3.21
- Composer
- PostgreSQL
- Aplicación dbeaver-ce (aplicación para administrar la base de datos PostgreSQL)
- Laravel 11
- Laravel Passport (para autenticación de API)
- Laravel Sanctum (para autenticación de JWT)
- Middleware JWT (Creación de un middleware para proteger las rutas)
- ReplicateHistoricalData (Clase para replicar datos históricos)
- app/Console/Kernel.php (Clase para definir las tareas de la aplicación)
- app/Models/Crurency.php (Modelo para la moneda)
- app/Models/CrytoHistory.php (Modelo para la historia de criptomonedas)
- app/Models/CryptoCurrency.php (Modelo para la criptomoneda)
- app/Controllers/AuthController.php (Controlador para autenticación)
- app/Controllers/CryptoController.php (Controlador para criptomonedas)
- app/Controllers/UserController.php (Controlador para usuarios)
- usar el comando `php artisan migrate:fresh --seed` para crear la base de datos con valores en las tablas currencies, cripto_currencies
- usar el comando `php artisan config:clear` para limpiar la cache de configuración
- usar el comando `php artisan app:actualizar-precios-cripto` para actualizar los precios de las criptomonedas
- configuracion del archivo .env
<!-- 
PP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:9sZhoat7jvh0qH+kvHVt0RUsMhjXm4FXlR59wvWbpVM=
APP_DEBUG=true
APP_TIMEZONE=America/Caracas
APP_URL=http://localhost

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file
# APP_MAINTENANCE_STORE=database

PHP_CLI_SERVER_WORKERS=4

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=api_crypto
DB_USERNAME=postgres
DB_PASSWORD=1234

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database
CACHE_PREFIX=

MEMCACHED_HOST=127.0.0.1

REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
-->

## Instalación
1. Clonar repositorio:
```bash
git clone git@github.com:VCTR93/crypto-api.git
cd crypto-api

- el archivo .env se enviará por separado 
