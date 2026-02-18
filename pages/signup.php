<?php
include 'db.php'; // adjust path if needed

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user';

    if ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);

        if ($stmt->execute()) {
            header("Location: login.php");
            exit;
        } else {
            $message = "Email already exists!";
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register | Fishify</title>
  <link rel="stylesheet" href="../css/authorize.css" />
  <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div class="auth-wrapper">
  <div class="auth-card">

    <h1>Create Account</h1>
    <p class="subtitle">
      Join Fishify and explore aquatic life.
    </p>

    <?php if($message): ?>
      <p style="color:red;"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label>Full Name</label>
        <input type="text" name="fullname" required />
      </div>

      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required />
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required />
      </div>

      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" required />
      </div>

      <button type="submit" class="primary-btn">
        Register
      </button>
    </form>

    <p class="footer-text">
      Already have an account?
      <a href="login.php">Login</a>
    </p>

  </div>
</div>

</body>
</html>