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
    $allowed = ['pending', 'confirmed', 'shipped', 'delivered', 'cancelled'];
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
  SELECT o.*, u.fullname AS customer_name, u.email AS customer_email,
         p.transaction_uuid, p.transaction_code, p.payment_status AS pay_status
  FROM orders o
  LEFT JOIN users u ON o.user_id = u.id
  LEFT JOIN payments p ON p.order_id = o.id
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
    /* Import Inter Font */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f4f7fo; margin: 0; color: #1e293b; }
    .page-wrap { padding: 40px; max-width: 900px; margin: 0 auto; }
    h1 { margin-bottom: 8px; font-size: 2rem; font-weight: 700; color: #1e293b; }
    .subtitle { color: #64748b; margin-bottom: 30px; font-size: 0.95rem; }
    
    .buttons { margin-bottom: 24px; }
    .buttons a {
      display: inline-block;
      padding: 10px 20px;
      margin-right: 12px;
      background: rgba(0, 102, 204, 0.1);
      color: #0066CC;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.2s ease;
    }
    .buttons a.back { background: rgba(100, 116, 139, 0.1); color: #475569; }
    .buttons a:hover { background: #0066CC; color: #ffffff; }
    .buttons a.back:hover { background: #475569; color: #ffffff; }

    .card {
      background: #fff;
      border-radius: 12px;
      padding: 24px;
      margin-bottom: 24px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .card h2 { margin: 0 0 16px; font-size: 1.1rem; color: #475569; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px; font-weight: 600; }
    .info-row { padding: 8px 0; color: #475569; display: flex; align-items: flex-start; }
    .info-row strong { color: #1e293b; display: inline-block; min-width: 150px; font-weight: 600; }
    
    table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px; }
    table th, table td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; text-align: left; }
    table th { background: #e6f0fa; color: #0052a3; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; border-radius: 6px 6px 0 0; }
    table td { color: #334155; }
    .total-row { font-weight: 700; font-size: 1.1rem; background: #f8fafc; }
    .total-row td { border-bottom: none; border-radius: 0 0 6px 6px; }
    
    form { margin-top: 20px; background: #f8fafc; padding: 20px; border-radius: 8px; border: 1px solid #e2e8f0; }
    label { display: block; margin-bottom: 8px; font-weight: 600; color: #334155; }
    select { padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; min-width: 200px; font-family: 'Inter', sans-serif; outline: none; transition: border-color 0.2s; }
    select:focus { border-color: #0066CC; }
    button[type="submit"] {
      margin-top: 14px;
      padding: 10px 20px;
      background: #0066CC;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s ease;
      font-family: 'Inter', sans-serif;
    }
    button[type="submit"]:hover { background: #0052a3; }
    
    .badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: capitalize;
    }
    .badge-pending { background: #fef3c7; color: #92400e; }
    .badge-paid, .badge-confirmed, .badge-delivered { background: #d1fae5; color: #065f46; }
    .badge-shipped, .badge-processing { background: #dbeafe; color: #1e40af; }
    .badge-failed, .badge-cancelled { background: #fee2e2; color: #991b1b; }
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
      <div class="info-row"><strong>Payment Method:</strong> <?= strtolower($order['payment_method']) === 'esewa' ? 'eSewa ePay v2' : 'Cash on Delivery (COD)' ?></div>
      <div class="info-row"><strong>Payment Status:</strong> <span class="badge badge-<?= strtolower($order['payment_status'] ?? ($order['pay_status'] ?? 'pending')) ?>"><?= htmlspecialchars($order['payment_status'] ?? ($order['pay_status'] ?? 'Pending')) ?></span></div>
      <?php if (!empty($order['transaction_code'])): ?>
        <div class="info-row"><strong>eSewa Ref ID:</strong> <code><?= htmlspecialchars($order['transaction_code']) ?></code></div>
      <?php endif; ?>
      <?php if (!empty($order['transaction_uuid'])): ?>
        <div class="info-row"><strong>Transaction UUID:</strong> <code><?= htmlspecialchars($order['transaction_uuid']) ?></code></div>
      <?php endif; ?>
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
        $status = strtolower($order['status'] ?? 'pending');
        $badge = 'badge-shipped';
        if ($status === 'pending') $badge = 'badge-pending';
        elseif ($status === 'confirmed' || $status === 'delivered') $badge = 'badge-delivered';
        elseif ($status === 'cancelled') $badge = 'badge-cancelled';
      ?>
      <p>Current status: <span class="badge <?= $badge ?>"><?= htmlspecialchars($order['status']) ?></span></p>
      <form method="post" action="">
        <label for="status">Change to:</label>
        <select name="status" id="status">
          <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>Pending</option>
          <option value="confirmed" <?= $status === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
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
