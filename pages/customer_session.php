<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/db.php';

$customer_required = true; // set false in includes that want optional login

if (!empty($customer_required) && empty($_SESSION['user_id'])) {
    $next = urlencode($_SERVER['REQUEST_URI']);
    header("Location: login.php?next=" . $next);
    exit;
}

if (!empty($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    // Admin can still access account pages if we want; for now allow.
}

$user_id = (int) ($_SESSION['user_id'] ?? 0);
$user_name = $_SESSION['name'] ?? '';
$user_email = '';
$user_phone = '';
$user_address = '';
if ($user_id) {
    $stmt = $conn->prepare("SELECT fullname, email FROM users WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows) {
            $u = $res->fetch_assoc();
            $user_name = $u['fullname'] ?? $user_name;
            $user_email = $u['email'] ?? '';
        }
        $stmt->close();
    }
    // Optional: phone & address (run setup_orders.php once to add these columns)
    $stmt2 = $conn->prepare("SELECT phone, address FROM users WHERE id = ?");
    if ($stmt2) {
        $stmt2->bind_param("i", $user_id);
        if ($stmt2->execute()) {
            $res2 = $stmt2->get_result();
            if ($res2 && $res2->num_rows) {
                $u2 = $res2->fetch_assoc();
                $user_phone = isset($u2['phone']) ? (string)$u2['phone'] : '';
                $user_address = isset($u2['address']) ? (string)$u2['address'] : '';
            }
        }
        $stmt2->close();
    }
}
