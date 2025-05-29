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

- APP_TIMEZONE=America/Caracas

- DB_CONNECTION=pgsql
- DB_HOST=127.0.0.1
- DB_PORT=5432
- DB_DATABASE=api_crypto
- DB_USERNAME=postgres
- DB_PASSWORD=1234


## Instalación
1. Clonar repositorio:
```bash
git clone git@github.com:VCTR93/crypto-api.git
cd crypto-api

- el archivo .env se enviará por separado 
