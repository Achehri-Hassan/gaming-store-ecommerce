<?php
// 1. بَدْء الـ Session لتفقد بيانات المستخدم
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. 🛡️ حماية الصفحة (Route Protection)
// التحقق واش المستخدم مدايرش Login أولا الـ Role ديالو ماشي admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // يلا كان زائر عادي أو مستخدم عادي، كيتمنع وكيتصيفط نيشان لصفحة الـ Login
    header("Location: login.php");
    exit;
}

// 3. ربط الاتصال بقاعدة البيانات لجلب الإحصائيات لاحقاً
require_once 'src/config/connection.php';
$conn = getConnection();

// (اختياري) جلب إجمالي المستخدمين كـ مثال للإحصائيات
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
    
    <style>
        :root {
            --bg-color: #0d0e12;
            --sidebar-color: #13151b;
            --card-color: #1f222a;
            --neon-green: #8bfb02;
            --text-color: #ffffff;
            --text-muted: #aaa;
            --border-color: #2a2e3d;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            display: flex;
            min-height: 100vh;
        }

        /* 📁 Sidebar Style */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-color);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .sidebar .logo {
            font-size: 20px;
            font-weight: 900;
            font-style: italic;
            text-transform: uppercase;
            color: var(--text-color);
            margin-bottom: 40px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar .logo span {
            color: var(--neon-green);
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--text-muted);
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(139, 251, 2, 0.08);
            color: var(--neon-green);
        }

        .sidebar-menu a.logout {
            margin-top: auto;
            color: #ff4d4d;
        }
        .sidebar-menu a.logout:hover {
            background: rgba(255, 77, 77, 0.08);
            color: #ff4d4d;
        }

        /* 🖥️ Main Content Style */
        .main-content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
        }

        .header-dash {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }

        .header-dash h1 {
            font-size: 26px;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--card-color);
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 14px;
            border: 1px solid var(--border-color);
        }

        /* 📊 Cards Grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background-color: var(--card-color);
            border: 1px solid var(--border-color);
            padding: 25px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: 0.3s;
        }

        .card:hover {
            border-color: var(--neon-green);
            transform: translateY(-3px);
        }

        .card-info h3 {
            font-size: 14px;
            color: var(--text-muted);
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .card-info p {
            font-size: 28px;
            font-weight: bold;
        }

        .card-icon {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            color: var(--neon-green);
            font-size: 24px;
        }

        .welcome-box {
            background: linear-gradient(135deg, var(--card-color), #161920);
            padding: 30px;
            border-radius: 12px;
            border-left: 4px solid var(--neon-green);
            margin-bottom: 30px;
        }

        .welcome-box h2 {
            margin-bottom: 10px;
            color: var(--neon-green);
        }
    </style>
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
                <i class="fas fa-user-shield" style="color: var(--neon-green);"></i>
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
                    <p>0</p> </div>
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