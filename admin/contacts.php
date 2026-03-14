<?php
include 'admin_session.php';

$stmt = $conn->prepare("SELECT id, name, email, subject, LEFT(message, 80) AS message_preview, created_at FROM contact_messages ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Messages | Admin Fishify</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f1f5f9; }
    .page-wrap { padding: 24px; max-width: 1100px; margin: 0 auto; }
    h1 { margin-bottom: 20px; color: #1e293b; }
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
    .buttons a:hover { opacity: 0.9; }
    table {
      border-collapse: collapse;
      width: 100%;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    table th, table td { padding: 12px 14px; border-bottom: 1px solid #e2e8f0; text-align: left; }
    table th { background: #0d6efd; color: #fff; font-weight: 600; }
    table td { color: #334155; }
    table a { color: #0d6efd; text-decoration: none; font-weight: 500; }
    table a:hover { text-decoration: underline; }
    .message-preview { color: #64748b; font-size: 0.9rem; max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .no-messages { padding: 40px; text-align: center; color: #64748b; background: #fff; border-radius: 10px; }
  </style>
</head>
<body>
  <?php include 'adminnavbar.php'; ?>
  <div class="page-wrap">
    <h1>Contact messages</h1>
    <div class="buttons">
      <a href="admindashboard.php" class="back">Back to Dashboard</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Date</th>
          <th>Name</th>
          <th>Email</th>
          <th>Subject</th>
          <th>Message</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= date('M j, Y g:i A', strtotime($row['created_at'])) ?></td>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></td>
          <td><?= htmlspecialchars($row['subject'] ?: '—') ?></td>
          <td class="message-preview" title="<?= htmlspecialchars($row['message_preview']) ?>"><?= htmlspecialchars($row['message_preview']) ?>…</td>
          <td><a href="contact-view.php?id=<?= (int)$row['id'] ?>">View</a></td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="no-messages">No contact messages yet.</div>
    <?php endif; ?>
  </div>
</body>
</html>
