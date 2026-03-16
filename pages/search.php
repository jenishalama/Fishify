<?php
include 'db.php';
$q = trim($_GET['q'] ?? '');
$products = [];
if ($q !== '') {
    $like = '%' . $conn->real_escape_string($q) . '%';
    $stmt = $conn->prepare("SELECT id, name, price, stock, image, category FROM products WHERE (name LIKE ? OR description LIKE ?) ORDER BY name ASC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $products[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search<?= $q !== '' ? ' "' . htmlspecialchars($q) . '"' : '' ?> | Fishify</title>
  <link rel="stylesheet" href="../css/style.css" />
  <link rel="stylesheet" href="../css/home.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
  <?php include 'header.php'; ?>

  <section class="search-results-hero">
    <div class="container">
      <h1 class="search-results-title">Search results</h1>
      <?php if ($q !== ''): ?>
        <p class="search-results-subtitle">Results for "<strong><?= htmlspecialchars($q) ?></strong>"</p>
      <?php else: ?>
        <p class="search-results-subtitle">Enter a search term in the bar above.</p>
      <?php endif; ?>
    </div>
  </section>

  <section class="search-results-content">
    <div class="container">
      <?php if ($q === ''): ?>
        <p class="search-results-empty">Use the search bar in the header to find fish, aquariums, accessories, and more.</p>
      <?php elseif (count($products) === 0): ?>
        <p class="search-results-empty"><i class="fas fa-search"></i> No products found for "<?= htmlspecialchars($q) ?>". Try different keywords.</p>
      <?php else: ?>
        <div class="products-grid" id="search-results">
          <?php foreach ($products as $row):
            $stock = (int)($row['stock'] ?? 0);
            $out = $stock <= 0;
            $img_src = !empty($row['image']) ? '../uploads/' . htmlspecialchars($row['image']) : '../Images/Homepage/bgfishify.jpg';
          ?>
          <div class="product-card<?= $out ? ' out-of-stock' : '' ?>" data-id="<?= (int)$row['id'] ?>" data-price="<?= (float)$row['price'] ?>" data-stock="<?= $stock ?>" data-category="<?= htmlspecialchars($row['category'] ?? '') ?>">
            <a href="product.php?id=<?= (int)$row['id'] ?>" class="product-image product-image-link">
              <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($row['name']) ?>" />
              <?php if ($out): ?>
                <span class="out-of-stock-badge">Out of Stock</span>
              <?php endif; ?>
            </a>
            <div class="product-info">
              <h3 class="product-title"><a href="product.php?id=<?= (int)$row['id'] ?>" class="product-title-link"><?= htmlspecialchars($row['name']) ?></a></h3>
              <div class="product-price">Rs <span><?= number_format((float)$row['price']) ?></span></div>
              <div class="product-stock"><?= $out ? 'Out of stock' : 'Stock: ' . $stock ?></div>
              <button class="btn add-to-cart"<?= $out ? ' disabled' : '' ?>>
                <i class="fas fa-cart-plus"></i> <?= $out ? 'Out of Stock' : 'Add to Cart' ?>
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>
    (function() {
      var searchInput = document.querySelector(".search-bar input");
      if (searchInput && new URLSearchParams(window.location.search).get("q")) {
        searchInput.value = decodeURIComponent(new URLSearchParams(window.location.search).get("q") || "");
      }
      if (typeof updateCartCount === "function") updateCartCount();
    })();
  </script>
</body>
</html>
