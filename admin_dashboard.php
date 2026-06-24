<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {

    header("Location: login.php");
    exit;
}


require_once 'src/config/connection.php';
$conn = getConnection();


$stmt = $conn->query("SELECT COUNT(*) as total_users FROM users where role = 'user' ");
$res = $stmt->fetch();
$total_users = $res['total_users'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tech Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

    <!-- style css -->
    <link rel="stylesheet" href="css/dashboard.css">

</head>

<body>

    <aside class="sidebar">
        <div class="logo">
            <i class="fas fa-gamepad"></i> Tech<span>Shop</span>
        </div>
        <ul class="sidebar-menu" style="height: 100%;">
            <li><a href="admin_dashboard.php" class="active"><i class="fas fa-chart-pie"></i> Overview</a></li>
            <li><a href="admin_products.php"><i class="fas fa-box"></i> Products (CRUD)</a></li>
            <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="index.php"><i class="fas fa-eye"></i> View Website</a></li>
            <li style="margin-top: auto;"><a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div class="header-dash">
            <h1>Dashboard Overview</h1>
            <div class="admin-profile">
                <i class="fas fa-user-shield"></i>
                <span>Admin: <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            </div>
        </div>

        <div class="welcome-box">
            <h2>Welcome Back, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
            <p>Here is what's happening with your gaming store today. You can manage products, view system statistics, and handle clients orders.</p>
        </div>

        <div class="cards-grid">
            <div class="card">
                <div class="card-info">
                    <h3>Total Users</h3>
                    <p><?= $total_users ?></p>
                </div>
                <div class="card-icon"><i class="fas fa-users"></i></div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Products</h3>
                    <p>0</p>
                </div>
                <div class="card-icon"><i class="fas fa-box"></i></div>
            </div>

            <div class="card">
                <div class="card-info">
                    <h3>Total Orders</h3>
                    <p>0</p>
                </div>
                <div class="card-icon"><i class="fas fa-shopping-bag"></i></div>
            </div>
        </div>
    </main>

</body>

</html>