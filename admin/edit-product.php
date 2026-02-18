<?php
include 'admin_session.php';
$id = $_GET['id'];

$sql = "SELECT * FROM products WHERE id=$id";
$res = $conn->query($sql);
$product = $res->fetch_assoc();

if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    if($_FILES['image']['name']){
        $image = $_FILES['image']['name'];
        $tmp_name = $_FILES['image']['tmp_name'];
        move_uploaded_file($tmp_name, "../uploads/$image");
        $sql = "UPDATE products SET name='$name', description='$description', price='$price', category='$category', stock='$stock', image='$image' WHERE id=$id";
    } else {
        $sql = "UPDATE products SET name='$name', description='$description', price='$price', category='$category', stock='$stock' WHERE id=$id";
    }

    if($conn->query($sql)){
        header("Location: products.php");
    } else { echo $conn->error; }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="admin-form.css">
</head>
<body>
  <form method="post" enctype="multipart/form-data">
  <input type="text" name="name" value="<?php echo $product['name']; ?>" required><br>
  <textarea name="description"><?php echo $product['description']; ?></textarea><br>
  <input type="number" name="price" value="<?php echo $product['price']; ?>" required><br>
  <select name="category" required>
    <option value="fish" <?php if($product['category']=='fish') echo 'selected'; ?>>Fish</option>
    <option value="aquarium" <?php if($product['category']=='aquarium') echo 'selected'; ?>>Aquarium</option>
    <option value="accessories" <?php if($product['category']=='accessories') echo 'selected'; ?>>Accessories</option>
    <option value="plants" <?php if($product['category']=='plants') echo 'selected'; ?>>Plants</option>
  </select><br>
  <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required><br>
  <input type="file" name="image"><br>
  <button type="submit" name="submit">Update Product</button>
</form>
</body>
</html>
