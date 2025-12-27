// CART KEY
const CART_KEY = "fishify_cart";

// Load cart from localStorage
function getCart() {
  return JSON.parse(localStorage.getItem(CART_KEY)) || [];
}

// Save cart to localStorage
function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  updateCartCount(); // header cart count from main.js
}

// Render cart items
function renderCart() {
  const cartContainer = document.querySelector(".cart-items");
  const subtotalEl = document.querySelector(".summary-row .amount");
  const totalEl = document.querySelector(".total-amount");
  const cart = getCart();

  if (!cartContainer) return;

  cartContainer.innerHTML = "";

  if (cart.length === 0) {
    cartContainer.innerHTML = `<p style="text-align:center; padding:20px;">Your cart is empty 🛒</p>`;
    subtotalEl.textContent = "Rs 0";
    totalEl.textContent = "Rs 0";
    return;
  }

  let subtotal = 0;

  cart.forEach((item, index) => {
    const itemTotal = item.price * item.qty;
    subtotal += itemTotal;

    const div = document.createElement("div");
    div.className = "cart-item";
    div.style = "display:flex; justify-content:space-between; align-items:center; padding:10px 0; border-bottom:1px solid #ccc;";

    div.innerHTML = `
      <span>${item.name} x ${item.qty}</span>
      <span>Rs ${itemTotal}</span>
      <button class="remove-item" data-index="${index}" style="margin-left:10px; background:#dc3545; color:white; border:none; padding:2px 6px; border-radius:4px; cursor:pointer;">Remove</button>
    `;
    cartContainer.appendChild(div);
  });

  subtotalEl.textContent = `Rs ${subtotal}`;
  totalEl.textContent = `Rs ${subtotal}`; // For now total = subtotal
}

// Remove item
function setupRemoveButtons() {
  const buttons = document.querySelectorAll(".remove-item");
  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      const index = btn.dataset.index;
      let cart = getCart();
      cart.splice(index, 1);
      saveCart(cart);
      renderCart();
    });
  });
}

// Clear cart
function setupClearCart() {
  const clearBtn = document.querySelector(".clear-cart");
  if (clearBtn) {
    clearBtn.addEventListener("click", () => {
      if (confirm("Are you sure you want to clear your cart?")) {
        localStorage.removeItem(CART_KEY);
        renderCart();
      }
    });
  }
}

// Initialize cart page
document.addEventListener("DOMContentLoaded", () => {
  renderCart();
  setupClearCart();

  // Use delegation for remove buttons because they are dynamically created
  document.querySelector(".cart-items").addEventListener("click", e => {
    if (e.target.classList.contains("remove-item")) {
      const index = e.target.dataset.index;
      let cart = getCart();
      cart.splice(index, 1);
      saveCart(cart);
      renderCart();
    }
  });
});