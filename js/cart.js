// ===== CART PAGE JAVASCRIPT =====
document.addEventListener('DOMContentLoaded', function() {
    // Load cart items
    loadCartItems();
    
    // Setup event listeners
    setupCartControls();
    setupCouponCode();
    setupCheckoutButton();
    
    // Initialize cart
    updateCartTotals();
});

// Load cart items from localStorage
function loadCartItems() {
    const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    const cartItemsContainer = document.querySelector('.cart-items');
    
    if (!cartItemsContainer) return;
    
    if (cart.length === 0) {
        cartItemsContainer.innerHTML = `
            <div class="empty-cart">
                <div class="empty-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <h3>Your cart is empty</h3>
                <p>Add some fishy friends to your cart!</p>
                <a href="fish.html" class="btn btn-primary">
                    <i class="fas fa-fish"></i> Browse Fish
                </a>
            </div>
        `;
        return;
    }
    
    cartItemsContainer.innerHTML = '';
    
    cart.forEach((item, index) => {
        const cartItem = createCartItem(item, index);
        cartItemsContainer.appendChild(cartItem);
    });
    
    // Add styles for empty cart
    if (!document.querySelector('#empty-cart-styles')) {
        const style = document.createElement('style');
        style.id = 'empty-cart-styles';
        style.textContent = `
            .empty-cart {
                text-align: center;
                padding: 4rem 2rem;
            }
            
            .empty-icon {
                font-size: 4rem;
                color: var(--cart-blue);
                margin-bottom: 1.5rem;
                opacity: 0.5;
            }
            
            .empty-cart h3 {
                color: var(--cart-dark);
                margin-bottom: 0.5rem;
            }
            
            .empty-cart p {
                color: var(--gray);
                margin-bottom: 2rem;
            }
        `;
        document.head.appendChild(style);
    }
}

