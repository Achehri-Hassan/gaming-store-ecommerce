# 🎮 Gaming Store — E-commerce Platform

A full-stack PHP/MySQL e-commerce web application for gaming gear (chairs, desks, controllers, consoles, mice, monitors, keyboards). Built with vanilla PHP (no framework), a layered architecture, and a custom admin back-office.

## ✨ Features

**Storefront**
- Home page with products grouped by category and a hero slider (Swiper.js)
- Product listing with search, category filter, price range filter, and pagination
- Detailed product page with image gallery and related products
- Shopping cart (add / update quantity / remove) synced between client and server
- Checkout flow with delivery information and order summary
- User registration & login (secure password hashing)
- Order history for logged-in customers

**Admin back-office**
- Dashboard with key metrics (total orders, revenue, pending / delivered counts)
- Product management (create, edit, delete, image gallery)
- Order management (search, filter by status, order detail, status update)
- Role-based access control (admin vs. regular user)

**Security**
- Prepared statements (PDO) against SQL injection
- CSRF tokens on all sensitive forms
- Password hashing with `password_hash()` / `password_verify()`
- Output escaping (XSS protection) via a central `h()` helper
- Environment variables for DB credentials (`.env`, never committed)

## 🏗️ Architecture

The project follows a lightweight layered structure inspired by MVC:

```
gaming-store-ecommerce/
├── index.php                # Home page (catalog by category)
├── login.php / register.php # Authentication
├── checkout.php             # Checkout flow
├── cart-handler.php         # AJAX cart endpoint
├── my-orders.php            # Customer order history
├── shop-details.php         # Search / filters / pagination
├── deails.php                # Single product page
├── database/
│   ├── script.sql            # Schema + seed data
│   └── orders_migration.sql  # Orders & order_items tables
├── src/
│   ├── config/connection.php # PDO connection (env-based)
│   ├── models/                # UserModel, ProductModel, OrderModel
│   ├── helpers/helpers.php    # Security, formatting, pagination
│   ├── controllers/           # Reserved for future MVC migration
│   ├── views/
│   │   ├── layouts/           # header, footer, cart
│   │   ├── partials/          # product_card, etc.
│   │   └── admin/             # dashboard, products, orders
│   └── assets/                 # Product images, banners
├── css/
└── js/
```

## 🗄️ Database schema

| Table | Purpose |
|---|---|
| `users` | Customer & admin accounts |
| `products` | Product catalog |
| `product_gallery` | Additional product images |
| `orders` | Customer orders |
| `order_items` | Line items per order |

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

## 🔑 Admin access

To access the back-office, log in with an account whose `role` column is set to `admin` in the `users` table, then visit:
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
- Dedicated controllers (`src/controllers/`) are scaffolded for a future migration to a fuller MVC structure.

## 📄 License

This project is for educational/portfolio purposes.