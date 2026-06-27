<?php
// Ensure CSRF token exists before rendering the header meta tag
require_once __DIR__ . '/../../helpers/helpers.php';
$csrfToken = csrf_token();
?>
<!-- CSRF token for JavaScript (cart AJAX, etc.) -->
<meta name="csrf-token" content="<?= h($csrfToken) ?>">

<header class="header">
  <div class="top-header">
    <div class="logo">
      <div class="logo-icon"><i class="fas fa-shopping-cart"></i></div>
      <div>
        <h1>Tech Shop</h1>
      </div>
    </div>
    <div class="search-box">
      <form method="GET" action="shop-details.php" style="display:contents">
        <input type="text" name="q" placeholder="Search for latest tech, gaming gear…"
               value="<?= h($_GET['q'] ?? '') ?>">
        <button type="submit">SEARCH</button>
      </form>
    </div>

    <div class="account">
      <?php if (isset($_SESSION['user_id'])): ?>
        <span>Hello, <?= h($_SESSION['username']) ?></span>
        <div class="account-link">
          My Account <i class="fas fa-chevron-down" style="font-size:12px;margin-left:5px"></i>
        </div>
        <div class="account-dropdown">
          <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="/src/views/admin/admin_dashboard.php"><i class="fas fa-user-shield"></i> Dashboard</a>
          <?php endif; ?>
          <a href="/my-orders.php"><i class="fas fa-box"></i> My Orders</a>
          <a href="/logout.php" class="logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
      <?php else: ?>
        <span>Hello, Sign in</span>
        <div class="account-link">
          <a href="/login.php" style="color:inherit;text-decoration:none">Account &amp; Lists</a>
          <i class="fas fa-chevron-down" style="font-size:12px;margin-left:5px"></i>
        </div>
        <div class="account-dropdown">
          <a href="/login.php"><i class="fas fa-sign-in-alt"></i> Sign In</a>
          <a href="/register.php"><i class="fas fa-user-plus"></i> Create Account</a>
        </div>
      <?php endif; ?>
    </div>

    <div class="cart" id="cart">
      <a href="/checkout.php" style="color:inherit;text-decoration:none">
        <i class="fas fa-shopping-bag"></i>
        <span class="cart-badge" id="cart-badge">0</span>
      </a>
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
        <li><a href="/index.php#chair-section">Gaming Chairs</a></li>
        <li><a href="/index.php#desk-section">Gaming Desks</a></li>
        <li><a href="/index.php#controllers-section">Controllers</a></li>
        <li><a href="/index.php#playStation-section">PlayStation</a></li>
        <li><a href="/index.php#mous-section">Gaming Mouse</a></li>
        <li><a href="/index.php#ecran-section">Monitors</a></li>
      </ul>
    </div>
    <button class="hamburger" id="hamburger" aria-label="Toggle navigation">
      <i class="fa-solid fa-bars"></i>
    </button>
    <div class="nav-links" id="nav-links">
      <a href="/index.php">Home</a>
      <a href="/shop-details.php">Shop</a>
      <a href="/checkout.php">Checkout</a>
    </div>
    <div class="support">
      <i class="fas fa-headset"></i>
      <span>24/7 Support: +212 600 000 000</span>
    </div>
  </nav>
</header>
