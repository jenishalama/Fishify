<?php
include 'customer_session.php';

// Optional: clear cart after order placed (cart is cleared by JS; we just show message)
$order_placed = isset($_GET['order_placed']) && (int)$_GET['order_id'] > 0;
$order_id = (int)($_GET['order_id'] ?? 0);

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
            (function() {
              try {
                localStorage.setItem('fishify_cart', '[]');
              } catch (e) {}
            })();
          </script>
        <?php endif; ?>

        <h2 style="margin-bottom:1rem;">Dashboard</h2>
        <div class="account-card">
          <h3><i class="fas fa-info-circle"></i> Welcome</h3>
          <p>From here you can see your recent orders, full order history, and manage your profile.</p>
        </div>
        <div class="account-card">
          <h3><i class="fas fa-clock"></i> Recent orders</h3>
          <?php if (empty($orders)): ?>
            <p>You haven't placed any orders yet. <a href="index.php">Start shopping</a>.</p>
          <?php else: ?>
            <table class="orders-table">
              <thead>
                <tr>
                  <th>Order</th>
                  <th>Date</th>
                  <th>Total</th>
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
                    <td><span class="badge badge-<?= $o['status'] ?>"><?= htmlspecialchars($o['status']) ?></span></td>
                    <td><a href="account-order-detail.php?id=<?= $o['id'] ?>">View</a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
        </div>
      </main>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>
  if (typeof updateCartCount === 'function') updateCartCount();
  </script>
</body>
</html>
