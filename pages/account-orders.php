<?php
include 'customer_session.php';

$orders = [];
$stmt = $conn->prepare("SELECT id, total, status, payment_method, payment_status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
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
  <title>Order history | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/account.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'header.php'; ?>
  <section class="account-hero">
    <div class="container">
      <h1>Order history</h1>
      <p>All orders you have placed</p>
    </div>
  </section>
  <div class="container">
    <div class="account-layout">
      <nav class="account-nav">
        <a href="account.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="account-orders.php" class="active"><i class="fas fa-shopping-bag"></i> Order history</a>
        <a href="account-profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </nav>
      <main class="account-content">
        <h2 style="margin-bottom:1rem;">Your orders</h2>
        <?php if (empty($orders)): ?>
          <div class="account-card">
            <p>No orders yet. <a href="index.php">Browse products</a> and add items to cart to checkout.</p>
          </div>
        <?php else: ?>
          <table class="orders-table">
            <thead>
              <tr>
                <th>Order</th>
                <th>Date</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
                <tr>
                  <td>#<?= $o['id'] ?></td>
                  <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
                  <td>Rs <?= number_format($o['total'], 0) ?></td>
                  <td>
                    <span style="font-size:0.85rem; font-weight:600;">
                      <?= strtolower($o['payment_method']) === 'esewa' ? 'eSewa' : 'COD' ?>
                    </span>
                    <br>
                    <small style="color:#64748b;"><?= htmlspecialchars($o['payment_status'] ?? 'Pending') ?></small>
                  </td>
                  <td><span class="badge badge-<?= strtolower($o['status']) ?>"><?= htmlspecialchars($o['status']) ?></span>
                  </td>
                  <td><a href="account-order-detail.php?id=<?= $o['id'] ?>">View details</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
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

  // Bind to header logout button if exists
  document.querySelectorAll('a[href="logout.php"]').forEach(function(el) {
    el.addEventListener('click', confirmLogout);
  });

  if (typeof updateCartCount === 'function') updateCartCount();
  </script>
</body>
</html>

</html>