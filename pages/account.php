<?php
include 'customer_session.php';

// Optional: clear cart after order placed (cart is cleared by JS; we just show message)
$order_placed = isset($_GET['order_placed']) && (int) $_GET['order_id'] > 0;
$order_id = (int) ($_GET['order_id'] ?? 0);

// Recent orders for dashboard
$orders = [];
$stmt = $conn->prepare("SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
  $orders[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/cart.css">
  <link rel="stylesheet" href="../css/account.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'header.php'; ?>

  <section class="account-hero">
    <div class="container">
      <h1>My Account</h1>
      <p>Hello, <?= htmlspecialchars($user_name) ?></p>
    </div>
  </section>

  <div class="container">
    <div class="account-layout">
      <nav class="account-nav">
        <a href="account.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
        <a href="account-orders.php"><i class="fas fa-shopping-bag"></i> Order history</a>
        <a href="account-profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </nav>
      <main class="account-content">
        <?php if ($order_placed && $order_id): ?>
          <div class="alert-success">
            Order #<?= $order_id ?> placed successfully. You can view it in Order history.
          </div>
          <script>
            (function () {
              try {
                localStorage.setItem('fishify_cart', '[]');
              } catch (e) { }
            })();
          </script>
        <?php endif; ?>

        <h2 style="margin-bottom:1rem;">Dashboard</h2>
        <div class="account-card">
          <h3><i class="fas fa-info-circle"></i> Welcome</h3>
          <p>From here you can see your recent orders, full order history, and manage your profile.</p>
        </div>
        <div class="account-card">
          <div class="section-header-row">
            <h3><i class="fas fa-clock" style="color:#0066CC;"></i> Recent Orders</h3>
            <a href="account-orders.php" style="color:#0066CC; font-weight:600; font-size:0.9rem;">View All Orders &rarr;</a>
          </div>

          <?php if (empty($orders)): ?>
            <p style="color:#64748b; margin-top:0.5rem;">You haven't placed any orders yet. <a href="index.php" style="color:#0066CC; font-weight:600;">Browse Products</a>.</p>
          <?php else: ?>
            <div style="overflow-x: auto;">
              <table class="orders-table">
                <thead>
                  <tr>
                    <th>Order</th>
                    <th>Date</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($orders as $o): ?>
                    <tr>
                      <td><strong>#<?= $o['id'] ?></strong></td>
                      <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                      <td><strong style="color:#0f172a;">Rs <?= number_format($o['total'], 0) ?></strong></td>
                      <td>
                        <?php if (isset($o['payment_method']) && strtolower($o['payment_method']) === 'esewa'): ?>
                          <span class="payment-pill payment-pill-esewa">
                            <i class="fas fa-mobile-alt"></i> eSewa
                          </span>
                        <?php else: ?>
                          <span class="payment-pill payment-pill-cod">
                            <i class="fas fa-money-bill-wave"></i> COD
                          </span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <span class="badge badge-<?= strtolower($o['status']) ?>">
                          <?= htmlspecialchars($o['status']) ?>
                        </span>
                      </td>
                      <td>
                        <a href="account-order-detail.php?id=<?= $o['id'] ?>">
                          Track / Details &rarr;
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </div>

      </main>
    </div>
  </div>

  <!-- Logout Confirmation Modal -->
  <div id="logout-confirm-modal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); align-items:center; justify-content:center; z-index:9999;">
    <div style="background:#fff; padding:2rem; border-radius:16px; max-width:400px; width:90%; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.15); border: 1px solid #cbd5e1;">
      <div style="font-size:2.5rem; color:#dc3545; margin-bottom:1rem;"><i class="fas fa-exclamation-circle"></i></div>
      <h3 style="font-family:'Outfit',sans-serif; margin-bottom:0.5rem; color:#0f172a; font-weight:700;">Confirm Logout</h3>
      <p style="color:#64748b; font-size:0.95rem; margin-bottom:1.5rem; line-height:1.5;">Are you sure you want to log out of your Fishify account?</p>
      <div style="display:flex; gap:1rem; justify-content:center;">
        <button onclick="closeLogoutModal()" style="padding:0.75rem 1.5rem; border-radius:8px; border:1px solid #cbd5e1; background:#f8fafc; color:#475569; font-weight:600; cursor:pointer;">Cancel</button>
        <a href="logout.php" style="padding:0.75rem 1.5rem; border-radius:8px; border:none; background:#dc3545; color:#fff; font-weight:600; text-decoration:none; cursor:pointer;">Yes, Logout</a>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>
  function confirmLogout(event) {
    event.preventDefault();
    document.getElementById('logout-confirm-modal').style.display = 'flex';
  }
  function closeLogoutModal() {
    document.getElementById('logout-confirm-modal').style.display = 'none';
  }

  // Bind logout triggers, but skip the modal's "Yes, Logout" link
  document.querySelectorAll('a[href="logout.php"]').forEach(function(el) {
    if (el.closest('#logout-confirm-modal')) return;
    el.addEventListener('click', confirmLogout);
  });

  if (typeof updateCartCount === 'function') updateCartCount();
  </script>
</body>

</html>