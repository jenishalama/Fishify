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
    /* Import Inter Font */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f4f7fo; margin: 0; color: #1e293b; }
    .page-wrap { padding: 40px; max-width: 1200px; margin: 0 auto; }
    h1 { margin-bottom: 30px; font-size: 2rem; font-weight: 700; color: #1e293b; }
    
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

    table {
      border-collapse: separate;
      border-spacing: 0;
      width: 100%;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    table th, table td { padding: 16px 20px; border-bottom: 1px solid #f1f5f9; text-align: left; }
    table th { background: #e6f0fa; color: #0052a3; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    table tr { transition: background 0.2s ease; }
    table tr:hover td { background: #f8fafc; }
    table tr:last-child td { border-bottom: none; }
    
    table td { color: #334155; }
    table a {
      color: #0066CC;
      text-decoration: none;
      font-weight: 500;
      padding: 4px 8px;
      border-radius: 4px;
      transition: background 0.2s, color 0.2s;
    }
    table a:hover { background: rgba(0, 102, 204, 0.1); text-decoration: none; }

    .badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-transform: capitalize;
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
              $status = $row['status'] ?? 'shipped';
              $badge = 'badge-shipped';
              if ($status === 'pending') $badge = 'badge-pending';
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
