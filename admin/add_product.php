<?php
include 'admin_session.php';

if(isset($_POST['submit'])){

    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    $image = "";

    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0){

        // Create unique file name
        $imageName = uniqid() . "." . pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $target = "../uploads/" . $imageName;

        if(move_uploaded_file($_FILES['image']['tmp_name'], $target)){
            $image = $imageName;
        } else {
            echo "Image upload failed!";
            exit();
        }
    }

    $sql = "INSERT INTO products (name, description, price, category, stock, image) 
            VALUES ('$name','$description','$price','$category','$stock','$image')";

    if($conn->query($sql)){
        header("Location: admindashboard.php");
        exit();
    } else {
        echo "Error: ".$conn->error;
    }
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
  <input type="text" name="name" placeholder="Product Name" required><br>
  <textarea name="description" placeholder="Description"></textarea><br>
  <input type="number" name="price" placeholder="Price" required><br>
  <select name="category" required>
    <option value="fish">Fish</option>
    <option value="aquarium">Aquarium</option>
    <option value="accessories">Accessories</option>
    <option value="plants">Aquatic Plants</option>
  </select><br>
  <input type="number" name="stock" placeholder="Stock Quantity" required><br>
  <input type="file" name="image" required><br>
  <button type="submit" name="submit">Add Product</button>
</form>
</body>
</html>
