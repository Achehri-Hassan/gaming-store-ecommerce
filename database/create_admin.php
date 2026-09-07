<?php

/**
 * database/create_admin.php
 *
 * Creates (or promotes) an admin account without ever putting a real
 * credential in source control or version history.
 *
 * Usage (run from the project root, via the command line — NOT the browser):
 *
 *     php database/create_admin.php you@example.com "a-strong-password" "Admin Name"
 *
 * If you omit the arguments, the script will prompt for them interactively
 * and hide the password while you type it (where the terminal supports it).
 *
 * The password is hashed with password_hash() before it ever touches the
 * database — the plaintext is never written to disk or logged anywhere.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    die('This script can only be run from the command line (php database/create_admin.php), not from a browser.');
}

require_once __DIR__ . '/../src/config/connection.php';

function prompt(string $label): string
{
    fwrite(STDOUT, $label);
    return trim((string) fgets(STDIN));
}

function promptHidden(string $label): string
{
    fwrite(STDOUT, $label);
    // Best-effort hide on Linux/macOS terminals; falls back to visible input
    // on platforms where `stty` isn't available (e.g. some Windows shells).
    if (stripos(PHP_OS, 'WIN') === false && shell_exec('command -v stty')) {
        shell_exec('stty -echo');
        $value = trim((string) fgets(STDIN));
        shell_exec('stty echo');
        fwrite(STDOUT, "\n");
        return $value;
    }
    return trim((string) fgets(STDIN));
}

$email    = $argv[1] ?? prompt('Admin email: ');
$password = $argv[2] ?? promptHidden('Admin password (min 8 characters): ');
$username = $argv[3] ?? prompt('Display name [Admin]: ');
$username = $username !== '' ? $username : 'Admin';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Error: '$email' is not a valid email address.\n");
    exit(1);
}

if (strlen($password) < 8) {
    fwrite(STDERR, "Error: password must be at least 8 characters.\n");
    exit(1);
}

$conn = getConnection();

$stmt = $conn->prepare('SELECT id, role FROM users WHERE email = :email');
$stmt->execute([':email' => $email]);
$existing = $stmt->fetch();

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

if ($existing) {
    $stmt = $conn->prepare('UPDATE users SET password = :password, role = :role WHERE id = :id');
    $stmt->execute([
        ':password' => $hashedPassword,
        ':role'     => 'admin',
        ':id'       => $existing['id'],
    ]);
    fwrite(STDOUT, "Existing account for $email updated and promoted to admin.\n");
} else {
    $stmt = $conn->prepare(
        'INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)'
    );
    $stmt->execute([
        ':username' => $username,
        ':email'    => $email,
        ':password' => $hashedPassword,
        ':role'     => 'admin',
    ]);
    fwrite(STDOUT, "Admin account created for $email.\n");
}
