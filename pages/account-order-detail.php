<?php
include 'customer_session.php';

$order_id = (int) ($_GET['id'] ?? 0);
if (!$order_id) {
  header("Location: account-orders.php");
  exit;
}

$stmt = $conn->prepare("
    SELECT o.*, p.transaction_uuid, p.transaction_code, p.payment_status AS pay_status
    FROM orders o
    LEFT JOIN payments p ON p.order_id = o.id
    WHERE o.id = ? AND o.user_id = ?
");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
$stmt->close();

if (!$order) {
  header("Location: account-orders.php");
  exit;
}

$items = [];
$stmt = $conn->prepare("SELECT product_name, price, qty, line_total FROM order_items WHERE order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
  $items[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order #<?= $order_id ?> | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/account.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include 'header.php'; ?>
  <section class="account-hero">
    <div class="container">
      <h1>Order #<?= $order_id ?></h1>
      <p>Placed on <?= date('F j, Y', strtotime($order['created_at'])) ?></p>
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
        <div class="order-detail-meta">
          <p><strong>Order Status:</strong> <span
              class="badge badge-<?= strtolower($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span>
          </p>
          <p><strong>Payment Method:</strong>
            <?= strtolower($order['payment_method']) === 'esewa' ? 'eSewa Mobile Wallet' : 'Cash on Delivery (COD)' ?>
          </p>
          <p><strong>Payment Status:</strong> <span
              class="badge badge-<?= strtolower($order['payment_status'] ?? ($order['pay_status'] ?? 'pending')) ?>"><?= htmlspecialchars($order['payment_status'] ?? ($order['pay_status'] ?? 'Pending')) ?></span>
          </p>
          <?php if (!empty($order['transaction_code'])): ?>
            <p><strong>eSewa Ref Code:</strong> <code><?= htmlspecialchars($order['transaction_code']) ?></code></p>
          <?php endif; ?>
          <p><strong>Shipping to:</strong> <?= htmlspecialchars($order['shipping_name']) ?>,
            <?= htmlspecialchars($order['shipping_phone']) ?></p>
          <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
        </div>
        <table class="order-detail-table">
          <thead>
            <tr>
              <th>Product</th>
              <th class="text-right">Price</th>
              <th class="text-right">Qty</th>
              <th class="text-right">Total</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['product_name']) ?></td>
                <td class="text-right">Rs <?= number_format($item['price'], 0) ?></td>
                <td class="text-right"><?= $item['qty'] ?></td>
                <td class="text-right">Rs <?= number_format($item['line_total'], 0) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-right"><strong>Order total</strong></td>
              <td class="text-right"><strong>Rs <?= number_format($order['total'], 0) ?></strong></td>
            </tr>
          </tfoot>
        </table>
        <p style="margin-top:1rem;"><a href="account-orders.php">&larr; Back to order history</a></p>
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