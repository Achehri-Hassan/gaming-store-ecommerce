<?php

require_once __DIR__ . '/../config/connection.php';

// ──────────────────────────────────────────────
// selectAll()  — home page, all active products
// ──────────────────────────────────────────────
function selectAll(): array {
    $conn = getConnection();

    $sql = "
        SELECT
            p.id, p.category, p.brand, p.name, p.slug,
            p.price, p.currency, p.status,
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

// ──────────────────────────────────────────────
// selectByCategory()  — shop.php filter
// BUG FIX: was missing COALESCE fallback to p.hover_image
// so hover never worked on the shop/category page
// ──────────────────────────────────────────────
function selectByCategory(string $category): array {
    $conn = getConnection();

    $stmt = $conn->prepare("
        SELECT
            p.id, p.category, p.brand, p.name, p.slug,
            p.price, p.currency, p.status,
            p.main_image,
            COALESCE(NULLIF(p.hover_image, ''), g.image_path) AS hover_image
        FROM products p
        LEFT JOIN product_gallery g
            ON g.product_id = p.id
            AND g.image_type = 'home'
        WHERE p.is_active = 1
          AND p.category = :category
        ORDER BY p.id ASC
    ");
    $stmt->execute([':category' => $category]);
    return $stmt->fetchAll();
}

// ──────────────────────────────────────────────
// selectGroupedByCategory()  — index.php sections
// ──────────────────────────────────────────────
function selectGroupedByCategory(): array {
    $grouped = [];
    foreach (selectAll() as $product) {
        $grouped[$product['category']][] = $product;
    }
    return $grouped;
}

// ──────────────────────────────────────────────
// selectById()  — shop-details.php
// BUG FIX: now explicitly selects p.hover_image
// so the related products grid also gets hover images
// ──────────────────────────────────────────────
function selectById(int $id): ?array {
    $conn = getConnection();

    $stmt = $conn->prepare("
        SELECT
            p.*,
            p.hover_image,
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
    ");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();

    if (!$row) return null;

    $row['gallery'] = $row['gallery_images']
        ? explode('|', $row['gallery_images'])
        : [];

    return $row;
}