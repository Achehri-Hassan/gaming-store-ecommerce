<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../models/OrderModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_admin();

// ── Actions ───────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // Update status
    if (isset($_POST['update_status'])) {
        $id     = (int) $_POST['order_id'];
        $status = clean($_POST['status'] ?? '');
        updateOrderStatus($id, $status)
            ? flash('success', "Order #$id status updated to " . ucfirst($status) . '.')
            : flash('error', 'Failed to update status.');
    }

    // Delete order
    if (isset($_POST['delete_order'])) {
        $id = (int) $_POST['order_id'];
        deleteOrder($id)
            ? flash('success', "Order #$id deleted.")
            : flash('error', 'Failed to delete order.');
    }

    header('Location: admin_orders.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
    exit;
}

// ── Filters & Pagination ──────────────────────────────────────────────────────
$search      = clean($_GET['search'] ?? '');
$statusFilter = clean($_GET['status'] ?? '');
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$perPage     = 15;

$total   = countAllOrders($search, $statusFilter);
$pager   = paginate($total, $perPage, $currentPage);
$orders  = getAllOrders($search, $statusFilter, $perPage, $pager['offset']);
$stats   = getOrderStats();

$statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders — Admin Panel</title>
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
        <h1><i class="fas fa-shopping-cart"></i> Orders Management</h1>
        <div class="admin-profile">
            <i class="fas fa-user-shield"></i>
            <span>Admin: <strong><?= h($_SESSION['username']) ?></strong></span>
        </div>
    </div>

    <?= render_flash() ?>

    <!-- ── Stats Bar ────────────────────────────────────────────────────────── -->
    <div class="cards-grid" style="margin-bottom:24px">
        <div class="card">
            <div class="card-info"><h3>Total Orders</h3><p><?= number_format((int)$stats['total_orders']) ?></p></div>
            <div class="card-icon"><i class="fas fa-shopping-bag"></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3>Revenue</h3><p><?= price((float)$stats['total_revenue']) ?></p></div>
            <div class="card-icon"><i class="fas fa-coins"></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3>Pending</h3><p><?= (int)$stats['pending'] ?></p></div>
            <div class="card-icon"><i class="fas fa-clock"></i></div>
        </div>
        <div class="card">
            <div class="card-info"><h3>Delivered</h3><p><?= (int)$stats['delivered'] ?></p></div>
            <div class="card-icon"><i class="fas fa-check-circle"></i></div>
        </div>
    </div>

    <!-- ── Search & Filter ──────────────────────────────────────────────────── -->
    <form method="GET" action="admin_orders.php" class="filter-bar">
        <input
            type="text"
            name="search"
            placeholder="Search by name, phone, city or ID…"
            value="<?= h($search) ?>"
            class="filter-input"
        >
        <select name="status" class="filter-select">
            <option value="">All Statuses</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= h($s) ?>" <?= $statusFilter === $s ? 'selected' : '' ?>>
                    <?= h(ucfirst($s)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn--primary"><i class="fas fa-search"></i> Filter</button>
        <?php if ($search || $statusFilter): ?>
            <a href="admin_orders.php" class="btn btn--secondary"><i class="fas fa-times"></i> Clear</a>
        <?php endif; ?>
    </form>

    <!-- ── Orders Table ──────────────────────────────────────────────────────── -->
    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>No orders found<?= $search || $statusFilter ? ' matching your filters.' : ' yet.' ?></p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Customer</th>
                        <th>Phone</th>
                        <th>City</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><strong>#<?= (int)$order['id'] ?></strong></td>
                            <td>
                                <div><?= h($order['customer_name']) ?></div>
                                <small style="color:#aaa"><?= h($order['email'] ?? '') ?></small>
                            </td>
                            <td><?= h($order['phone']) ?></td>
                            <td><?= h($order['city']) ?></td>
                            <td><strong><?= price((float)$order['total_price']) ?></strong></td>
                            <td>
                                <span class="status-badge status-badge--<?= h($order['status']) ?>">
                                    <?= h(ucfirst($order['status'])) ?>
                                </span>
                            </td>
                            <td><?= h(date('d M Y', strtotime($order['created_at']))) ?></td>
                            <td class="actions-cell">
                                <button
                                    class="btn btn--icon btn--view"
                                    onclick="openOrderModal(<?= (int)$order['id'] ?>)"
                                    title="View Details"
                                ><i class="fas fa-eye"></i></button>

                                <!-- Quick-status form -->
                                <form method="POST" action="admin_orders.php" style="display:inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                    <select name="status" class="quick-status" onchange="this.form.submit()">
                                        <?php foreach ($statuses as $s): ?>
                                            <option value="<?= h($s) ?>" <?= $order['status'] === $s ? 'selected' : '' ?>>
                                                <?= h(ucfirst($s)) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" name="update_status" value="1">
                                </form>

                                <form method="POST" action="admin_orders.php" style="display:inline"
                                      onsubmit="return confirm('Delete order #<?= (int)$order['id'] ?>? This cannot be undone.')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
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

        <!-- ── Pagination ──────────────────────────────────────────────────── -->
        <?php if ($pager['total_pages'] > 1): ?>
            <div class="pagination">
                <?php
                $qs = http_build_query(array_filter(['search' => $search, 'status' => $statusFilter]));
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
</main>

<!-- ── Order Detail Modal ────────────────────────────────────────────────────── -->
<div class="modal-overlay" id="orderModalOverlay" onclick="closeOrderModal()"></div>
<div class="modal" id="orderModal">
    <div class="modal-header">
        <h3 id="modalTitle">Order Details</h3>
        <button class="modal-close" onclick="closeOrderModal()">&times;</button>
    </div>
    <div class="modal-body" id="modalBody">
        <p>Loading…</p>
    </div>
</div>

<script>
function openOrderModal(id) {
    document.getElementById('orderModalOverlay').classList.add('active');
    document.getElementById('orderModal').classList.add('active');
    document.getElementById('modalTitle').textContent = 'Order #' + id;
    document.getElementById('modalBody').innerHTML = '<p style="text-align:center"><i class="fas fa-spinner fa-spin"></i> Loading…</p>';

    fetch('admin_order_detail.php?id=' + id)
        .then(r => r.text())
        .then(html => { document.getElementById('modalBody').innerHTML = html; })
        .catch(() => { document.getElementById('modalBody').innerHTML = '<p>Failed to load order details.</p>'; });
}

function closeOrderModal() {
    document.getElementById('orderModalOverlay').classList.remove('active');
    document.getElementById('orderModal').classList.remove('active');
}
</script>

</body>
</html>
