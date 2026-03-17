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
