<?php
include 'admin_session.php';

// Handle form submission
if (isset($_POST['submit'])) {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    // Check if new image is uploaded
    if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
        // Get old image to delete
        $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldImage = $result->fetch_assoc()['image'];
        $stmt->close();

        // Upload new image
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp_name, "../uploads/$image");

        // Delete old image
        if ($oldImage != '' && file_exists("../uploads/$oldImage")) {
            unlink("../uploads/$oldImage");
        }

        // Update with new image
        $sql = "UPDATE products SET name=?, description=?, price=?, category=?, stock=?, image=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssi", $name, $description, $price, $category, $stock, $image, $id);
    } else {
        // Update without changing image
        $sql = "UPDATE products SET name=?, description=?, price=?, category=?, stock=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdssi", $name, $description, $price, $category, $stock, $id);
    }

    if ($stmt->execute()) {
        // Instead of header(), use JS to close modal in parent and refresh parent
        echo "<script>
            if (window.top !== window.self) {
                window.parent.location.reload();
            } else {
                window.location.href = 'products.php?updated=success';
            }
        </script>";
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
    exit;
}

// Get product data for editing
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        header("Location: products.php");
        exit;
    }
    $stmt->close();
} else {
    header("Location: products.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="admin-form.css">
</head>

<body>
    
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $product['id']; ?>">
        <input type="text" name="name" placeholder="Product Name" value="<?= htmlspecialchars($product['name']); ?>"
            required><br>
        <textarea name="description"
            placeholder="Description"><?= htmlspecialchars($product['description']); ?></textarea><br>
        <input type="number" name="price" placeholder="Price" value="<?= $product['price']; ?>" required><br>
        <select name="category" required>
            <option value="fish" <?= $product['category'] == 'fish' ? 'selected' : ''; ?>>Fish</option>
            <option value="aquarium" <?= $product['category'] == 'aquarium' ? 'selected' : ''; ?>>Aquarium</option>
            <option value="accessories" <?= $product['category'] == 'accessories' ? 'selected' : ''; ?>>Accessories
            </option>
            <option value="plants" <?= $product['category'] == 'plants' ? 'selected' : ''; ?>>Aquatic Plants</option>
        </select><br>
        <input type="number" name="stock" placeholder="Stock Quantity" value="<?= $product['stock']; ?>" required><br>

        <?php if ($product['image'] != ''): ?>
            <div style="margin: 10px 0;">
                <p>Current Image:</p>
                <img src="../uploads/<?= $product['image']; ?>" width="100" alt="Current product image">
            </div>
        <?php endif; ?>

        <input type="file" name="image"><br>
        <button type="submit" name="submit">Update Product</button>
    </form>
</body>

</html>