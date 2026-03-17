<?php
include 'customer_session.php';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_name = trim($_POST['shipping_name'] ?? '');
    $shipping_phone = trim($_POST['shipping_phone'] ?? '');
    $shipping_address = trim($_POST['shipping_address'] ?? '');
    $cart_json = $_POST['cart_json'] ?? '';

    if (!$shipping_name || !$shipping_phone || !$shipping_address) {
        $error = 'Please fill in name, phone, and address.';
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

                $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, shipping_name, shipping_phone, shipping_address, payment_method) VALUES (?, ?, 'shipped', ?, ?, ?, 'cash_on_delivery')");
                $stmt->bind_param("idsss", $user_id, $total, $shipping_name, $shipping_phone, $shipping_address);
                if ($stmt->execute()) {
                    $order_id = (int) $conn->insert_id;
                    $stmt->close();

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

                    // Deduct quantity from products table (only for numeric product IDs)
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

                    header("Location: account.php?order_placed=1&order_id=" . $order_id);
                    exit;
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

            <div class="payment-note">
              <i class="fas fa-money-bill-wave"></i>
              <span>Payment: <strong>Cash on delivery</strong> only. Pay when your order arrives.</span>
            </div>
            <button type="submit" class="btn btn-checkout btn-place-order">
              <i class="fas fa-lock"></i> Place order
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
