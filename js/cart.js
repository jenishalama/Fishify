// -------------------- Render Cart --------------------
function renderCart() {
  const cartContainer = document.querySelector(".cart-items");
  const rawCart = typeof getCart === "function" ? getCart() : [];

  if (!cartContainer) return;

  cartContainer.innerHTML = "";

  // Normalize any legacy cart items (quantity -> qty, old key shape)
  const cart = rawCart.map(item => {
    const qty = item.qty != null ? item.qty : (item.quantity || 1);
    return { ...item, qty };
  });
  // If there were legacy items, persist normalized form
  if (cart.length !== rawCart.length || cart.some((it, idx) => it.qty !== rawCart[idx]?.qty)) {
    if (typeof saveCart === "function") {
      saveCart(cart);
    }
  }

  if (cart.length === 0) {
    cartContainer.classList.add("is-empty");
    cartContainer.innerHTML = `
      <div class="empty-cart">
        <div class="empty-cart-icon">
          <i class="fas fa-shopping-basket"></i>
        </div>
        <h3>Your cart is empty</h3>
        <p class="empty-cart-text">
          Looks like you haven&apos;t added anything yet. Use &quot;Continue Shopping&quot; below to explore our fishes, aquariums, accessories and plants.
        </p>
      </div>
    `;
    updateSummary(0);
    return;
  }

  cartContainer.classList.remove("is-empty");

  let subtotal = 0;

  cart.forEach(item => {
    const lineQty = item.qty || 1;
    const price = item.price || 0;
    subtotal += price * lineQty;

    const itemEl = document.createElement("div");
    itemEl.classList.add("cart-item");
    itemEl.innerHTML = `
      <div class="item-image">
        <img src="${item.image || "../Images/Homepage/bgfishify.jpg"}" alt="${item.name}" style="width:100%;height:100%;object-fit:cover;">
      </div>
      <div class="item-details">
        <div class="item-header">
          <h3>${item.name}</h3>
          <button class="remove-item" data-id="${item.id}" title="Remove item">
            <i class="fas fa-trash"></i>
          </button>
        </div>
        <p class="item-description">${item.description || ""}</p>
        <div class="item-controls">
          <div class="quantity-control">
            <button class="qty-btn decrease" data-id="${item.id}">-</button>
            <span class="qty-input">${lineQty}</span>
            <button class="qty-btn increase" data-id="${item.id}">+</button>
          </div>
          <div class="item-price">
            Rs ${price.toLocaleString()}
          </div>
        </div>
      </div>
    `;

    cartContainer.appendChild(itemEl);
  });

  updateSummary(subtotal);

  // Add event listeners
  setupCartButtons();
}

// -------------------- Cart Helpers --------------------
function removeFromCart(id) {
  const cart = typeof getCart === "function" ? getCart() : [];
  const next = cart.filter(item => String(item.id) !== String(id));
  if (typeof saveCart === "function") {
    saveCart(next);
  } else {
    localStorage.setItem("fishify_cart", JSON.stringify(next));
  }
  renderCart();
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

  // Quantity buttons (cap increase at item.stock when present)
  document.querySelectorAll(".qty-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const id = btn.dataset.id;
      const cart = typeof getCart === "function" ? getCart() : [];
      const item = cart.find(i => i.id == id);
      if (!item) return;

      const currentQty = item.qty || item.quantity || 1;
      const maxQty = typeof item.stock === "number" && item.stock >= 0 ? item.stock : 9999;

      if (btn.classList.contains("increase")) {
        item.qty = Math.min(currentQty + 1, maxQty);
      }
      if (btn.classList.contains("decrease") && currentQty > 1) {
        item.qty = currentQty - 1;
      }
      if (item.quantity != null) delete item.quantity;

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

  const formatted = `Rs ${subtotal.toLocaleString()}`;
  if (subtotalEl) subtotalEl.textContent = formatted;
  if (totalEl) totalEl.textContent = formatted;
}

// -------------------- Initialize --------------------
document.addEventListener("DOMContentLoaded", () => {
  renderCart();
  updateCartCount();

  const checkoutBtn = document.querySelector(".btn-checkout");
  if (checkoutBtn) {
    checkoutBtn.addEventListener("click", e => {
      e.preventDefault();

      const cart = typeof getCart === "function" ? getCart() : [];
      if (!cart.length) {
        if (typeof showCartToast === "function") {
          showCartToast("Your cart is empty. Please add some items first.", "error");
        } else {
          alert("Your cart is empty. Please add some items first.");
        }
        return;
      }

      openInvoiceModal(cart);
    });
  }
});

// -------------------- Invoice Modal --------------------
function openInvoiceModal(cart) {
  let overlay = document.querySelector(".invoice-overlay");
  if (overlay) overlay.remove();

  overlay = document.createElement("div");
  overlay.className = "invoice-overlay";

  const modal = document.createElement("div");
  modal.className = "invoice-modal";

  const header = document.createElement("div");
  header.className = "invoice-header";
  header.innerHTML = `
    <h3><i class="fas fa-file-invoice"></i> Order Invoice</h3>
    <button type="button" class="invoice-close">&times;</button>
  `;

  const body = document.createElement("div");
  body.className = "invoice-body";

  let subtotal = 0;
  const rows = cart.map(item => {
    const qty = item.qty || item.quantity || 1;
    const price = item.price || 0;
    const lineTotal = qty * price;
    subtotal += lineTotal;
    return `
      <tr>
        <td>${item.name}</td>
        <td class="invoice-center">${qty}</td>
        <td class="invoice-right">Rs ${price.toLocaleString()}</td>
        <td class="invoice-right">Rs ${lineTotal.toLocaleString()}</td>
      </tr>
    `;
  }).join("");

  body.innerHTML = `
    <p class="invoice-note">
      Review your order details below. Payment method is <strong>Cash on Delivery</strong>.
    </p>
    <table class="invoice-table">
      <thead>
        <tr>
          <th>Product</th>
          <th class="invoice-center">Qty</th>
          <th class="invoice-right">Price</th>
          <th class="invoice-right">Total</th>
        </tr>
      </thead>
      <tbody>
        ${rows}
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" class="invoice-right"><strong>Order Total</strong></td>
          <td class="invoice-right"><strong>Rs ${subtotal.toLocaleString()}</strong></td>
        </tr>
      </tfoot>
    </table>
    <p class="invoice-payment">
      <i class="fas fa-money-bill-wave"></i>
      You&apos;ll pay in cash when your order is delivered.
    </p>
  `;

  const isLoggedIn = typeof window.FISHIFY_LOGGED_IN !== "undefined" && window.FISHIFY_LOGGED_IN;
  const footer = document.createElement("div");
  footer.className = "invoice-footer";
  footer.innerHTML = `
    <button type="button" class="invoice-cancel">Review Again</button>
    <button type="button" class="invoice-confirm">
      ${isLoggedIn ? "Proceed to Checkout" : "Continue to Login"}
    </button>
  `;

  modal.appendChild(header);
  modal.appendChild(body);
  modal.appendChild(footer);
  overlay.appendChild(modal);
  document.body.appendChild(overlay);

  const close = () => overlay.remove();

  overlay.addEventListener("click", e => {
    if (e.target === overlay) close();
  });
  modal.querySelector(".invoice-close").addEventListener("click", close);
  modal.querySelector(".invoice-cancel").addEventListener("click", close);
  modal.querySelector(".invoice-confirm").addEventListener("click", () => {
    if (isLoggedIn) {
      window.location.href = "checkout.php";
    } else {
      window.location.href = "login.php?next=" + encodeURIComponent("checkout.php");
    }
  });
}