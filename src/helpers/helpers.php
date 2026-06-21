<?php

function h(?string $value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function price(float $amount, string $currency = 'DH'): string
{
    return number_format($amount, 0) . ' ' . $currency;
}

function asset_url(string $category, string $slot, string $filename): string
{
    if (empty($filename)) {
        return '';
    }

  $map = [
    'chair' => [
        'src/assets/products/chair/chair_home/',
        'src/assets/products/chair/chair_home/',
        'src/assets/products/chair/chair_shop/',
    ],
    'desk' => [
        'src/assets/products/desk/desk_home/',
        'src/assets/products/desk/desk_home/',
        'src/assets/products/desk/desk_shop/',
    ],
    'controller' => [
        'src/assets/products/controllers/controllers_home/',
        'src/assets/products/controllers/controllers_home/',
        'src/assets/products/controllers/controllers_shop/',
    ],
    'playstation' => [
        'src/assets/products/PlayStation/playStation_home/',
        'src/assets/products/PlayStation/playStation_home/',
        'src/assets/products/PlayStation/playStation_shop/',
    ],
    'mouse' => [
        'src/assets/products/mous/mous_home/',
        'src/assets/products/mous/mous_home/',
        'src/assets/products/mous/mous_shop/',
    ],
    'ecran' => [
        'src/assets/products/ecran/ecran_home/',
        'src/assets/products/ecran/ecran_home/',
        'src/assets/products/ecran/ecran_shop/',
    ],
];

    if (!isset($map[$category])) {
        return '';
    }

    $folder = match ($slot) {

        'main'  => $map[$category][0],

        'hover' => $map[$category][1],

        'shop'  => $map[$category][2],

        default => $map[$category][0],

    };

    return $folder . $filename;
}