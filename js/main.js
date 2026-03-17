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
  const count = cart.reduce((total, item) => total + (item.qty || 0), 0);
  document.querySelectorAll(".cart-count").forEach(el => {
    el.textContent = count;
    el.style.display = count > 0 ? "flex" : "none";
  });
}

// Lightweight toast notification (top-right)
function showCartToast(message, type = "success") {
  let container = document.querySelector(".cart-toast-container");
  if (!container) {
    container = document.createElement("div");
    container.className = "cart-toast-container";
    container.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 2000;
      display: flex;
      flex-direction: column;
      gap: 10px;
    `;
    document.body.appendChild(container);
  }

  const toast = document.createElement("div");
  toast.className = `cart-toast cart-toast-${type}`;

  // Use Fishify theme colors
  const bgColor = type === "error" ? "#DC3545" : "rgba(0,102,204,0.95)"; // danger / primary
  const accent = type === "error" ? "#ffc9c9" : "#A7E9FF";

  toast.style.cssText = `
    min-width: 260px;
    max-width: 340px;
    padding: 14px 18px;
    border-radius: 12px;
    background: ${bgColor};
    color: #fff;
    font-size: 0.9rem;
    box-shadow: 0 12px 30px rgba(0,0,0,0.28);
    display: grid;
    grid-template-columns: auto 1fr auto;
    column-gap: 10px;
    row-gap: 4px;
    align-items: center;
    opacity: 0;
    transform: translateY(-10px) translateX(10px);
    overflow: hidden;
  `;

  // Icon circle
  const icon = document.createElement("div");
  icon.textContent = type === "error" ? "!" : "✓";
  icon.style.cssText = `
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid ${accent};
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
  `;

  // Text container
  const textWrap = document.createElement("div");
  const title = document.createElement("div");
  title.textContent = type === "error" ? "Something went wrong" : "Added to cart";
  title.style.cssText = `
    font-weight: 600;
    margin-bottom: 2px;
  `;
  const body = document.createElement("div");
  body.textContent = message;
  body.style.cssText = `
    font-size: 0.85rem;
    opacity: 0.9;
  `;
  textWrap.appendChild(title);
  textWrap.appendChild(body);

  // Close "x"
  const closeBtn = document.createElement("button");
  closeBtn.type = "button";
  closeBtn.innerHTML = "&times;";
  closeBtn.style.cssText = `
    background: transparent;
    border: none;
    color: #ffffffcc;
    font-size: 1.1rem;
    cursor: pointer;
    padding: 0 0 0 6px;
    align-self: flex-start;
  `;
  closeBtn.addEventListener("click", () => {
    toast.style.opacity = "0";
    toast.style.transform = "translateY(-10px) translateX(10px)";
    setTimeout(() => toast.remove(), 200);
  });

  // Progress bar
  const progress = document.createElement("div");
  progress.style.cssText = `
    grid-column: 1 / -1;
    height: 3px;
    margin-top: 6px;
    border-radius: 999px;
    background: rgba(255,255,255,0.18);
    overflow: hidden;
  `;
  const progressInner = document.createElement("div");
  progressInner.style.cssText = `
    width: 100%;
    height: 100%;
    background: ${accent};
    transform-origin: left;
    transform: scaleX(1);
    transition: transform 2.5s linear;
  `;
  progress.appendChild(progressInner);

  toast.appendChild(icon);
  toast.appendChild(textWrap);
  toast.appendChild(closeBtn);
  toast.appendChild(progress);

  container.appendChild(toast);

  // Trigger animation + progress
  requestAnimationFrame(() => {
    toast.style.opacity = "1";
    toast.style.transform = "translateY(0) translateX(0)";
    progressInner.style.transform = "scaleX(0)";
  });

  const hideAfter = 2600;
  setTimeout(() => {
    if (!document.body.contains(toast)) return;
    toast.style.opacity = "0";
    toast.style.transform = "translateY(-10px) translateX(10px)";
    setTimeout(() => toast.remove(), 200);
  }, hideAfter);
}

// Add product to cart (respects product.stock when present)
function addToCart(product) {
  const maxQty = typeof product.stock === "number" && product.stock >= 0 ? product.stock : 9999;
  if (maxQty <= 0) {
    showCartToast(`"${product.name}" is out of stock.`, "error");
    return;
  }
  const cart = getCart();
  const existing = cart.find(p => p.id === product.id);
  if (existing) {
    const newQty = Math.min(existing.qty + 1, maxQty);
    if (newQty <= existing.qty) {
      showCartToast(`Only ${maxQty} available for "${product.name}".`, "error");
      return;
    }
    existing.qty = newQty;
  } else {
    cart.push({ ...product, qty: 1 });
  }
  saveCart(cart);
  showCartToast(`Added "${product.name}" to cart 🛒`, "success");
}

// ---------------- SEARCH BAR ----------------
function setupSearchBar() {
  const searchInput = document.querySelector(".search-bar input");
  const searchBtn = document.querySelector(".search-bar button");

  if (searchInput && searchBtn) {
    const goToSearch = () => {
      const query = searchInput.value.trim();
      if (!query) return;
      let path = "search.php";
      if (!window.location.pathname.includes("/pages/")) {
        path = "pages/search.php";
      }
      window.location.href = path + "?q=" + encodeURIComponent(query);
    };

    searchBtn.addEventListener("click", goToSearch);
    searchInput.addEventListener("keypress", e => {
      if (e.key === "Enter") {
        e.preventDefault();
        goToSearch();
      }
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
  const buttons = document.querySelectorAll(".add-to-cart:not(:disabled)");
  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      const card = btn.closest(".product-card");
      if (!card) return;

      const idRaw = card.dataset.id;
      const id = idRaw && !isNaN(parseInt(idRaw, 10)) ? parseInt(idRaw, 10) : (card.querySelector(".product-title")?.textContent?.trim() || "");
      const priceEl = card.querySelector(".product-price span");
      const price = priceEl ? parseFloat(priceEl.textContent.replace("Rs", "").replace(/,/g, "").trim()) : 0;
      const stockRaw = card.dataset.stock;
      const stock = stockRaw !== undefined && !isNaN(parseInt(stockRaw, 10)) ? parseInt(stockRaw, 10) : 9999;

      const product = {
        id,
        name: card.querySelector(".product-title")?.textContent?.trim() || "Product",
        price,
        image: card.querySelector("img")?.src || "",
        description: card.querySelector(".product-description")?.textContent?.trim() || "",
        category: card.dataset.category || "default",
        stock
      };

      addToCart(product);
    });
  });
}

// ---------------- PRICE SLIDER COLOR ----------------
function updatePriceSliderColor(slider) {
  if (!slider) return;
  const min = parseFloat(slider.min) || 0;
  const max = parseFloat(slider.max) || 10000;
  const val = parseFloat(slider.value);
  const percentage = ((val - min) / (max - min)) * 100;
  
  // Use Fishify primary color (#0066CC)
  slider.style.background = `linear-gradient(to right, #0066CC ${percentage}%, #e0e0e0 ${percentage}%)`;
}

// ---------------- INIT ----------------
document.addEventListener("DOMContentLoaded", () => {
  updateCartCount();
  setupSearchBar();
  setupMobileMenu();
  setupAddToCartButtons();
});