# Despliegue en Hostinger Web Hosting — alquilerbol.com

Guía para el **primer despliegue controlado**. No contiene credenciales: las pones tú en el `.env` del servidor.

Stack: Laravel 9.52 · PHP 8.2 (mínimo real **8.2**, ver nota) · MySQL de Hostinger · Vite precompilado.

> **PHP:** `composer.json` pide `^8.0.2`, pero el `composer.lock` actual incluye paquetes de producción que exigen **PHP ≥ 8.2**
> (`symfony/event-dispatcher 7.x`, `symfony/string 7.x`, `dragonmantank/cron-expression 3.6`, `nette/utils 4.1`).
> En hPanel → *Avanzado → Configuración PHP* elige **PHP 8.2 o superior** (8.2 recomendado con Laravel 9).

## 1. Estructura recomendada

El proyecto va **fuera** de `public_html`. Solo el contenido de `public/` queda expuesto.

```
/home/<usuario>/domains/alquilerbol.com/
├── laravel/                 ← proyecto Laravel (NO público)
│   ├── app/ bootstrap/ config/ database/ resources/ routes/ storage/ vendor/
│   ├── .env                 ← se crea aquí, NUNCA en public_html
│   └── artisan composer.json composer.lock
└── public_html/             ← contenido de public/ (público)
    ├── index.php  .htaccess  favicon.ico  robots.txt
    └── build/               ← manifest.json + assets de Vite
```

`public/index.php` ya detecta esta estructura (usa `../laravel` si no encuentra `../vendor`), con rutas relativas y sin rutas absolutas.
Si prefieres otro nombre de carpeta, cámbialo en `$basePath` de `public/index.php`.

## 2. Qué subir

- `app/ bootstrap/ config/ database/ resources/ routes/ storage/` (con sus subcarpetas vacías), `composer.json`, `composer.lock`, `artisan`.
- `public/*` → a `public_html/` (incluye `public/build/` ya compilado y `.htaccess`).
- `vendor/` generado en el servidor (sección 5) **o** local con `--no-dev` (ver abajo).

## 3. Qué NO subir

`.env` local, `node_modules/`, `public/hot`, `.git/`, `tests/`, `phpunit.xml`, `*.sql`, `*.zip`, `storage/logs/*.log`,
`storage/framework/{cache,sessions,views}/*` (contenido), `bootstrap/cache/*.php` (excepto `.gitignore`), cualquier backup con credenciales.

## 4. Construir el frontend (en tu PC, antes de subir)

Hostinger no ejecuta Node. Compila local:

```
npm ci
npm run build
```

Comprueba que existe `public/build/manifest.json` y que **no** existe `public/hot`. Sube `public/build/` a `public_html/build/` aunque esté en `.gitignore`.

## 5. Dependencias PHP

Con SSH en Hostinger (dentro de `laravel/`):

```
composer install --no-dev --optimize-autoloader
```

Respeta `composer.lock`. **No uses `composer update`.** Sin SSH: ejecuta ese mismo comando en tu PC y sube `vendor/` (comprimido, y descomprímelo desde el administrador de archivos).

## 6. Crear el `.env` en el servidor

Créalo en `laravel/.env` (administrador de archivos o SSH), copiando `.env.example`. Valores de producción:

```
APP_NAME="Condominio Cruz Azul"
APP_ENV=production
APP_KEY=              # se genera con: php artisan key:generate --force
APP_DEBUG=false
APP_URL=https://alquilerbol.com
LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=              # el que muestra hPanel → Bases de datos MySQL
DB_PORT=3306
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=

SESSION_DRIVER=file
SESSION_SECURE_COOKIE=true
CACHE_DRIVER=file
QUEUE_CONNECTION=sync
SEED_DEMO_DATA=false  # nunca true en producción
```

`file` + `sync` bastan: la app no usa colas, WebSockets ni Redis. Permisos del `.env`: `chmod 640`.

## 7. Permisos

```
chmod -R 775 storage bootstrap/cache
```

(`755`/`775` según lo que permita el hosting; nunca `777`). `storage:link` **no es necesario**: la app no usa `Storage::` ni archivos subidos.

## 8. Comandos Artisan seguros

Tras comprobar que el `.env` es correcto:

```
php artisan key:generate --force      # solo si APP_KEY está vacío
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate:status            # solo lectura: revisa qué falta
```

Solo si `migrate:status` muestra migraciones **Pending** y ya existe backup:

```
php artisan migrate --force
```

Si cambias algo en `.env`, ejecuta `php artisan config:clear` y vuelve a `config:cache`.

## 9. Verificar

1. `https://alquilerbol.com` redirige a `/login` con estilos cargados (si no, falta `public_html/build/`).
2. `http://alquilerbol.com` redirige a `https`.
3. Inicia sesión como administrador; abre Arrendatarios, Facturas y Pagos.
4. `https://alquilerbol.com/.env` y `/storage/logs/laravel.log` deben dar **404/403**.
5. Un error forzado no debe mostrar trazas (`APP_DEBUG=false`).
6. Revisa `laravel/storage/logs/laravel.log`.

## 10. Rollback manual básico

- **Antes de cada despliegue:** exporta la BD desde hPanel (phpMyAdmin → Exportar) y comprime la carpeta `laravel/` actual en un `.zip` fuera de `public_html`.
- **Código:** restaura el `.zip` y ejecuta `php artisan config:clear && php artisan cache:clear && php artisan view:clear`.
- **BD:** importa el volcado solo si una migración dañó datos. No uses `migrate:rollback` en producción sin backup verificado.
- Modo mantenimiento durante el cambio: `php artisan down` / `php artisan up`.

## 11. NO ejecutar en producción

`migrate:fresh` · `migrate:reset` · `migrate:rollback` · `db:seed` / `migrate --seed` · `composer update` · `npm run dev` · `php artisan tinker` con datos reales
· `APP_DEBUG=true` · `SEED_DEMO_DATA=true` · dejar `.env`, `.git` o `*.sql` dentro de `public_html`.

## 12. Después del primer despliegue

Laravel 9 está fuera de soporte de seguridad (`composer audit` reporta avisos en `laravel/framework`). Planifica la actualización
(9 → 10 → 11/12) como etapa independiente, con backup y en un entorno de pruebas.
