<?php
require_once __DIR__ . '/../../config/connection.php';
require_once __DIR__ . '/../../helpers/helpers.php';
require_once __DIR__ . '/../../models/OrderModel.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_admin();

// ── Actions ───────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

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
$currentPage = max(1, (int) ($_GET['page'] ?? 1));
$perPage     = 15;

$total   = countUniqueCustomers($search);
$pager   = paginate($total, $perPage, $currentPage);
$customers = getUniqueCustomers($search, $perPage, $pager['offset']);
$stats   = getOrderStats();
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

    // هنا نقوم باستدعاء ملف تفاصيل المنتجات التي اشتراها بالكامل
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