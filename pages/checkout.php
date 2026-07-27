<?php
include 'customer_session.php';

$error = '';
$success = false;
$payment_method = $_POST['payment_method'] ?? 'esewa';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_name = trim($_POST['shipping_name'] ?? '');
    $shipping_phone = trim($_POST['shipping_phone'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $cart_json = $_POST['cart_json'] ?? '';
    $payment_method = trim($_POST['payment_method'] ?? 'cash_on_delivery');

    if (!in_array($payment_method, ['cash_on_delivery', 'esewa'])) {
        $payment_method = 'cash_on_delivery';
    }

    if (!$shipping_name || !$shipping_phone || !$shipping_address) {
        $error = 'Please fill in name, phone, and delivery address.';
    } elseif (!preg_match("/^[a-zA-Z\s]{3,50}$/", $shipping_name)) {
        $error = 'Recipient Name must be between 3-50 characters containing only letters and spaces.';
    } elseif (!preg_match("/^[0-9+ ]{9,15}$/", $shipping_phone)) {
        $error = 'Please enter a valid phone number (9-15 digits).';
    } else {
        $cart = json_decode($cart_json, true);
        if (!is_array($cart) || empty($cart)) {
            $error = 'Your cart is empty. Add items from the shop first.';
        } else {
            // Validate product IDs and stock before placing order
            $product_ids = [];
            foreach ($cart as $item) {
                $id = $item['id'] ?? null;
                if (is_numeric($id) && (int) $id > 0) {
                    $product_ids[(int) $id] = (int) ($item['qty'] ?? 1);
                }
            }
            $stock_errors = [];
            if (!empty($product_ids)) {
                $ids_list = implode(',', array_map('intval', array_keys($product_ids)));
                $res = $conn->query("SELECT id, name, stock FROM products WHERE id IN ($ids_list)");
                $db_stock = [];
                while ($row = $res->fetch_assoc()) {
                    $db_stock[(int) $row['id']] = ['name' => $row['name'], 'stock' => (int) $row['stock']];
                }
                foreach ($product_ids as $pid => $order_qty) {
                    if (!isset($db_stock[$pid])) {
                        $stock_errors[] = "Product ID $pid is no longer available.";
                        continue;
                    }
                    $available = $db_stock[$pid]['stock'];
                    if ($available < $order_qty) {
                        $stock_errors[] = sprintf(
                            '"%s" only has %d in stock; you have %d in cart. Please reduce quantity or remove the item.',
                            $db_stock[$pid]['name'],
                            $available,
                            $order_qty
                        );
                    }
                }
            }
            foreach ($cart as $item) {
                $id = $item['id'] ?? null;
                if ($id !== null && $id !== '' && !is_numeric($id)) {
                    $stock_errors[] = 'Some items in your cart are not from the current catalog. Please remove them and add products from the shop.';
                    break;
                }
            }

            if (!empty($stock_errors)) {
                $error = implode(' ', $stock_errors);
            } else {
                $total = 0;
                foreach ($cart as $item) {
                    $qty = (int) ($item['qty'] ?? 1);
                    $price = (float) ($item['price'] ?? 0);
                    $total += $qty * $price;
                }

                $order_status = ($payment_method === 'cash_on_delivery') ? 'Confirmed' : 'Pending';
                $payment_status = 'Pending';

                $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, shipping_name, shipping_phone, shipping_address, payment_method, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("idssssss", $user_id, $total, $order_status, $shipping_name, $shipping_phone, $shipping_address, $payment_method, $payment_status);
                
                if ($stmt->execute()) {
                    $order_id = (int) $conn->insert_id;
                    $stmt->close();

                    // Insert order items
                    $ins = $conn->prepare("INSERT INTO order_items (order_id, product_name, price, qty, line_total) VALUES (?, ?, ?, ?, ?)");
                    foreach ($cart as $item) {
                        $qty = (int) ($item['qty'] ?? 1);
                        $price = (float) ($item['price'] ?? 0);
                        $line_total = $qty * $price;
                        $name = $conn->real_escape_string($item['name'] ?? 'Product');
                        $ins->bind_param("isidd", $order_id, $name, $price, $qty, $line_total);
                        $ins->execute();
                    }
                    $ins->close();

                    // Generate unique transaction UUID
                    $prefix = ($payment_method === 'esewa') ? 'FISHIFY' : 'COD';
                    $transaction_uuid = sprintf("%s-%d-%d", $prefix, $order_id, time());

                    // Create Payment Record
                    $pay_stmt = $conn->prepare("INSERT INTO payments (order_id, transaction_uuid, payment_method, payment_status, amount) VALUES (?, ?, ?, 'Pending', ?)");
                    $pay_stmt->bind_param("issd", $order_id, $transaction_uuid, $payment_method, $total);
                    $pay_stmt->execute();
                    $pay_stmt->close();

                    if ($payment_method === 'cash_on_delivery') {
                        // Deduct stock immediately for COD
                        $deduct = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
                        foreach ($cart as $item) {
                            $id = $item['id'] ?? null;
                            $qty = (int) ($item['qty'] ?? 1);
                            if (is_numeric($id) && (int) $id > 0 && $qty > 0) {
                                $pid = (int) $id;
                                $deduct->bind_param("iii", $qty, $pid, $qty);
                                $deduct->execute();
                            }
                        }
                        $deduct->close();

                        // Redirect to Confirmation
                        header("Location: payment_success.php?order_id=" . $order_id . "&method=cod");
                        exit;
                    } else {
                        // eSewa Payment Direct Gateway Form Submission
                        require_once '../config/esewa.php';

                        $total_amount = round((float)$total, 2);
                        $formatted_amount = (string)$total_amount;
                        $product_code = ESEWA_PRODUCT_CODE;
                        $signature = generateEsewaSignature($formatted_amount, $transaction_uuid, $product_code);

                        $baseUrl = getAppBaseUrl();
                        $success_url = $baseUrl . '/pages/payment_success.php';
                        $failure_url = $baseUrl . '/pages/payment_failure.php';
                        ?>
                        <!DOCTYPE html>
                        <html lang="en">
                        <head>
                          <meta charset="UTF-8">
                          <title>Redirecting to eSewa Gateway...</title>
                          <style>
                            body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fbff; text-align: center; padding-top: 90px; }
                            .gateway-box { background: #fff; max-width: 440px; margin: 0 auto; padding: 40px 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
                            .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #60bb46; border-radius: 50%; width: 44px; height: 44px; animation: spin 1s linear infinite; margin: 20px auto; }
                            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
                          </style>
                        </head>
                        <body onload="document.getElementById('esewa_pay_form').submit();">
                          <div class="gateway-box">
                            <img src="https://esewa.com.np/common/images/esewa_logo.png" alt="eSewa" style="width: 140px; margin-bottom: 15px;" onerror="this.src='https://cdn.esewa.com.np/ui/images/esewa_logo.png'">
                            <div class="spinner"></div>
                            <h3 style="color: #1e293b; margin-bottom: 8px;">Opening eSewa Gateway...</h3>
                            <p style="color: #64748b; font-size: 0.9rem;">Connecting to official eSewa payment interface. Please wait...</p>
                            
                            <form id="esewa_pay_form" method="POST" action="<?= ESEWA_PAYMENT_URL ?>">
                              <input type="hidden" name="amount" value="<?= $formatted_amount ?>">
                              <input type="hidden" name="tax_amount" value="0">
                              <input type="hidden" name="total_amount" value="<?= $formatted_amount ?>">
                              <input type="hidden" name="transaction_uuid" value="<?= htmlspecialchars($transaction_uuid) ?>">
                              <input type="hidden" name="product_code" value="<?= htmlspecialchars($product_code) ?>">
                              <input type="hidden" name="product_service_charge" value="0">
                              <input type="hidden" name="product_delivery_charge" value="0">
                              <input type="hidden" name="success_url" value="<?= htmlspecialchars($success_url) ?>">
                              <input type="hidden" name="failure_url" value="<?= htmlspecialchars($failure_url) ?>">
                              <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
                              <input type="hidden" name="signature" value="<?= htmlspecialchars($signature) ?>">
                              <button type="submit" style="margin-top:15px; background:#60bb46; color:#fff; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; font-weight:600;">
                                Click here if you are not redirected automatically
                              </button>
                            </form>
                          </div>
                          <script type="text/javascript">
                            document.getElementById('esewa_pay_form').submit();
                          </script>
                        </body>
                        </html>
                        <?php
                        exit;
                    }
                }
                $error = 'Could not create order. Please try again.';
            }
        }
    }
}

$shipping_name = $shipping_name ?? $user_name;
$shipping_phone = $shipping_phone ?? ($user_phone ?? '');
$shipping_address = $shipping_address ?? ($user_address ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Checkout | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/cart.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .payment-method-group {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      margin-top: 0.5rem;
    }
    .payment-option-card {
      display: flex;
      align-items: center;
      padding: 1.2rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.2s ease;
      background: #fafbfd;
    }
    .payment-option-card:hover {
      border-color: var(--cart-blue, #2D9CDB);
      background: #f0f7ff;
    }
    .payment-option-card.active {
      border-color: #60bb46;
      background: #f4fbf1;
      box-shadow: 0 0 0 3px rgba(96, 187, 70, 0.15);
    }
    .payment-option-card input[type="radio"] {
      margin-right: 1rem;
      accent-color: #60bb46;
      width: 20px;
      height: 20px;
    }
    .payment-option-content {
      display: flex;
      align-items: center;
      justify-content: space-between;
      width: 100%;
    }
    .payment-option-info {
      display: flex;
      flex-direction: column;
    }
    .payment-option-title {
      font-weight: 600;
      font-size: 1rem;
      color: #1e293b;
    }
    .payment-option-sub {
      font-size: 0.85rem;
      color: #64748b;
    }
    .esewa-badge-img {
      height: 28px;
    }
  </style>
</head>
<body>
  <?php include 'header.php'; ?>

  <section class="cart-content checkout-content">
    <div class="container">
      <div class="checkout-layout">
        <div class="checkout-form-card">
          <div class="checkout-form-header">
            <h2><i class="fas fa-truck"></i> Shipping &amp; contact</h2>
           </div>
          <?php if ($error): ?>
            <div class="checkout-error" role="alert">
              <i class="fas fa-exclamation-circle"></i>
              <?= htmlspecialchars($error) ?>
            </div>
          <?php endif; ?>
          <form id="checkout-form" method="POST" action="">
            <input type="hidden" name="cart_json" id="cart_json" value="">

            <div class="checkout-section">
              <h3><i class="fas fa-user"></i> Contact details</h3>
              <div class="form-group">
                <label for="shipping_name">Full name <span class="required">*</span></label>
                <input type="text" id="shipping_name" name="shipping_name" value="<?= htmlspecialchars($shipping_name) ?>" placeholder="Your full name" required>
              </div>
              <div class="form-group">
                <label for="shipping_phone">Phone number <span class="required">*</span></label>
                <input type="tel" id="shipping_phone" name="shipping_phone" value="<?= htmlspecialchars($shipping_phone) ?>" placeholder="e.g. 98xxxxxxxx" required>
              </div>
            </div>

            <div class="checkout-section">
              <h3><i class="fas fa-map-marker-alt"></i> Delivery address</h3>
              <div class="form-group">
                <label for="shipping_address">Full delivery address <span class="required">*</span></label>
                <textarea id="shipping_address" name="shipping_address" rows="4" placeholder="Street, area, city, district" required><?= htmlspecialchars($shipping_address) ?></textarea>
              </div>
            </div>

            <div class="checkout-section">
              <h3><i class="fas fa-wallet"></i> Payment Method</h3>
              <div class="payment-method-group">
                <label class="payment-option-card <?= ($payment_method === 'esewa') ? 'active' : '' ?>" id="card-esewa">
                  <input type="radio" name="payment_method" value="esewa" <?= ($payment_method === 'esewa') ? 'checked' : '' ?> onchange="togglePaymentSelection('esewa')">
                  <div class="payment-option-content">
                    <div class="payment-option-info">
                      <span class="payment-option-title"><i class="fas fa-mobile-alt" style="color:#60bb46;"></i> eSewa Mobile Wallet</span>
                      <span class="payment-option-sub">Pay securely using eSewa ePay Gateway</span>
                    </div>
                    <img src="https://esewa.com.np/common/images/esewa_logo.png" alt="eSewa" class="esewa-badge-img" onerror="this.src='https://cdn.esewa.com.np/ui/images/esewa_logo.png'">
                  </div>
                </label>

                <label class="payment-option-card <?= ($payment_method === 'cash_on_delivery') ? 'active' : '' ?>" id="card-cod">
                  <input type="radio" name="payment_method" value="cash_on_delivery" <?= ($payment_method === 'cash_on_delivery') ? 'checked' : '' ?> onchange="togglePaymentSelection('cash_on_delivery')">
                  <div class="payment-option-content">
                    <div class="payment-option-info">
                      <span class="payment-option-title"><i class="fas fa-money-bill-wave" style="color:#27AE60;"></i> Cash on Delivery (COD)</span>
                      <span class="payment-option-sub">Pay with cash when your order arrives</span>
                    </div>
                  </div>
                </label>
              </div>
            </div>

            <button type="submit" class="btn btn-checkout btn-place-order">
              <i class="fas fa-lock"></i> Place Order &amp; Pay
            </button>
          </form>
        </div>

        <div class="checkout-summary-card">
          <h2><i class="fas fa-receipt"></i> Order summary</h2>
          <div id="checkout-cart-summary" class="checkout-summary-list"></div>
          <div class="checkout-total-row">
            <span>Total</span>
            <span id="checkout-total">Rs 0</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>
  function togglePaymentSelection(method) {
    document.getElementById('card-esewa').classList.remove('active');
    document.getElementById('card-cod').classList.remove('active');
    if (method === 'esewa') {
      document.getElementById('card-esewa').classList.add('active');
    } else {
      document.getElementById('card-cod').classList.add('active');
    }
  }

  (function() {
    var form = document.getElementById('checkout-form');
    var cartInput = document.getElementById('cart_json');
    var summaryEl = document.getElementById('checkout-cart-summary');
    var totalEl = document.getElementById('checkout-total');

    function renderSummary() {
      var cart = typeof getCart === 'function' ? getCart() : [];
      if (cart.length === 0) {
        summaryEl.innerHTML = '<p>Your cart is empty. <a href="cart.php">Go to cart</a></p>';
        totalEl.textContent = 'Rs 0';
        return;
      }
      var html = '';
      var total = 0;
      cart.forEach(function(item) {
        var qty = item.qty || 1;
        var price = item.price || 0;
        var line = qty * price;
        total += line;
        html += '<div class="checkout-line"><span>' + (item.name || 'Item') + ' × ' + qty + '</span><span>Rs ' + line.toLocaleString() + '</span></div>';
      });
      summaryEl.innerHTML = html;
      totalEl.textContent = 'Rs ' + total.toLocaleString();
    }

    form.addEventListener('submit', function(e) {
      var cart = typeof getCart === 'function' ? getCart() : [];
      if (cart.length === 0) {
        e.preventDefault();
        alert('Your cart is empty.');
        return;
      }
      cartInput.value = JSON.stringify(cart);
    });

    renderSummary();
  })();
  </script>
</body>
</html>
