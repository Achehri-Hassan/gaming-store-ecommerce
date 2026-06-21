<?php

/** @var array $product */

$main_src = asset_url(
  $product['category'],
  'main',
  $product['main_image'] ?? ''
);

$hover_src = asset_url(
  $product['category'],
  'hover',
  $product['hover_image'] ?? ''
);

?>

<div class="swiper-slide">

  <a href="shop-details.php?id=<?= (int)$product['id'] ?>">

    <div class="p-card">

      <div class="p-img">

        <img
          src="<?= $main_src ?>"
          class="main-img"
          alt="<?= h($product['name']) ?>"
          loading="lazy">

        <?php if (!empty($product['hover_image'])): ?>

          <img
            src="<?= $hover_src ?>"
            class="hover-img"
            alt="<?= h($product['name']) ?>"
            loading="lazy">

        <?php endif; ?>

      </div>

      <div class="p-details">

        <span class="p-brand">
          <?= h($product['brand']) ?>
        </span>

        <h3>
          <?= h($product['name']) ?>
        </h3>

        <div class="p-shop">
          <div class="p-price">
            <?= price($product['price'], $product['currency']) ?>
          </div>
          <i class="fa-solid fa-bag-shopping add-to-cart-btn" data-id="<?= (int)$product['id'] ?>"></i>
        </div>

        <div class="p-stock">

          <span class="dott"></span>

          <?= h($product['status']) ?>

        </div>

      </div>

    </div>

  </a>

</div>