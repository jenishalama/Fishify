<?php
include 'admin_session.php';

$stmt = $conn->prepare("
  SELECT o.id, o.user_id, o.total, o.status, o.shipping_name, o.shipping_phone, o.payment_method, o.created_at,
         u.fullname AS customer_name, u.email AS customer_email
  FROM orders o
  LEFT JOIN users u ON o.user_id = u.id
  ORDER BY o.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Orders | Fishify</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f1f5f9; }
    .page-wrap { padding: 24px; max-width: 1200px; margin: 0 auto; }
    h1 { margin-bottom: 20px; color: #1e293b; }
    .buttons a {
      display: inline-block;
      padding: 10px 18px;
      margin-right: 10px;
      background: #0d6efd;
      color: #fff;
      text-decoration: none;
      border-radius: 6px;
      font-weight: 600;
      font-size: 14px;
    }
    .buttons a.back { background: #64748b; }
    .buttons a:hover { opacity: 0.9; }
    table {
      border-collapse: collapse;
      width: 100%;
      margin-top: 20px;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    table th, table td { padding: 12px 14px; border-bottom: 1px solid #e2e8f0; }
    table th {
      background: #0d6efd;
      color: #fff;
      text-align: left;
      font-weight: 600;
    }
    table td { color: #334155; }
    table a { color: #0d6efd; text-decoration: none; font-weight: 500; }
    table a:hover { text-decoration: underline; }
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
    .no-orders { padding: 40px; text-align: center; color: #64748b; background: #fff; border-radius: 10px; }
  </style>
</head>
<body>
  <?php include 'adminnavbar.php'; ?>
  <div class="page-wrap">
    <h1>Orders</h1>
    <div class="buttons">
      <a href="admindashboard.php" class="back">Back to Dashboard</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Order ID</th>
          <th>Customer</th>
          <th>Shipping</th>
          <th>Total</th>
          <th>Status</th>
          <th>Date</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td>#<?= (int)$row['id'] ?></td>
          <td>
            <?= htmlspecialchars($row['customer_name'] ?? 'N/A') ?><br>
            <small style="color:#64748b"><?= htmlspecialchars($row['customer_email'] ?? '') ?></small>
          </td>
          <td>
            <?= htmlspecialchars($row['shipping_name']) ?><br>
            <small style="color:#64748b"><?= htmlspecialchars($row['shipping_phone']) ?></small>
          </td>
          <td>Rs <?= number_format((float)$row['total'], 0) ?></td>
          <td>
            <?php
              $status = $row['status'] ?? 'pending';
              $badge = 'badge-pending';
              if ($status === 'processing') $badge = 'badge-processing';
              elseif ($status === 'shipped') $badge = 'badge-shipped';
              elseif ($status === 'delivered') $badge = 'badge-delivered';
              elseif ($status === 'cancelled') $badge = 'badge-cancelled';
            ?>
            <span class="badge <?= $badge ?>"><?= htmlspecialchars($status) ?></span>
          </td>
          <td><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
          <td><a href="order-detail.php?id=<?= (int)$row['id'] ?>">View</a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="no-orders">No orders yet.</div>
    <?php endif; ?>
  </div>
</body>
</html>