function createCartItem(item, index) {
    const colors = ['#2D9CDB', '#219653', '#F2994A', '#9B51E0', '#EB5757'];
    const color = colors[index % colors.length];
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'cart-item';
    itemDiv.dataset.index = index;
    itemDiv.dataset.price = item.price;
    
    itemDiv.innerHTML = `
        <div class="item-image" style="background: linear-gradient(135deg, ${color}80, ${color})">
            <div class="item-effect"></div>
            <span class="item-initial">${item.name.charAt(0)}</span>
        </div>
        <div class="item-details">
            <div class="item-header">
                <h3>${item.name}</h3>
                <button class="remove-item" data-index="${index}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <p class="item-description">${item.description || 'Premium aquatic product'}</p>
            <div class="item-seller">
                <i class="fas fa-store"></i>
                <span>${item.seller || 'Fishify Store'}</span>
            </div>
            <div class="item-controls">
                <div class="quantity-control">
                    <button class="qty-btn minus" data-index="${index}">
                        <i class="fas fa-minus"></i>
                    </button>
                    <input type="text" class="qty-input" value="${item.quantity || 1}" 
                           data-index="${index}" readonly>
                    <button class="qty-btn plus" data-index="${index}">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
                <div class="item-price">
                    ${Fishify.formatPrice(item.price * (item.quantity || 1))}
                </div>
            </div>
        </div>
    `;
    
    // Add item effect style
    const itemImage = itemDiv.querySelector('.item-image');
    itemImage.style.position = 'relative';
    itemImage.style.overflow = 'hidden';
    
    const itemEffect = itemDiv.querySelector('.item-effect');
    itemEffect.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: radial-gradient(circle at 30% 30%, rgba(255,255,255,0.3) 2%, transparent 20%),
                    radial-gradient(circle at 70% 70%, rgba(255,255,255,0.3) 2%, transparent 20%);
    `;
    
    const itemInitial = itemDiv.querySelector('.item-initial');
    itemInitial.style.cssText = `
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2.5rem;
        font-weight: bold;
        color: white;
        opacity: 0.8;
    `;
    
    return itemDiv;
}

// Setup cart controls
function setupCartControls() {
    // Quantity controls
    document.addEventListener('click', function(e) {
        if (e.target.closest('.qty-btn')) {
            const button = e.target.closest('.qty-btn');
            const index = parseInt(button.dataset.index);
            const isPlus = button.classList.contains('plus');
            
            updateQuantity(index, isPlus ? 1 : -1);
        }
        
        // Remove item
        if (e.target.closest('.remove-item')) {
            const button = e.target.closest('.remove-item');
            const index = parseInt(button.dataset.index);
            
            removeItem(index);
        }
    });
    
    // Quantity input change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            const input = e.target;
            const index = parseInt(input.dataset.index);
            const value = parseInt(input.value) || 1;
            
            updateQuantity(index, value, true);
        }
    });
    
    // Clear cart button
    const clearCartBtn = document.querySelector('.clear-cart');
    if (clearCartBtn) {
        clearCartBtn.addEventListener('click', clearCart);
    }
    
    // Continue shopping
    const continueShopping = document.querySelector('.continue-shopping');
    if (continueShopping) {
        continueShopping.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = 'fish.html';
        });
    }
}

function updateQuantity(index, change, setAbsolute = false) {
    const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    
    if (index >= 0 && index < cart.length) {
        if (setAbsolute) {
            cart[index].quantity = Math.max(1, change);
        } else {
            cart[index].quantity = Math.max(1, (cart[index].quantity || 1) + change);
        }
        
        localStorage.setItem('fishify_cart', JSON.stringify(cart));
        Fishify.updateCartCount();
        
        // Update display
        const qtyInput = document.querySelector(`.qty-input[data-index="${index}"]`);
        const itemPrice = document.querySelector(`.cart-item[data-index="${index}"] .item-price`);
        
        if (qtyInput) {
            qtyInput.value = cart[index].quantity;
        }
        
        if (itemPrice) {
            const total = cart[index].price * cart[index].quantity;
            itemPrice.textContent = Fishify.formatPrice(total);
        }
        
        updateCartTotals();
        Fishify.showNotification('Cart updated!');
    }
}

function removeItem(index) {
    const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    
    if (index >= 0 && index < cart.length) {
        const itemName = cart[index].name;
        cart.splice(index, 1);
        
        localStorage.setItem('fishify_cart', JSON.stringify(cart));
        Fishify.updateCartCount();
        
        // Reload cart items
        loadCartItems();
        updateCartTotals();
        
        Fishify.showNotification(`${itemName} removed from cart`);
    }
}

function clearCart() {
    if (confirm('Are you sure you want to clear your cart?')) {
        localStorage.removeItem('fishify_cart');
        Fishify.updateCartCount();
        loadCartItems();
        updateCartTotals();
        Fishify.showNotification('Cart cleared!');
    }
}

// Update cart totals
function updateCartTotals() {
    const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.price * (item.quantity || 1);
    });
    
    const shipping = 15.00;
    const tax = subtotal * 0.08; // 8% tax
    const total = subtotal + shipping + tax;
    
    // Update display
    const subtotalEl = document.querySelector('.summary-row .amount:nth-child(1)');
    const shippingEl = document.querySelector('.summary-row .amount:nth-child(2)');
    const taxEl = document.querySelector('.summary-row .amount:nth-child(3)');
    const totalEl = document.querySelector('.total-amount');
    
    if (subtotalEl) subtotalEl.textContent = Fishify.formatPrice(subtotal);
    if (shippingEl) shippingEl.textContent = Fishify.formatPrice(shipping);
    if (taxEl) taxEl.textContent = Fishify.formatPrice(tax);
    if (totalEl) totalEl.textContent = Fishify.formatPrice(total);
}

// Coupon code functionality
function setupCouponCode() {
    const applyBtn = document.querySelector('.apply-coupon');
    const couponInput = document.querySelector('.coupon-input input');
    
    if (applyBtn && couponInput) {
        applyBtn.addEventListener('click', function() {
            const code = couponInput.value.trim().toUpperCase();
            
            if (!code) {
                Fishify.showNotification('Please enter a coupon code', 'error');
                return;
            }
            
            applyCoupon(code);
        });
        
        couponInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const code = this.value.trim().toUpperCase();
                applyCoupon(code);
            }
        });
    }
}

function applyCoupon(code) {
    const validCoupons = {
        'FISHIFY10': 0.1,   // 10% off
        'AQUATIC20': 0.2,   // 20% off
        'WELCOME15': 0.15,  // 15% off
        'REEF25': 0.25      // 25% off
    };
    
    if (validCoupons[code]) {
        const discount = validCoupons[code];
        const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
        let subtotal = 0;
        
        cart.forEach(item => {
            subtotal += item.price * (item.quantity || 1);
        });
        
        const discountAmount = subtotal * discount;
        const newTotal = subtotal - discountAmount + 15.00 + (subtotal * 0.08);
        
        // Show discount applied
        Fishify.showNotification(`Coupon applied! ${discount * 100}% discount`);
        
        // Update totals with discount
        updateTotalsWithDiscount(discountAmount);
        
        // Save coupon in localStorage
        localStorage.setItem('applied_coupon', JSON.stringify({
            code: code,
            discount: discount,
            amount: discountAmount
        }));
    } else {
        Fishify.showNotification('Invalid coupon code', 'error');
    }
}

function updateTotalsWithDiscount(discountAmount) {
    const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    let subtotal = 0;
    
    cart.forEach(item => {
        subtotal += item.price * (item.quantity || 1);
    });
    
    const shipping = 15.00;
    const tax = subtotal * 0.08;
    const total = subtotal - discountAmount + shipping + tax;
    
    // Add discount row if it doesn't exist
    let discountRow = document.querySelector('.summary-row.discount');
    if (!discountRow) {
        const summaryDetails = document.querySelector('.summary-details');
        if (summaryDetails) {
            discountRow = document.createElement('div');
            discountRow.className = 'summary-row discount';
            discountRow.innerHTML = `
                <span>Discount</span>
                <span class="amount discount-amount" style="color: var(--cart-green);">-${Fishify.formatPrice(discountAmount)}</span>
            `;
            
            const subtotalRow = document.querySelector('.summary-row:nth-child(1)');
            if (subtotalRow) {
                subtotalRow.after(discountRow);
            }
        }
    } else {
        discountRow.querySelector('.discount-amount').textContent = 
            `-${Fishify.formatPrice(discountAmount)}`;
    }
    
    // Update total
    const totalEl = document.querySelector('.total-amount');
    if (totalEl) {
        totalEl.textContent = Fishify.formatPrice(total);
    }
}
