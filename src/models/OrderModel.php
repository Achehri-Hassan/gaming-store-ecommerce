<?php

require_once __DIR__ . '/../config/connection.php';

// ─── Create ────────────────────────────────────────────────────────────────────

/**
 * Insert a new order and return its ID.
 *
 * @param int    $userId
 * @param float  $totalPrice
 * @param string $customerName
 * @param string $phone
 * @param string $address
 * @param string $city
 * @return int|false  New order ID or false on failure.
 */
function createOrder(int $userId, float $totalPrice, string $customerName, string $phone, string $address, string $city)
{
    $conn = getConnection();
    $stmt = $conn->prepare("
        INSERT INTO orders (user_id, total_price, customer_name, phone, address, city, status)
        VALUES (:user_id, :total_price, :customer_name, :phone, :address, :city, 'pending')
    ");

    $ok = $stmt->execute([
        ':user_id'       => $userId,
        ':total_price'   => $totalPrice,
        ':customer_name' => $customerName,
        ':phone'         => $phone,
        ':address'       => $address,
        ':city'          => $city,
    ]);

    return $ok ? (int) $conn->lastInsertId() : false;
}

/**
 * Insert all items for an order.
 *
 * @param int   $orderId
 * @param array $items  [ ['id' => x, 'price' => y, 'quantity' => z], … ]
 */
function createOrderItems(int $orderId, array $items): bool
{
    $conn = getConnection();
    $stmt = $conn->prepare("
        INSERT INTO order_items (order_id, product_id, quantity, price)
        VALUES (:order_id, :product_id, :quantity, :price)
    ");

    foreach ($items as $item) {
        $ok = $stmt->execute([
            ':order_id'   => $orderId,
            ':product_id' => (int) $item['id'],
            ':quantity'   => (int) $item['quantity'],
            ':price'      => (float) $item['price'],
        ]);
        if (!$ok) return false;
    }
    return true;
}

// ─── Read ─────────────────────────────────────────────────────────────────────

/**
 * Retrieve all orders with optional search and status filter.
 * Returns paginated results.
 */
function getAllOrders(string $search = '', string $status = '', int $limit = 20, int $offset = 0): array
{
    $conn   = getConnection();
    $where  = [];
    $params = [];

    if ($search !== '') {
        $where[]           = '(o.customer_name LIKE :search OR o.phone LIKE :search OR o.city LIKE :search OR o.id = :search_id)';
        $params[':search']    = '%' . $search . '%';
        $params[':search_id'] = is_numeric($search) ? (int) $search : 0;
    }
    if ($status !== '') {
        $where[]          = 'o.status = :status';
        $params[':status'] = $status;
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $stmt = $conn->prepare("
        SELECT o.*, u.username, u.email
        FROM orders o
        LEFT JOIN users u ON u.id = o.user_id
        {$whereClause}
        ORDER BY o.created_at DESC
        LIMIT :limit OFFSET :offset
    ");

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/** Count all orders (for pagination). */
function countAllOrders(string $search = '', string $status = ''): int
{
    $conn   = getConnection();
    $where  = [];
    $params = [];

    if ($search !== '') {
        $where[]              = '(customer_name LIKE :search OR phone LIKE :search OR city LIKE :search OR id = :search_id)';
        $params[':search']    = '%' . $search . '%';
        $params[':search_id'] = is_numeric($search) ? (int) $search : 0;
    }
    if ($status !== '') {
        $where[]          = 'status = :status';
        $params[':status'] = $status;
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
    $stmt = $conn->prepare("SELECT COUNT(*) FROM orders {$whereClause}");
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

/** Get a single order by ID (includes user info). */
function getOrderById(int $id): ?array
{
    $conn = getConnection();
    $stmt = $conn->prepare(" SELECT o.*, u.username, u.email
        FROM orders o
        LEFT JOIN users u ON u.id = o.user_id
        WHERE o.id = :id
    ");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

/** Get all orders for a specific user. */
function getUserOrders(int $userId): array
{
    $conn = getConnection();
    $stmt = $conn->prepare(" SELECT * FROM orders
        WHERE user_id = :user_id
        ORDER BY created_at DESC
    ");
    $stmt->execute([':user_id' => $userId]);
    return $stmt->fetchAll();
}

/** Get all line items for an order (with product name). */
function getOrderItems(int $orderId): array
{
    $conn = getConnection();
    $stmt = $conn->prepare(" SELECT oi.*, p.name AS product_name, p.main_image, p.category
        FROM order_items oi
        LEFT JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id = :order_id
    ");
    $stmt->execute([':order_id' => $orderId]);
    return $stmt->fetchAll();
}

// ─── Update ───────────────────────────────────────────────────────────────────

/** Update the status of an order. */
function updateOrderStatus(int $id, string $status): bool
{
    $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($status, $allowed, true)) return false;

    $conn = getConnection();
    $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE id = :id");
    return $stmt->execute([':status' => $status, ':id' => $id]);
}

// ─── Delete ───────────────────────────────────────────────────────────────────

/** Delete an order and its items (cascade handles items if FK set). */
function deleteOrder(int $id): bool
{
    $conn = getConnection();
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

// ─── Stats ────────────────────────────────────────────────────────────────────

/** Return aggregate stats for the admin dashboard. */
function getOrderStats(): array
{
    $conn = getConnection();
    $row  = $conn->query(" SELECT COUNT(*) AS total_orders,
            COALESCE(SUM(total_price), 0) AS total_revenue,
            SUM(status = 'pending')   AS pending,
            SUM(status = 'delivered') AS delivered
        FROM orders
    ")->fetch();
    return $row ?: ['total_orders' => 0, 'total_revenue' => 0, 'pending' => 0, 'delivered' => 0];
}



/**
 * الحصول على جميع المنتجات التي تم شراؤها في تاريخ محدد
 * 
 * @param int $userId
 * @param string $date (Format: Y-m-d)
 * @return array
 */
function getProductsByOrderDate(int $userId, string $date): array
{
    $conn = getConnection(); //[cite: 1]
    
    // query كتجيب كاع المنتجات ديال هاد النهار فجدول واحد ديريكت
    $stmt = $conn->prepare(" SELECT 
            oi.quantity,
            oi.price AS purchased_price,
            p.name AS product_name,
            p.main_image,
            p.category,
            o.id AS order_id,
            o.status,
            o.created_at
        FROM order_items oi
        INNER JOIN orders o ON o.id = oi.order_id
        LEFT JOIN products p ON p.id = oi.product_id
        WHERE o.user_id = :user_id 
          AND DATE(o.created_at) = :order_date
        ORDER BY o.created_at DESC
    ");
    
    $stmt->execute([
        ':user_id'    => $userId,
        ':order_date' => $date
    ]);
    
    return $stmt->fetchAll();
}


/**
 * الحصول على جميع المنتجات المشتراة بين تاريخين (Date Range).
 * إذا كان $from أو $to فارغين (null) كنرجعو كاع الطلبيات بلا حدود.
 *
 * @param int         $userId
 * @param string|null $from   (Format: Y-m-d) or null for no lower bound
 * @param string|null $to     (Format: Y-m-d) or null for no upper bound
 * @return array
 */
function getProductsByOrderDateRange(int $userId, ?string $from, ?string $to): array
{
    $conn   = getConnection();
    $where  = ['o.user_id = :user_id'];
    $params = [':user_id' => $userId];

    if ($from !== null && $from !== '') {
        $where[]              = 'DATE(o.created_at) >= :from_date';
        $params[':from_date'] = $from;
    }
    if ($to !== null && $to !== '') {
        $where[]            = 'DATE(o.created_at) <= :to_date';
        $params[':to_date'] = $to;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $stmt = $conn->prepare(" SELECT
            oi.quantity,
            oi.price AS purchased_price,
            p.name AS product_name,
            p.main_image,
            p.category,
            o.id AS order_id,
            o.status,
            o.created_at,
            o.total_price
        FROM order_items oi
        INNER JOIN orders o ON o.id = oi.order_id
        LEFT JOIN products p ON p.id = oi.product_id
        {$whereClause}
        ORDER BY o.created_at DESC, o.id DESC
    ");

    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * جلب المستخدمين الفريدين الذين قاموا بطلبات بدون تكرار الاسم
 */
function getUniqueCustomers(string $search = '', int $limit = 20, int $offset = 0): array
{
    $conn   = getConnection();
    $where  = [];
    $params = [];

    if ($search !== '') {
        $where[]           = '(customer_name LIKE :search OR phone LIKE :search OR city LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

    $stmt = $conn->prepare(" SELECT 
            MIN(id) AS id,
            user_id,
            customer_name,
            phone,
            city,
            MAX(created_at) AS created_at,
            SUM(total_price) AS total_price,
            'pending' AS status
        FROM orders
        {$whereClause}
        GROUP BY user_id, customer_name, phone, city
        ORDER BY created_at DESC
        LIMIT :limit OFFSET :offset
    ");

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * حساب عدد المستخدمين الفريدين للـ Pagination
 */
function countUniqueCustomers(string $search = ''): int
{
    $conn   = getConnection();
    $params = [];
    $where  = '';

    if ($search !== '') {
        $where             = 'WHERE (customer_name LIKE :search OR phone LIKE :search OR city LIKE :search)';
        $params[':search'] = '%' . $search . '%';
    }

    $stmt = $conn->prepare("SELECT COUNT(DISTINCT user_id, customer_name) FROM orders {$where}");
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}