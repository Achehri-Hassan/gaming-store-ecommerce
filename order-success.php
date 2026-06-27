<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'src/config/connection.php';
require_once 'src/helpers/helpers.php';
require_once 'src/models/OrderModel.php';

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$orderId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$order   = $orderId ? getOrderById($orderId) : null;

// Only allow the owner or admin to view this page
if (!$order || ((int)$order['user_id'] !== (int)$_SESSION['user_id'] && $_SESSION['role'] !== 'admin')) {
    header('Location: index.php');
    exit;
}

$items = getOrderItems($orderId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — Gaming Store</title>
    <link rel="stylesheet" href="css/components/style.css">
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php include 'src/views/layouts/header.php'; ?>

<main class="checkout-page">
    <div class="checkout-container" style="max-width:700px">
        <div class="success-card">
            <div class="success-icon"><i class="fas fa-check-circle"></i></div>
            <h1>Order Confirmed!</h1>
            <p>Thank you, <strong><?= h($order['customer_name']) ?></strong>. Your order has been placed successfully.</p>
            <p class="order-ref">Order #<strong><?= (int) $order['id'] ?></strong></p>

            <div class="order-details-box">
                <div class="detail-row"><span>Status</span> <span class="status-badge status-badge--<?= h($order['status']) ?>"><?= h(ucfirst($order['status'])) ?></span></div>
                <div class="detail-row"><span>Phone</span> <span><?= h($order['phone']) ?></span></div>
                <div class="detail-row"><span>City</span> <span><?= h($order['city']) ?></span></div>
                <div class="detail-row"><span>Address</span> <span><?= h($order['address']) ?></span></div>
                <div class="detail-row detail-row--total"><span>Total</span> <span><?= price((float)$order['total_price']) ?></span></div>
            </div>

            <?php if (!empty($items)): ?>
                <h3 style="margin:24px 0 12px; text-align:left">Items Ordered</h3>
                <ul class="order-items">
                    <?php foreach ($items as $item): ?>
                        <li class="order-item">
                            <img src="<?= h(asset_url($item['category'] ?? '', 'main', $item['main_image'] ?? '')) ?>"
                                 alt="<?= h($item['product_name']) ?>"
                                 class="order-item__img"
                                 onerror="this.src='src/assets/placeholder.webp'">
                            <div class="order-item__info">
                                <span class="order-item__name"><?= h($item['product_name'] ?? 'Deleted product') ?></span>
                                <span class="order-item__qty">Qty: <?= (int)$item['quantity'] ?></span>
                            </div>
                            <span class="order-item__price"><?= price((float)$item['price'] * (int)$item['quantity']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <a href="index.php" class="btn-back-shop" style="margin-top:24px; display:inline-block">
                <i class="fas fa-home"></i> Back to Store
            </a>
        </div>
    </div>
</main>

<?php include 'src/views/layouts/footer.php'; ?>
</body>
</html>
