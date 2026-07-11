<?php

session_start();
require_once 'src/config/connection.php';
require_once 'src/models/ProductModel.php';
require_once 'src/helpers/helpers.php';


$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
  header('Location: index.php');
  exit;
}

$product = selectById($id);
$gallery = selectProductImages($id);
$related = selectRelated($product['category'], $id);


$main_src  = asset_url($product['category'], 'main', $product['main_image']);
$hover_src = asset_url($product['category'], 'hover', $product['hover_image'] ?? '');


$all_thumbs = [];


if (!empty($product['main_image'])) {
  $all_thumbs[] = $main_src;
}


if (!empty($product['hover_image'])) {
  $all_thumbs[] = $hover_src;
}


foreach ($gallery as $img) {
  $all_thumbs[] = asset_url($product['category'], 'shop', $img);
}

$all_thumbs = array_values(array_unique($all_thumbs));




?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($product['name']) ?> – Tech Shop</title>
  <link rel="stylesheet" href="css/shop.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

  <!-- ═══════ HEADER ═══════ -->
  <?php include "src/views/layouts/header.php" ?>

  <!-- ═══════ CART SIDEBAR ═══════ -->
  <?php include "src/views/layouts/cart.php" ?>

  <!-- ═══════ PRODUCT DETAIL ═══════ -->
  <main>
    <div class="details-wrapper">

      <!-- Product layout -->
      <div class="product-page">

        <!-- 1. Thumbnail column -->
        <div class="thumb-list">
          <?php foreach ($all_thumbs as $i => $src): ?>
            <img src="<?= h($src) ?>" class="thumb <?= $i === 0 ? 'active' : '' ?>" alt="Product image <?= $i + 1 ?>"
              data-src="<?= h($src) ?>">
          <?php endforeach; ?>
        </div>

        <!-- 2. Main image -->
        <div class="main-view">
          <img id="main-img" src="<?= h($all_thumbs[0] ?? '') ?>" alt="<?= h($product['name']) ?>">
        </div>

        <!-- 3. Product info -->
        <div class="product-info">
          <span class="p-brand-tag"><?= h($product['brand']) ?></span>
          <h1><?= h($product['name']) ?></h1>

          <div class="stock-badge">
            <span class="stock-dot"></span>
            <?= h($product['status']) ?>
          </div>

          <?php if (!empty($product['description'])): ?>
            <div class="description-block">
              <h3>Description</h3>
              <p><?= h($product['description']) ?></p>
            </div>
          <?php endif; ?>

          <hr class="divider">

          <div class="price-block">
            <div class="price-label">Price</div>
            <div class="price-value">
              <?= number_format((float)$product['price'], 0) ?>
              <span class="price-currency"><?= h($product['currency']) ?></span>
            </div>
          </div>

          <div class="action-buttons">
            <button class="btn-add-cart add-to-cart-btn" data-id="<?= (int)$product['id'] ?>">
              <i class="fas fa-shopping-bag"></i> Add to Cart
            </button>
            <a href="https://wa.me/+212648767954?text=Hi, I'm interested in: <?= urlencode($product['name']) ?>"
              class="btn-whatsapp" target="_blank" rel="noopener">
              <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
          </div>

          <hr class="divider">


        </div>

      </div>
    </div>

    <!-- ═══════ RELATED PRODUCTS ═══════ -->
    <?php if (!empty($related)): ?>
      <div class="related-section">
        <h2>More <?= ucfirst(h($product['category'])) ?>s</h2>
        <div class="related-grid">
          <?php foreach ($related as $rel):
            $rel_main  = asset_url($rel['category'], 'main',  $rel['main_image']);
            $rel_hover = asset_url($rel['category'], 'hover', $rel['hover_image'] ?? '');
          ?>
            <a href="shop-details.php?id=<?= (int)$rel['id'] ?>" style="text-decoration:none;">
              <div class="p-card">
                <div class="p-img">
                  <img src="<?= h($rel_main) ?>" class="main-img" alt="<?= h($rel['name']) ?>" loading="lazy">
                  <?php if ($rel_hover): ?>
                    <img src="<?= h($rel_hover) ?>" class="hover-img" alt="<?= h($rel['name']) ?>" loading="lazy">
                  <?php endif; ?>
                </div>
                <div class="p-details">
                  <span class="p-brand"><?= h($rel['brand']) ?></span>
                  <h3><?= h($rel['name']) ?></h3>
                  <div class="p-shop">
                    <div class="p-price"><?= number_format((float)$rel['price'], 0) ?> <?= h($rel['currency']) ?></div>
                    <i class="fa-solid fa-bag-shopping add-to-cart-btn" data-id="<?= (int)$rel['id'] ?>"></i>
                  </div>
                  <div class="p-stock"><span class="dott"></span><?= h($rel['status']) ?></div>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Features bar -->
    <section class="features-container">
      <div class="card card-blue">
        <div class="icon-section"><i class="fa-solid fa-truck-fast"></i></div>
        <div class="text-section">
          <h3>Home delivery</h3>
          <p>Your order has been prepared</p>
        </div>
      </div>
      <div class="card card-green">
        <div class="icon-section"><i class="fa-regular fa-heart"></i></div>
        <div class="text-section">
          <h3>Satisfaction guaranteed</h3>
          <p>Always there to ensure you</p>
        </div>
      </div>
      <div class="card card-green">
        <div class="icon-section"><i class="fa-solid fa-headset"></i></div>
        <div class="text-section">
          <h3>Customer support</h3>
          <p>We are at your service all week</p>
        </div>
      </div>
    </section>
  </main>

  <!-- ═══════ FOOTER ═══════ -->
  <?php include "src/views/layouts/footer.php"  ?>



  <script src="js/header.js"></script>
  <script src="js/main.js"></script>
  <script src="js/shop.js"></script>

</body>

</html>