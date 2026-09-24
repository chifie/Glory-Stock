# Glory-Stock

Inventory Management System & POS.

The original plain-PHP app has been fully converted to **Laravel MVC** and now
runs on **PostgreSQL**. The application lives in the [`glorystock/`](glorystock/)
directory — see [glorystock/README.md](glorystock/README.md) for the full
structure map and setup instructions.

## Quick Start

```bash
cd glorystock
composer install
cp .env.example .env
php artisan key:generate

# Configure your PostgreSQL database in .env:
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=inventory_db
# DB_USERNAME=postgres
# DB_PASSWORD=secret

php artisan migrate --seed   # seeds legacy users/products/sales/expenses
php artisan serve            # http://127.0.0.1:8000
```

## Reference Files

- `inventory_db (1).sql` — phpMyAdmin dump of the original legacy database,
  kept as a data-migration reference (its schema is now fully covered by
  Laravel migrations and the seeder).
- `logo.png` — brand logo, also copied into `glorystock/public/`.

## Testing

```bash
cd glorystock
php artisan test
```
