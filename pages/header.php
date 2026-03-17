<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$isLoggedIn = !empty($_SESSION['user_id']);
$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>

<header class="sticky-header">
  <div class="container">
    <div class="header-top">
      <a href="index.php" class="logo">
        <i class="fas fa-fish"></i>
        <span>Fishify</span>
      </a>

      <div class="search-bar">
        <input type="text" placeholder="Search for fish, aquariums, accessories..." />
        <button><i class="fas fa-search"></i></button>
      </div>

      <div class="header-actions">
        <?php if ($isLoggedIn): ?>
          <?php if ($isAdmin): ?>
            <a href="../admin/admindashboard.php" class="login-btn"><i class="fas fa-cog"></i><span>Admin</span></a>
          <?php else: ?>
            <a href="account.php" class="login-btn"><i class="fas fa-user"></i><span>My Account</span></a>
          <?php endif; ?>
          <a href="logout.php" class="login-btn"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
        <?php else: ?>
          <a href="login.php" class="login-btn"><i class="fas fa-user"></i><span>Login</span></a>
        <?php endif; ?>

        <a href="cart.php" class="cart-btn">
          <i class="fas fa-shopping-cart"></i>
          <span class="cart-count">0</span>
        </a>

        <button class="mobile-menu-btn">
          <i class="fas fa-bars"></i>
        </button>
      </div>
    </div>

    <nav>
      <ul class="main-nav">
        <li><a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">Home</a></li>
        <li><a href="fish.php" class="<?= ($currentPage == 'fish.php') ? 'active' : '' ?>">Fish</a></li>
        <li><a href="aquarium.php" class="<?= ($currentPage == 'aquarium.php') ? 'active' : '' ?>">Aquarium</a></li>
        <li><a href="accessories.php" class="<?= ($currentPage == 'accessories.php') ? 'active' : '' ?>">Accessories</a>
        </li>
        <li><a href="aquaticplants.php" class="<?= ($currentPage == 'aquaticplants.php') ? 'active' : '' ?>">Aquatic
            Plants</a></li>
        <li><a href="contact.php" class="<?= ($currentPage == 'contact.php') ? 'active' : '' ?>">Contact</a></li>
      </ul>
    </nav>
  </div>
</header>