<?php
$servername = "localhost";
$username = "root"; // XAMPP default
$password = "";     // XAMPP default

// Connect to MySQL server
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1️⃣ Create Database
$sql = "CREATE DATABASE IF NOT EXISTS fishify";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . $conn->error;
}

// Select the database
$conn->select_db("fishify");

// 2️⃣ Create Users Table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user'
)";

if ($conn->query($sql) === TRUE) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error;
}

// 3️⃣ Add phone & address to users (for profile and checkout)
$r = $conn->query("SHOW COLUMNS FROM users LIKE 'phone'");
if ($r->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER password");
    echo "Added users.phone<br>";
}
$r = $conn->query("SHOW COLUMNS FROM users LIKE 'address'");
if ($r->num_rows === 0) {
    $conn->query("ALTER TABLE users ADD COLUMN address TEXT DEFAULT NULL AFTER phone");
    echo "Added users.address<br>";
}

// 4️⃣ Orders table (track every checkout)
$conn->query("
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    shipping_name VARCHAR(100) NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'cash_on_delivery',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)
");
echo "Orders table ready<br>";

// 5️⃣ Order items (products per order)
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
echo "Order items table ready<br>";

// 6️⃣ Products table: ensure it exists and has stock column (for quantity deduction on order)
$t = $conn->query("SHOW TABLES LIKE 'products'");
if ($t && $t->num_rows > 0) {
    $r = $conn->query("SHOW COLUMNS FROM products LIKE 'stock'");
    if ($r->num_rows === 0) {
        $conn->query("ALTER TABLE products ADD COLUMN stock INT NOT NULL DEFAULT 0 AFTER category");
        echo "Added products.stock<br>";
    }
} else {
    $conn->query("
    CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        category VARCHAR(50) NOT NULL,
        stock INT NOT NULL DEFAULT 0,
        image VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
    ");
    echo "Products table created with stock column<br>";
}

// 7️⃣ Contact messages (from public contact form)
$conn->query("
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) DEFAULT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");
echo "Contact messages table ready<br>";

$conn->close();
?>