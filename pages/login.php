<?php
session_start();
include 'db.php';

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/admindashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $next = isset($_POST['next']) ? trim($_POST['next']) : (isset($_GET['next']) ? trim($_GET['next']) : '');

  // Check empty fields
  if (empty($email) || empty($password)) {
    $message = "All fields are required!";
  } else {

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $user = $result->fetch_assoc();

      if (password_verify($password, $user['password'])) {

        // 🔹 Store session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['fullname'];

        // 🔹 Role-based redirect
        if ($user['role'] === 'admin') {
          header("Location: ../admin/admindashboard.php");
        } else {
          if ($next !== '' && strpos($next, '//') === false) {
            header("Location: " . $next);
          } else {
            header("Location: index.php");
          }
        }
        exit;

      } else {
        $message = "Incorrect password!";
      }
    } else {
      $message = "User not found!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Fishify</title>
  <link rel="stylesheet" href="../css/authorize.css" />
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <div class="auth-wrapper">
    <div class="auth-card">

      <h1>Welcome Back!</h1>
      <p class="subtitle">
        Dive back into the world of aquatic wonders.
      </p>
      <form method="POST" action="">
        <?php if (!empty($_GET['next'])): ?>
          <input type="hidden" name="next" value="<?= htmlspecialchars($_GET['next']) ?>">
        <?php endif; ?>
        <div class="form-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="aqua@fishify.com" required />
        </div>

        <div class="form-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="••••••••" required />
        </div>

        <?php if (!empty($message)): ?>
          <p style="color:red;"><?php echo $message; ?></p>
        <?php endif; ?>

        <button type="submit" class="primary-btn">
          Login
        </button>

      </form>
      <p class="footer-text">
        Don’t have an account?
        <a href="../pages/signup.php">Create one</a>
      </p>
    </div>
  </div>