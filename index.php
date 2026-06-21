<?php



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
<header class="header">
  <div class="top-header">
    <div class="logo">
      <div class="logo-icon"><i class="fas fa-shopping-cart"></i></div>
      <div><h1>Tech Shop</h1></div>
    </div>
    <div class="search-box">
      <input type="text" placeholder="Search for latest tech, gaming gear..." />
      <button>SEARCH</button>
    </div>
    <div class="header-actions">
      <div class="account">
        <span>Hello, Sign in</span>
        <div class="account-link">Account &amp; Lists <i class="fas fa-chevron-down"></i></div>
      </div>
      <div class="cart" id="cart">
        <i class="fas fa-shopping-bag"></i>
        <span class="cart-badge" id="cart-badge">0</span>
      </div>
    </div>
  </div>

  <nav class="navbar">
    <div class="categories">
      <div class="category-btn">
        <i class="fas fa-bars"></i>
        <span>Browse Categories</span>
        <i class="fas fa-chevron-down" style="font-size:10px"></i>
      </div>
      <ul class="dropdown-menu">
        <li><a href="#chair-section">Gaming Chairs</a></li>
        <li><a href="#desk-section">Gaming Desks</a></li>
        <li><a href="#controllers-section">Controllers</a></li>
        <li><a href="#playStation-section">PlayStation Gaming</a></li>
        <li><a href="#mous-section">Mouse Gaming</a></li>
        <li><a href="#ecran-section">Écran</a></li>
      </ul>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="nav-links" id="nav-links">
      <a href="index.php">Home</a>
      <a href="shop.php">Shop</a>
      <a href="deals.php">Deals</a>
      <a href="#">Contact</a>
    </div>
    <div class="support">
      <i class="fas fa-headset"></i>
      <span>24/7 Support: +212 600 000 000</span>
    </div>
  </nav>
</header>


<!-- main content -->
<main>
 
  <!-- ═══════════════════════ CART ═══════════════════════ -->
  <section>
    <!-- <div class="carts">
      <h2 class="cart-title">Your Cart</h2>
      <div class="cart-content"></div>
      <div class="total">
        <div class="total-title">Total</div>
        <div class="total-price">0 dh</div>
        <button class="btn-buy">Buy Now</button>
        <i class="fa-solid fa-xmark" id="cart-close"></i>
      </div>
    </div> -->

    <div class="carts">
  <h2 class="cart-title">
    Your Cart
    <i class="fa-solid fa-xmark" id="cart-close"></i> </h2>
  
  <div class="cart-content"></div> 
  
  <div class="cart-footer">
    <div class="total">
      <div class="total-title">Total</div>
      <div class="total-price">0 DH</div> </div>
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
                class="main-product-img"
              />
              <h4>Logitech G102 LightSync</h4>
              <p>199.00 dh</p>
              <div class="color-dots">
                <span
                  class="dot pink"
                  style="background-color: #55a0e7"
                  data-image="src/assets/products/mous/souri-2.webp"
                ></span>
                <span
                  class="dot gold"
                  style="background-color: #f4f4f8"
                  data-image="src/assets/products/mous/souri-3.webp"
                ></span>
                <span
                  class="dot blue"
                  style="background-color: #141414"
                  data-image="src/assets/products/mous/souri-1.webp"
                ></span>
              </div>
            </div>

            <div class="swiper-slide product-card">
              <span class="badge">New!</span>
              <img
                src="src/assets/products/keyabord/Keyboard-1.webp"
                alt="Redragon K617 Fizz"
                class="main-product-img"
              />
              <h4>Redragon K617 Fizz</h4>
              <p>299.00 dh</p>
              <div class="color-dots">
                <span
                  class="dot pink"
                  style="background-color: #f9f9f9"
                  data-image="src/assets/products/keyabord/Keyboard-2.webp"
                ></span>
                <span
                  class="dot"
                  style="background-color: #141414"
                  data-image="src/assets/products/keyabord/Keyboard-1.webp"
                ></span>
              </div>
            </div>

            <div class="swiper-slide product-card">
              <span class="badge">New!</span>
              <img
                src="src/assets/products/controllers/gamesir-1.webp"
                alt="GameSir Nova Lite 2.4G Wireless"
                class="main-product-img"
              />
              <h4>GameSir Nova Lite 2.4G Wireless</h4>
              <p>399.00 dh</p>
              <div class="color-dots">
                <span
                  class="dot pink"
                  style="background-color: #2ac068"
                  data-image="src/assets/products/controllers/gamesir-2.webp"
                ></span>
                <span
                  class="dot gold"
                  data-image="src/assets/products/controllers/gamesir-3.webp"
                  style="background-color: #ffb2bf"
                ></span>
                <span
                  class="dot gold"
                  data-image="src/assets/products/controllers/gamesir-4.webp"
                  style="background-color: #4c9be6"
                ></span>
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
      <div class="text-section"><h3>Home delivery</h3><p>Your order has been prepared</p></div>
    </div>
    <div class="card card-green">
      <div class="icon-section"><i class="fa-regular fa-heart"></i></div>
      <div class="text-section"><h3>Satisfaction guaranteed</h3><p>Always there to ensure you</p></div>
    </div>
    <div class="card card-green">
      <div class="icon-section"><i class="fa-solid fa-headset"></i></div>
      <div class="text-section"><h3>Customer support</h3><p>We are at your service all week</p></div>
    </div>
  </section>

</main>

<!-- ═══════════════════════ FOOTER ════════════════════════ -->
<footer class="footer">
  <div class="footer-container">
    <div class="footer-section brand-info">
      <h2 class="brand-name">Tech shop</h2>
      <p class="description">Lorem ipsum, dolor sit amet consectetur adipisicing elit.</p>
      <div class="contact-item">
        <div class="icon-box"><i class="fas fa-phone-alt"></i></div>
        <span>+212648757954</span>
      </div>
      <p class="hours">Mon - Sat, 9am - 8pm</p>
      <div class="contact-item">
        <div class="icon-box"><i class="fas fa-envelope"></i></div>
        <span>techShop@gmail.com</span>
      </div>
    </div>
    <div class="footer-section shop-links">
      <h2 class="footer-title">Shop</h2>
      <ul>
        <li><a href="shop.php?category=mouse">Mouse</a></li>
        <li><a href="shop.php?category=chair">Chair</a></li>
        <li><a href="shop.php?category=desk">Desk</a></li>
        <li><a href="shop.php?category=controller">Controllers</a></li>
        <li><a href="shop.php?category=ecran">Écran</a></li>
      </ul>
    </div>
    <div class="footer-section newsletter">
      <h2 class="footer-title">Join Our Newsletter</h2>
      <div class="subscribe-box">
        <input type="email" placeholder="Enter Your Email" />
        <button>Subscribe</button>
      </div>
      <div class="social-payment">
        <div class="social-icons">
          <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
          <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
        </div>
        <div class="payment-methods">
          <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" />
          <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" />
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>2026 <strong>Tech shop</strong>. All rights reserved</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>
<!-- <script src="js/header.js"></script> -->
 <script src="js/main.js"></script>
</body>
</html>