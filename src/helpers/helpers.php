<?php

// ─── Output Escaping ───────────────────────────────────────────────────────────

/** Escape a value for safe HTML output. */
function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// ─── CSRF ──────────────────────────────────────────────────────────────────────

/** Generate (or return existing) CSRF token for this session. */
function csrf_token(): string
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Render a hidden CSRF input field. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

/** Validate POST CSRF token — dies on failure. For normal HTML form posts. */
function verify_csrf(): void
{
    if (!is_valid_csrf_token($_POST['csrf_token'] ?? null)) {
        http_response_code(403);
        die('Invalid CSRF token. Please go back and try again.');
    }
}

/**
 * Validate a CSRF token value without terminating the request. Used by
 * JSON/AJAX endpoints (e.g. cart-handler.php) that need to return a
 * structured error response instead of dying with an HTML message.
 */
function is_valid_csrf_token(?string $token): bool
{
    return !empty($token) && hash_equals(csrf_token(), $token);
}

// ─── Flash Messages ────────────────────────────────────────────────────────────

/** Store a flash message (success | error | info | warning). */
function flash(string $type, string $message): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** Render and clear all pending flash messages. */
function render_flash(): string
{
    if (empty($_SESSION['flash'])) return '';

    $icons = [
        'success' => '✓',
        'error'   => '✕',
        'info'    => 'ℹ',
        'warning' => '⚠',
    ];

    $html = '';
    foreach ($_SESSION['flash'] as $f) {
        $type    = h($f['type']);
        $icon    = $icons[$f['type']] ?? 'ℹ';
        $message = h($f['message']);
        $html   .= <<<HTML
            <div class="flash flash--{$type}" role="alert">
                <span class="flash__icon">{$icon}</span>
                <span class="flash__msg">{$message}</span>
                <button class="flash__close" onclick="this.parentElement.remove()" aria-label="Close">&times;</button>
            </div>
            HTML;
    }
    unset($_SESSION['flash']);
    return $html;
}

// ─── Auth Guards ───────────────────────────────────────────────────────────────

/** Redirect to login if not authenticated. */
function require_login(string $redirectTo = ''): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id'])) {
        if ($redirectTo) $_SESSION['redirect_to'] = $redirectTo;
        header('Location: /login.php');
        exit;
    }
}

/** Redirect to home if not an admin. */
function require_admin(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: /index.php');
        exit;
    }
}

// ─── Formatting ────────────────────────────────────────────────────────────────

/** Format a number as a price string. */
function price(float $amount, string $currency = 'DH'): string
{
    return number_format($amount, 0, '.', ',') . ' ' . $currency;
}

// ─── Asset Paths ───────────────────────────────────────────────────────────────

/**
 * Returns the web-root-relative path to a product image.
 *
 * @param string $category  e.g. 'chair', 'mouse'
 * @param string $slot      'main' | 'hover' | 'shop'
 * @param string $filename  e.g. 'chair-1.webp'
 */
function asset_url(string $category, string $slot, string $filename): string
{
    if (empty($filename)) return '';

    // Uploaded products land here
    $uploadCheck = 'src/assets/uploads/' . $filename;
    if (file_exists($uploadCheck)) return $uploadCheck;

    $base = 'src/assets/products/';

    $map = [
        'chair'       => ['main' => $base . 'chair/chair_home/',           'hover' => $base . 'chair/chair_home/',           'shop' => $base . 'chair/chair_shop/'],
        'desk'        => ['main' => $base . 'desk/desk_home/',             'hover' => $base . 'desk/desk_home/',             'shop' => $base . 'desk/desk_shop/'],
        'controller'  => ['main' => $base . 'controllers/controllers_home/', 'hover' => $base . 'controllers/controllers_home/', 'shop' => $base . 'controllers/controllers_shop/'],
        'playstation' => ['main' => $base . 'PlayStation/playStation_home/', 'hover' => $base . 'PlayStation/playStation_home/', 'shop' => $base . 'PlayStation/playStation_shop/'],
        'mouse'       => ['main' => $base . 'mous/mous_home/',             'hover' => $base . 'mous/mous_home/',             'shop' => $base . 'mous/mous_shop/'],
        'ecran'       => ['main' => $base . 'ecran/ecran_home/',           'hover' => $base . 'ecran/ecran_home/',           'shop' => $base . 'ecran/ecran_shop/'],
        'keyboard'    => ['main' => $base . 'keyabord/',                   'hover' => $base . 'keyabord/',                   'shop' => $base . 'keyabord/'],
        'headset'     => ['main' => $base . 'headset/',                    'hover' => $base . 'headset/',                    'shop' => $base . 'headset/'],
    ];

    return isset($map[$category][$slot]) ? $map[$category][$slot] . $filename : '';
}

