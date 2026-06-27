<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../models/ProductModel.php';
require_once __DIR__ . '/../../models/OrderModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_admin();

$conn = getConnection();

$total_users    = (int) $conn->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$total_products = countActiveProducts();
$stats          = getOrderStats();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Gaming Store</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="/css/dashboard.css">
</head>
<body>

<aside class="sidebar">
    <div class="logo"><i class="fas fa-gamepad"></i> Tech<span>Shop</span></div>
    <ul class="sidebar-menu" style="height:100%">
        <li><a href="admin_dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Overview</a></li>
        <li><a href="admin_products.php"><i class="fas fa-box"></i> Products (CRUD)</a></li>
        <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="/index.php"><i class="fas fa-eye"></i> View Website</a></li>
        <li style="margin-top:auto"><a href="/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>

<main class="main-content">
    <div class="header-dash">
        <h1>Dashboard Overview</h1>
        <div class="admin-profile">
            <i class="fas fa-user-shield"></i>
            <span>Admin: <strong><?= h($_SESSION['username']) ?></strong></span>
        </div>
    </div>

    <?= render_flash() ?>

    <div class="welcome-box">
        <h2>Welcome Back, <?= h($_SESSION['username']) ?>!</h2>
        <p>Here is what's happening with your gaming store today.</p>
    </div>

    <div class="cards-grid">
        <div class="card">
            <div class="card-info"><h3>Total Users</h3><p><?= number_format($total_users) ?></p></div>
            <div class="card-icon"><i class="fas fa-users"></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3>Active Products</h3><p><?= number_format($total_products) ?></p></div>
            <div class="card-icon"><i class="fas fa-box"></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3>Total Orders</h3><p><?= number_format((int)$stats['total_orders']) ?></p></div>
            <div class="card-icon"><i class="fas fa-shopping-bag"></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3>Revenue</h3><p><?= price((float)$stats['total_revenue']) ?></p></div>
            <div class="card-icon"><i class="fas fa-coins"></i></div>
        </div>
    </div>

    <div class="cards-grid" style="margin-top:16px">
        <div class="card">
            <div class="card-info"><h3>Pending Orders</h3><p><?= (int)$stats['pending'] ?></p></div>
            <div class="card-icon" style="color:#f59e0b"><i class="fas fa-clock"></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3>Delivered</h3><p><?= (int)$stats['delivered'] ?></p></div>
            <div class="card-icon" style="color:#10b981"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <div style="margin-top:24px; display:flex; gap:12px; flex-wrap:wrap">
        <a href="admin_products.php" class="btn btn--primary"><i class="fas fa-plus"></i> Add Product</a>
        <a href="admin_orders.php" class="btn btn--secondary"><i class="fas fa-list"></i> View Orders</a>
    </div>
</main>

</body>
</html>
