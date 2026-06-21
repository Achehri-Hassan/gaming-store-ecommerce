<?php


require_once 'src/config/connection.php';
require_once 'src/models/ProductModel.php';
require_once 'src/helpers/helpers.php';

// cart-handler.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


header('Content-Type: application/json');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get JSON Input
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$product_id = isset($input['product_id']) ? (int)$input['product_id'] : 0;

switch ($action) {
    case 'add':
        if ($product_id > 0) {
            $product = selectById($product_id);
            if ($product) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += 1;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => (float)$product['price'],
                        'currency' => $product['currency'],
                        'image' => asset_url($product['category'], 'main', $product['main_image']),
                        'quantity' => 1
                    ];
                }
            }
        }
        break;

    // 🟢 زِيد هاد الـ Case الجديدة هنا:
    case 'update_quantity':
        $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            } else {
                // يلا رcheap الكمية لـ 0، كلوكا كتحيد المنتج من السلة تلقائياً
                unset($_SESSION['cart'][$product_id]);
            }
        }
        break;

    case 'remove':
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
        }
        break;

    case 'get':
    default:
        break;
}

// Calculate totals
$total_items = 0;
$total_price = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_items += $item['quantity'];
    $total_price += $item['price'] * $item['quantity'];
}

echo json_encode([
    'success' => true,
    'cart' => array_values($_SESSION['cart']),
    'total_items' => $total_items,
    'total_price' => number_format($total_price, 0) . ' DH'
]);