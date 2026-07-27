<?php
include 'customer_session.php';
require_once '../config/esewa.php';

$transaction_uuid = trim($_GET['transaction_uuid'] ?? '');

if (!$transaction_uuid) {
    header("Location: checkout.php");
    exit;
}

// Retrieve payment and order info securely
$stmt = $conn->prepare("
    SELECT p.id AS payment_id, p.transaction_uuid, p.amount, p.payment_status,
           o.id AS order_id, o.user_id, o.shipping_name
    FROM payments p
    JOIN orders o ON p.order_id = o.id
    WHERE p.transaction_uuid = ? AND o.user_id = ?
");
$stmt->bind_param("si", $transaction_uuid, $user_id);
$stmt->execute();
$res = $stmt->get_result();
$payment = $res->fetch_assoc();
$stmt->close();

if (!$payment) {
    die("Invalid or unauthorized transaction request.");
}

$total_amount = round((float)$payment['amount'], 2);
$formatted_amount = (string)$total_amount;
$product_code = ESEWA_PRODUCT_CODE;

// Generate HMAC-SHA256 signature
$signature = generateEsewaSignature($formatted_amount, $transaction_uuid, $product_code);

// Construct success and failure URLs
$baseUrl = getAppBaseUrl();
$success_url = $baseUrl . '/pages/payment_success.php';
$failure_url = $baseUrl . '/pages/payment_failure.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Opening eSewa Gateway... | Fishify</title>
  <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f8fbff; text-align: center; padding-top: 90px; }
    .gateway-box { background: #fff; max-width: 440px; margin: 0 auto; padding: 40px 30px; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
    .spinner { border: 4px solid #f3f3f3; border-top: 4px solid #60bb46; border-radius: 50%; width: 44px; height: 44px; animation: spin 1s linear infinite; margin: 20px auto; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
  </style>
</head>
<body onload="document.getElementById('esewa-form').submit();">
  <div class="gateway-box">
    <img src="https://esewa.com.np/common/images/esewa_logo.png" alt="eSewa Logo" style="width: 140px; margin-bottom: 15px;" onerror="this.src='https://cdn.esewa.com.np/ui/images/esewa_logo.png'">
    <div class="spinner"></div>
    <h3 style="color: #1e293b; margin-bottom: 8px;">Opening eSewa Gateway...</h3>
    <p style="color: #64748b; font-size: 0.9rem;">Connecting to official eSewa payment interface. Please wait...</p>

    <form id="esewa-form" method="POST" action="<?= ESEWA_PAYMENT_URL ?>">
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
    document.getElementById('esewa-form').submit();
  </script>
</body>
</html>
