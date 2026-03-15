<?php
include 'customer_session.php';

$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    header("Location: account-orders.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
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
          <p><strong>Status:</strong> <span class="badge badge-<?= $order['status'] ?>"><?= htmlspecialchars($order['status']) ?></span></p>
          <p><strong>Shipping to:</strong> <?= htmlspecialchars($order['shipping_name']) ?>, <?= htmlspecialchars($order['shipping_phone']) ?></p>
          <p><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></p>
          <p><strong>Payment:</strong> <?= htmlspecialchars($order['payment_method']) ?></p>
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
  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>if (typeof updateCartCount === 'function') updateCartCount();</script>
</body>
</html>
