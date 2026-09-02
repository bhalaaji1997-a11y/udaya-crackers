# Udaya Crackers

A PHP + MySQL fireworks storefront inspired by the browsing and cart patterns of modern Indian cracker stores, with an original Udaya visual identity and original generated imagery.

## Run locally

```bash
php -S 0.0.0.0:8080 -t .
```

Then open `http://localhost:8080`.

## Connect MySQL

1. Create a MySQL database and import `database.sql`.
2. Set these environment variables:

```text
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=udaya_crackers
DB_USER=your_user
DB_PASS=your_password
```

The page uses the local preview store in `data/store.json` until a working database connection is available. Once MySQL is connected, products are loaded from `products`, stock is decremented at checkout, and orders are written to `orders` and `order_items`.

## Admin panel

Open `/admin/` to manage the catalogue:

- Dashboard with active products, low-stock alerts, and new orders
- Add, edit, feature, archive, and price products
- Create, rename, and remove unused categories
- Update stock quantities and low-stock thresholds
- Review customer details and advance orders through new, confirmed, packed, shipped, delivered, or cancelled

For preview mode the admin panel is open so the workspace can be tested without sharing a password. Set `ADMIN_PASSWORD` as a secret before publishing; the panel will then require that password and use the PHP session for access control.

## Files

- `index.php` — storefront, catalog, category filters, search, cart and checkout form
- `checkout.php` — validates and persists orders
- `config.php` — PDO connection and catalog helpers
- `database.sql` — MySQL schema and seed products
- `admin/index.php` — protected back-office dashboard and CRUD screens
- `admin/style.css` / `admin/admin.js` — responsive admin UI and table controls
- `data/store.json` — preview-mode catalog, inventory, and orders
- `public/style.css` / `public/app.js` — responsive UI and cart behavior