# 🎮 Gaming Store — E-commerce Platform

A full-stack PHP/MySQL e-commerce web application for gaming gear (chairs, desks, controllers, consoles, mice, monitors, keyboards). Built with vanilla PHP (no framework) and a custom admin back-office.

## ✨ Features

**Storefront**
- Home page with products grouped by category and a hero slider (Swiper.js)
- Product listing with search, category filter, price range filter, and pagination
- Detailed product page (`shop-details.php`) with image gallery and related products
- Shopping cart (add / update quantity / remove), CSRF-protected, synced between client and server
- Checkout flow with delivery information, order summary, **server-side price recalculation, and real-time stock validation**
- User registration & login (secure password hashing)
- Order history for logged-in customers, scoped strictly to their own orders

**Admin back-office**
- Dashboard with key metrics (total orders, revenue, pending / delivered counts)
- Product management: create, edit, delete, image gallery — CSRF-protected, POST-only, category validated against a fixed whitelist
- Customer summary view (orders grouped by customer, with purchase history)
- **Order status management**: filter orders by status, update an order's status (pending / processing / shipped / delivered / cancelled) from the admin UI
- Role-based access control (admin vs. regular user), enforced server-side on every admin page

**Security**
- Prepared statements (PDO) against SQL injection — no string-concatenated queries anywhere
- CSRF tokens on **all** state-changing requests, including AJAX/JSON endpoints (cart) and admin product/order actions — not just the login/checkout forms
- Destructive actions (delete product, delete order) are POST-only; no state change is ever triggered by a GET request
- Password hashing with `password_hash()` / `password_verify()`, session regeneration on login
- Output escaping (XSS protection) via a central `h()` helper
- Image uploads are validated by real file content (`getimagesize()` + MIME check), not just by filename extension, capped at 2MB, and always saved under a server-generated filename
- Product category values are validated against a fixed whitelist before ever being used to build a filesystem path (prevents path traversal on upload)
- Inactive/deactivated products can't be viewed, added to cart, or purchased via a direct link — enforced in the database query, not just hidden in the UI
- Stock is validated and decremented atomically inside the checkout transaction — a shopper can never order more than what's actually in stock, and stock can never go negative
- Environment variables for DB credentials (`.env`, never committed)
- No real admin credential is stored in the repository — see **Admin setup** below

## 🏗️ Architecture

This is **not** an MVC application, and the README no longer claims otherwise. It's a straightforward layered structure:

- **Models** (`src/models/`) — the only layer that talks to the database (PDO, prepared statements).
- **Helpers** (`src/helpers/helpers.php`) — cross-cutting concerns: CSRF, output escaping, auth guards, pagination, secure file uploads.
- **Pages** (top-level `.php` files and `src/views/`) — each page is its own lightweight "controller + view": it reads input, calls the model layer, and renders HTML. There is no separate controller/router layer routing requests to these pages.

```
gaming-store-ecommerce/
├── index.php                 # Home page (catalog by category)
├── login.php / register.php  # Authentication
├── logout.php
├── checkout.php               # Checkout flow (server-side price + stock enforcement)
├── cart-handler.php           # AJAX cart endpoint (CSRF-protected)
├── my-orders.php              # Customer order history (scoped to logged-in user)
├── order-success.php
├── shop.php / shop-details.php# Listing (search/filter/pagination) and single product page
├── database/
│   ├── script.sql             # Schema + seed data (no admin account seeded — see below)
│   ├── orders_migration.sql   # orders & order_items tables
│   └── create_admin.php       # CLI script to create/promote an admin account safely
├── src/
│   ├── config/connection.php  # PDO connection (env-based)
│   ├── models/                # UserModel, ProductModel, OrderModel
│   ├── helpers/helpers.php    # Security, formatting, pagination, secure uploads
│   ├── views/
│   │   ├── layouts/           # header, footer, cart
│   │   ├── partials/          # product_card, etc.
│   │   └── admin/             # dashboard, products, orders
│   └── assets/                # Product images, banners
├── css/
└── js/
```

## 🗄️ Database schema

| Table | Purpose |
|---|---|
| `users` | Customer & admin accounts |
| `products` | Product catalog, including `stock` (enforced at checkout) |
| `product_gallery` | Additional product images |
| `orders` | Customer orders, including `status` (pending/processing/shipped/delivered/cancelled) |
| `order_items` | Line items per order (price snapshotted at purchase time) |

No migration file was needed for the security/feature fixes below — `orders.status` and `products.stock` already existed in the schema; the fixes were entirely in the application layer (actually validating and using columns that were already there).

## 🚀 Getting started

### Requirements
- PHP >= 8.1
- MySQL / MariaDB
- A local server stack (XAMPP, MAMP, Laragon, or `php -S`)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repo-url>
   cd gaming-store-ecommerce
   ```

2. **Create the database**
   ```bash
   mysql -u root -p < database/script.sql
   mysql -u root -p gaming_store < database/orders_migration.sql
   ```

3. **Configure environment variables**
   ```bash
   cp .env.example .env
   ```
   Then edit `.env` with your local database credentials:
   ```
   DB_HOST=localhost
   DB_NAME=gaming_store
   DB_USER=root
   DB_PASS=
   APP_ENV=development
   APP_URL=http://localhost/gaming-store-ecommerce
   APP_NAME="Gaming Store"
   ```

4. **Serve the project**
   - Place the folder inside your server's document root (e.g. `htdocs/`), **or**
   - Run PHP's built-in server:
     ```bash
     php -S localhost:8000
     ```

5. **Open in your browser**
   ```
   http://localhost:8000
   ```

## 🔑 Admin setup

No admin account ships in `database/script.sql` — a real-looking hardcoded credential in a public repo is a leak risk, so instead you create your own after installing:

```bash
php database/create_admin.php you@example.com "a-strong-password" "Admin Name"
```

Or run it with no arguments and it will prompt you interactively (hiding the password where your terminal supports it). This hashes the password with `password_hash()` before it ever touches the database — nothing is stored or logged in plaintext. Then log in normally at `login.php` and visit:
```
/src/views/admin/admin_dashboard.php
```

## 🛠️ Tech stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.1+ (procedural, layered structure) |
| Database | MySQL / MariaDB (InnoDB) |
| Data access | PDO with prepared statements |
| Frontend | HTML5, CSS3, vanilla JavaScript |
| Libraries | Swiper.js (carousel), Font Awesome (icons) |

## 📌 Notes

- This project was built as a hands-on training/portfolio project to practice full-stack PHP development: relational database design, secure authentication, cart/checkout logic, and an admin dashboard.
- It has been through a security-focused audit and fix pass covering CSRF, path traversal, file upload validation, stock enforcement, and inactive-product access — see git history / commit notes for details if you're reviewing this as a portfolio piece.

## 📄 License

This project is for educational/portfolio purposes.
