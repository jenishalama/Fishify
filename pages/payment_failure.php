<?php
include 'customer_session.php';
require_once '../config/esewa.php';

$reason = $_GET['reason'] ?? 'Payment was cancelled or failed on the eSewa gateway.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Failed | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .failure-card-wrap {
      padding: 3rem 0;
      background: linear-gradient(to bottom, #fff5f5 0%, #fef2f2 100%);
      min-height: 75vh;
    }
    .failure-card {
      max-width: 600px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 20px;
      padding: 3rem 2.5rem;
      box-shadow: 0 10px 30px rgba(235, 87, 87, 0.12);
      border: 1px solid rgba(235, 87, 87, 0.2);
      text-align: center;
    }
    .icon-badge {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      margin: 0 auto 1.5rem;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2.5rem;
      background: rgba(235, 87, 87, 0.12);
      color: #EB5757;
    }
    .failure-card h1 {
      font-size: 1.8rem;
      color: #991b1b;
      margin-bottom: 0.5rem;
    }
    .failure-card p {
      color: #64748b;
      margin-bottom: 2rem;
    }
    .action-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
    }
    .btn-action {
      padding: 0.85rem 1.75rem;
      border-radius: 10px;
      font-weight: 600;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.2s ease;
    }
    .btn-primary { background: #0066CC; color: #ffffff; }
    .btn-primary:hover { background: #0052a3; }
    .btn-outline { border: 2px solid #64748b; color: #475569; background: transparent; }
    .btn-outline:hover { background: rgba(100, 116, 139, 0.08); }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <div class="failure-card-wrap">
    <div class="container">
      <div class="failure-card">
        <div class="icon-badge">
          <i class="fas fa-times-circle"></i>
        </div>
        <h1>Payment Unsuccessful</h1>
        <p><?= htmlspecialchars($reason) ?></p>

        <div class="action-buttons">
          <a href="checkout.php" class="btn-action btn-primary">
            <i class="fas fa-redo"></i> Return to Checkout
          </a>
          <a href="cart.php" class="btn-action btn-outline">
            <i class="fas fa-shopping-cart"></i> View Cart
          </a>
        </div>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
</body>
</html>
