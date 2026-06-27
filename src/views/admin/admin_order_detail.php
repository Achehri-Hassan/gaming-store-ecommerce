<?php
// Partial — loaded via AJAX from admin_orders.php

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../models/OrderModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo '<p>Access denied.</p>';
    exit;
}

$id    = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$order = $id ? getOrderById($id) : null;

if (!$order) {
    echo '<p>Order not found.</p>';
    exit;
}

$items = getOrderItems($id);
?>
<div class="order-detail">
    <div class="order-detail-meta">
        <div><strong>Customer:</strong> <?= h($order['customer_name']) ?></div>
        <div><strong>Username:</strong> <?= h($order['username'] ?? '—') ?></div>
        <div><strong>Email:</strong> <?= h($order['email'] ?? '—') ?></div>
        <div><strong>Phone:</strong> <?= h($order['phone']) ?></div>
        <div><strong>City:</strong> <?= h($order['city']) ?></div>
        <div><strong>Address:</strong> <?= h($order['address']) ?></div>
        <div><strong>Status:</strong> <span class="status-badge status-badge--<?= h($order['status']) ?>"><?= h(ucfirst($order['status'])) ?></span></div>
        <div><strong>Date:</strong> <?= h($order['created_at']) ?></div>
    </div>

    <h4 style="margin:16px 0 8px">Items</h4>
    <?php if (empty($items)): ?>
        <p>No items found.</p>
    <?php else: ?>
        <table class="detail-items-table">
            <thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr></thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= h($item['product_name'] ?? 'Deleted product') ?></td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td><?= price((float)$item['price']) ?></td>
                        <td><?= price((float)$item['price'] * (int)$item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td><strong><?= price((float)$order['total_price']) ?></strong></td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</div>
