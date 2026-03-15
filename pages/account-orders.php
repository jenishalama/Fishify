<?php
include 'customer_session.php';

$orders = [];
$stmt = $conn->prepare("SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC");
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
                  <td><a href="account-order-detail.php?id=<?= $o['id'] ?>">View details</a></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php endif; ?>
      </main>
    </div>
  </div>
  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>if (typeof updateCartCount === 'function') updateCartCount();</script>
</body>
</html>
