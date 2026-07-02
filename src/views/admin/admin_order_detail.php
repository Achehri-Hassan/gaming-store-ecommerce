<?php
// Partial — loaded via AJAX from admin_orders.php

require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../models/OrderModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo '<p style="color:red; text-align:center;">Access denied.</p>';
    exit;
}

// تعديل باش نستقبلو الـ user_id لي جاي من الـ Fetch ديريكت
$userId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;

if ($userId <= 0) {
    echo '<p style="color:red; text-align:center; padding:10px;">Invalid Customer ID.</p>';
    exit;
}

// جلب جميع الطلبيات والمنتجات لي شراها هاد الكليان بـ Query وحدة نقية
$conn = getConnection();
$stmt = $conn->prepare(" SELECT 
        oi.quantity,
        oi.price AS purchased_price,
        p.name AS product_name,
        p.main_image,
        p.category,
        o.id AS order_id,
        o.status,
        o.created_at,
        o.customer_name,
        o.phone,
        o.city,
        o.address
    FROM order_items oi
    INNER JOIN orders o ON o.id = oi.order_id
    LEFT JOIN products p ON p.id = oi.product_id
    WHERE o.user_id = :user_id
    ORDER BY o.created_at DESC
");
$stmt->execute([':user_id' => $userId]);
$purchases = $stmt->fetchAll();

if (empty($purchases)) {
    echo '<p style="text-align:center; padding:20px; color:#666;">This customer has no purchase history.</p>';
    exit;
}

// أخذ معلومات الزبون من أحدث طلبية
$clientInfo = $purchases[0];
?>

<div class="order-detail">
    <div class="order-detail-meta" style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #007bff; font-size: 0.95rem; line-height: 1.6;">
        <div><strong>Customer:</strong> <?= h($clientInfo['customer_name']) ?></div>
        <div><strong>Phone:</strong> <?= h($clientInfo['phone']) ?></div>
        <div><strong>City:</strong> <?= h($clientInfo['city']) ?></div>
        <div><strong>Address:</strong> <?= h($clientInfo['address']) ?></div>
    </div>

    <h4 style="margin:16px 0 10px; color:#333;"><i class="fas fa-box-open"></i> Purchased Products History</h4>
    
    <table class="detail-items-table" style="width:100%; border-collapse: collapse; font-size: 0.9rem;">
        <thead>
            <tr style="background:#f1f1f1; text-align:left;">
                <th style="padding:10px;">Product</th>
                <th style="padding:10px;">Order ID</th>
                <th style="padding:10px;">Qty</th>
                <th style="padding:10px;">Unit Price</th>
                <th style="padding:10px;">Total</th>
                <th style="padding:10px;">Status</th>
                <th style="padding:10px;">Date</th>
            </tr>
        </thead>
        <tbody>
            
            <?php 
            $grandTotal = 0;
            foreach ($purchases as $item): 
                $subtotal = (float)$item['purchased_price'] * (int)$item['quantity'];
                $grandTotal += $subtotal;
                
                
            ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding:10px;">
                        <div style="display:flex; align-items:center; gap:10px;">
                           
                            <span style="font-weight: 500;"><?= h($item['product_name'] ?? 'Deleted product') ?></span>
                        </div>
                    </td>
                    <td style="padding:10px;"><span class="badge" style="background:#e9ecef; padding:3px 6px; border-radius:4px; color:#495057;">#<?= (int)$item['order_id'] ?></span></td>
                    <td style="padding:10px;"><strong><?= (int)$item['quantity'] ?></strong></td>
                    <td style="padding:10px;"><?= price((float)$item['purchased_price']) ?></td>
                    <td style="padding:10px;"><strong><?= price($subtotal) ?></strong></td>
                    <td style="padding:10px;">
                        <span class="status-badge status-badge--<?= h($item['status']) ?>" style="padding:2px 6px; font-size:0.8rem;">
                            <?= h(ucfirst($item['status'])) ?>
                        </span>
                    </td>
                    <td style="padding:10px; font-size:0.85rem; color:#666;"><?= h(date('d M Y', strtotime($item['created_at']))) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr style="background:#f8f9fa; font-weight:bold;">
                <td colspan="4" style="padding:10px; text-align:right;">Total Spent:</td>
                <td colspan="3" style="padding:10px; color:#28a745; font-size: 1.05rem;"><strong><?= price($grandTotal) ?></strong></td>
            </tr>
        </tfoot>
    </table>
</div>