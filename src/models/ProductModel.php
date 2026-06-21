<?php
// src/models/ProductModel.php

require_once __DIR__ . '/../config/connection.php';

// ──────────────────────────────────────────────────────────────
// selectAll()
// BUG FIX: Removed the inner subquery for MIN(sort_order).
// A simple JOIN ON image_type = 'home' is enough — each product
// has only ONE home image, so no MIN() logic is needed.
// ──────────────────────────────────────────────────────────────
// function selectAll(): array {
//     $conn = getConnection();

//     $sql = "
//         SELECT
//             p.id,
//             p.category,
//             p.brand,
//             p.name,
//             p.slug,
//             p.price,
//             p.currency,
//             p.status,
//             p.main_image,
//             g.image_path AS hover_image
//         FROM products p
//         LEFT JOIN product_gallery g
//             ON g.product_id = p.id
//             AND g.image_type = 'home'
//         WHERE p.is_active = 1
//         ORDER BY p.id ASC
//     ";

//     return $conn->query($sql)->fetchAll();
// }



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

// ──────────────────────────────────────────────────────────────
// selectByCategory($category)
// Fetch products for ONE category — used on shop.php
// ──────────────────────────────────────────────────────────────
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

// ──────────────────────────────────────────────────────────────
// selectGroupedByCategory()
// One DB call → pre-grouped array for index.php
// Returns: [ 'chair' => [...], 'mouse' => [...], ... ]
// ──────────────────────────────────────────────────────────────
function selectGroupedByCategory(): array {
    $products = selectAll();

    $grouped = [];
    foreach ($products as $product) {
        $grouped[$product['category']][] = $product;
    }
    return $grouped;
}

// ──────────────────────────────────────────────────────────────
// selectById($id) — for shop-details.php
// ──────────────────────────────────────────────────────────────
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