<?php

function getConnection()
{
    $server = "localhost";
    $dbname = "gaming_store";
    $username = "root";
    $password = "";

    try {

        $conn = new PDO(
            "mysql:host=$server;dbname=$dbname;charset=utf8",
            $username,
            $password
        );

        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $conn;

    } catch (PDOException $e) {

        die("Error: " . $e->getMessage());
    }
}

?>