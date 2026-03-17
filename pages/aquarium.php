<?php
include 'db.php'; // database connection
$category = 'aquarium';
$sql = "SELECT * FROM products WHERE category='$category' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aquarium - Fishify</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/aquarium.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>

<!-- ================= HEADER (UNCHANGED) ================= -->
  <?php include 'header.php'; ?>

<!-- ================= FILTERS ================= -->
<section class="aquarium-filters-section">
  <div class="container">
    <h2 class="aquarium-filters-title">Filters</h2>
    <div class="aquarium-filters-grid">
      <div class="aquarium-filter-group">
        <h3>Price Range (Rs.)</h3>
        <input type="range" min="0" max="30000" value="30000" class="aquarium-price-slider" />
        <div class="aquarium-price-range">
          <span>Rs. 0</span>
          <span>Rs. 30,000</span>
        </div>
      </div>
      <div class="aquarium-filter-group">
        <h3>Availability</h3>
        <div class="aquarium-filter-options">
          <label>
            <input type="checkbox" name="availability" value="in-stock" />
            <span>In Stock</span>
          </label>
          <label>
            <input type="checkbox" name="availability" value="out-of-stock" />
            <span>Out of Stock</span>
          </label>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= PRODUCTS ================= -->
<section class="aquarium-products-section">
  <div class="container">
    <div class="aquarium-products-header">
      <h2>Available Aquariums</h2>
      <div class="aquarium-sort">
        <span>Sort by:</span>
        <select>
          <option>Popularity</option>
          <option>Price: Low to High</option>
          <option>Price: High to Low</option>
        </select>
      </div>
    </div>

    <div class="aquarium-grid">
      <?php if($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <?php $stock = (int)$row['stock']; $out = $stock <= 0; $stock_status = $out ? 'out-of-stock' : 'in-stock'; ?>
          <div class="aquarium-card<?php echo $out ? ' out-of-stock' : ''; ?>" data-id="<?php echo (int)$row['id']; ?>" data-price="<?php echo $row['price']; ?>" data-stock="<?php echo $stock; ?>" data-stock-status="<?php echo $stock_status; ?>">
            <a href="product.php?id=<?php echo (int)$row['id']; ?>" class="aquarium-image aquarium-image-link">
              <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
              <?php if ($out): ?><span class="out-of-stock-badge">Out of Stock</span><?php endif; ?>
            </a>
            <div class="aquarium-info">
              <h3><a href="product.php?id=<?php echo (int)$row['id']; ?>" class="aquarium-title-link"><?php echo htmlspecialchars($row['name']); ?></a></h3>
              <div class="aquarium-price">Rs. <?php echo number_format($row['price']); ?></div>
              <div class="card-stock"><?php echo $out ? 'Out of stock' : 'Stock: ' . $stock; ?></div>
              <button class="btn btn-primary add-to-cart"<?php echo $out ? ' disabled' : ''; ?>>
                <i class="fas fa-cart-plus"></i> <?php echo $out ? 'Out of Stock' : 'Add to Cart'; ?>
              </button>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No aquariums available.</p>
      <?php endif; ?>
    </div>

    <!-- Pagination (Static for now) -->
    <div class="pagination">
      <a href="#" class="page-link disabled"><i class="fas fa-chevron-left"></i> Previous</a>
      <div class="page-numbers">
        <a href="#" class="page-number active">1</a>
        <a href="#" class="page-number">2</a>
      </div>
      <a href="#" class="page-link">Next <i class="fas fa-chevron-right"></i></a>
    </div>
  </div>
</section>

<!-- ================= FOOTER ================= -->
  <?php include 'footer.php'; ?>

<script src="../js/main.js"></script>
<script src="../js/aquarium.js"></script>
</body>
</html>