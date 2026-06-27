<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'src/config/connection.php';
require_once 'src/helpers/helpers.php';
require_once 'src/models/OrderModel.php';

require_login('my-orders.php');

if (isset($_GET['action']) && $_GET['action'] === 'get_order_details') {
    $orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $order = $orderId ? getOrderById($orderId) : null;

 
    if (!$order || ((int)$order['user_id'] !== (int)$_SESSION['user_id'] && $_SESSION['role'] !== 'admin')) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized or order not found.']);
        exit;
    }

    $items = getOrderItems($orderId);
    
  
    $formattedItems = [];
    foreach ($items as $item) {
        $formattedItems[] = [
            'name' => $item['product_name'] ?? 'Deleted product',
            'quantity' => (int)$item['quantity'],
            'price' => price((float)$item['price'] * (int)$item['quantity']),
            'image' => asset_url($item['category'] ?? '', 'main', $item['main_image'] ?? '')
        ];
    }

    echo json_encode([
        'success' => true,
        'order' => [
            'id' => $order['id'],
            'customer_name' => $order['customer_name'],
            'status' => ucfirst($order['status']),
            'status_raw' => $order['status'],
            'phone' => $order['phone'],
            'city' => $order['city'],
            'address' => $order['address'],
            'total_price' => price((float)$order['total_price'])
        ],
        'items' => $formattedItems
    ]);
    exit;
}

$orders = getUserOrders((int) $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — Gaming Store</title>
    <link rel="stylesheet" href="css/components/style.css">
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .orders-page { padding: 40px 16px; max-width: 900px; margin: 0 auto; }
        .orders-page h1 { color: #e5e7eb; margin-bottom: 24px; }
        .order-card { background: #1a1a1a; border: 1px solid #2a2a2a; border-radius: 10px; padding: 20px; margin-bottom: 16px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; }
        .order-card__id { color: #9ca3af; font-size: .9rem; }
        .order-card__total { color: #3b82f6; font-weight: 700; }
        .order-card__date { color: #6b7280; font-size: .85rem; }
        .order-card__actions a { text-decoration: none; cursor: pointer; }
        .empty-state { text-align: center; padding: 60px 20px; color: #4b5563; }
        .empty-state i { font-size: 3rem; margin-bottom: 12px; display: block; }

     
        .order-details-modal {
            display: none; 
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .modal-details-content {
            background: #181a20; 
            border: 2px solid #2f333e;
            border-radius: 16px;
            padding: 30px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
            color: #fff;
            position: relative;
        }
        .close-modal-btn {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 1.5rem;
            color: #9ca3af;
            background: none;
            border: none;
            cursor: pointer;
            transition: color 0.2s;
        }
        .close-modal-btn:hover { color: #fff; }
        
        .modal-details-content h2 {
            font-size: 1.8rem;
            margin-bottom: 15px;
            text-align: center;
            color: #e5e7eb;
        }
        .modal-order-ref {
            background: #232732;
            padding: 8px 16px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 20px;
            font-size: 1.1rem;
            border-left: 4px solid #3b82f6;
        }
        
        /* الأنيميشن فاش كيطلع المودال */
        .animate-pop {
            transform: scale(0.8);
            opacity: 0;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .order-details-modal.show .animate-pop {
            transform: scale(1);
            opacity: 1;
        }
    </style>
</head>
<body>
<?php include 'src/views/layouts/header.php'; ?>
<main class="orders-page">
    <h1><i class="fas fa-box"></i> My Orders</h1>

    <?= render_flash() ?>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <i class="fas fa-shopping-bag"></i>
            <p>You haven't placed any orders yet.</p>
            <a href="index.php" class="btn-place-order" style="display:inline-flex;margin-top:12px">
                <i class="fas fa-store"></i> Start Shopping
            </a>
        </div>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <div>
                    <div class="order-card__id">Order #<?= (int)$order['id'] ?></div>
                    <div class="order-card__date"><?= h(date('d M Y', strtotime($order['created_at']))) ?></div>
                </div>
                <span class="status-badge status-badge--<?= h($order['status']) ?>"><?= h(ucfirst($order['status'])) ?></span>
                <div class="order-card__total"><?= price((float)$order['total_price']) ?></div>
                <div class="order-card__actions">
                    <a onclick="openOrderDetails(<?= (int)$order['id'] ?>)" class="btn-back-shop">
                        <i class="fas fa-eye"></i> View
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

<div id="orderDetailsModal" class="order-details-modal">
    <div class="modal-details-content animate-pop">
        <button class="close-modal-btn" onclick="closeOrderDetails()">&times;</button>
        
        <h2>Order Details</h2>
        <div style="text-align: center;">
            <p class="modal-order-ref">Order #<strong id="modalOrderId"></strong></p>
        </div>

        <div class="order-details-box" style="margin-top: 10px;">
            <div class="detail-row"><span>Status</span> <span id="modalOrderStatus" class="status-badge"></span></div>
            <div class="detail-row"><span>Customer</span> <span id="modalCustomerName"></span></div>
            <div class="detail-row"><span>Phone</span> <span id="modalCustomerPhone"></span></div>
            <div class="detail-row"><span>City</span> <span id="modalCustomerCity"></span></div>
            <div class="detail-row"><span>Address</span> <span id="modalCustomerAddress"></span></div>
            <div class="detail-row detail-row--total"><span>Total</span> <span id="modalOrderTotal"></span></div>
        </div>

        <h3 style="margin:24px 0 12px; text-align:left; font-size:1.2rem; border-bottom:1px solid #2a2a2a; padding-bottom:8px;">Items Ordered</h3>
        
        <ul class="order-items" id="modalOrderItemsList">
            </ul>
    </div>
</div>

<?php include 'src/views/layouts/footer.php'; ?>

<script>
function openOrderDetails(orderId) {
    
    fetch(`my-orders.php?action=get_order_details&id=${orderId}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
         
            document.getElementById('modalOrderId').innerText = data.order.id;
            document.getElementById('modalCustomerName').innerText = data.order.customer_name;
            document.getElementById('modalCustomerPhone').innerText = data.order.phone;
            document.getElementById('modalCustomerCity').innerText = data.order.city;
            document.getElementById('modalCustomerAddress').innerText = data.order.address;
            document.getElementById('modalOrderTotal').innerText = data.order.total_price;
            
        
            const statusBadge = document.getElementById('modalOrderStatus');
            statusBadge.innerText = data.order.status;
            statusBadge.className = `status-badge status-badge--${data.order.status_raw}`;

           
            const itemsList = document.getElementById('modalOrderItemsList');
            itemsList.innerHTML = ''; 

            data.items.forEach(item => {
                const li = document.createElement('li');
                li.className = 'order-item';
                li.innerHTML = `
                    <img src="${item.image}" alt="${item.name}" class="order-item__img" onerror="this.src='src/assets/placeholder.webp'">
                    <div class="order-item__info">
                        <span class="order-item__name">${item.name}</span>
                        <span class="order-item__qty">Qty: ${item.quantity}</span>
                    </div>
                    <span class="order-item__price">${item.price}</span>
                `;
                itemsList.appendChild(li);
            });

            // 3. إظهار المودال بالأنيميشن
            const modal = document.getElementById('orderDetailsModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        } else {
            alert(data.message || "Could not load order details.");
        }
    })
    .catch(error => {
        console.error('Error fetching order details:', error);
        alert("An error occurred while loading details.");
    });
}

function closeOrderDetails() {
    const modal = document.getElementById('orderDetailsModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}


window.onclick = function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target == modal) {
        closeOrderDetails();
    }
}
</script>
</body>
</html>