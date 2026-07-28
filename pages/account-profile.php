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
    $error = 'Full name and email are required.';
  } elseif (!preg_match("/^[a-zA-Z\s]{3,50}$/", $fullname)) {
    $error = 'Full name must be between 3-50 characters and contain only letters and spaces.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } elseif ($phone !== '' && !preg_match("/^[0-9+ ]{9,15}$/", $phone)) {
    $error = 'Please enter a valid phone number (9-15 digits).';
  } elseif ($new_password !== '' && (strlen($new_password) < 8 || !preg_match("/[0-9]/", $new_password) || !preg_match("/[A-Z]/", $new_password) || !preg_match("/[a-z]/", $new_password))) {
    $error = 'New password must be at least 8 characters long, contain at least one number, one uppercase, and one lowercase letter.';
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
            <input type="text" name="phone" value="<?= htmlspecialchars($user_phone ?? '') ?>"
              placeholder="e.g. 98xxxxxxxx">
          </div>
          <div class="form-group">
            <label>Address</label>
            <textarea name="address" rows="3"
              placeholder="Default delivery address"><?= htmlspecialchars($user_address ?? '') ?></textarea>
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
  <!-- Logout Confirmation Modal -->
  <div id="logout-confirm-modal" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.65); align-items:center; justify-content:center; z-index:9999;">
    <div style="background:#fff; padding:2rem; border-radius:16px; max-width:400px; width:90%; text-align:center; box-shadow:0 10px 30px rgba(0,0,0,0.15); border: 1px solid #cbd5e1;">
      <div style="font-size:2.5rem; color:#dc3545; margin-bottom:1rem;"><i class="fas fa-exclamation-circle"></i></div>
      <h3 style="font-family:'Outfit',sans-serif; margin-bottom:0.5rem; color:#0f172a; font-weight:700;">Confirm Logout</h3>
      <p style="color:#64748b; font-size:0.95rem; margin-bottom:1.5rem; line-height:1.5;">Are you sure you want to log out of your Fishify account?</p>
      <div style="display:flex; gap:1rem; justify-content:center;">
        <button onclick="closeLogoutModal()" style="padding:0.75rem 1.5rem; border-radius:8px; border:1px solid #cbd5e1; background:#f8fafc; color:#475569; font-weight:600; cursor:pointer;">Cancel</button>
        <a href="logout.php" style="padding:0.75rem 1.5rem; border-radius:8px; border:none; background:#dc3545; color:#fff; font-weight:600; text-decoration:none; cursor:pointer;">Yes, Logout</a>
      </div>
    </div>
  </div>

  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>
  function confirmLogout(event) {
    event.preventDefault();
    document.getElementById('logout-confirm-modal').style.display = 'flex';
  }
  function closeLogoutModal() {
    document.getElementById('logout-confirm-modal').style.display = 'none';
  }

  // Bind logout triggers, but skip the modal's "Yes, Logout" link
  document.querySelectorAll('a[href="logout.php"]').forEach(function(el) {
    if (el.closest('#logout-confirm-modal')) return;
    el.addEventListener('click', confirmLogout);
  });

  if (typeof updateCartCount === 'function') updateCartCount();
  </script>
</body>

</html>