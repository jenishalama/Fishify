<?php
/**
 * Run this once to add orders support and customer profile fields.
 * Visit: http://localhost/Fishify/pages/setup_orders.php
 */
include 'db.php';

$done = [];

// Add phone & address to users if not present
$cols = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
if ($cols->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER password");
    $done[] = "Added users.phone";
}
$cols = $conn->query("SHOW COLUMNS FROM users LIKE 'address'");
if ($cols->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL AFTER phone");
    $done[] = "Added users.address";
}

// Orders table
$conn->query("
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'shipped',
    shipping_name VARCHAR(100) NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'cash_on_delivery',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)
");
$done[] = "Created/verified orders table";

// Order items table (snapshot of product at order time)
$conn->query("
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    qty INT NOT NULL,
    line_total DECIMAL(12,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
)
");
$done[] = "Created/verified order_items table";

echo "<h2>Setup complete</h2><ul><li>" . implode("</li><li>", $done) . "</li></ul>";
echo "<p><a href='index.php'>Back to Home</a></p>";
$conn->close();
