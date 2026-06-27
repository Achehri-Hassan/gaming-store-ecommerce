



<?php

require_once __DIR__ . '/../config/connection.php';

function loginUser(string $email): ?array
{
    $conn = getConnection();

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);

    $user = $stmt->fetch();

    return $user ?: null;
}


function createUser(string $username, string $email, string $password): bool
{
    $conn = getConnection();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare(" INSERT INTO users (username, email, password, role)
        VALUES (:username, :email, :password, 'user')
    ");

    return $stmt->execute([
        ':username' => $username,
        ':email' => $email,
        ':password' => $hashedPassword
    ]);
}



function emailExists(string $email): bool
{
    $conn = getConnection();

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $email]);

    return (bool) $stmt->fetch();
}


?>