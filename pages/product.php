<?php
include 'db.php';
$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: index.php');
    exit;
}
$stmt = $conn->prepare("SELECT id, name, description, price, category, stock, image FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$product) {
    header('Location: index.php');
    exit;
}
$stock = (int)$product['stock'];
$out_of_stock = $stock <= 0;
$img_src = !empty($product['image']) ? '../uploads/' . htmlspecialchars($product['image']) : '../Images/Homepage/bgfishify.jpg';
$category_links = [
    'fish' => ['url' => 'fish.php', 'label' => 'Fish'],
    'aquarium' => ['url' => 'aquarium.php', 'label' => 'Aquarium'],
    'accessories' => ['url' => 'accessories.php', 'label' => 'Accessories'],
    'plants' => ['url' => 'aquaticplants.php', 'label' => 'Plants'],
];
$back_url = $category_links[$product['category']]['url'] ?? 'index.php';
$back_label = $category_links[$product['category']]['label'] ?? 'Shop';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['name']) ?> | Fishify</title>
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/product.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <section class="product-detail">
    <div class="container">
      <a href="<?= htmlspecialchars($back_url) ?>" class="product-back"><i class="fas fa-arrow-left"></i> Back to <?= htmlspecialchars($back_label) ?></a>
      <div class="product-detail-grid">
        <div class="product-gallery">
          <div class="main-image">
            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($product['name']) ?>" />
            <?php if ($out_of_stock): ?>
              <span class="product-out-of-stock-badge">Out of Stock</span>
            <?php endif; ?>
          </div>
        </div>
        <div class="product-info">
          <div class="product-header">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p class="product-category"><?= htmlspecialchars(ucfirst($product['category'] ?? '')) ?></p>
          </div>
          <div class="product-price-section product-price-single">
            <span class="current-price">Rs <?= number_format((float)$product['price']) ?></span>
          </div>
          <div class="product-stock-section">
            <strong>Quantity available:</strong>
            <?php if ($out_of_stock): ?>
              <span class="stock-out">Out of stock</span>
            <?php else: ?>
              <span class="stock-in"><?= $stock ?> in stock</span>
            <?php endif; ?>
          </div>
          <?php if (!empty(trim($product['description'] ?? ''))): ?>
            <div class="product-description">
              <h3>Description</h3>
              <p><?= nl2br(htmlspecialchars(trim($product['description']))) ?></p>
            </div>
          <?php endif; ?>
          <div class="product-actions">
            <div class="action-row-top">
              <div class="quantity-selector">
                <button type="button" class="qty-btn minus" <?= $out_of_stock ? ' disabled' : '' ?> aria-label="Decrease Quantity">
                  <i class="fas fa-minus"></i>
                </button>
                <input type="number" class="qty-input" value="1" min="1" max="<?= $out_of_stock ? 0 : $stock ?>" readonly>
                <button type="button" class="qty-btn plus" <?= $out_of_stock ? ' disabled' : '' ?> aria-label="Increase Quantity">
                  <i class="fas fa-plus"></i>
                </button>
              </div>
              <button type="button" class="btn btn-add-to-cart" id="productAddToCart" <?= $out_of_stock ? ' disabled' : '' ?>
                data-id="<?= (int)$product['id'] ?>"
                data-name="<?= htmlspecialchars($product['name']) ?>"
                data-price="<?= (float)$product['price'] ?>"
                data-stock="<?= $stock ?>"
                data-image="<?= htmlspecialchars($img_src) ?>">
                <i class="fas fa-shopping-bag"></i> 
                <?= $out_of_stock ? 'Out of Stock' : 'Add to Cart' ?>
              </button>
            </div>
            
            <button type="button" class="btn btn-buy-now" id="productBuyNow" <?= $out_of_stock ? ' disabled' : '' ?>
              data-id="<?= (int)$product['id'] ?>"
              data-name="<?= htmlspecialchars($product['name']) ?>"
              data-price="<?= (float)$product['price'] ?>"
              data-stock="<?= $stock ?>"
              data-image="<?= htmlspecialchars($img_src) ?>">
              <i class="fas fa-bolt"></i> Buy Now
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php'; ?>
  <script src="../js/main.js"></script>
  <script>
  (function() {
    var qtyInput = document.querySelector('.product-detail .qty-input');
    var minusBtn = document.querySelector('.product-detail .qty-btn.minus');
    var plusBtn = document.querySelector('.product-detail .qty-btn.plus');
    var addBtn = document.getElementById('productAddToCart');
    var maxQty = addBtn && addBtn.dataset.stock ? parseInt(addBtn.dataset.stock, 10) : 1;

    function updateQty(val) {
      val = Math.max(0, Math.min(maxQty, val));
      if (qtyInput) qtyInput.value = val;
    }

    if (minusBtn) minusBtn.addEventListener('click', function() {
      updateQty(parseInt(qtyInput.value, 10) - 1);
    });
    if (plusBtn) plusBtn.addEventListener('click', function() {
      updateQty(parseInt(qtyInput.value, 10) + 1);
    });

    function addProductToCart(qty) {
      if (!addBtn || qty < 1) return;
      var id = addBtn.dataset.id ? parseInt(addBtn.dataset.id, 10) : null;
      var name = addBtn.dataset.name || '';
      var price = parseFloat(addBtn.dataset.price) || 0;
      var stock = addBtn.dataset.stock ? parseInt(addBtn.dataset.stock, 10) : 999;
      var image = addBtn.dataset.image || '';
      for (var i = 0; i < qty; i++) {
        if (typeof addToCart === 'function') {
          addToCart({ id: id, name: name, price: price, stock: stock, image: image });
        }
      }
      if (typeof updateCartCount === 'function') updateCartCount();
    }

    if (addBtn && !addBtn.disabled) addBtn.addEventListener('click', function() {
      var qty = parseInt(qtyInput.value, 10) || 1;
      if (qty < 1) return;
      addProductToCart(qty);
      if (typeof showCartToast === 'function') {
        showCartToast(qty > 1 ? 'Added ' + qty + ' to cart' : 'Added "' + addBtn.dataset.name + '" to cart', 'success');
      } else {
        alert('Added to cart');
      }
    });

    var buyNowBtn = document.getElementById('productBuyNow');
    if (buyNowBtn && !buyNowBtn.disabled) buyNowBtn.addEventListener('click', function() {
      var qty = parseInt(qtyInput.value, 10) || 1;
      if (qty < 1) return;
      addProductToCart(qty);
      window.location.href = 'checkout.php';
    });

    if (typeof updateCartCount === 'function') updateCartCount();
  })();
  </script>
</body>
</html>
