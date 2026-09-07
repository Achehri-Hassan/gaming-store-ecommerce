<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../models/OrderModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_admin();

// The set of statuses an order can be in — must match the ENUM in the
// `orders` table (see database/orders_migration.sql) and the whitelist
// enforced server-side by updateOrderStatus().
const ORDER_STATUSES = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

// ── Actions ───────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (isset($_POST['delete_order'])) {
        $id = (int) $_POST['order_id'];
        deleteOrder($id)
            ? flash('success', "Order #$id deleted.")
            : flash('error', 'Failed to delete order.');
    }

    if (isset($_POST['update_order_status'])) {
        $id     = (int) ($_POST['order_id'] ?? 0);
        $status = (string) ($_POST['status'] ?? '');

        if ($id > 0 && in_array($status, ORDER_STATUSES, true)) {
            updateOrderStatus($id, $status)
                ? flash('success', "Order #$id status updated to \"$status\".")
                : flash('error', "Failed to update order #$id.");
        } else {
            flash('error', 'Invalid order or status value.');
        }
    }

    header('Location: admin_orders.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// ── Filters & Pagination (customer summary) ─────────────────────────────────────
$search      = clean($_GET['search'] ?? '');
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$perPage     = 15;

$total   = countUniqueCustomers($search);
$pager   = paginate($total, $perPage, $currentPage);
$customers = getUniqueCustomers($search, $perPage, $pager['offset']);
$stats   = getOrderStats();

// ── Filters & Pagination (individual orders — status management) ───────────────
$ordersSearch      = clean($_GET['orders_search'] ?? '');
$ordersStatus      = $_GET['orders_status'] ?? '';
$ordersStatus      = in_array($ordersStatus, ORDER_STATUSES, true) ? $ordersStatus : '';
$ordersCurrentPage = max(1, (int) ($_GET['orders_page'] ?? 1));
$ordersPerPage     = 10;

$ordersTotal = countAllOrders($ordersSearch, $ordersStatus);
$ordersPager = paginate($ordersTotal, $ordersPerPage, $ordersCurrentPage);
$orders      = getAllOrders($ordersSearch, $ordersStatus, $ordersPerPage, $ordersPager['offset']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers Orders — Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="stylesheet" href="/css/dashboard.css">
    <link rel="stylesheet" href="/css/admin_orders.css">
</head>
<body>

<aside class="sidebar">
    <div class="logo"><i class="fas fa-gamepad"></i> Tech<span>Shop</span></div>
    <ul class="sidebar-menu" style="height:100%">
        <li><a href="admin_dashboard.php"><i class="fas fa-chart-pie"></i> Overview</a></li>
        <li><a href="admin_products.php"><i class="fas fa-box"></i> Products</a></li>
        <li><a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
        <li><a href="/index.php"><i class="fas fa-eye"></i> View Website</a></li>
        <li style="margin-top:auto"><a href="/logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>

<main class="main-content">
    <div class="header-dash">
        <h1><i class="fas fa-shopping-cart"></i> Customers Management</h1>
        <div class="admin-profile">
            <i class="fas fa-user-shield"></i>
            <span>Admin: <strong><?= h($_SESSION['username']) ?></strong></span>
        </div>
    </div>

    <?= render_flash() ?>

    <div class="cards-grid" style="margin-bottom:24px">
        <div class="card">
            <div class="card-info"><h3>Total Orders</h3><p><?= number_format((int)$stats['total_orders']) ?></p></div>
            <div class="card-icon"><i class="fas fa-shopping-bag"></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3>Total Revenue</h3><p><?= price((float)$stats['total_revenue']) ?></p></div>
            <div class="card-icon"><i class="fas fa-coins"></i></div>
        </div>
    </div>

    <form method="GET" action="admin_orders.php" class="filter-bar">
        <input
            type="text"
            name="search"
            placeholder="Search by customer name, phone or city…"
            value="<?= h($search) ?>"
            class="filter-input"
            style="width: 100%; max-width: 400px;"
        >
        <button type="submit" class="btn btn--primary"><i class="fas fa-search"></i> Search</button>
        <?php if ($search): ?>
            <a href="admin_orders.php" class="btn btn--secondary"><i class="fas fa-times"></i> Clear</a>
        <?php endif; ?>
    </form>

    <?php if (empty($customers)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No customers found.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Total Spent</th>
                        <th>Last Activity</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $client): ?>
                        <tr>
                            <td><strong><?= h($client['customer_name']) ?></strong></td>
                            <td><?= h($client['phone']) ?></td>
                            <td><?= h($client['city']) ?></td>
                            <td><strong><?= price((float)$client['total_price']) ?></strong></td>
                            <td><?= h(date('d M Y', strtotime($client['created_at']))) ?></td>
                            <td class="actions-cell">
                                <button
                                    class="btn btn--icon btn--view"
                                    onclick="openCustomerPurchasesModal(<?= (int)$client['user_id'] ?>, '<?= h($client['customer_name']) ?>')"
                                    title="View Customer Purchases"
                                ><i class="fas fa-eye"></i></button>

                                <form method="POST" action="admin_orders.php" style="display:inline"
                                      onsubmit="return confirm('Delete records for this client?')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="order_id" value="<?= (int)$client['id'] ?>">
                                    <input type="hidden" name="delete_order" value="1">
                                    <button type="submit" class="btn btn--icon btn--danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($pager['total_pages'] > 1): ?>
            <div class="pagination">
                <?php
                $qs = http_build_query(array_filter(['search' => $search]));
                $base = 'admin_orders.php?' . ($qs ? $qs . '&' : '');
                ?>
                <?php if ($pager['has_prev']): ?>
                    <a href="<?= $base ?>page=<?= $pager['current'] - 1 ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $pager['total_pages']; $p++): ?>
                    <a href="<?= $base ?>page=<?= $p ?>"
                       class="page-btn <?= $p === $pager['current'] ? 'active' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($pager['has_next']): ?>
                    <a href="<?= $base ?>page=<?= $pager['current'] + 1 ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

    <!-- ═══════ Orders — status management & filtering ═══════ -->
    <div class="header-dash" style="margin-top:36px">
        <h1><i class="fas fa-truck"></i> Order Status</h1>
    </div>

    <form method="GET" action="admin_orders.php" class="filter-bar">
        <input
            type="text"
            name="orders_search"
            placeholder="Search by order #, customer, phone or city…"
            value="<?= h($ordersSearch) ?>"
            class="filter-input"
            style="width: 100%; max-width: 320px;"
        >
        <select name="orders_status" class="filter-input" style="max-width:200px">
            <option value="">All statuses</option>
            <?php foreach (ORDER_STATUSES as $st): ?>
                <option value="<?= h($st) ?>" <?= $ordersStatus === $st ? 'selected' : '' ?>>
                    <?= h(ucfirst($st)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn--primary"><i class="fas fa-filter"></i> Filter</button>
        <?php if ($ordersSearch || $ordersStatus): ?>
            <a href="admin_orders.php" class="btn btn--secondary"><i class="fas fa-times"></i> Clear</a>
        <?php endif; ?>
    </form>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No orders match this filter.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>Total</th>
                        <th>Placed</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?= (int) $order['id'] ?></td>
                            <td><?= h($order['customer_name']) ?></td>
                            <td><?= h($order['phone']) ?></td>
                            <td><?= price((float) $order['total_price']) ?></td>
                            <td><?= h(date('d M Y', strtotime($order['created_at']))) ?></td>
                            <td>
                                <span class="status-badge status-badge--<?= h($order['status']) ?>">
                                    <?= h(ucfirst($order['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" action="admin_orders.php" style="display:flex; gap:6px; align-items:center;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <input type="hidden" name="update_order_status" value="1">
                                    <select name="status" class="filter-input" style="padding:4px 6px;">
                                        <?php foreach (ORDER_STATUSES as $st): ?>
                                            <option value="<?= h($st) ?>" <?= $order['status'] === $st ? 'selected' : '' ?>>
                                                <?= h(ucfirst($st)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn--icon btn--primary" title="Update status">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($ordersPager['total_pages'] > 1): ?>
            <div class="pagination">
                <?php
                $ordersQs   = http_build_query(array_filter(['orders_search' => $ordersSearch, 'orders_status' => $ordersStatus]));
                $ordersBase = 'admin_orders.php?' . ($ordersQs ? $ordersQs . '&' : '');
                ?>
                <?php if ($ordersPager['has_prev']): ?>
                    <a href="<?= $ordersBase ?>orders_page=<?= $ordersPager['current'] - 1 ?>" class="page-btn"><i class="fas fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php for ($p = 1; $p <= $ordersPager['total_pages']; $p++): ?>
                    <a href="<?= $ordersBase ?>orders_page=<?= $p ?>"
                       class="page-btn <?= $p === $ordersPager['current'] ? 'active' : '' ?>"><?= $p ?></a>
                <?php endfor; ?>
                <?php if ($ordersPager['has_next']): ?>
                    <a href="<?= $ordersBase ?>orders_page=<?= $ordersPager['current'] + 1 ?>" class="page-btn"><i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>

<div class="modal-overlay" id="orderModalOverlay" onclick="closeOrderModal()"></div>
<div class="modal" id="orderModal">
    <div class="modal-header">
        <h3 id="modalTitle">Customer Purchases</h3>
        <button class="modal-close" onclick="closeOrderModal()">&times;</button>
    </div>
    <div class="modal-body" id="modalBody">
        <p>Loading…</p>
    </div>
</div>

<script>
function openCustomerPurchasesModal(userId, name) {
    document.getElementById('orderModalOverlay').classList.add('active');
    document.getElementById('orderModal').classList.add('active');
    document.getElementById('modalTitle').textContent = 'Purchases by ' + name;
    document.getElementById('modalBody').innerHTML = '<p style="text-align:center"><i class="fas fa-spinner fa-spin"></i> Fetching client history…</p>';

   
    fetch('admin_order_detail.php?user_id=' + userId)
        .then(r => r.text())
        .then(html => { document.getElementById('modalBody').innerHTML = html; })
        .catch(() => { document.getElementById('modalBody').innerHTML = '<p>Failed to load customer details.</p>'; });
}

function closeOrderModal() {
    document.getElementById('orderModalOverlay').classList.remove('active');
    document.getElementById('orderModal').classList.remove('active');
}
</script>

</body>
</html>