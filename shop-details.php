<?php

session_start();
require_once 'src/config/connection.php';
require_once 'src/models/ProductModel.php';
require_once 'src/helpers/helpers.php';

// ── Get product ID from URL ──────────────────
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

$product = selectById($id);

if (!$product) {
    header('Location: index.php');
    exit;
}

// ── Build image list: main + gallery ─────────
$main_src  = asset_url($product['category'], 'main',  $product['main_image']);
$hover_src = asset_url($product['category'], 'hover', $product['hover_image'] ?? '');

$gallery_images = [];
if (!empty($product['gallery'])) {
    foreach ($product['gallery'] as $img) {
        $gallery_images[] = asset_url($product['category'], 'shop', $img);
    }
}

// All thumbnails: main first, then gallery
$all_thumbs = array_filter(array_merge([$main_src, $hover_src], $gallery_images));
$all_thumbs = array_values(array_unique($all_thumbs));

// ── Related products (same category, exclude current) ──
$conn = getConnection();
$rel_stmt = $conn->prepare("
    SELECT id, category, brand, name, slug, price, currency, status, main_image, hover_image
    FROM products
    WHERE category = :cat AND id != :id AND is_active = 1
    ORDER BY RAND()
    LIMIT 8
");
$rel_stmt->execute([':cat' => $product['category'], ':id' => $id]);
$related = $rel_stmt->fetchAll();
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
          <?php if (isset($_SESSION['user_id'])): ?>
            <span>Hello, <?= h($_SESSION['username']) ?></span>
            <div class="account-link">My Account <i class="fas fa-chevron-down" style="font-size:12px;margin-left:5px;"></i></div>
            <div class="account-dropdown">
              <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php"><i class="fas fa-user-shield"></i> Dashboard</a>
              <?php endif; ?>
              <a href="logout.php" class="logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
          <?php else: ?>
            <span>Hello, Sign in</span>
            <div class="account-link">
              <a href="login.php" style="color:inherit;text-decoration:none;">Account &amp; Lists</a>
              <i class="fas fa-chevron-down" style="font-size:12px;margin-left:5px;"></i>
            </div>
            <div class="account-dropdown">
              <a href="login.php"><i class="fas fa-sign-in-alt"></i> Sign In</a>
              <a href="register.php"><i class="fas fa-user-plus"></i> Create Account</a>
            </div>
          <?php endif; ?>
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
          <i class="fas fa-bars"></i><span>Browse Categories</span>
          <i class="fas fa-chevron-down" style="font-size:10px"></i>
        </div>
        <ul class="dropdown-menu">
          <li><a href="index.php#chair-section">Gaming Chairs</a></li>
          <li><a href="index.php#desk-section">Gaming Desks</a></li>
          <li><a href="index.php#controllers-section">Controllers</a></li>
          <li><a href="index.php#playStation-section">PlayStation Gaming</a></li>
          <li><a href="index.php#mous-section">Mouse Gaming</a></li>
          <li><a href="index.php#ecran-section">Écran</a></li>
        </ul>
      </div>
      <button class="hamburger" id="hamburger"><i class="fa-solid fa-bars"></i></button>
      <div class="nav-links" id="nav-links">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="#">Deals</a>
        <a href="#">Contact</a>
      </div>
      <div class="support">
        <i class="fas fa-headset"></i>
        <span>24/7 Support: +212 600 000 000</span>
      </div>
    </nav>
  </header>

  <!-- ═══════ CART SIDEBAR ═══════ -->
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

  <!-- ═══════ PRODUCT DETAIL ═══════ -->
  <main>
    <div class="details-wrapper">

      <!-- Product layout -->
      <div class="product-page">

        <!-- 1. Thumbnail column -->
        <div class="thumb-list">
          <?php foreach ($all_thumbs as $i => $src): ?>
            <img
              src="<?= h($src) ?>"
              class="thumb <?= $i === 0 ? 'active' : '' ?>"
              alt="Product image <?= $i + 1 ?>"
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
              <img src="<?= h($rel_main) ?>"  class="main-img"  alt="<?= h($rel['name']) ?>" loading="lazy">
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

  <!-- ═══════ FOOTER ═══════ -->
  <?php include "src/views/layouts/footer.php"  ?>

 

  <!-- WhatsApp float -->
  <!-- <a href="https://wa.me/+212648767954" class="whatsapp-fixed" target="_blank" rel="noopener">
    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp">
  </a> -->

  <script>
    // ── Thumbnail switcher ──────────────────────
    const mainImg   = document.getElementById('main-img');
    const thumbs    = document.querySelectorAll('.thumb');

    thumbs.forEach(thumb => {
      thumb.addEventListener('click', () => {
        // Fade out
        mainImg.classList.add('fading');
        setTimeout(() => {
          mainImg.src = thumb.dataset.src;
          mainImg.classList.remove('fading');
        }, 200);
        // Active state
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
      });
    });

  

    // ── Cart logic ──────────────────────────────
    const cartIcon      = document.getElementById('cart');
    const cartContainer = document.querySelector('.carts');
    const cartCloseBtn  = document.getElementById('cart-close');
    const cartBadge     = document.getElementById('cart-badge');
    const cartContent   = document.querySelector('.cart-content');
    const totalPriceEl  = document.querySelector('.total-price');

    if (cartIcon)     cartIcon.addEventListener('click', () => cartContainer.classList.add('active'));
    if (cartCloseBtn) cartCloseBtn.addEventListener('click', () => cartContainer.classList.remove('active'));

    fetchCart({ action: 'get' });

    document.addEventListener('click', e => {
      // Add to cart
      if (e.target.classList.contains('add-to-cart-btn')) {
        e.preventDefault();
        const pid = e.target.dataset.id;
        fetchCart({ action: 'add', product_id: parseInt(pid) });
      
        cartContainer.classList.add('active');
      }
      // Remove
      if (e.target.classList.contains('cart-remove-item')) {
        fetchCart({ action: 'remove', product_id: parseInt(e.target.dataset.id) });
      }
      // Quantity +
      if (e.target.classList.contains('cart-qty-plus')) {
        fetchCart({ action: 'update_quantity', product_id: parseInt(e.target.dataset.id), quantity: parseInt(e.target.dataset.qty) + 1 });
      }
      // Quantity -
      if (e.target.classList.contains('cart-qty-minus')) {
        fetchCart({ action: 'update_quantity', product_id: parseInt(e.target.dataset.id), quantity: parseInt(e.target.dataset.qty) - 1 });
      }
      // Close cart on outside click
      if (cartContainer && cartContainer.classList.contains('active')) {
        if (!cartContainer.contains(e.target) && !cartIcon.contains(e.target) && !e.target.classList.contains('add-to-cart-btn')) {
          cartContainer.classList.remove('active');
        }
      }
    });

    function fetchCart(data) {
      fetch('cart-handler.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
      .then(r => r.json())
      .then(res => { if (res.success) updateCartUI(res.cart, res.total_items, res.total_price); })
      .catch(err => console.error('Cart error:', err));
    }

    function updateCartUI(cart, totalItems, totalPrice) {
      if (cartBadge)    cartBadge.textContent  = totalItems;
      if (totalPriceEl) totalPriceEl.textContent = totalPrice;
      if (!cartContent) return;

      if (cart.length === 0) {
        cartContent.innerHTML = `
          <div class="empty-cart-msg">
            <i class="fa-solid fa-basket-shopping"></i>
            <p>Your cart is empty.</p>
          </div>`;
        return;
      }

      cartContent.innerHTML = cart.map(item => `
        <div class="cart-box">
          <img src="${item.image}" alt="${item.name}">
          <div class="detail-box">
            <div class="cart-product-title">${item.name}</div>
            <div class="cart-price">${item.price} ${item.currency}</div>
            <div class="cart-quantity-controls">
              <button class="cart-qty-btn cart-qty-minus" data-id="${item.id}" data-qty="${item.quantity}">-</button>
              <span class="cart-qty-number">${item.quantity}</span>
              <button class="cart-qty-btn cart-qty-plus" data-id="${item.id}" data-qty="${item.quantity}">+</button>
            </div>
          </div>
          <i class="fa-solid fa-trash cart-remove-item" data-id="${item.id}"></i>
        </div>
      `).join('');
    }
  </script>

  <script src="js/header.js"></script>
</body>
</html>