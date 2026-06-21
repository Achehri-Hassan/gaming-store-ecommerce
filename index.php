<?php


session_start();

require_once 'src/config/connection.php';
require_once 'src/models/ProductModel.php';
require_once 'src/helpers/helpers.php';



$products = selectGroupedByCategory();



?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TECH SHOP</title>
  <link rel="stylesheet" href="css/components/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
</head>

<body>

  <!-- ═══════════════════════ HEADER ═══════════════════════ -->

 <?php include "src/views/layouts/header.php"; ?>


  <!-- main content -->
  <main>

    <!-- ═══════════════════════ CART ═══════════════════════ -->
    <section>

      <div class="carts">
        <h2 class="cart-title">
          Your Cart
          <i class="fa-solid fa-xmark" id="cart-close"></i>
        </h2>

        <div class="cart-content"></div>

        <div class="cart-footer">
          <div class="total">
            <div class="total-title">Total</div>
            <div class="total-price">0 DH</div>
          </div>
          <button type="button" class="btn-buy">Checkout</button>
        </div>
      </div>

    </section>

    <!-- ═══════════════════════ HERO SLIDER ════════════════ -->
    <section>
      <div class="back_swiper">
        <div class="swiper myHeroSwiper">
          <div class="swiper-wrapper">
            <div class="swiper-slide hero-slide" style="background-image:url('src/assets/banners_hero_section/man.jpg');">
              <div class="overlay"></div>
              <div class="content">
                <span class="badges">Next Gen Gear</span>
                <h1 class="title">DOMINATE THE <br /><span class="blue">GAMING</span> WORLD</h1>
                <p class="desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                <div class="buttons">
                  <a href="#" class="btn primary"><span>SHOP NOW</span></a>
                  <a href="#" class="btn outline"><span>DEALS</span></a>
                </div>
              </div>
            </div>
            <div class="swiper-slide hero-slide" style="background-image:url('src/assets/banners_hero_section/woman.jpg');">
              <div class="overlay"></div>
              <div class="content">
                <span class="badges">ULTRA PERFORMANCE</span>
                <h1 class="title">DOMINATE THE <br /><span class="blue">GAMING</span> WORLD</h1>
                <p class="desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                <a href="#" class="btn white"><span>EXPLORE MORE</span></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- WhatsApp float -->
    <a href="https://wa.me/+212648767954" class="whatsapp-fixed" target="_blank" rel="noopener">
      <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" />
    </a>


    <!-- products home -->
    <section class="product-slider">
      <h2 class="slider-title">Gaming Accessories</h2>
      <div class="swiper productSwiper">
        <div class="swiper-wrapper">
          <!-- slide -->
          <div class="swiper-slide product-card">
            <span class="badge">New!</span>
            <img
              src="src/assets/products/mous/souri-1.webp"
              alt="Logitech G102 LightSync"
              class="main-product-img" />
            <h4>Logitech G102 LightSync</h4>
            <p>199.00 dh</p>
            <div class="color-dots">
              <span
                class="dot pink"
                style="background-color: #55a0e7"
                data-image="src/assets/products/mous/souri-2.webp"></span>
              <span
                class="dot gold"
                style="background-color: #f4f4f8"
                data-image="src/assets/products/mous/souri-3.webp"></span>
              <span
                class="dot blue"
                style="background-color: #141414"
                data-image="src/assets/products/mous/souri-1.webp"></span>
            </div>
          </div>

          <div class="swiper-slide product-card">
            <span class="badge">New!</span>
            <img
              src="src/assets/products/keyabord/Keyboard-1.webp"
              alt="Redragon K617 Fizz"
              class="main-product-img" />
            <h4>Redragon K617 Fizz</h4>
            <p>299.00 dh</p>
            <div class="color-dots">
              <span
                class="dot pink"
                style="background-color: #f9f9f9"
                data-image="src/assets/products/keyabord/Keyboard-2.webp"></span>
              <span
                class="dot"
                style="background-color: #141414"
                data-image="src/assets/products/keyabord/Keyboard-1.webp"></span>
            </div>
          </div>

          <div class="swiper-slide product-card">
            <span class="badge">New!</span>
            <img
              src="src/assets/products/controllers/gamesir-1.webp"
              alt="GameSir Nova Lite 2.4G Wireless"
              class="main-product-img" />
            <h4>GameSir Nova Lite 2.4G Wireless</h4>
            <p>399.00 dh</p>
            <div class="color-dots">
              <span
                class="dot pink"
                style="background-color: #2ac068"
                data-image="src/assets/products/controllers/gamesir-2.webp"></span>
              <span
                class="dot gold"
                data-image="src/assets/products/controllers/gamesir-3.webp"
                style="background-color: #ffb2bf"></span>
              <span
                class="dot gold"
                data-image="src/assets/products/controllers/gamesir-4.webp"
                style="background-color: #4c9be6"></span>
            </div>
          </div>
        </div>

        <!-- arrows -->
        <div class="swiper-controls">
          <div class="swiper-button-prev custom-prev"></div>
          <div class="swiper-button-next custom-next"></div>
        </div>
      </div>

    </section>

    <!-- ═══════════════════ CHAIR SECTION ══════════════════ -->
    <section class="tech-collection" id="chair-section">
      <div class="collection-container">
        <div class="collection-info">
          <h2>OUR COLLECTION OF DEVICES</h2>
          <p>
            Our specialists at Techspace.ma have selected a wide choice of
            computer devices for you.
          </p>
          <a href="#" class="see-more-btn">SHOP NOW</a>
        </div>

        <div class="collection-slider-wrapper chair">
          <div class="swiper mySwiper">
            <div class="swiper-wrapper">
              <?php foreach ($products['chair'] ?? [] as $product): ?>
                <?php include __DIR__ . '/src/views/partials/product_card.php'; ?>
              <?php endforeach; ?>
            </div>


            <div class="swiper-button-next next"></div>
            <div class="swiper-button-prev prev"></div>
          </div>
        </div>
      </div>
    </section>


    <!-- ═══════════════════ DESK SECTION ═══════════════════ -->
    <section class="tech-collection desks" id="desk-section">
      <div class="section-header">
        <h2 class="section-title">DISCOVER OUR GAMING DESk</h2>

        <div class="section-controls">
          <button class="desk-prev">
            <i class="fa-solid fa-chevron-left"></i>
          </button>
          <button class="desk-next">
            <i class="fa-solid fa-chevron-right"></i>
          </button>
        </div>
      </div>

      <div class="product-grid swiper deskSwiper">
        <!-- CARD -->
        <div class="swiper-wrapper">
          <?php foreach ($products['desk'] ?? [] as $product): ?>
            <?php include __DIR__ . '/src/views/partials/product_card.php'; ?>
          <?php endforeach; ?>
        </div>

      </div>
    </section>



    <!-- ════════════════ CONTROLLERS SECTION ═══════════════ -->


    <section class="tech-collection controllers" id="controllers-section">
      <div class="section-header">
        <h2 class="section-title">ULTIMATE GRAPHICS POWER</h2>

        <div class="section-controls">
          <button class="Controller-prev">
            <i class="fa-solid fa-chevron-left"></i>
          </button>
          <button class="Controller-next">
            <i class="fa-solid fa-chevron-right"></i>
          </button>
        </div>
      </div>

      <div class="product-grid swiper ControllerSwiper">
        <div class="swiper-wrapper">
          <?php foreach ($products['controller'] ?? [] as $product): ?>
            <?php include __DIR__ . '/src/views/partials/product_card.php'; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ═══════════════ PLAYSTATION SECTION ════════════════ -->
    <section class="tech-collection" id="playStation-section">
      <div class="section-header nj">
        <h2 class="section-title">PLAYSTATION GAMING</h2>
      </div>
      <div class="product-grid swiper playstation-swiper">
        <div class="swiper-wrapper">
          <?php foreach ($products['playstation'] ?? [] as $product): ?>
            <?php include __DIR__ . '/src/views/partials/product_card.php'; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ════════════════ MOUSE SECTION ═════════════════════ -->
    <section class="tech-collection mous" id="mous-section">
      <div class="section-header nj">
        <h2 class="section-title">ULTIMATE MOUSE POWER</h2>
        <div class="section-controls mous_button">
          <button class="mous-prev"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="mous-next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>
      <div class="product-grid swiper mous-swiper">
        <div class="swiper-wrapper">
          <?php foreach ($products['mouse'] ?? [] as $product): ?>
            <?php include __DIR__ . '/src/views/partials/product_card.php'; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ════════════════ ECRAN SECTION ═════════════════════ -->
    <section class="tech-collection Ecran" id="ecran-section">
      <div class="section-header nj">
        <h2 class="section-title">ULTIMATE GRAPHICS POWER</h2>
        <div class="section-controls ecran_button">
          <button class="ecran-prev"><i class="fa-solid fa-chevron-left"></i></button>
          <button class="ecran-next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
      </div>
      <div class="product-grid swiper ecran-swiper">
        <div class="swiper-wrapper">
          <?php foreach ($products['ecran'] ?? [] as $product): ?>
            <?php include __DIR__ . '/src/views/partials/product_card.php'; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- ════════════════ FEATURES BAR ══════════════════════ -->
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


  <!-- ═══════════════════════ FOOTER ════════════════════════ -->
  <?php include "src/views/layouts/footer.php"; ?>

  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script src="js/script.js"></script>
  <script src="js/header.js"></script>
  <script src="js/main.js"></script>
</body>

</html>