/** Return the filesystem "home/main" upload folder for a given category. */
function upload_folder(string $category): string
{
    if (!is_valid_category($category)) return '';

    $base = 'src/assets/products/';
    $folders = [
        'chair'       => $base . 'chair/chair_home/',
        'desk'        => $base . 'desk/desk_home/',
        'controller'  => $base . 'controllers/controllers_home/',
        'playstation' => $base . 'PlayStation/playStation_home/',
        'mouse'       => $base . 'mous/mous_home/',
        'ecran'       => $base . 'ecran/ecran_home/',
        'keyboard'    => $base . 'keyabord/',
        'headset'     => $base . 'headset/',
    ];
    return $folders[$category] ?? '';
}

/** Delete an image file safely. */
function delete_image(string $folder, string $filename): bool
{
    if (empty($folder) || empty($filename)) return false;
    $path = rtrim($folder, '/') . '/' . $filename;
    return file_exists($path) && unlink($path);
}

/** Return the filesystem "shop gallery" upload folder for a given category. */
function shop_upload_folder(string $category): ?string
{
    if (!is_valid_category($category)) return null;

    $base = 'src/assets/products/';
    $folders = [
        'chair'       => $base . 'chair/chair_shop/',
        'desk'        => $base . 'desk/desk_shop/',
        'controller'  => $base . 'controllers/controllers_shop/',
        'playstation' => $base . 'PlayStation/playStation_shop/',
        'mouse'       => $base . 'mous/mous_shop/',
        'ecran'       => $base . 'ecran/ecran_shop/',
        'keyboard'    => $base . 'keyboard/',
        'headset'     => $base . 'headset/',
    ];
    return $folders[$category] ?? null;
}

// ─── Secure File Upload ─────────────────────────────────────────────────────────

/** Max upload size for a single product image, in bytes (2 MB). */
const MAX_UPLOAD_BYTES = 2 * 1024 * 1024;

/** Extensions we accept, mapped to the exact MIME types PHP must detect for them. */
const ALLOWED_IMAGE_TYPES = [
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'png'  => ['image/png'],
    'webp' => ['image/webp'],
];

/**
 * Securely validate and move an uploaded image into $destinationFolder
 * (must be one of the values returned by upload_folder()/shop_upload_folder(),
 * i.e. already resolved against the category whitelist).
 *
 * Validates: upload error state, file size, real image content via
 * getimagesize(), and that the detected MIME type matches an allowed
 * image type — not just the filename's extension. Generates the final
 * filename itself, so the original filename (and any path traversal or
 * double-extension trick inside it) is never used.
 *
 * Returns the new filename on success, or null on any validation failure.
 */
function secure_image_upload(array $file, string $destinationFolder): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Reject empty or oversized files.
    if (empty($file['size']) || $file['size'] > MAX_UPLOAD_BYTES) {
        return null;
    }

    // Extension must be one of the allowed ones (case-insensitive).
    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
    if (!isset(ALLOWED_IMAGE_TYPES[$extension])) {
        return null;
    }

    // Verify the upload really is an image by reading its actual content
    // (getimagesize() parses the file's binary header — a renamed .php
    // file will fail this check even if its extension says .jpg).
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false || !in_array($imageInfo['mime'], ALLOWED_IMAGE_TYPES[$extension], true)) {
        return null;
    }

    if (!is_uploaded_file($file['tmp_name'])) {
        return null;
    }

    if (!is_dir($destinationFolder)) {
        mkdir($destinationFolder, 0755, true);
    }

    // Filename is always generated here — the original name/extension the
    // client supplied is discarded except for the whitelisted extension,
    // so it can never contain path traversal sequences or a double
    // extension like "shell.php.jpg".
    $newFileName = bin2hex(random_bytes(8)) . '_' . time() . '.' . $extension;
    $destinationPath = rtrim($destinationFolder, '/') . '/' . $newFileName;

    return move_uploaded_file($file['tmp_name'], $destinationPath) ? $newFileName : null;
}

// ─── Pagination ────────────────────────────────────────────────────────────────

/** Build pagination data array. */
function paginate(int $total, int $perPage, int $currentPage): array
{
    $totalPages = (int) ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages ?: 1));
    return [
        'total'       => $total,
        'per_page'    => $perPage,
        'current'     => $currentPage,
        'total_pages' => $totalPages,
        'offset'      => ($currentPage - 1) * $perPage,
        'has_prev'    => $currentPage > 1,
        'has_next'    => $currentPage < $totalPages,
    ];
}

// ─── Input Sanitisation ────────────────────────────────────────────────────────

/** Sanitise a plain-text input (trim + strip tags). */
function clean(string $input): string
{
    return trim(strip_tags($input));
}

/** Validate Moroccan / international phone numbers. */
function valid_phone(string $phone): bool
{
    return (bool) preg_match('/^[0-9\+\-\s\(\)]{7,20}$/', $phone);
}
