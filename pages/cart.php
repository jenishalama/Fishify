<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$cart_user_logged_in = !empty($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - Fishify</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/cart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <!-- header -->
  <?php include 'header.php'; ?>
    <!-- Cart Hero -->
    <section class="cart-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Your Shopping Cart</h1>
                <p>Review your items before checkout</p>
            </div>
        </div>
    </section>

    <!-- Cart Content -->
    <section class="cart-content">
        <div class="container">
            <div class="cart-layout">

                <!-- Cart Items -->
                <div class="cart-items-section">
                    <div class="section-header">
                        <h2><i class="fas fa-shopping-basket"></i> Items in Cart</h2>
                    </div>
                    <div class="cart-items">
                        <!-- Items will be dynamically injected here by cart.js -->
                    </div>
                    <div class="cart-actions">
                        <a href="fish.php" class="continue-shopping">
                            <i class="fas fa-arrow-left"></i> Continue Shopping
                        </a>
                        <button class="clear-cart">
                            <i class="fas fa-times"></i> Clear Cart
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="order-summary-section">
                    <div class="summary-card">
                        <h2><i class="fas fa-receipt"></i> Order Summary</h2>
                        <div class="summary-details">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span class="amount">Rs 0</span>
                            </div>
                            <div class="summary-divider"></div>
                            <div class="summary-row total">
                                <span>Order Total</span>
                                <span class="amount total-amount">Rs 0</span>
                            </div>
                        </div>

                        <div class="payment-options">
                            <div class="payment-option">
                                <i class="fas fa-money-bill-wave"></i>
                                <div class="payment-copy">
                                    <strong>Cash on delivery only</strong>
                                    <span>All orders are paid in cash when your items are delivered.</span>
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-checkout">
                            <i class="fas fa-lock"></i> Proceed to Checkout
                        </button>

                        <p class="secure-checkout">
                            <i class="fas fa-shield-alt"></i>
                            Secure checkout. Your information is protected.
                        </p>
                    </div>

                    <!-- Need Help Section -->
                    <div class="help-card">
                        <h3><i class="fas fa-question-circle"></i> Need Help?</h3>
                        <p>Our customer support team is available 24/7 to assist you with your order.</p>
                        <div class="help-contacts">
                            <a href="#" class="help-link">
                                <i class="fas fa-phone"></i>
                                <span>Call Us : 9865081814</span>
                            </a>
                            <a href="#" class="help-link">
                                <i class="fas fa-envelope"></i>
                                <span>fishify@gmail.com</span>
                            </a>
                                <a href="#" class="help-link">
                                <i class="fas fa-comment"></i>
                                <span>Live Chat</span>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
      <div class="container">
        <div class="footer-grid">
          <div class="footer-section">
            <h4>Fishify</h4>
            <p>Your premium destination for ornamental fish and aquatic supplies.</p>
            <div class="connect_icon">
              <i class="fa-brands fa-facebook"></i>
              <i class="fa-brands fa-instagram"></i>
              <i class="fa-brands fa-twitter"></i>
              <i class="fa-brands fa-tiktok"></i>
            </div>
          </div>
          <div class="footer-section">
            <h4>Shop</h4>
            <ul class="footer-links">
              <li><a href="aquarium.php">Aquarium</a></li>
              <li><a href="fish.php">Fishes</a></li>
              <li><a href="accessories.php">Accessories</a></li>
              <li><a href="aquaticplants.php">Plants</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h4>About Us</h4>
            <ul class="footer-links">
              <li><a href="#">Our Story</a></li>
              <li><a href="../pages/contact.php">Contact Us</a></li>
              <li><a href="#">Careers</a></li>
            </ul>
          </div>
          <div class="footer-section">
            <h4>Support</h4>
            <ul class="footer-links">
              <li><a href="#">Help Center</a></li>
              <li><a href="#">Shipping</a></li>
              <li><a href="#">Returns</a></li>
            </ul>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; 2025 Fishify. All rights reserved. Made with <i class="fas fa-heart"></i></p>
        </div>
      </div>
    </footer>

    <script src="../js/main.js"></script>
    <script>
    window.FISHIFY_LOGGED_IN = <?= $cart_user_logged_in ? 'true' : 'false' ?>;
    </script>
    <script src="../js/cart.js"></script>
</body>
</html>