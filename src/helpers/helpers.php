<?php


function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}


function price(float $amount, string $currency = 'DH'): string
{
    return number_format($amount, 0) . ' ' . $currency;
}



/**
 * Returns the relative web path to a product image.
 *
 * @param string $category  e.g. 'chair', 'mouse'
 * @param string $slot      'main' | 'hover' | 'shop'
 * @param string $filename  e.g. 'chair-1.webp'
 */
function asset_url(string $category, string $slot, string $filename): string
{
    if (empty($filename)) return '';

    // Base path for each category
    $base = '/src/assets/products/';
    // $base = __DIR__ . '/src/assets/products';

    $map = [
        'chair'       => [
            'main'  => $base . 'chair/chair_home/',
            'hover' => $base . 'chair/chair_home/',
            'shop'  => $base . 'chair/chair_shop/',
        ],
        'desk'        => [
            'main'  => $base . 'desk/desk_home/',
            'hover' => $base . 'desk/desk_home/',
            'shop'  => $base . 'desk/desk_shop/',
        ],
        'controller'  => [
            'main'  => $base . 'controllers/controllers_home/',
            'hover' => $base . 'controllers/controllers_home/',
            'shop'  => $base . 'controllers/controllers_shop/',
        ],
        'playstation' => [
            'main'  => $base . 'PlayStation/playStation_home/',
            'hover' => $base . 'PlayStation/playStation_home/',
            'shop'  => $base . 'PlayStation/playStation_shop/',
        ],
        'mouse'       => [
            'main'  => $base . 'mous/mous_home/',
            'hover' => $base . 'mous/mous_home/',
            'shop'  => $base . 'mous/mous_shop/',
        ],
        'ecran'       => [
            'main'  => $base . 'ecran/ecran_home/',
            'hover' => $base . 'ecran/ecran_home/',
            'shop'  => $base . 'ecran/ecran_shop/',
        ],
        'keyboard'    => [
            'main'  => $base . 'keyabord/',
            'hover' => $base . 'keyabord/',
            'shop'  => $base . 'keyabord/',
        ],
        'headset'     => [
            'main'  => $base . 'headset/',
            'hover' => $base . 'headset/',
            'shop'  => $base . 'headset/',
        ],
    ];

    if (!isset($map[$category][$slot])) return '';

    return $map[$category][$slot] . $filename;
}


function upload_folder(string $category): string
{
    $base = '/src/assets/products/';

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


function delete_image(string $folder, string $filename): bool
{
    if (empty($folder) || empty($filename)) return false;

    $path = rtrim($folder, '/') . '/' . $filename;

    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}
