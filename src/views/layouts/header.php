<?php
/**
 * Reusable header partial.
 * Include at top of every page:  require __DIR__ . '/src/views/layouts/header.php';
 *
 * Expects session to be started before including.
 */
?>


  <header class="header">
    <div class="top-header">
      <div class="logo">
        <div class="logo-icon"><i class="fas fa-shopping-cart"></i></div>
        <div>
          <h1>Tech Shop</h1>
        </div>
      </div>
      <div class="search-box">
        <input type="text" placeholder="Search for latest tech, gaming gear..." />
        <button>SEARCH</button>
      </div>
      <!-- <div class="header-actions">
      <div class="account">
        <span>Hello, Sign in</span>
        <div class="account-link">Account &amp; Lists <i class="fas fa-chevron-down"></i></div>
      </div>
      <div class="cart" id="cart">
        <i class="fas fa-shopping-bag"></i>
        <span class="cart-badge" id="cart-badge">0</span>
      </div>
    </div> -->
      <div class="header-actions">

        <div class="account">
          <?php if (isset($_SESSION['user_id'])): ?>
            <span>Hello, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <div class="account-link">
              My Account <i class="fas fa-chevron-down" style="font-size: 12px; margin-left: 5px;"></i>
            </div>

            <div class="account-dropdown">
              <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin_dashboard.php"><i class="fas fa-user-shield"></i> Dashboard</a>
              <?php endif; ?>
              <a href="logout.php" class="logout-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
          <?php else: ?>
            <span>Hello, Sign in</span>
            <div class="account-link">
              <a href="login.php" style="color: inherit; text-decoration: none;">Account &amp; Lists</a>
              <i class="fas fa-chevron-down" style="font-size: 12px; margin-left: 5px;"></i>
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

