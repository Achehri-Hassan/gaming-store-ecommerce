<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'src/config/connection.php';
require_once 'src/helpers/helpers.php';
require_once 'src/models/OrderModel.php';

require_login('my-orders.php');

$orders = getUserOrders((int) $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — Gaming Store</title>
    <link rel="stylesheet" href="css/components/style.css">
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .orders-page { padding: 40px 16px; max-width: 900px; margin: 0 auto; }
        .orders-page h1 { color: #e5e7eb; margin-bottom: 24px; }
        .order-card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 10px; padding: 20px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .order-card__id { color: #9ca3af; font-size: .9rem; }
        .order-card__total { color: #3b82f6; font-weight: 700; }
        .order-card__date { color: #6b7280; font-size: .85rem; }
        .order-card__actions a { text-decoration: none; }
        .empty-state { text-align: center; padding: 60px 20px; color: #4b5563; }
        .empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; }
    </style>
</head>
<body>
<?php include 'src/views/layouts/header.php'; ?>
<main class="orders-page">
    <h1><i class="fas fa-box"></i> My Orders</h1>

    <?= render_flash() ?>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <p>You haven't placed any orders yet.</p>
            <a href="index.php" class="btn-place-order" style="display:inline-flex;margin-top:12px">
                <i class="fas fa-store"></i> Start Shopping
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div>
                    <div class="order-card__id">Order #<?= (int)$order['id'] ?></div>
                    <div class="order-card__date"><?= h(date('d M Y', strtotime($order['created_at']))) ?></div>
                </div>
                <span class="status-badge status-badge--<?= h($order['status']) ?>"><?= h(ucfirst($order['status'])) ?></span>
                <div class="order-card__total"><?= price((float)$order['total_price']) ?></div>
                <div class="order-card__actions">
                    <a href="order-success.php?id=<?= (int)$order['id'] ?>" class="btn-back-shop">
                        <i class="fas fa-eye"></i> View
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>
<?php include 'src/views/layouts/footer.php'; ?>
</body>
</html>
