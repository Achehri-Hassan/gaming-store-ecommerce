<?php



require_once __DIR__ . '/../config/connection.php';


function selectAll(): array {
    $conn = getConnection();

    $sql = "
        SELECT
            p.id,
            p.category,
            p.brand,
            p.name,
            p.slug,
            p.price,
            p.currency,
            p.status,
            p.main_image,
            COALESCE(NULLIF(p.hover_image, ''), g.image_path) AS hover_image
        FROM products p
        LEFT JOIN product_gallery g
            ON g.product_id = p.id
            AND g.image_type = 'home'
        WHERE p.is_active = 1
        ORDER BY p.id DESC
    ";

    return $conn->query($sql)->fetchAll();
}


function selectByCategory(string $category): array {
    $conn = getConnection();

    $sql = "
        SELECT
            p.id,
            p.category,
            p.brand,
            p.name,
            p.slug,
            p.price,
            p.currency,
            p.status,
            p.main_image,
            g.image_path AS hover_image
        FROM products p
        LEFT JOIN product_gallery g
            ON g.product_id = p.id
            AND g.image_type = 'home'
        WHERE p.is_active = 1
          AND p.category = :category
        ORDER BY p.id ASC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':category' => $category]);
    return $stmt->fetchAll();
}


function selectGroupedByCategory(): array {
    $products = selectAll();

    $grouped = [];
    foreach ($products as $product) {
        $grouped[$product['category']][] = $product;
    }
    return $grouped;
}



function selectById(int $id): ?array {
    $conn = getConnection();

    $sql = "
        SELECT
            p.*,
            GROUP_CONCAT(
                g.image_path ORDER BY g.sort_order SEPARATOR '|'
            ) AS gallery_images
        FROM products p
        LEFT JOIN product_gallery g
            ON g.product_id = p.id
            AND g.image_type = 'shop'
        WHERE p.id = :id
          AND p.is_active = 1
        GROUP BY p.id
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) return null;

    $row['gallery'] = $row['gallery_images']
        ? explode('|', $row['gallery_images'])
        : [];

    return $row;
}