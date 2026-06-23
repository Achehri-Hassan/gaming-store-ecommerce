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

    $stmt = $conn->prepare(" SELECT *  FROM products
        WHERE id = :id
    ");

    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}


function selectProductImages(int $id): array {
    $conn = getConnection();

    $stmt = $conn->prepare(" SELECT image_path
        FROM product_gallery
        WHERE product_id = :id
        ORDER BY sort_order ASC
    ");

    $stmt->execute([':id' => $id]);

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


function selectRelated(string $category, int $id): array {
    $conn = getConnection();

    $stmt = $conn->prepare(" SELECT *
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


function selectByCategoryForAdmin(string $category): array {
    $conn = getConnection();
    $stmt = $conn->prepare("
        SELECT p.*, COALESCE(p.hover_image, g.image_path) as calculated_hover 
        FROM products p 
        LEFT JOIN product_gallery g ON g.product_id = p.id
        WHERE p.category = :category 
        GROUP BY p.id
        ORDER BY p.id DESC
    ");
    $stmt->execute([':category' => $category]);
    return $stmt->fetchAll();
}



function createProduct(array $data): bool{
    $conn = getConnection();

    $stmt = $conn->prepare(" INSERT INTO products ( category, brand, name, slug, price, main_image,           hover_image,    
    description,
    is_active)

    VALUES ( :category , :brand , :name , :slug ,:price , :main_image ,:hover_image , :description , 1)
    ");

    return $stmt->execute($data);
}


// function createProduct(array $data) {
//     $conn = getConnection();

//     $stmt = $conn->prepare(" INSERT INTO products ( category, brand, name, slug, price, main_image, hover_image, description, is_active)
//         VALUES ( :category , :brand , :name , :slug , :price , :main_image , :hover_image , :description , 1)
//     ");

//     if ($stmt->execute($data)) {

//         return (int)$conn->lastInsertId();
//     }
    
//     return false;
// }


function updateProduct(array $data): bool{
    $conn = getConnection();

    $stmt = $conn->prepare(" UPDATE products
        SET name = :name, brand = :brand, description = :description, price = :price, category = :category,
         main_image = :main_image,
         hover_image = :hover_image WHERE id = :id
    ");

    return $stmt->execute($data);
}


function deleteProduct(int $id): bool{
    $conn = getConnection();

    $stmt = $conn->prepare(" DELETE FROM products WHERE id = :id
    ");

    return $stmt->execute([
        ':id' => $id
    ]);
}


function addGalleryImage(int $productId, string $image): bool{
    $conn = getConnection();

    $stmt = $conn->prepare(" INSERT INTO product_gallery ( product_id, image_path , sort_order) 
       VALUES( :product_id , :image, 1 )
       ");

    return $stmt->execute([
        ':product_id' => $productId,
        ':image'      => $image
    ]);
}



?>