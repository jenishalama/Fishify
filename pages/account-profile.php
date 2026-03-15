<?php
include 'customer_session.php';

$updated = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$fullname || !$email) {
        $error = 'Name and email are required.';
    } elseif ($new_password !== '' && $new_password !== $confirm_password) {
        $error = 'New passwords do not match.';
    } else {
        if ($new_password !== '') {
            $hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, password = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $fullname, $email, $hash, $phone, $address, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET fullname = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $fullname, $email, $phone, $address, $user_id);
        }
        if ($stmt->execute()) {
            $updated = true;
            $_SESSION['name'] = $fullname;
            $user_name = $fullname;
            $user_email = $email;
            $user_phone = $phone;
            $user_address = $address;
        } else {
            $error = 'Update failed. Email may already be in use.';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/account.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <section class="account-hero">
    <div class="container">
      <h1>Profile</h1>
      <p>Manage your account details</p>
    </div>
  </section>
  <div class="container">
    <div class="account-layout">
      <nav class="account-nav">
        <a href="account.php"><i class="fas fa-home"></i> Dashboard</a>
        <a href="account-orders.php"><i class="fas fa-shopping-bag"></i> Order history</a>
        <a href="account-profile.php" class="active"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </nav>
      <main class="account-content">
        <?php if ($updated): ?>
          <div class="alert-success">Profile updated successfully.</div>
        <?php endif; ?>
        <?php if ($error): ?>
          <div class="checkout-error" style="margin-bottom:1rem;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <h2 style="margin-bottom:1rem;">Edit profile</h2>
        <form class="profile-form" method="POST" action="">
          <div class="form-group">
            <label>Full name</label>
            <input type="text" name="fullname" value="<?= htmlspecialchars($user_name) ?>" required>
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user_email) ?>" required>
          </div>
          <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($user_phone ?? '') ?>" placeholder="e.g. 98xxxxxxxx">
          </div>
          <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3" placeholder="Default delivery address"><?= htmlspecialchars($user_address ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>New password (leave blank to keep current)</label>
            <input type="password" name="new_password" placeholder="••••••••">
          </div>
          <div class="form-group">
            <label>Confirm new password</label>
            <input type="password" name="confirm_password" placeholder="••••••••">
          </div>
          <button type="submit" class="btn">Save changes</button>
        </form>
      </main>
    </div>
  </div>
  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>if (typeof updateCartCount === 'function') updateCartCount();</script>
</body>
</html>
