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
    body { font-family: Arial, sans-serif; background: #f1f5f9; }
    .page-wrap { padding: 24px; max-width: 640px; margin: 0 auto; }
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
    .card h2 { margin: 0 0 14px; font-size: 1rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; }
    .info-row { padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
    .info-row:last-child { border-bottom: none; }
    .info-row strong { color: #475569; display: inline-block; min-width: 80px; }
    .message-body { white-space: pre-wrap; word-break: break-word; color: #334155; line-height: 1.6; margin-top: 8px; }
    a[href^="mailto"] { color: #0d6efd; text-decoration: none; }
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
