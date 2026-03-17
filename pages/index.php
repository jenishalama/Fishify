<?php
include __DIR__ . '/db.php';
$stmt = $conn->prepare("SELECT id, name, price, stock, image, category FROM products ORDER BY created_at DESC LIMIT 8");
$stmt->execute();
$featured_products = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Fishify - Premium Aquatic Store</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body>
  <!-- header -->
  <?php require __DIR__ . '/header.php'; ?>
  <!-- Hero Section -->
  <section class="hero">
    <div class="container">
      <div class="hero-content">
        <h1>
          Your premium destination for ornamental fish <br />and aquatic
          supplies.
        </h1>
        <div class="search-hero">
          <input type="text" placeholder="Search fish, tanks, filters..." />
          <button><i class="fas fa-search"></i></button>
        </div>
      </div>
    </div>
  </section>

  <!-- Categories -->
  <section class="categories-section">
    <div class="container">
      <h2 class="section-title">Explore Our Categories</h2>

      <div class="categories-grid">
        <a href="fish.php" class="category-card">
          <img src="../Images/Homepage/fishes.jpg" alt="Fish" />
          <div class="overlay">
            <div class="category-icon">
              <i class="fas fa-fish"></i>
            </div>
            <h3>Fishes</h3>
          </div>
        </a>

        <a href="aquarium.php" class="category-card">
          <img src="../Images/Homepage/aquarium.jpg" alt="Aquarium" />
          <div class="overlay">
            <div class="category-icon">
              <i class="fas fa-water"></i>
            </div>
            <h3>Aquarium</h3>
          </div>
        </a>

        <a href="accessories.php" class="category-card">
          <img src="../Images/Homepage/accesssories.jpg" alt="Accessories" />
          <div class="overlay">
            <div class="category-icon">
              <i class="fas fa-tools"></i>
            </div>
            <h3>Accessories</h3>
          </div>
        </a>

        <a href="aquaticplants.php" class="category-card">
          <img src="../Images/Homepage/aquatic plants.jpg" alt="Plants" />
          <div class="overlay">
            <div class="category-icon">
              <i class="fas fa-leaf"></i>
            </div>
            <h3>Plants</h3>
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- Featured Products -->
  <section class="featured-products">
    <div class="container">
      <h2 class="section-title">Featured Aquatic Products</h2>

      <div class="products-grid">
        <?php if ($featured_products->num_rows > 0): ?>
          <?php while ($row = $featured_products->fetch_assoc()):
            $stock = (int) ($row['stock'] ?? 0);
            $out_of_stock = $stock <= 0;
            $card_class = 'product-card' . ($out_of_stock ? ' out-of-stock' : '');
            $img_src = !empty($row['image']) ? '../uploads/' . htmlspecialchars($row['image']) : '../Images/Homepage/bgfishify.jpg';
            ?>
            <div class="<?= $card_class ?>" data-id="<?= (int) $row['id'] ?>" data-price="<?= (float) $row['price'] ?>"
              data-stock="<?= $stock ?>" data-category="<?= htmlspecialchars($row['category'] ?? '') ?>">
              <a href="product.php?id=<?= (int) $row['id'] ?>" class="product-image product-image-link">
                <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($row['name']) ?>" />
                <?php if ($out_of_stock): ?>
                  <span class="out-of-stock-badge">Out of Stock</span>
                <?php endif; ?>
              </a>
              <div class="product-info">
                <h3 class="product-title"><a href="product.php?id=<?= (int) $row['id'] ?>"
                    class="product-title-link"><?= htmlspecialchars($row['name']) ?></a></h3>
                <div class="product-price">Rs <span><?= number_format((float) $row['price']) ?></span></div>
                <div class="product-stock"><?= $out_of_stock ? 'Out of stock' : 'Stock: ' . $stock ?></div>
                <button class="btn add-to-cart" <?= $out_of_stock ? ' disabled' : '' ?>>
                  <i class="fas fa-cart-plus"></i> <?= $out_of_stock ? 'Out of Stock' : 'Add to Cart' ?>
                </button>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p class="no-featured">No products yet. Add products from the admin panel.</p>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <div class="popu-fish">
    <h1 class="section-title">Our Popular Fish Breeds</h1>
    <div class="pfish-grid">
      <div class="pfish-cards">
        <div class="pfishcard">
          <img src="../Images/Homepage/betta.jpg" class="pfishcard-img" />
          <h3 class="pfish-title">Siamese Fighting Fish</h3>
        </div>

        <div class="pfishcard">
          <img src="../Images/Homepage/guppies.jpg" class="pfishcard-img" />
          <h3 class="pfish-title">Guppies</h3>
        </div>

        <div class="pfishcard">
          <img src="../Images/Homepage/angel fish.jpg" class="pfishcard-img" />
          <h3 class="pfish-title">Angelfish</h3>
        </div>

        <div class="pfishcard">
          <img src="../Images/Homepage/neontetra.jpg" class="pfishcard-img" />
          <h3 class="pfish-title">Neon Tetra</h3>
        </div>
      </div>
    </div>
  </div>

  <?php require __DIR__ . '/footer.php'; ?>

  <script src="../js/main.js"></script>
</body>

</html>