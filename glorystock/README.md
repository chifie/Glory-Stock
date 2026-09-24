# GloryStock — Laravel Edition (PostgreSQL)

Inventory Management System & POS, converted from plain PHP to **Laravel MVC**, running on **PostgreSQL**.

## Structure (Legacy → Laravel)

| Legacy file | Laravel equivalent |
|---|---|
| `login.php` / `register.php` / `logout.php` | `AuthController` + `resources/views/auth/*` |
| `dashboard.php` | `DashboardController` + `resources/views/dashboard/index.blade.php` |
| `pos.php` | `PosController` + `resources/views/pos/index.blade.php` |
| `add_product.php` / `edit_product.php` / `delete_product.php` | `ProductController` + `resources/views/products/form.blade.php` |
| `categories.php` | `CategoryController` + `resources/views/categories/index.blade.php` |
| `expenses.php` | `ExpenseController` + `resources/views/expenses/index.blade.php` |
| `sales_history.php` / `void_sale.php` | `SalesHistoryController` + `resources/views/sales/history.blade.php` |
| `stock_log.php` | `StockLogController` + `resources/views/stock-log/index.blade.php` |
| `receipt.php` | `ReceiptController` + `resources/views/receipt/show.blade.php` |
| `reports.php` | `ReportController` + `resources/views/reports/index.blade.php` |
| `daily_close.php` | `DailyCloseController` + `resources/views/reports/daily-close.blade.php` |
| `print_reorder.php` | `ReorderController` + `resources/views/reports/reorder.blade.php` |
| `users.php` / `delete_user.php` | `UserController` + `resources/views/users/index.blade.php` |
| `access_denied.php` | `EnsureUserIsAdmin` middleware + `resources/views/auth/access-denied.blade.php` |
| `db_connect.php` | Laravel Eloquent + `.env` database config |
| `nav.php` | `resources/views/partials/navbar.blade.php` |

## Models

`User`, `Category`, `Product`, `Sale`, `StockLog`, `Expense` — matching the legacy
`inventory_db` schema exactly (including `sales.sale_date`, `stock_log`, `expenses`
tables and legacy bcrypt password hashes).

## Getting Started

```bash
cd glorystock
composer install
cp .env.example .env
php artisan key:generate

# Configure your database in .env (PostgreSQL example):
# DB_CONNECTION=pgsql
# DB_HOST=127.0.0.1
# DB_PORT=5432
# DB_DATABASE=inventory_db
# DB_USERNAME=postgres
# DB_PASSWORD=secret

php artisan migrate --seed   # seeds legacy users/products/sales/expenses
php artisan serve            # http://127.0.0.1:8000
```

Legacy users keep their passwords (`fetty`, `chifie` = staff; `monica` = admin).
Admin registration still requires the security key (default `PRO-99-SECURE`,
override with `GLORYSTOCK_ADMIN_KEY` in `.env`).

## Testing

```bash
php artisan test
```
