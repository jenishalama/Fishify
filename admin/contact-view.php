<?php
include 'admin_session.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header("Location: contacts.php");
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, subject, message, created_at FROM contact_messages WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$msg = $res->fetch_assoc();
$stmt->close();

if (!$msg) {
    header("Location: contacts.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Message #<?= $id ?> | Admin Fishify</title>
  <style>
    /* Import Inter Font */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body { font-family: 'Inter', 'Segoe UI', sans-serif; background: #f4f7fo; margin: 0; color: #1e293b; }
    .page-wrap { padding: 40px; max-width: 800px; margin: 0 auto; }
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
    .info-row { padding: 10px 0; border-bottom: 1px solid #f1f5f9; display: flex; align-items: flex-start; }
    .info-row:last-child { border-bottom: none; }
    .info-row strong { color: #475569; display: inline-block; min-width: 100px; font-weight: 600; }
    .message-body { white-space: pre-wrap; word-break: break-word; color: #334155; line-height: 1.7; margin-top: 8px; padding: 16px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0; }
    a[href^="mailto"] { color: #0066CC; text-decoration: none; font-weight: 500; }
    a[href^="mailto"]:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <?php include 'adminnavbar.php'; ?>
  <div class="page-wrap">
    <div class="buttons">
      <a href="contacts.php" class="back">← Back to messages</a>
    </div>
    <h1>Message #<?= (int)$msg['id'] ?></h1>
    <p class="subtitle">Received on <?= date('F j, Y \a\t g:i A', strtotime($msg['created_at'])) ?></p>

    <div class="card">
      <h2>From</h2>
      <div class="info-row"><strong>Name</strong> <?= htmlspecialchars($msg['name']) ?></div>
      <div class="info-row"><strong>Email</strong> <a href="mailto:<?= htmlspecialchars($msg['email']) ?>"><?= htmlspecialchars($msg['email']) ?></a></div>
      <div class="info-row"><strong>Subject</strong> <?= htmlspecialchars($msg['subject'] ?: '—') ?></div>
    </div>

    <div class="card">
      <h2>Message</h2>
      <div class="message-body"><?= nl2br(htmlspecialchars($msg['message'])) ?></div>
    </div>
  </div>
</body>
</html>
