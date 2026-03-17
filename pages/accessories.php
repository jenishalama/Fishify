<?php
include 'db.php'; // database connection
$category = 'accessories';
$sql = "SELECT * FROM products WHERE category='$category' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Accessories - Fishify</title>

    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/accessories.css" />
    <link rel="stylesheet" href="../css/fish.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  </head>

  <body>
    <!-- Header (Same as Fish) -->
     <?php include 'header.php'; ?>
           

    <!-- Filters Section -->
    <section class="filters-section">
      <div class="container">
        <h2 class="filters-title">Filters</h2>
        <div class="filters-grid">
          <div class="filter-group">
            <h3>Price Range (Rs.)</h3>
            <input type="range" min="0" max="15000" value="15000" class="price-slider" />
            <div class="price-range">
              <span>Rs. 0</span>
              <span>Rs. 15,000</span>
            </div>
          </div>
          <div class="filter-group">
            <h3>Category</h3>
            <div class="filter-options">
              <label><input type="checkbox" /> Filtration</label>
              <label><input type="checkbox" /> Lighting</label>
              <label><input type="checkbox" /> Decor</label>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Accessories Products -->
    <section class="fish-products">
      <div class="container">
        <div class="products-header">
          <h2>Available Accessories</h2>
          <div class="sort-by">
            <span>Sort by:</span>
            <select>
              <option>Popularity</option>
              <option>Price: Low to High</option>
              <option>Price: High to Low</option>
            </select>
          </div>
        </div>

        <div class="fish-grid">
          <?php if($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
              <?php $stock_num = isset($row['stock']) ? (int)$row['stock'] : 999; $out = $stock_num <= 0; $stock_status = $out ? 'out-of-stock' : 'in-stock'; ?>
              <div class="fish-card<?php echo $out ? ' out-of-stock' : ''; ?>" data-id="<?php echo (int)$row['id']; ?>" data-price="<?php echo $row['price']; ?>" data-stock="<?php echo $stock_num; ?>" data-stock-status="<?php echo $stock_status; ?>">
                <a href="product.php?id=<?php echo (int)$row['id']; ?>" class="fish-image fish-image-link">
                  <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" />
                  <?php if ($out): ?><span class="out-of-stock-badge">Out of Stock</span><?php endif; ?>
                </a>
                <div class="fish-info">
                  <h3><a href="product.php?id=<?php echo (int)$row['id']; ?>" class="fish-title-link"><?php echo htmlspecialchars($row['name']); ?></a></h3>
                  <div class="fish-price">Rs. <span class="amount"><?php echo number_format($row['price']); ?></span></div>
                  <div class="card-stock"><?php echo $out ? 'Out of stock' : 'Stock: ' . $stock_num; ?></div>
                  <button class="btn btn-primary add-to-cart"<?php echo $out ? ' disabled' : ''; ?>>
                    <i class="fas fa-cart-plus"></i> <?php echo $out ? 'Out of Stock' : 'Add to Cart'; ?>
                  </button>
                </div>
              </div>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No accessories available.</p>
          <?php endif; ?>
        </div>

        <!-- Pagination -->
        <div class="pagination">
          <a href="#" class="page-link disabled">
            <i class="fas fa-chevron-left"></i> Previous
          </a>
          <div class="page-numbers">
            <a href="#" class="page-number active">1</a>
            <a href="#" class="page-number">2</a>
          </div>
          <a href="#" class="page-link">
            Next <i class="fas fa-chevron-right"></i>
          </a>
        </div>
      </div>
    </section>

    <!-- Footer -->
  <?php include 'footer.php'; ?>

    <script src="../js/main.js"></script>
    <script src="../js/accessories.js"></script>
  </body>
</html>