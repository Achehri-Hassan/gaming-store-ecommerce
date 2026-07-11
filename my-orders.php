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
    <style>
        :root {
            --bg-dark: #0b0e14;
            --card-bg: #131722;
            --border-color: #202738;
            --text-muted: #8f9cae;
            --accent-blue: #3b82f6;
            --neon-glow: 0 0 12px rgba(59, 132, 246, 0.2);
        }

        body { background-color: var(--bg-dark); color: #f3f4f6; }
        .orders-container { max-width: 1100px; margin: 40px auto; padding: 0 20px; font-family: 'Inter', sans-serif; }
        
        .page-title { font-size: 1.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #fff; margin-bottom: 30px; display: flex; align-items: center; gap: 12px; }
        .page-title i { color: var(--accent-blue); text-shadow: var(--neon-glow); }

        /* ─── Ultra Clean Filter Controls ─── */
        .quest-filters { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px; margin-bottom: 35px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); }
        .preset-group { display: flex; gap: 6px; background: #0b0e14; padding: 4px; border-radius: 8px; border: 1px solid var(--border-color); }
        .preset-btn { padding: 8px 16px; border-radius: 6px; color: var(--text-muted); text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: all 0.2s ease; }
        .preset-btn:hover { color: #fff; }
        .preset-btn.active { background: var(--accent-blue); color: #fff; box-shadow: var(--neon-glow); }

        .date-form { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
        .date-input-wrapper { display: flex; align-items: center; background: #0b0e14; border: 1px solid var(--border-color); border-radius: 8px; padding: 2px 12px; }
        .date-input-wrapper label { font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); font-weight: 700; margin-right: 8px; }
        .date-form input[type="date"] { background: transparent; border: none; color: #fff; padding: 8px 4px; font-size: 0.85rem; outline: none; font-weight: 600; }
        .date-form input[type="date"]::-webkit-calendar-picker-indicator { filter: invert(1); cursor: pointer; }
        
        .submit-filter-btn { background: #1e293b; border: 1px solid var(--border-color); color: #fff; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 0.85rem; font-weight: 600; transition: all 0.2s; display: flex; align-items: center; gap: 8px; }
        .submit-filter-btn:hover { background: var(--accent-blue); border-color: var(--accent-blue); box-shadow: var(--neon-glow); }

        /* ─── Modern Quest Log List (No Tables) ─── */
        .quest-list { display: flex; flex-direction: column; gap: 20px; }
        .quest-row { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 14px; padding: 24px; display: grid; grid-template-columns: 1.5fr 2.5fr 1fr; align-items: center; gap: 20px; transition: transform 0.2s, border-color 0.2s; }
        .quest-row:hover { transform: translateY(-2px); border-color: #334155; }

        /* Left Section: Info */
        .quest-info { display: flex; flex-direction: column; gap: 6px; }
        .quest-id { font-size: 1.1rem; font-weight: 800; color: #fff; letter-spacing: 0.5px; }
        .quest-date { color: var(--text-muted); font-size: 0.8rem; display: flex; align-items: center; gap: 6px; }

        /* Center Section: Inventory Style Items */
        .quest-inventory { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .inventory-item { position: relative; width: 56px; height: 56px; background: #0b0e14; border: 1px solid var(--border-color); border-radius: 10px; padding: 4px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.15s; }
        .inventory-item:hover { border-color: var(--accent-blue); box-shadow: var(--neon-glow); }
        .inventory-item img { width: 100%; height: 100%; object-fit: cover; border-radius: 6px; }
        .item-qty { position: absolute; bottom: -5px; right: -5px; background: #ef4444; color: #fff; font-size: 0.7rem; font-weight: 800; padding: 2px 6px; border-radius: 6px; border: 2px solid var(--card-bg); }
        
        /* Tooltip simple description on hover */
        .inventory-item:hover::after { content: attr(data-name); position: absolute; bottom: 65px; left: 50%; transform: translateX(-50%); background: #000; color: #fff; font-size: 0.75rem; padding: 6px 10px; border-radius: 6px; white-space: nowrap; border: 1px solid var(--border-color); z-index: 10; box-shadow: 0 4px 15px rgba(0,0,0,0.5); }

        /* Right Section: Badges & Pricing */
        .quest-meta { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
        .quest-price { font-size: 1.2rem; font-weight: 800; color: #fff; }
        
        /* Badges Minimalist */
        .badge-glitch { font-size: 0.75rem; font-weight: 700; padding: 6px 14px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge-pending { background: rgba(234, 179, 8, 0.1); color: #facc15; border: 1px solid rgba(234, 179, 8, 0.2); }
        .badge-processing { background: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
        .badge-shipped { background: rgba(168, 85, 247, 0.1); color: #c084fc; border: 1px solid rgba(168, 85, 247, 0.2); }
        .badge-delivered { background: rgba(34, 197, 94, 0.1); color: #4ade80; border: 1px solid rgba(34, 197, 94, 0.2); }
        .badge-cancelled { background: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }

        /* Responsive */
        @media (max-width: 850px) {
            .quest-row { grid-template-columns: 1fr; gap: 16px; text-align: center; }
            .quest-inventory { justify-content: center; }
            .quest-meta { align-items: center; }
        }
    </style>
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