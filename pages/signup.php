<?php
include 'db.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user';

    // 🔹 Validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required!";
    }
    // Validate Full Name (letters and spaces only, 3-50 chars)
    elseif (!preg_match("/^[a-zA-Z\s]{3,50}$/", $fullname)) {
        $message = "Full Name must be between 3-50 characters and contain only letters and spaces!";
    }
    // Validate Email Address
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address!";
    }
    // Validate Password Strength (Min 8 chars, at least 1 number, 1 uppercase, 1 lowercase letter)
    elseif (strlen($password) < 8 || !preg_match("/[0-9]/", $password) || !preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password)) {
        $message = "Password must be at least 8 characters long, contain at least one number, one uppercase, and one lowercase letter!";
    }
    // Confirm Password Match
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
    }

    else {
        //  Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already exists!";
        } else {

            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user
            $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $fullname, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $message = "Something went wrong. Try again!";
            }
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