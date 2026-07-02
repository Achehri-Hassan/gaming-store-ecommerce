<?php
// checkout.php — Customer fills in shipping info, then we save the order.

if (session_status() === PHP_SESSION_NONE) session_start();

require_once 'src/config/connection.php';
require_once 'src/helpers/helpers.php';
require_once 'src/models/OrderModel.php';
require_once 'src/models/ProductModel.php';

// Must be logged in
if (empty($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = 'checkout.php';
    header('Location: login.php');
    exit;
}

// Cart must not be empty


$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $name    = clean($_POST['customer_name'] ?? '');
    $phone   = clean($_POST['phone'] ?? '');
    $address = clean($_POST['address'] ?? '');
    $city    = clean($_POST['city'] ?? '');

    // ── Validation ────────────────────────────────────────────────────────────
    if (strlen($name) < 2)          $errors[] = 'Full name must be at least 2 characters.';
    if (!valid_phone($phone))        $errors[] = 'Please enter a valid phone number.';
    if (strlen($address) < 5)       $errors[] = 'Please enter a valid address.';
    if (strlen($city) < 2)          $errors[] = 'Please enter a valid city.';

    if (empty($errors)) {
        // ── Re-verify prices from DB (never trust session prices for totals) ──
        $total = 0;
        $items = [];

        foreach ($_SESSION['cart'] as $item) {

            $product = selectById((int) $item['id']);
            if (!$product) continue;
            $qty    = max(1, (int) $item['quantity']);
            $price  = (float) $product['price'];
            $total += $price * $qty;
            $items[] = ['id' => $product['id'], 'price' => $price, 'quantity' => $qty];
        }

        if (empty($items)) {
            // تشييك واش الطلب جاي بـ AJAX
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'errors' => ['Cart is empty or products are unavailable.']]);
                exit;
            }
            flash('error', 'Cart is empty or products are unavailable.');
            header('Location: checkout.php');
            exit;
        }

        $conn = getConnection();
        $conn->beginTransaction();
        try {
            $orderId = createOrder(
                (int) $_SESSION['user_id'],
                $total,
                $name,
                $phone,
                $address,
                $city
            );
            createOrderItems($orderId, $items);
            $conn->commit();

            // ── Clear cart ───────────────────────────────────────
            $_SESSION['cart'] = [];

            // إيلا كان الطلب داز بـ AJAX، كنرجعو JSON للمودال بلا ما يوقع ريفريش
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode([
                    'success' => true,
                    'order_id' => $orderId,
                    'customer_name' => $name,
                    'phone' => $phone,
                    'city' => $city,
                    'address' => $address,
                    'total_price' => price($total)
                ]);
                exit;
            }

            flash('success', "Order #$orderId placed successfully! We will contact you shortly.");
            header('Location: order-success.php?id=' . $orderId);
            exit;
        } catch (Exception $e) {
            $conn->rollBack();

            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                echo json_encode(['success' => false, 'errors' => ['Something went wrong placing your order. Please try again.']]);
                exit;
            }
            $errors[] = 'Something went wrong placing your order. Please try again.';
        }
    } else {
        // إيلا كانو أخطاء ديال الـ Validation فـ طلب الـ AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['success' => false, 'errors' => $errors]);
            exit;
        }
    }
}

// ── Build cart summary ────────────────────────────────────────────────────────
$cartItems  = [];
$cartTotal  = 0;
foreach ($_SESSION['cart'] as $item) {
    $cartItems[] = $item;
    $cartTotal  += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout — Gaming Store</title>
    <link rel="stylesheet" href="css/components/style.css">
    <link rel="stylesheet" href="css/form.css">
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <?php include 'src/views/layouts/header.php'; ?>

    <?php include "src/views/layouts/cart.php" ?>

    <main class="checkout-page">
        <div class="checkout-container">

            <div class="checkout-header">
                <h1><i class="fas fa-shopping-bag"></i> Checkout</h1>
                <p>Complete your order by filling in your delivery information below.</p>
            </div>

            <?= render_flash() ?>


            <div id="ajaxErrorContainer" class="alert alert--error" style="display: none;">
                <i class="fas fa-exclamation-circle"></i>
                <ul id="ajaxErrorList"></ul>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert--error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        <?php foreach ($errors as $err): ?>
                            <li><?= h($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="checkout-grid">


                <section class="checkout-form-section">
                    <h2 class="section-heading"><i class="fas fa-map-marker-alt"></i> Delivery Information</h2>

                    <form method="POST" action="checkout.php" class="checkout-form" id="checkoutForm" novalidate>
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="customer_name">Full Name <span class="required">*</span></label>
                            <input type="text" id="customer_name" name="customer_name"
                                placeholder="name"
                                value="<?= h($_SESSION['username'] ?? '') ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                placeholder="e.g. +212 6XX XXX XXX"
                                value="<?= h($_POST['phone'] ?? '') ?>"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="city">City <span class="required">*</span></label>
                            <input
                                type="text"
                                id="city"
                                name="city"
                                placeholder="e.g. Casablanca"
                                value="<?= h($_POST['city'] ?? '') ?>"
                                required>
                        </div>

                        <div class="form-group form-group--full">
                            <label for="address">Address <span class="required">*</span></label>
                            <input
                                type="text"
                                id="address"
                                name="address"
                                placeholder="Street, neighbourhood, building…"
                                value="<?= h($_POST['address'] ?? '') ?>"
                                required>
                        </div>

                        <div class="checkout-submit">
                            <button type="submit" class="btn-place-order" id="placeOrderBtn">
                                <i class="fas fa-lock"></i> Place Order
                            </button>
                            <a href="index.php" class="btn-back-shop">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                        </div>
                    </form>
                </section>


                <aside class="order-summary">

                    <?php if (empty($cartItems)): ?>
                        <div class="empty-state">
                            <i class="fas fa-shopping-cart"></i>
                            <p>Your cart is empty.</p>
                        </div>
                    <?php else: ?>
                        <ul class="order-items">
                            <?php foreach ($cartItems as $item): ?>
                                <li class="order-item">
                                    <img
                                        src="<?= h($item['image'] ?? 'src/assets/placeholder.webp') ?>"
                                        alt="<?= h($item['name']) ?>"
                                        class="order-item__img"
                                        onerror="this.src='src/assets/placeholder.webp'">
                                    <div class="order-item__info">
                                        <span class="order-item__name"><?= h($item['name']) ?></span>
                                        <span class="order-item__qty">Qty: <?= (int) $item['quantity'] ?></span>
                                    </div>
                                    <span class="order-item__price">
                                        <?= price($item['price'] * $item['quantity']) ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="order-totals">
                            <div class="totals-row">
                                <span>Subtotal</span>
                                <span><?= price($cartTotal) ?></span>
                            </div>
                            <div class="totals-row">
                                <span>Delivery</span>
                                <span class="free-tag">Free</span>
                            </div>
                            <div class="totals-row totals-row--total">
                                <span>Total</span>
                                <span class="price_span"><?= price($cartTotal) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>

                </aside>

            </div>
        </div>
    </main>

    <?php include 'src/views/layouts/footer.php'; ?>


    <div id="successModal" class="order-success-modal">
        <div class="success-modal-content animate-pop">

            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Order Confirmed!</h1>
            <p>Thank you, <strong id="modalCustomerName"></strong>. Your order has been placed successfully.</p>
            <p class="order-ref">Order #<strong id="modalOrderId"></strong></p>

            <div class="modal-progress-bar"></div>
        </div>
    </div>

   
    <script src="js/order.js"></script>
    <script src="js/header.js"></script>
    <script src="js/main.js"></script>

</body>

</html>