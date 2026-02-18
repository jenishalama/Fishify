<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fishify - Premium Aquatic Store</title>
    <link rel="stylesheet" href="../css/style.css" />
    <link rel="stylesheet" href="../css/home.css" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
  </head>
  <body>
    <!-- header -->
     <?php include 'header.php'; ?>
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
          <a href="fish.html" class="category-card">
            <img src="../Images/Homepage/fishes.jpg" alt="Fish" />
            <div class="overlay">
              <div class="category-icon">
                <i class="fas fa-fish"></i>
              </div>
              <h3>Fishes</h3>
            </div>
          </a>

          <a href="aquarium.html" class="category-card">
            <img src="../Images/Homepage/aquarium.jpg" alt="Aquarium" />
            <div class="overlay">
              <div class="category-icon">
                <i class="fas fa-water"></i>
              </div>
              <h3>Aquarium</h3>
            </div>
          </a>

          <a href="accessories.html" class="category-card">
            <img src="../Images/Homepage/accesssories.jpg" alt="Accessories" />
            <div class="overlay">
              <div class="category-icon">
                <i class="fas fa-tools"></i>
              </div>
              <h3>Accessories</h3>
            </div>
          </a>

          <a href="#" class="category-card">
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
    <!-- Featured Products -->
    <section class="featured-products">
      <div class="container">
        <h2 class="section-title">Featured Aquatic Products</h2>

        <div class="products-grid">
          <!-- Product 1 -->
          <div class="product-card">
            <div class="product-image">
              <img
                src="../Images/Homepage/Aqua tank pro 100 galon.jpg"
                alt="Smart Heater Thermostat"
              />
            </div>
            <div class="product-info">
              <h3 class="product-title">Aqua Tank Pro 100</h3>
              <div class="product-price">Rs <span>300</span></div>
              <button class="btn add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>

          <!-- Product 2 -->
          <div class="product-card">
            <div class="product-image">
              <img
                src="../Images/Homepage/Nano Cube Aquarium Kit.webp"
                alt="Smart Heater Thermostat"
              />
            </div>
            <div class="product-info">
              <h3 class="product-title">Nano Cube Aquarium Kit</h3>
              <div class="product-price">Rs <span>300</span></div>
              <button class="btn add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>

          <!-- Product 3 -->
          <div class="product-card">
            <div class="product-image">
              <img
                src="../Images/Homepage/Marine LED Light System.webp"
                alt="Smart Heater Thermostat"
              />
            </div>
            <div class="product-info">
              <h3 class="product-title">Marine LED Light</h3>
              <div class="product-price">Rs <span>300</span></div>
              <button class="btn add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>

          <!-- Product 4 -->
          <div class="product-card">
            <div class="product-image">
              <img
                src="../Images/Homepage/Advanced CO2 Regulator.webp"
                alt="Smart Heater Thermostat"
              />
            </div>
            <div class="product-info">
              <h3 class="product-title">Advanced CO2</h3>
              <div class="product-price">Rs <span>300</span></div>
              <button class="btn add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>

          <!-- Product 5 -->
          <div class="product-card">
            <div class="product-image">
              <img
                src="../Images/Homepage/Premium Plant Substrate.jpg"
                alt="Smart Heater Thermostat"
              />
            </div>
            <div class="product-info">
              <h3 class="product-title">Premium Plant</h3>
              <div class="product-price">Rs <span>300</span></div>
              <button class="btn add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>

          <!-- Product 6 -->
          <div class="product-card">
            <div class="product-image">
              <img
                src="../Images/Homepage/Smart Heater Thermostat.jpg"
                alt="Smart Heater Thermostat"
              />
            </div>
            <div class="product-info">
              <h3 class="product-title">Smart Heater</h3>
              <div class="product-price">Rs <span>300</span></div>
              <button class="btn add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>

          <!-- Product 7 -->
          <div class="product-card">
            <div class="product-image">
              <img src="../Images/Homepage/Automatic Fish Feeder.webp" alt="" />
            </div>
            <div class="product-info">
              <h3 class="product-title">Automatic Fish Feeder</h3>
              <div class="product-price">Rs <span>300</span></div>
              <button class="btn add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>

          <!-- Product 8 -->
          <div class="product-card">
            <div class="product-image">
              <img
                src="../Images/Homepage/Magnetic Gravel Cleaner.jpg"
                alt="Magnetic Gravel Cleaner"
              />
            </div>
            <div class="product-info">
              <h3 class="product-title">Magnetic Gravel Cleaner</h3>
              <div class="product-price">Rs <span>300</span></div>
              <button class="btn add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>
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
            <img
              src="../Images/Homepage/angel fish.jpg"
              class="pfishcard-img"
            />
            <h3 class="pfish-title">Angelfish</h3>
          </div>

          <div class="pfishcard">
            <img src="../Images/Homepage/neontetra.jpg" class="pfishcard-img" />
            <h3 class="pfish-title">Neon Tetra</h3>
          </div>
        </div>
      </div>
    </div>

  <?php include 'footer.php'; ?>
            
    <script src="../js/main.js"></script>
  </body>
</html>
