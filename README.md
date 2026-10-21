# CRM Condominio Cruz Azul

Aplicación web para administrar un condominio: arrendatarios, servicios, facturas, pagos, vencimientos, historial de cobros y reportes.
Está construida con Laravel 9, Blade, MySQL y Vite.

Tiene dos roles:

- **Administrador:** gestiona arrendatarios, servicios, facturas, pagos, historial y reportes.
- **Arrendatario:** consulta únicamente sus propias facturas, servicios, pagos, comprobantes y estado de cuenta.

## Requisitos

- PHP 8.2 o superior, con las extensiones habituales de Laravel y `pdo_mysql`. (`composer.json` declara `^8.0.2`, pero las dependencias bloqueadas en `composer.lock` exigen 8.2.)
- Composer 2.
- Node.js 16 o superior y npm.
- MySQL o MariaDB **local** para desarrollo. Los tests no lo necesitan.

## Instalación local

1. Instala las dependencias:

   ```powershell
   composer install
   npm install
   ```

2. Crea tu archivo de entorno y genera la clave de la aplicación:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

3. Edita `.env` y configura una base de datos **local y vacía** (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`). Toda la configuración sensible vive únicamente en `.env`, que no se versiona.

4. Crea las tablas. Hazlo solo en una base de datos local o nueva:

   ```powershell
   php artisan migrate
   ```

5. Compila el frontend:

   ```powershell
   npm run build   # recursos compilados, sin servidor de Vite
   npm run dev     # alternativa para desarrollo, con recarga en caliente
   ```

6. Arranca la aplicación:

   ```powershell
   php artisan serve
   ```

   Abre `http://127.0.0.1:8000`.

### Datos de demostración (opcional, solo local)

`DatabaseSeeder` está bloqueado por defecto y no se ejecuta como parte de la instalación. Si quieres datos de ejemplo en una base de datos local **desechable**, define `SEED_DEMO_DATA=true` en tu `.env` local y ejecuta `php artisan db:seed`. El seeder nunca modifica usuarios que ya existan. Las cuentas de demostración están definidas únicamente en `database/seeders/DatabaseSeeder.php` y son solo para desarrollo.

## Pruebas

```powershell
php artisan test
```

Los tests se ejecutan siempre sobre **SQLite en memoria**. Esto está forzado en `phpunit.xml` y verificado en `tests/TestCase.php`, de modo que nunca usan la base de datos configurada en `.env`.

## ⚠️ Advertencia

**Nunca ejecutes `php artisan migrate:fresh`, `migrate:reset`, `migrate:rollback` ni seeders (`db:seed`, `migrate --seed`) sobre una base de datos de producción o compartida.** Destruyen o sobrescriben datos.

En producción, antes de cualquier migración haz un backup de la base de datos, comprueba con `php artisan migrate:status` que haya migraciones realmente pendientes y aplica solo `php artisan migrate --force`.

## Archivos que no se versionan

`.env` y cualquier `.env.*` (excepto `.env.example`), `vendor/`, `node_modules/`, `public/build/`, `public/hot`, volcados de base de datos (`*.sql`, `*.sql.gz`) y archivos comprimidos (`*.zip`).

## Despliegue

La guía de despliegue está en [`DEPLOY_HOSTINGER.md`](DEPLOY_HOSTINGER.md).

## Estado de seguridad

Laravel 9 ya no recibe parches de seguridad. La actualización a una versión soportada está pendiente y debe hacerse como una etapa independiente, con backup y pruebas.
