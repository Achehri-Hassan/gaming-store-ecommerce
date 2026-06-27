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

/** Validate POST CSRF token — dies on failure. */
function verify_csrf(): void
{
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals(csrf_token(), $_POST['csrf_token'])
    ) {
        http_response_code(403);
        die('Invalid CSRF token. Please go back and try again.');
    }
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

/** Return the filesystem upload folder for a given category. */
function upload_folder(string $category): string
{
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
