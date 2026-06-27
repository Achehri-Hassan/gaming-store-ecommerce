<?php

require_once __DIR__ . '/../config/connection.php';

// ─── Read ─────────────────────────────────────────────────────────────────────

function selectAll(): array
{
    $conn = getConnection();
    return $conn->query("SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC")->fetchAll();
}

function selectGroupedByCategory(): array
{
    $grouped = [];
    foreach (selectAll() as $product) {
        $grouped[$product['category']][] = $product;
    }
    return $grouped;
}

function selectById(int $id): ?array
{
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function selectProductImages(int $id): array
{
    $conn = getConnection();
    $stmt = $conn->prepare("SELECT image_path FROM product_gallery WHERE product_id = :id ORDER BY sort_order ASC");
    $stmt->execute([':id' => $id]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function selectRelated(string $category, int $id): array
{
    $conn = getConnection();
    $stmt = $conn->prepare(" SELECT * FROM products
        WHERE category = :cat AND id != :id AND is_active = 1
        ORDER BY RAND() LIMIT 8
    ");
    $stmt->execute([':cat' => $category, ':id' => $id]);
    return $stmt->fetchAll();
}

function selectByCategoryForAdmin(string $category): array
{
    $conn = getConnection();
    $stmt = $conn->prepare(" SELECT p.*, COALESCE(p.hover_image, g.image_path) AS calculated_hover
        FROM products p
        LEFT JOIN product_gallery g ON g.product_id = p.id
        WHERE p.category = :category
        GROUP BY p.id
        ORDER BY p.id DESC
    ");
    $stmt->execute([':category' => $category]);
    return $stmt->fetchAll();
}

/**
 * Search + filter products with pagination.
 * Used by shop-details.php.
 */
function searchProducts(string $query = '', string $category = '', float $minPrice = 0, float $maxPrice = 0, int $limit = 12, int $offset = 0): array
{
    $conn   = getConnection();
    $where  = ['is_active = 1'];
    $params = [];

    if ($query !== '') {
        $where[]          = '(name LIKE :q OR brand LIKE :q OR description LIKE :q)';
        $params[':q']     = '%' . $query . '%';
    }
    if ($category !== '') {
        $where[]           = 'category = :cat';
        $params[':cat']    = $category;
    }
    if ($minPrice > 0) {
        $where[]           = 'price >= :min';
        $params[':min']    = $minPrice;
    }
    if ($maxPrice > 0) {
        $where[]           = 'price <= :max';
        $params[':max']    = $maxPrice;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);

    $stmt = $conn->prepare("
        SELECT * FROM products {$whereClause}
        ORDER BY id DESC
        LIMIT :limit OFFSET :offset
    ");
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function countSearchProducts(string $query = '', string $category = '', float $minPrice = 0, float $maxPrice = 0): int
{
    $conn   = getConnection();
    $where  = ['is_active = 1'];
    $params = [];

    if ($query !== '') {
        $where[]      = '(name LIKE :q OR brand LIKE :q OR description LIKE :q)';
        $params[':q'] = '%' . $query . '%';
    }
    if ($category !== '') {
        $where[]       = 'category = :cat';
        $params[':cat'] = $category;
    }
    if ($minPrice > 0) {
        $where[] = 'price >= :min';
        $params[':min'] = $minPrice;
    }
    if ($maxPrice > 0) {
        $where[] = 'price <= :max';
        $params[':max'] = $maxPrice;
    }

    $whereClause = 'WHERE ' . implode(' AND ', $where);
    $stmt = $conn->prepare("SELECT COUNT(*) FROM products {$whereClause}");
    $stmt->execute($params);
    return (int) $stmt->fetchColumn();
}

// ─── Create ────────────────────────────────────────────────────────────────────

function createProduct(array $data)
{
    $conn = getConnection();
    $stmt = $conn->prepare(" INSERT INTO products (category, brand, name, slug, price, main_image, hover_image, description, is_active)
        VALUES (:category, :brand, :name, :slug, :price, :main_image, :hover_image, :description, 1)
    ");
    return $stmt->execute($data) ? (int) $conn->lastInsertId() : false;
}

// ─── Update ───────────────────────────────────────────────────────────────────

function updateProduct(array $data): bool
{
    $conn = getConnection();
    $stmt = $conn->prepare(" UPDATE products
        SET name = :name, brand = :brand, description = :description,
            price = :price, category = :category,
            main_image = :main_image, hover_image = :hover_image
        WHERE id = :id
    ");
    return $stmt->execute($data);
}

// ─── Delete ───────────────────────────────────────────────────────────────────

function deleteProduct(int $id): bool
{
    $conn = getConnection();
    // Gallery rows deleted by FK cascade (ON DELETE CASCADE)
    $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
    return $stmt->execute([':id' => $id]);
}

// ─── Gallery ──────────────────────────────────────────────────────────────────

function addGalleryImage(int $productId, string $image): bool
{
    $conn = getConnection();
    $stmt = $conn->prepare("
        INSERT INTO product_gallery (product_id, image_path, sort_order)
        VALUES (:product_id, :image, 1)
    ");
    return $stmt->execute([':product_id' => $productId, ':image' => $image]);
}

// ─── Stats ────────────────────────────────────────────────────────────────────

function countActiveProducts(): int
{
    return (int) getConnection()->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn();
}
