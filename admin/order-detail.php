<?php
include 'admin_session.php';

$order_id = (int)($_GET['id'] ?? 0);
if (!$order_id) {
    header("Location: orders.php");
    exit;
}

// Update status if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = trim($_POST['status']);
    $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (in_array($new_status, $allowed, true)) {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $order_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: order-detail.php?id=" . $order_id);
    exit;
}

$stmt = $conn->prepare("
  SELECT o.*, u.fullname AS customer_name, u.email AS customer_email
  FROM orders o
  LEFT JOIN users u ON o.user_id = u.id
  WHERE o.id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$res = $stmt->get_result();
$order = $res->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: orders.php");
    exit;
}

$items = [];
$stmt = $conn->prepare("SELECT product_name, price, qty, line_total FROM order_items WHERE order_id = ? ORDER BY id");
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
  <title>Order #<?= $order_id ?> | Admin Fishify</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f1f5f9; }
    .page-wrap { padding: 24px; max-width: 800px; margin: 0 auto; }
    h1 { margin-bottom: 8px; color: #1e293b; }
    .subtitle { color: #64748b; margin-bottom: 24px; }
    .buttons a {
      display: inline-block;
      padding: 10px 18px;
      margin-right: 10px;
      margin-bottom: 20px;
      background: #0d6efd;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 600;
      font-size: 14px;
    }
    .buttons a.back { background: #64748b; }
    .card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .card h2 { margin: 0 0 14px; font-size: 1.1rem; color: #334155; border-bottom: 1px solid #e2e8f0; padding-bottom: 10px; }
    .info-row { padding: 6px 0; color: #475569; }
    .info-row strong { color: #1e293b; display: inline-block; min-width: 120px; }
    table { width: 100%; border-collapse: collapse; }
    table th, table td { padding: 10px 12px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    table th { background: #f8fafc; color: #475569; font-weight: 600; font-size: 13px; }
    table td { color: #334155; }
    .total-row { font-weight: 700; font-size: 1.1rem; }
    .total-row td { border-bottom: none; padding-top: 14px; }
    form { margin-top: 16px; }
    label { display: block; margin-bottom: 6px; font-weight: 600; color: #334155; }
    select { padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; min-width: 160px; }
    button[type="submit"] {
      margin-top: 10px;
      padding: 8px 18px;
      background: #0d6efd;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
    }
    button[type="submit"]:hover { opacity: 0.9; }
    .badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-processing { background: #dbeafe; color: #1e40af; }
    .badge-shipped { background: #e0e7ff; color: #3730a3; }
    .badge-delivered { background: #d1fae5; color: #065f46; }
    .badge-cancelled { background: #fee2e2; color: #991b1b; }
  </style>
</head>
<body>
  <?php include 'adminnavbar.php'; ?>
  <div class="page-wrap">
    <div class="buttons">
      <a href="orders.php" class="back">← Back to Orders</a>
    </div>
    <h1>Order #<?= $order_id ?></h1>
    <p class="subtitle">Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>

    <div class="card">
      <h2>Customer &amp; shipping</h2>
      <div class="info-row"><strong>Customer:</strong> <?= htmlspecialchars($order['customer_name'] ?? 'N/A') ?></div>
      <div class="info-row"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email'] ?? '') ?></div>
      <div class="info-row"><strong>Ship to:</strong> <?= htmlspecialchars($order['shipping_name']) ?></div>
      <div class="info-row"><strong>Phone:</strong> <?= htmlspecialchars($order['shipping_phone']) ?></div>
      <div class="info-row"><strong>Address:</strong> <?= nl2br(htmlspecialchars($order['shipping_address'])) ?></div>
      <div class="info-row"><strong>Payment:</strong> <?= htmlspecialchars($order['payment_method']) ?></div>
    </div>

    <div class="card">
      <h2>Items</h2>
      <table>
        <thead>
          <tr>
            <th>Product</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td>Rs <?= number_format((float)$item['price'], 0) ?></td>
            <td><?= (int)$item['qty'] ?></td>
            <td>Rs <?= number_format((float)$item['line_total'], 0) ?></td>
          </tr>
          <?php endforeach; ?>
          <tr class="total-row">
            <td colspan="3">Order total</td>
            <td>Rs <?= number_format((float)$order['total'], 0) ?></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="card">
      <h2>Update status</h2>
      <?php
        $status = $order['status'] ?? 'pending';
        $badge = 'badge-pending';
        if ($status === 'processing') $badge = 'badge-processing';
        elseif ($status === 'shipped') $badge = 'badge-shipped';
        elseif ($status === 'delivered') $badge = 'badge-delivered';
        elseif ($status === 'cancelled') $badge = 'badge-cancelled';
      ?>
      <p>Current status: <span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span></p>
      <form method="post" action="">
        <label for="status">Change to:</label>
        <select name="status" id="status">
          <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="processing" <?= $status === 'processing' ? 'selected' : '' ?>>Processing</option>
          <option value="shipped" <?= $status === 'shipped' ? 'selected' : '' ?>>Shipped</option>
          <option value="delivered" <?= $status === 'delivered' ? 'selected' : '' ?>>Delivered</option>
          <option value="cancelled" <?= $status === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <br>
        <button type="submit">Update status</button>
      </form>
    </div>
  </div>
</body>
</html>
