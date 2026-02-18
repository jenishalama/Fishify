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
              <label><input type="checkbox" /> Equipment</label>
              <label><input type="checkbox" /> Maintenance</label>
              <label><input type="checkbox" /> Supplies</label>
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
              <div class="fish-card" data-price="<?php echo $row['price']; ?>">
                <div class="fish-image">
                  <img src="../uploads/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" />
                </div>
                <div class="fish-info">
                  <h3><?php echo $row['name']; ?></h3>
                  <div class="fish-price">Rs. <span class="amount"><?php echo number_format($row['price']); ?></span></div>
                  <button class="btn btn-primary add-to-cart">
                    <i class="fas fa-cart-plus"></i> Add to Cart
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