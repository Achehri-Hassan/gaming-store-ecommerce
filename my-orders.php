<?php
if (session_status() === PHP_SESSION_NONE) session_start(); //
require_once 'src/config/connection.php'; //
require_once 'src/helpers/helpers.php'; //[cite: 2]
require_once 'src/models/OrderModel.php'; //[cite: 2]

require_login('my-orders.php'); //[cite: 2]

$userId = (int) $_SESSION['user_id']; //[cite: 2]

// إذا اختار العميل تاريخ معين، كنفلترو بيه، وإلا كنخدو تاريخ اليوم كمثال أو كاع التواريخ
$selectedDate = isset($_GET['date']) ? clean($_GET['date']) : date('Y-m-d'); //[cite: 3, 4]

// جلب جميع المنتجات المشتراة في هذا التاريخ في جدول واحد
$productsPurchased = getProductsByOrderDate($userId, $selectedDate);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products by Date — Gaming Store</title>
    <link rel="stylesheet" href="css/components/style.css"> <!--[cite: 2] -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> <!--[cite: 2] -->
    <style>
        .orders-page { padding: 40px 16px; max-width: 1000px; margin: 0 auto; }
        .orders-page h1 { color: #e5e7eb; margin-bottom: 24px; }
        
        .date-filter-form { margin-bottom: 30px; background: #1a1a1a; padding: 20px; border-radius: 8px; border: 1px solid #2a2a2a; display: flex; gap: 15px; align-items: flex-end; }
        .date-filter-form .form-group { margin-bottom: 0; flex: 1; }
        .date-filter-form input[type="date"] { width: 100%; padding: 10px; background: #2a2a2a; border: 1px solid #3a3a3a; color: #fff; border-radius: 6px; }
        .btn-filter { background: #3b82f6; color: #fff; border: none; padding: 11px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-filter:hover { background: #2563eb; }

        /* Style ديال الـ Table الموحد */
        .products-table-container { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 10px; overflow: hidden; margin-top: 20px; }
        .products-table { width: 100%; border-collapse: collapse; text-align: left; color: #e5e7eb; }
        .products-table th, .products-table td { padding: 16px; border-bottom: 1px solid #2a2a2a; }
        .products-table th { background: #232732; color: #9ca3af; font-weight: 600; }
        .product-flex { display: flex; align-items: center; gap: 12px; }
        .product-img { width: 50px; height: 50px; object-fit: cover; border-radius: 6px; background: #2a2a2a; }
        .empty-state { text-align: center; padding: 60px 20px; color: #4b5563; }
        .empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; }
    </style>
</head>
<body>
<?php include 'src/views/layouts/header.php'; ?> <!--[cite: 2] -->

<main class="orders-page">
    <h1><i class="fas fa-calendar-day"></i> Products Purchased on Date</h1>

    <!-- فورمة خفيفة باش يختار الـ Date لي بغا -->
    <form method="GET" action="my-orders.php" class="date-filter-form">
        <div class="form-group">
            <label style="color: #9ca3af; display:block; margin-bottom:8px;">Select Purchase Date:</label>
            <input type="date" name="date" value="<?= h($selectedDate) ?>"> <!--[cite: 4] -->
        </div>
        <button type="submit" class="btn-filter"><i class="fas fa-search"></i> Filter</button>
    </form>

    <?= render_flash() ?> <!--[cite: 2] -->

    <?php if (empty($productsPurchased)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <p>No products found for <?= h(date('d M Y', strtotime($selectedDate))) ?>.</p> <!--[cite: 2, 4] -->
        </div>
    <?php else: ?>
        <!-- جدول واحد فيه كاع المنتجات ديال داك النهار -->
        <div class="products-table-container">
            <table class="products-table">
                <thead>
                    <tr>
                        <th>Product Details</th>
                        <th>Order Ref</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productsPurchased as $item): ?>
                        <tr>
                            <td>
                                <div class="product-flex">
                                    <img src="<?= asset_url($item['category'] ?? '', 'main', $item['main_image'] ?? '') ?>" 
                                         alt="<?= h($item['product_name']) ?>" 
                                         class="product-img"
                                         onerror="this.src='src/assets/placeholder.webp'"> <!--[cite: 2, 4] -->
                                    <span><?= h($item['product_name'] ?? 'Deleted Product') ?></span> <!--[cite: 2, 4] -->
                                </div>
                            </td>
                            <td><span style="color:#9ca3af;">#<?= (int)$item['order_id'] ?></span></td> <!--[cite: 2] -->
                            <td><strong><?= (int)$item['quantity'] ?></strong></td> <!--[cite: 2] -->
                            <td style="color:#3b82f6; font-weight:700;"><?= price((float)$item['purchased_price']) ?></td> <!--[cite: 2, 4] -->
                            <td>
                                <span class="status-badge status-badge--<?= h($item['status']) ?>">
                                    <?= h(ucfirst($item['status'])) ?> <!--[cite: 2, 4] -->
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include 'src/views/layouts/footer.php'; ?> <!--[cite: 2] -->
</body>
</html>