<?php
include 'db.php'; // database connection
$category = 'plants';
$sql = "SELECT * FROM products WHERE category='$category' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aquatic Plants - Fishify</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/aquaticplants.css">
    <link rel="stylesheet" href="../css/fish.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
      <?php include 'header.php'; ?>

    <!-- Filters Section -->
    <section class="filters-section">
        <div class="container">
            <h2 class="filters-title">Filters</h2>
            <div class="filters-grid">
                <div class="filter-group">
                    <h3>Price Range (Rs.)</h3>
                    <input type="range" min="0" max="10000" value="10000" class="price-slider" id="priceSlider" />
                    <div class="price-range">
                        <span>Rs. <span id="minPrice">0</span></span>
                        <span>Rs. <span id="maxPrice">10,000+</span></span>
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Plant Type</h3>
                    <div class="filter-options">
                        <label><input type="checkbox" value="foreground" /> Foreground</label>
                        <label><input type="checkbox" value="midground" /> Midground</label>
                        <label><input type="checkbox" value="background" /> Background</label>
                        <label><input type="checkbox" value="floating" /> Floating</label>
                        <label><input type="checkbox" value="stem" /> Stem Plants</label>
                        <label><input type="checkbox" value="carpet" /> Carpeting</label>
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Care Level</h3>
                    <div class="filter-options">
                        <label><input type="checkbox" value="easy" /> Easy</label>
                        <label><input type="checkbox" value="medium" /> Medium</label>
                        <label><input type="checkbox" value="difficult" /> Difficult</label>
                    </div>
                </div>

                <div class="filter-group">
                    <h3>Light Requirement</h3>
                    <div class="filter-options">
                        <label><input type="checkbox" value="low" /> Low</label>
                        <label><input type="checkbox" value="medium" /> Medium</label>
                        <label><input type="checkbox" value="high" /> High</label>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Aquatic Plants Products -->
    <section class="fish-products">
        <div class="container">
            <div class="products-header">
                <h2>Available Aquatic Plants</h2>
                <div class="sort-by">
                    <span>Sort by:</span>
                    <select id="sortSelect">
                        <option value="popularity">Popularity</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="name">Name: A to Z</option>
                    </select>
                </div>
            </div>

          <div class="fish-grid" id="plantsGrid">
  <?php if($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>

      <?php $stock_num = isset($row['stock']) ? (int)$row['stock'] : 999; $out = $stock_num <= 0; $stock_status = $out ? 'out-of-stock' : 'in-stock'; ?>
      <div class="fish-card<?php echo $out ? ' out-of-stock' : ''; ?>" 
           data-id="<?php echo (int)$row['id']; ?>" 
           data-price="<?php echo $row['price']; ?>" 
           data-stock="<?php echo $stock_num; ?>"
           data-stock-status="<?php echo $stock_status; ?>"
           data-type="<?php echo isset($row['type']) ? $row['type'] : ''; ?>" 
           data-care="<?php echo isset($row['care_level']) ? $row['care_level'] : ''; ?>" 
           data-light="<?php echo isset($row['light']) ? $row['light'] : ''; ?>"
           data-name="<?php echo $row['name']; ?>">
        <a href="product.php?id=<?php echo (int)$row['id']; ?>" class="fish-image fish-image-link">
              <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
              <?php if ($out): ?><span class="out-of-stock-badge">Out of Stock</span><?php endif; ?>
            </a>
         <div class="fish-info">
              <h3><a href="product.php?id=<?php echo (int)$row['id']; ?>" class="fish-title-link"><?php echo htmlspecialchars($row['name']); ?></a></h3>
              <div class="fish-price">Rs. <span class="amount"><?php echo $row['price']; ?></span></div>
              <div class="card-stock"><?php echo $out ? 'Out of stock' : 'Stock: ' . $stock_num; ?></div>
              <button class="btn btn-primary add-to-cart"<?php echo $out ? ' disabled' : ''; ?>>
                <i class="fas fa-cart-plus"></i> <?php echo $out ? 'Out of Stock' : 'Add to Cart'; ?>
              </button>
        </div>
      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p>No aquatic plants available.</p>
  <?php endif; ?>
</div>

            <div class="no-results" id="noResults" style="display: none;">
                <p>No plants match your filters. Try adjusting your criteria.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
      <?php include 'footer.php'; ?>


    <script src="../js/aquaticplants.js"></script>
</body>
</html>