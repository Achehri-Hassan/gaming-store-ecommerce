<?php

/**
 * Load .env file variables into the environment.
 * Simple parser — no external dependency needed.
 */
function loadEnv(string $path): void
{
    if (!file_exists($path)) return;

    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (!str_contains($line, '=')) continue;

        [$key, $value] = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value, " \t\n\r\0\x0B\"'");

        if (!array_key_exists($key, $_ENV)) {
            putenv("$key=$value");
            $_ENV[$key] = $value;
        }
    }
}

// Load .env from project root (two levels up from src/config/)
loadEnv(dirname(__DIR__, 2) . '/.env');

/**
 * Return a PDO connection using environment variables.
 */
function getConnection(): PDO
{
    static $conn = null;

    if ($conn !== null) return $conn;

    $host   = getenv('DB_HOST') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'gaming_store';
    $user   = getenv('DB_USER') ?: 'root';
    $pass   = getenv('DB_PASS') ?: '';

    try {
        $conn = new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        return $conn;
    } catch (PDOException $e) {
        // Never expose credentials in production
        $msg = (getenv('APP_ENV') === 'development')
            ? 'DB Error: ' . $e->getMessage()
            : 'A database error occurred. Please try again later.';
        die($msg);
    }
}
