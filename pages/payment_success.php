<?php
include 'customer_session.php';
require_once '../config/esewa.php';

$error_message = '';
$success = false;
$order = null;
$payment = null;
$clear_cart = false;

// Case 1: eSewa Callback Response (?data=base64_encoded_json)
if (isset($_GET['data'])) {
  $encoded_data = $_GET['data'];
  $json_str = base64_decode($encoded_data);
  $data = json_decode($json_str, true);

  if (is_array($data) && !empty($data['transaction_uuid'])) {
    $transaction_uuid = $data['transaction_uuid'];
    $transaction_code = $data['transaction_code'] ?? ($data['ref_id'] ?? null);
    $total_amount = $data['total_amount'] ?? 0;
    $product_code = $data['product_code'] ?? ESEWA_PRODUCT_CODE;

    // Fetch payment and order record from database
    $stmt = $conn->prepare("
            SELECT p.id AS payment_id, p.order_id, p.transaction_uuid, p.amount, p.payment_status AS payment_table_status,
                   p.transaction_code AS existing_ref,
                   o.user_id, o.total, o.status AS order_status, o.payment_status AS order_payment_status,
                   o.shipping_name, o.shipping_phone, o.shipping_address, o.created_at
            FROM payments p
            JOIN orders o ON p.order_id = o.id
            WHERE p.transaction_uuid = ?
        ");
    $stmt->bind_param("s", $transaction_uuid);
    $stmt->execute();
    $res = $stmt->get_result();
    $record = $res->fetch_assoc();
    $stmt->close();

    if (!$record) {
      $error_message = "Transaction record not found in system.";
    } else {
      // Verify payment status with official eSewa Status Check API (Server-to-Server)
      $status_check = checkEsewaStatus($product_code, $record['amount'], $transaction_uuid);

      if ($status_check['success'] && strtoupper($status_check['status']) === 'COMPLETE') {
        $ref_id = $status_check['ref_id'] ?? $transaction_code;

        // Prevent duplicate processing if already marked Paid
        if ($record['payment_table_status'] !== 'Paid') {
          // Update payments table
          $up1 = $conn->prepare("UPDATE payments SET payment_status = 'Paid', transaction_code = ? WHERE id = ?");
          $up1->bind_param("si", $ref_id, $record['payment_id']);
          $up1->execute();
          $up1->close();

          // Update orders table
          $up2 = $conn->prepare("UPDATE orders SET payment_status = 'Paid', status = 'Confirmed' WHERE id = ?");
          $up2->bind_param("i", $record['order_id']);
          $up2->execute();
          $up2->close();

          // Deduct stock for items in order (only once when payment is confirmed)
          $items_stmt = $conn->prepare("SELECT product_name, qty FROM order_items WHERE order_id = ?");
          $items_stmt->bind_param("i", $record['order_id']);
          $items_stmt->execute();
          $items_res = $items_stmt->get_result();

          $deduct = $conn->prepare("UPDATE products SET stock = stock - ? WHERE name = ? AND stock >= ?");
          while ($item_row = $items_res->fetch_assoc()) {
            $p_name = $item_row['product_name'];
            $p_qty = (int) $item_row['qty'];
            if ($p_qty > 0) {
              $deduct->bind_param("isi", $p_qty, $p_name, $p_qty);
              $deduct->execute();
            }
          }
          $deduct->close();
          $items_stmt->close();

          $record['payment_table_status'] = 'Paid';
          $record['order_status'] = 'Confirmed';
          $record['order_payment_status'] = 'Paid';
        }

        $record['transaction_code'] = $ref_id;
        $order = $record;
        $success = true;
        $clear_cart = true;
      } else {
        // Payment status check failed or is incomplete
        $up1 = $conn->prepare("UPDATE payments SET payment_status = 'Failed' WHERE id = ?");
        $up1->bind_param("i", $record['payment_id']);
        $up1->execute();
        $up1->close();

        $up2 = $conn->prepare("UPDATE orders SET payment_status = 'Failed', status = 'Cancelled' WHERE id = ?");
        $up2->bind_param("i", $record['order_id']);
        $up2->execute();
        $up2->close();

        $error_message = "eSewa status check returned status: " . htmlspecialchars($status_check['status'] ?? 'UNKNOWN') . ". Payment could not be confirmed.";
      }
    }
  } else {
    $error_message = "Invalid or corrupted callback data received from payment gateway.";
  }
}
// Case 2: Cash on Delivery Direct Callback (?order_id=...&method=cod)
elseif (isset($_GET['order_id']) && ($_GET['method'] ?? '') === 'cod') {
  $order_id = (int) $_GET['order_id'];
  $stmt = $conn->prepare("
        SELECT o.id AS order_id, o.user_id, o.total, o.status AS order_status, o.payment_status AS order_payment_status,
               o.shipping_name, o.shipping_phone, o.shipping_address, o.payment_method, o.created_at,
               p.transaction_uuid, p.transaction_code, p.payment_status AS payment_table_status
        FROM orders o
        LEFT JOIN payments p ON p.order_id = o.id
        WHERE o.id = ? AND o.user_id = ?
    ");
  $stmt->bind_param("ii", $order_id, $user_id);
  $stmt->execute();
  $res = $stmt->get_result();
  $order = $res->fetch_assoc();
  $stmt->close();

  if ($order) {
    $success = true;
    $clear_cart = true;
  } else {
    $error_message = "Order not found or unauthorized.";
  }
} else {
  header("Location: account-orders.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $success ? 'Order Confirmation' : 'Payment Error' ?> | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .success-card-wrap {
      padding: 3rem 0;
      background: linear-gradient(to bottom, #f8fbff 0%, #f0f7ff 100%);
      min-height: 75vh;
    }

    .success-card {
      max-width: 650px;
      margin: 0 auto;
      background: #ffffff;
      border-radius: 20px;
      padding: 3rem 2.5rem;
      box-shadow: 0 10px 30px rgba(45, 156, 219, 0.12);
      border: 1px solid rgba(45, 156, 219, 0.15);
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
    }

    .icon-badge.success {
      background: rgba(39, 174, 96, 0.12);
      color: #27AE60;
    }

    .icon-badge.error {
      background: rgba(235, 87, 87, 0.12);
      color: #EB5757;
    }

    .success-card h1 {
      font-size: 1.8rem;
      color: #1e293b;
      margin-bottom: 0.5rem;
    }

    .success-card p {
      color: #64748b;
      margin-bottom: 1.5rem;
    }

    .order-details-box {
      background: #f8fafc;
      border-radius: 14px;
      padding: 1.5rem;
      text-align: left;
      margin-bottom: 2rem;
      border: 1px solid #e2e8f0;
    }

    .order-details-box div {
      display: flex;
      justify-content: space-between;
      padding: 0.6rem 0;
      border-bottom: 1px solid #e2e8f0;
      font-size: 0.95rem;
    }

    .order-details-box div:last-child {
      border-bottom: none;
    }

    .order-details-box label {
      color: #64748b;
      font-weight: 500;
    }

    .order-details-box span {
      color: #0f172a;
      font-weight: 600;
    }

    .status-badge {
      display: inline-block;
      padding: 4px 12px;
      border-radius: 20px;
      font-size: 0.82rem;
      font-weight: 600;
      text-transform: uppercase;
    }

    .status-badge.paid {
      background: #d1fae5;
      color: #065f46;
    }

    .status-badge.pending {
      background: #fef3c7;
      color: #92400e;
    }

    .status-badge.failed {
      background: #fee2e2;
      color: #991b1b;
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

    .btn-primary {
      background: #0066CC;
      color: #ffffff;
    }

    .btn-primary:hover {
      background: #0052a3;
    }

    .btn-outline {
      border: 2px solid #0066CC;
      color: #0066CC;
      background: transparent;
    }

    .btn-outline:hover {
      background: rgba(0, 102, 204, 0.08);
    }
  </style>
</head>

<body>
  <?php include 'header.php'; ?>

  <div class="success-card-wrap">
    <div class="container">
      <div class="success-card">
        <?php if ($success && $order): ?>
          <div class="icon-badge success">
            <i class="fas fa-check-circle"></i>
          </div>
          <h1>Thank you for your order!</h1>
          <p>Your order has been placed successfully and is being processed.</p>

          <div class="order-details-box">
            <div>
              <label>Order ID:</label>
              <span>#<?= (int) $order['order_id'] ?></span>
            </div>
            <div>
              <label>Total Amount:</label>
              <span>Rs <?= number_format((float) $order['total'], 2) ?></span>
            </div>
            <div>
              <label>Payment Method:</label>
              <span><?= (!empty($order['transaction_uuid']) || strtolower($order['payment_method'] ?? '') === 'esewa') ? 'eSewa ePay v2' : 'Cash on Delivery (COD)' ?></span>
            </div>
            <div>
              <label>Payment Status:</label>
              <span>
                <strong class="status-badge <?= strtolower($order['order_payment_status'] ?? 'pending') ?>">
                  <?= htmlspecialchars($order['order_payment_status'] ?? 'Pending') ?>
                </strong>
              </span>
            </div>
            <?php if (!empty($order['transaction_code'])): ?>
              <div>
                <label>eSewa Transaction Ref:</label>
                <span><?= htmlspecialchars($order['transaction_code']) ?></span>
              </div>
            <?php endif; ?>
            <div>
              <label>Shipping To:</label>
              <span><?= htmlspecialchars($order['shipping_name']) ?>
                (<?= htmlspecialchars($order['shipping_phone']) ?>)</span>
            </div>
          </div>

          <div class="action-buttons">
            <a href="account-order-detail.php?id=<?= (int) $order['order_id'] ?>" class="btn-action btn-primary">
              <i class="fas fa-receipt"></i> View Order Details
            </a>
            <a href="index.php" class="btn-action btn-outline">
              <i class="fas fa-shopping-bag"></i> Continue Shopping
            </a>
          </div>

        <?php else: ?>
          <div class="icon-badge error">
            <i class="fas fa-exclamation-triangle"></i>
          </div>
          <h1>Payment Verification Failed</h1>
          <p><?= htmlspecialchars($error_message ?: 'An unexpected error occurred while verifying payment.') ?></p>

          <div class="action-buttons">
            <a href="checkout.php" class="btn-action btn-primary">
              <i class="fas fa-redo"></i> Try Checkout Again
            </a>
            <a href="contact.php" class="btn-action btn-outline">
              <i class="fas fa-envelope"></i> Contact Support
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>

  <?php if ($clear_cart): ?>
    <script>
      // Empty localStorage cart after successful order placement
      if (typeof saveCart === 'function') {
        saveCart([]);
      } else {
        localStorage.removeItem('fishify_cart');
        localStorage.removeItem('cart');
      }
      if (typeof updateCartCount === 'function') {
        updateCartCount();
      }
    </script>
  <?php endif; ?>
</body>

</html>