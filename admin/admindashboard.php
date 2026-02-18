<?php
include 'admin_session.php';

// Count total products
$sql = "SELECT COUNT(*) as total FROM products";
$res = $conn->query($sql);
$totalProducts = $res->fetch_assoc()['total'];

// Count products by category
$categories = ['fish', 'aquarium', 'accessories', 'plants'];
$categoryCount = [];

foreach($categories as $cat){
    $sql = "SELECT COUNT(*) as cnt FROM products WHERE category='$cat'";
    $res = $conn->query($sql);
    $categoryCount[$cat] = $res->fetch_assoc()['cnt'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fishify</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
        }

        .page-content {
            padding: 25px;
        }

        h1 {
            margin-bottom: 20px;
           margin-left: 500px;
        }

        .dashboard {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            width: 220px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

        .card h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 26px;
            font-weight: bold;
            color: #0d6efd;
        }

        a.button {
            display: inline-block;
            margin-top: 12px;
            padding: 8px 14px;
            background: #0d6efd;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
        }

        a.button:hover {
            background: #084298;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background: #0d6efd;
            color: white;
        }

        td a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 500;
        }

        td a:hover {
            text-decoration: underline;
        }

    </style>
</head>

<body>

<?php include 'adminnavbar.php'; ?>

<div class="page-content">

    <h1 class="heading1">Admin Dashboard</h1>

    <div class="dashboard">
        <div class="card">
            <h2>Total Products</h2>
            <p><?php echo $totalProducts; ?></p>
            <a class="button" href="products.php">Manage Products</a>
        </div>

        <?php foreach($categories as $cat): ?>
        <div class="card">
            <h2><?php echo ucfirst($cat); ?></h2>
            <p><?php echo $categoryCount[$cat]; ?></p>
            <a class="button" href="products.php?category=<?php echo $cat; ?>">
                View <?php echo ucfirst($cat); ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <h2 style="margin-top:30px;">Latest Products</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>

        <?php
        $sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 5";
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['name']; ?></td>
            <td><?= ucfirst($row['category']); ?></td>
            <td>Rs. <?= $row['price']; ?></td>
            <td><?= $row['stock']; ?></td>
            <td>
                <a href="edit-product.php?id=<?= $row['id']; ?>">Edit</a> |
                <a href="delete-product.php?id=<?= $row['id']; ?>"
                   onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>