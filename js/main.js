// ---------------- CART LOGIC ----------------
const CART_KEY = "fishify_cart";

// Get cart from localStorage
function getCart() {
  return JSON.parse(localStorage.getItem(CART_KEY)) || [];
}

// Save cart to localStorage
function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  updateCartCount();
}

// Update cart count in header
function updateCartCount() {
  const cart = getCart();
  const count = cart.reduce((total, item) => total + item.qty, 0);
  const el = document.querySelector(".cart-count");
  if (el) el.textContent = count;
}

// Add product to cart
function addToCart(product) {
  const cart = getCart();
  const existing = cart.find(p => p.id === product.id);
  if (existing) {
    existing.qty++;
  } else {
    cart.push({ ...product, qty: 1 });
  }
  saveCart(cart);
  alert(`Added "${product.name}" to cart 🛒`);
}

// ---------------- SEARCH BAR ----------------
function setupSearchBar() {
  const searchInput = document.querySelector(".search-bar input");
  const searchBtn = document.querySelector(".search-bar button");

  if (searchInput && searchBtn) {
    searchBtn.addEventListener("click", () => {
      const query = searchInput.value.trim();
      if (!query) return;

      let path = "pages/search.html";
      if (window.location.pathname.includes("/pages/")) {
        path = "search.html";
      }

      window.location.href = path + "?q=" + encodeURIComponent(query);
    });

    searchInput.addEventListener("keypress", e => {
      if (e.key === "Enter") searchBtn.click();
    });
  }
}

// ---------------- MOBILE MENU ----------------
function setupMobileMenu() {
  const btn = document.querySelector(".mobile-menu-btn");
  const nav = document.querySelector(".main-nav");
  if (btn && nav) {
    btn.addEventListener("click", () => {
      nav.classList.toggle("show");
    });
  }
}

// ---------------- ADD TO CART BUTTONS ----------------
function setupAddToCartButtons() {
  const buttons = document.querySelectorAll(".add-to-cart");
  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      const card = btn.closest(".product-card");

      const product = {
        id: card.dataset.id || card.querySelector(".product-title").textContent.trim(),
        name: card.querySelector(".product-title").textContent.trim(),
        price: parseFloat(card.querySelector(".product-price span").textContent.replace("Rs", "").replace(/,/g, "").trim()),
        image: card.querySelector("img")?.src || "",
        description: card.querySelector(".product-description")?.textContent.trim() || "",
        category: card.dataset.category || "default",
        qty: 1
      };

      addToCart(product);
    });
  });
}

// ---------------- INIT ----------------
document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();
  setupSearchBar();
  setupMobileMenu();
  setupAddToCartButtons();
});