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
            'assets/products/chair/chair_home/',
            'assets/products/chair/chair_home/',
            'assets/products/chair/chair_shop/',
        ],

        'desk' => [
            'assets/products/desk/desk_home/',
            'assets/products/desk/desk_home/',
            'assets/products/desk/desk_shop/',
        ],

        'controller' => [
            'assets/products/controllers/controllers_home/',
            'assets/products/controllers/controllers_home/',
            'assets/products/controllers/controllers_shop/',
        ],

        'playstation' => [
            'assets/products/PlayStation/playStation_home/',
            'assets/products/PlayStation/playStation_home/',
            'assets/products/PlayStation/playStation_shop/',
        ],

        'mouse' => [
            'assets/products/mous/mous_home/',
            'assets/products/mous/mous_home/',
            'assets/products/mous/mous_shop/',
        ],

        'ecran' => [
            'assets/products/ecran/ecran_home/',
            'assets/products/ecran/ecran_home/',
            'assets/products/ecran/ecran_shop/',
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