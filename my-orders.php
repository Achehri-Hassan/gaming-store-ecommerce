<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'src/config/connection.php';
require_once 'src/helpers/helpers.php';
require_once 'src/models/OrderModel.php';

require_login('my-orders.php');

$userId = (int) $_SESSION['user_id'];

function valid_date_or_null(?string $value): ?string
{
    if ($value === null || $value === '') return null;
    $d = DateTime::createFromFormat('Y-m-d', $value);
    return ($d && $d->format('Y-m-d') === $value) ? $value : null;
}

$fromDate = valid_date_or_null($_GET['from'] ?? null);
$toDate   = valid_date_or_null($_GET['to'] ?? null);

if (isset($_GET['range'])) {
    $today = date('Y-m-d');
    switch ($_GET['range']) {
        case '7':
            $fromDate = date('Y-m-d', strtotime('-7 days'));
            $toDate   = $today;
            break;
        case '30':
            $fromDate = date('Y-m-d', strtotime('-30 days'));
            $toDate   = $today;
            break;
        case 'month':
            $fromDate = date('Y-m-01');
            $toDate   = $today;
            break;
        case 'all':
        default:
            $fromDate = null;
            $toDate = null;
    }
}

$rows = getProductsByOrderDateRange($userId, $fromDate, $toDate);

$orders = [];
foreach ($rows as $row) {
    $oid = (int) $row['order_id'];
    if (!isset($orders[$oid])) {
        $orders[$oid] = [
            'id'         => $oid,
            'created_at' => $row['created_at'],
            'status'     => $row['status'],
            'total'      => (float) $row['total_price'],
            'items'      => [],
        ];
    }
    $orders[$oid]['items'][] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quest Log — My Orders</title>
    <link rel="stylesheet" href="css/components/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/order.css">
</head>
<body>

<?php include 'src/views/layouts/header.php'; ?>

<div class="orders-container">
    <h1 class="page-title"><i class="fas fa-terminal"></i> Quest Log / My Orders</h1>

    <!-- Inline Modern Filter Bar -->
    <div class="quest-filters">
        <div class="preset-group">
            <a class="preset-btn <?= ($fromDate === null && $toDate === null) ? 'active' : '' ?>" href="my-orders.php?range=all">All</a>
            <a class="preset-btn" href="my-orders.php?range=7">7 Days</a>
            <a class="preset-btn" href="my-orders.php?range=30">30 Days</a>
            <a class="preset-btn" href="my-orders.php?range=month">This Month</a>
        </div>

        <form method="GET" action="my-orders.php" class="date-form">
            <div class="date-input-wrapper">
                <label>From</label>
                <input type="date" name="from" value="<?= h($fromDate ?? '') ?>">
            </div>
            <div class="date-input-wrapper">
                <label>To</label>
                <input type="date" name="to" value="<?= h($toDate ?? '') ?>">
            </div>
            <button type="submit" class="submit-filter-btn"><i class="fas fa-filter"></i> Filter</button>
        </form>
    </div>

    <?= render_flash() ?>

    <div class="quest-list">
        <?php if (empty($orders)): ?>
            <div style="text-align: center; padding: 60px; color: var(--text-muted);">
                <i class="fas fa-folder-open" style="font-size: 2.5rem; margin-bottom: 15px; display:block;"></i>
                <p>No transactions or quests registered for this timeline.</p>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="quest-row">
                    <!-- Left: Quest Basics -->
                    <div class="quest-info">
                        <span class="quest-id">ORDER #<?= (int) $order['id'] ?></span>
                        <span class="quest-date"><i class="far fa-clock"></i> <?= h(date('d M Y, H:i', strtotime($order['created_at']))) ?></span>
                    </div>

                    <!-- Center: Inventory Slotted Items (No Table) -->
                    <div class="quest-inventory">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="inventory-item" data-name="<?= h($item['product_name'] ?? 'Item') ?> (<?= price((float)$item['purchased_price']) ?>)">
                                <img src="<?= asset_url($item['category'] ?? '', 'main', $item['main_image'] ?? '') ?>" 
                                     alt="Product image"
                                     onerror="this.src='src/assets/placeholder.webp'">
                                <?php if ((int)$item['quantity'] > 1): ?>
                                    <span class="item-qty">x<?= (int)$item['quantity'] ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Right: Reward / Meta -->
                    <div class="quest-meta">
                        <span class="badge-glitch badge-<?= h($order['status']) ?>">
                            <?= h($order['status']) ?>
                        </span>
                        <span class="quest-price"><?= price($order['total']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include 'src/views/layouts/footer.php'; ?>

</body>
</html>