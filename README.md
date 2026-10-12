# GloryStock

Inventory Management System & Point of Sale for small businesses.

Modern SaaS landing page (React + Vite + Tailwind), Laravel 13 API backend
Sanctum token auth, PostgreSQL database.

## Repository structure

```
GloryStock/
├── frontend/    # React 19 + Vite 8 + Tailwind CSS 4 SPA
│   ├── src/
│   ├── public/
│   ├── package.json
│   ├── vite.config.ts
│   └── README.md
├── backend/     # Laravel 13 (MVC + JSON API, Sanctum, PostgreSQL)
│   ├── app/
│   ├── routes/
│   ├── database/
│   ├── config/
│   ├── composer.json
│   └── README.md
└── README.md    # this file
```

## Quick start (both apps)

### 1. Backend — Laravel API

```bash
cd backend
composer install
cp .env.example .env
php artisan key:generate

# configure PostgreSQL in .env, then:
php artisan migrate --seed    # seeds legacy users/products/sales/expenses
php artisan serve             # http://127.0.0.1:8090 (or :8000 if free)
```

The API lives under `/api/*` (login, POS, dashboard, reports, etc.).
Full endpoint list: `backend/routes/api.php`.

### 2. Frontend — React SPA

```bash
cd frontend
npm install
npm run dev
```

Vite proxies `/api/*` to the Laravel app (`127.0.0.1:8090`) automatically —
see `vite.config.ts`.

| URL | What |
|---|---|
| http://127.0.0.1:5173/ | Marketing landing page |
| http://127.0.0.1:5173/login | Auth (staff or admin, legacy accounts kept) |
| http://127.0.0.1:5173/register | Sign-up (admin key: `GLORYSTOCK_ADMIN_KEY`) |
| http://127.0.0.1:5173/pos | POS terminal (after login) |

### Production build

```bash
cd frontend && npm run build   # outputs dist/
cd backend && php artisan test # 27 tests green
```

## Reference files

- `inventory_db (1).sql` — phpMyAdmin dump of the original legacy database, kept as
  a data-migration reference (its data is now fully covered by Laravel migrations/seeder).
- `logo.png` — brand logo (also copied into `frontend/public/` and `backend/public/`).

## Testing

```bash
cd backend && php artisan test
cd frontend && npm run build   # type-checks the SPA too
```
