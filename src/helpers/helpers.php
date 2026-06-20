<?php
// src/helpers/helpers.php

// ──────────────────────────────────────────────────────────────
// h() — safe HTML output (prevents XSS)
// ──────────────────────────────────────────────────────────────
function h(?string $value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ──────────────────────────────────────────────────────────────
// price() — format price with currency
// ──────────────────────────────────────────────────────────────
function price(float $amount, string $currency = 'DH'): string {
    return number_format($amount, 2) . ' ' . h($currency);
}

// ──────────────────────────────────────────────────────────────
// asset_url() — build image path per category
//
// BUG FIX: product_card.php was calling asset_url(..., 'hover', ...)
// but the condition here checked for 'home' — 'hover' never matched,
// so it always fell to the else branch and used the wrong folder.
//
// The card now passes 'home' for main and 'hover' for hover image.
// We align the map to use index 0 = main image folder (used by main_image),
// index 1 = hover image folder (used by gallery 'home' type images).
// ──────────────────────────────────────────────────────────────
function asset_url(string $category, string $slot, string $filename): string {
    if ($filename === '' || $filename === null) return '';

    // $slot = 'main'  → the product's main_image column (homepage card, left img)
    // $slot = 'hover' → the gallery home-type image (homepage card hover effect)
    // $slot = 'shop'  → gallery shop-type images (product detail page)
    $map = [
        //  category       =>  [ main_folder,                         hover_folder,                        shop_folder ]
        'chair'       => ['assets/products/chairs/chairs_home/',  'assets/products/chairs/chairs_home/', 'assets/products/chairs/chairs_shop/'],
        'desk'        => ['assets/products/desks/desks_home/',    'assets/products/desks/desks_home/',   'assets/products/desks/desks_shop/'],
        'controller'  => ['assets/products/controllers/ctrl_home/', 'assets/products/controllers/ctrl_home/', 'assets/products/controllers/ctrl_shop/'],
        'playstation' => ['assets/products/playstation/ps_home/', 'assets/products/playstation/ps_home/', 'assets/products/playstation/ps_shop/'],
        'mouse'       => ['assets/products/mous/mous_home/',      'assets/products/mous/mous_home/',     'assets/products/mous/mous_shop/'],
        'ecran'       => ['assets/products/ecran/ecran_home/',    'assets/products/ecran/ecran_home/',   'assets/products/ecran/ecran_shop/'],
    ];

    $folders = $map[$category] ?? ['assets/products/', 'assets/products/', 'assets/products/'];

    $folder = match($slot) {
        'main'  => $folders[0],
        'hover' => $folders[1],
        'shop'  => $folders[2],
        default => $folders[0],
    };

    return h($folder . $filename);
}