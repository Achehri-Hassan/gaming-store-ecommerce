<?php


  require_once "src/config/connection.php";


function selectAll() {
  
        $conn = getConnection();
        $sql = "SELECT * FROM products WHERE is_active = 1 ORDER BY id ASC";
        return $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    
}



?>