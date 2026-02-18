const CART_KEY = "fishify_cart";

// -------------------- Cart Helpers --------------------
function getCart() {
  return JSON.parse(localStorage.getItem(CART_KEY)) || [];
}

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

// -------------------- Render Cart --------------------
function renderCart() {
  const cartContainer = document.querySelector(".cart-items");
  const cart = getCart();

  if (!cartContainer) return;

  cartContainer.innerHTML = "";

  if (cart.length === 0) {
    cartContainer.innerHTML = `<p>Your cart is empty.</p>`;
    updateSummary(0);
    return;
  }

  let subtotal = 0;

  cart.forEach(item => {
    subtotal += item.price * item.qty;

    const itemEl = document.createElement("div");
    itemEl.classList.add("cart-item");
    itemEl.innerHTML = `
      <div class="cart-item-image">
        <img src="${item.image || '../Images/Homepage/bgfishify.jpg'}" alt="${item.name}">
      </div>
      <div class="cart-item-info">
        <h3>${item.name}</h3>
        <p>Rs ${item.price}</p>
        <div class="quantity-controls">
          <button class="qty-btn decrease" data-id="${item.id}">-</button>
          <span class="qty">${item.qty}</span>
          <button class="qty-btn increase" data-id="${item.id}">+</button>
        </div>
      </div>
      <div class="cart-item-actions">
        <button class="remove-item" data-id="${item.id}">
          <i class="fas fa-trash"></i> Remove
        </button>
      </div>
    `;

    cartContainer.appendChild(itemEl);
  });

  updateSummary(subtotal);

  // Add event listeners
  setupCartButtons();
}

// -------------------- Cart Actions --------------------
function setupCartButtons() {
  // Remove item
  document.querySelectorAll(".remove-item").forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      removeFromCart(id);
    });
  });

  // Quantity buttons
  document.querySelectorAll(".qty-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      const cart = getCart();
      const item = cart.find(i => i.id == id);
      if (!item) return;

      if (btn.classList.contains("increase")) item.qty++;
      if (btn.classList.contains("decrease") && item.qty > 1) item.qty--;

      saveCart(cart);
      renderCart();
    });
  });

  // Clear cart
  const clearBtn = document.querySelector(".clear-cart");
  if (clearBtn) {
    clearBtn.addEventListener("click", () => {
      saveCart([]);
      renderCart();
    });
  }
}

// -------------------- Update Summary --------------------
function updateSummary(subtotal) {
  const subtotalEl = document.querySelector(".summary-details .amount");
  const totalEl = document.querySelector(".total-amount");

  if (subtotalEl) subtotalEl.textContent = `Rs ${subtotal}`;
  if (totalEl) totalEl.textContent = `Rs ${subtotal}`;
}

// -------------------- Initialize --------------------
document.addEventListener("DOMContentLoaded", () => {
  renderCart();
  updateCartCount();
});