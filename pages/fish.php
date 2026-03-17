<?php
include 'db.php'; // Database connection
$category = 'fish'; // Set category for this page
$sql = "SELECT * FROM products WHERE category='$category' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fish - Fishify</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/fish.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <!-- header -->
  <?php include 'header.php'; ?>

  <!-- Filters -->
  <section class="filters-section">
    <div class="container">
      <h2 class="filters-title">Filters</h2>
      <div class="filters-grid">
        <div class="filter-group">
          <h3>Price Range (Rs.)</h3>
          <input type="range" min="0" max="10000" value="10000" class="price-slider" id="fishPriceSlider" />
          <div class="price-range">
            <span>Rs. 0</span>
            <span class="price-range-max">Rs. 10,000</span>
          </div>
        </div>
        <div class="filter-group">
          <h3>Availability</h3>
          <div class="filter-options">
            <label>
              <input type="checkbox" name="availability" value="in-stock" id="filter-in-stock" />
              <span>In Stock</span>
            </label>
            <label>
              <input type="checkbox" name="availability" value="out-of-stock" id="filter-out-of-stock" />
              <span>Out of Stock</span>
            </label>
          </div>
        </div>
        <div class="filter-group filter-actions">
          <button type="button" class="btn-clear-filters">Clear filters</button>
        </div>
      </div>
    </div>
  </section>

  <!-- products -->

  <section class="fish-products">
    <div class="container">
      <div class="products-header">
        <h2>Available Fish</h2>
        <div class="sort-by">
          <span>Sort by:</span>
          <select>
            <option value="default">Default</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
          </select>
        </div>
      </div>

      <div class="fish-grid">
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <?php $stock = (int) $row['stock'];
            $out = $stock <= 0; ?>
            <div class="fish-card<?php echo $out ? ' out-of-stock' : ''; ?>"
     data-id="<?php echo (int)$row['id']; ?>"
     data-price="<?php echo (int)$row['price']; ?>"
     data-stock="<?php echo $stock; ?>"
     data-stock-status="<?php echo $out ? 'out-of-stock' : 'in-stock'; ?>">

  <a href="product.php?id=<?php echo (int)$row['id']; ?>" class="fish-image fish-image-link">
    <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
    <?php if ($out): ?>
      <span class="out-of-stock-badge">Out of Stock</span>
    <?php endif; ?>
  </a>

  <div class="fish-info">
    <h3>
      <a href="product.php?id=<?php echo (int)$row['id']; ?>" class="fish-title-link">
        <?php echo htmlspecialchars($row['name']); ?>
      </a>
    </h3>

    <div class="fish-price">
      Rs. <span class="amount"><?php echo (int)$row['price']; ?></span>
    </div>

    <div class="card-stock">
      <?php echo $out ? 'Out of stock' : 'Stock: ' . $stock; ?>
    </div>

    <button class="btn btn-primary add-to-cart" <?php echo $out ? 'disabled' : ''; ?>>
      <i class="fas fa-cart-plus"></i>
      <?php echo $out ? 'Out of Stock' : 'Add to Cart'; ?>
    </button>
  </div>
</div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No fish available at the moment.</p>
        <?php endif; ?>
      </div>
       <div class="no-results" id="noResults" style="display: none;">
                <p>No fish match your filters. Try adjusting your criteria.</p>
         </div>
    </div>
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

  <!-- footer -->
  <?php include 'footer.php'; ?>

  <script src="../js/main.js"></script>
  <script src="../js/fish.js"></script>
</body>

</html>