<?php

require_once __DIR__ . '/../config/connection.php';


function selectAll(): array{
    $conn = getConnection();

    $sql = " SELECT * FROM products WHERE is_active = 1 ORDER BY id DESC ";

    return $conn->query($sql)->fetchAll();
}

function selectGroupedByCategory(): array {
    $grouped = [];
    foreach (selectAll() as $product) {
        $grouped[$product['category']][] = $product;
    }
    return $grouped;
}


function selectById(int $id): ?array {
    $conn = getConnection();

    $stmt = $conn->prepare("
        SELECT *
        FROM products
        WHERE id = :id AND is_active = 1
    ");

    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}


function selectProductImages(int $id): array {
    $conn = getConnection();

    $stmt = $conn->prepare("
        SELECT image_path
        FROM product_gallery
        WHERE product_id = :id
        ORDER BY sort_order ASC
    ");

    $stmt->execute([':id' => $id]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function selectRelated(string $category, int $id): array {
    $conn = getConnection();

    $stmt = $conn->prepare("
        SELECT *
        FROM products
        WHERE category = :cat
          AND id != :id
          AND is_active = 1
        ORDER BY RAND()
        LIMIT 8
    ");

    $stmt->execute([
        ':cat' => $category,
        ':id' => $id
    ]);

    return $stmt->fetchAll();
}


?>