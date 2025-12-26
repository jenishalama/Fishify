// Get cart items from localStorage
let cartItems = JSON.parse(localStorage.getItem("cartItems")) || [];

// Elements
const cartContainer = document.querySelector(".cart-items");
const subtotalEl = document.querySelector(".summary-row .amount");
const totalEl = document.querySelector(".total-amount");

// Render Cart
function renderCart() {
  cartContainer.innerHTML = "";
  let subtotal = 0;

  cartItems.forEach((item, index) => {
    subtotal += item.price * item.qty;

    const cartItem = document.createElement("div");
    cartItem.className = "cart-item";
    cartItem.innerHTML = `
      <div class="item-details">
        <div class="item-header">
          <h3>${item.title}</h3>
          <button class="remove-item" data-index="${index}">
            <i class="fas fa-trash"></i>
          </button>
        </div>
        <p class="item-description">${item.description}</p>
        <div class="item-seller">
          <i class="fas fa-store"></i>
          <span>${item.seller}</span>
        </div>
        <div class="item-controls">
          <div class="quantity-control">
            <button class="qty-btn minus" data-index="${index}"><i class="fas fa-minus"></i></button>
            <input type="text" value="${item.qty}" class="qty-input" data-index="${index}">
            <button class="qty-btn plus" data-index="${index}"><i class="fas fa-plus"></i></button>
          </div>
          <div class="item-price">Rs ${item.price * item.qty}</div>
        </div>
      </div>
    `;
    cartContainer.appendChild(cartItem);
  });

  subtotalEl.textContent = `Rs ${subtotal}`;
  totalEl.textContent = `Rs ${subtotal}`; // You can add shipping/tax if needed
  updateCartCount();
}

// Handle Remove, Quantity Change
cartContainer.addEventListener("click", (e) => {
  const index = e.target.closest(".remove-item")?.dataset.index;
  if (index !== undefined) {
    cartItems.splice(index, 1);
    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    renderCart();
  }

  const minusIndex = e.target.closest(".minus")?.dataset.index;
  if (minusIndex !== undefined) {
    if (cartItems[minusIndex].qty > 1) cartItems[minusIndex].qty -= 1;
    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    renderCart();
  }

  const plusIndex = e.target.closest(".plus")?.dataset.index;
  if (plusIndex !== undefined) {
    cartItems[plusIndex].qty += 1;
    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    renderCart();
  }
});

// Handle manual quantity input
cartContainer.addEventListener("input", (e) => {
  if (e.target.classList.contains("qty-input")) {
    const index = e.target.dataset.index;
    let val = parseInt(e.target.value);
    if (isNaN(val) || val < 1) val = 1;
    cartItems[index].qty = val;
    localStorage.setItem("cartItems", JSON.stringify(cartItems));
    renderCart();
  }
});

// Clear Cart
document.querySelector(".clear-cart").addEventListener("click", () => {
  cartItems = [];
  localStorage.setItem("cartItems", JSON.stringify(cartItems));
  renderCart();
});

// Initialize
renderCart();