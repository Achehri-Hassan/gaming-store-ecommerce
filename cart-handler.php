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
$input      = json_decode(file_get_contents('php://input'), true) ?? [];
$action     = $input['action'] ?? '';
$product_id = isset($input['product_id']) ? (int) $input['product_id'] : 0;

// Reasonable ceiling on quantity per line item — prevents a client from
// setting an absurd quantity that has no real stock backing it. Checkout
// still re-validates the real stock from the DB before an order is created.
const MAX_CART_QUANTITY = 99;

// The 'get' action is read-only and doesn't need CSRF; every state-changing
// action does. The token travels inside the JSON body (see js/main.js's
// fetchCart()), so it is validated here rather than via verify_csrf(),
// which only reads $_POST (empty for a raw JSON request body).
$stateChangingActions = ['add', 'update_quantity', 'remove'];

if (in_array($action, $stateChangingActions, true)) {
    if (!is_valid_csrf_token($input['csrf_token'] ?? null)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
        exit;
    }
}

switch ($action) {
    case 'add':
        if ($product_id > 0) {
            // Only an ACTIVE product can be added to the cart — an inactive
            // or soft-deleted product should never be purchasable, even if
            // its ID is guessed or an old link is reused.
            $product = selectActiveById($product_id);
            if ($product) {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] = min(
                        MAX_CART_QUANTITY,
                        $_SESSION['cart'][$product_id]['quantity'] + 1
                    );
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


    case 'update_quantity':
        $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;
        if ($product_id > 0 && isset($_SESSION['cart'][$product_id])) {
            if ($quantity > 0) {
                $_SESSION['cart'][$product_id]['quantity'] = min($quantity, MAX_CART_QUANTITY);
            } else {
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
