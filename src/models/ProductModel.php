<?php


  require_once "src/config/connection.php";


// function selectAll() {
  
//         $conn = getConnection();
//         $sql = "SELECT * FROM products WHERE is_active = 1 ORDER BY id ASC";
//         return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
// }



function selectAll() {

    $conn = getConnection();

    $sql = "
    SELECT
        p.*,
        (
          SELECT pg.image_path
          FROM product_gallery pg
          WHERE pg.product_id = p.id
          AND pg.image_path LIKE 'shop_%'
          ORDER BY pg.sort_order
          LIMIT 1
        ) AS hover_image

    FROM products p

    WHERE p.is_active = 1

    ORDER BY p.id ASC
    ";

    return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}



?>