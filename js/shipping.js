// ===== SHIPPING PAGE JAVASCRIPT =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form
    initShippingForm();
    
    // Initialize shipping methods
    initShippingMethods();
    
    // Initialize order summary
    updateOrderSummary();
    
    // Load saved shipping info
    loadSavedShippingInfo();
});

// Shipping form functionality
function initShippingForm() {
    const form = document.querySelector('.shipping-form');
    if (!form) return;
    
    // Form validation
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateShippingForm()) {
            saveShippingInfo();
            proceedToPayment();
        }
    });
    
    // Real-time validation
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
    
    // Phone number formatting
    const phoneInput = document.getElementById('phone-number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            formatPhoneNumber(this);
        });
    }
    
    // ZIP code validation
    const zipInput = document.getElementById('zip-code');
    if (zipInput) {
        zipInput.addEventListener('blur', function() {
            validateZipCode(this);
        });
    }
}

function validateShippingForm() {
    const form = document.querySelector('.shipping-form');
    const requiredInputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;
    
    requiredInputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    // Validate email format
    const emailInput = document.getElementById('email');
    if (emailInput && emailInput.value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailInput.value)) {
            showFieldError(emailInput, 'Please enter a valid email address');
            isValid = false;
        }
    }
    
    // Validate phone number
    const phoneInput = document.getElementById('phone-number');
    if (phoneInput && phoneInput.value) {
        const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
        if (!phoneRegex.test(phoneInput.value.replace(/\D/g, ''))) {
            showFieldError(phoneInput, 'Please enter a valid phone number');
            isValid = false;
        }
    }
    
    if (!isValid) {
        Fishify.showNotification('Please fix the errors in the form', 'error');
    }
    
    return isValid;
}

function validateField(field) {
    if (!field.value.trim()) {
        showFieldError(field, 'This field is required');
        return false;
    }
    
    // Special validation for specific fields
    switch(field.id) {
        case 'email':
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(field.value)) {
                showFieldError(field, 'Please enter a valid email address');
                return false;
            }
            break;
            
        case 'phone-number':
            const phoneRegex = /^[\+]?[(]?[0-9]{3}[)]?[-\s\.]?[0-9]{3}[-\s\.]?[0-9]{4,6}$/;
            if (!phoneRegex.test(field.value.replace(/\D/g, ''))) {
                showFieldError(field, 'Please enter a valid phone number');
                return false;
            }
            break;
            
        case 'zip-code':
            const zipRegex = /^\d{5}(-\d{4})?$/;
            if (!zipRegex.test(field.value)) {
                showFieldError(field, 'Please enter a valid ZIP code');
                return false;
            }
            break;
    }
    
    clearFieldError(field);
    return true;
}

function showFieldError(field, message) {
    // Remove existing error
    clearFieldError(field);
    
    // Add error style to field
    field.style.borderColor = '#E74C3C';
    field.style.boxShadow = '0 0 0 2px rgba(231, 76, 60, 0.1)';
    
    // Create error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: #E74C3C;
        font-size: 0.85rem;
        margin-top: 0.25rem;
    `;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.style.borderColor = '';
    field.style.boxShadow = '';
    
    const errorDiv = field.parentNode.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function formatPhoneNumber(input) {
    let value = input.value.replace(/\D/g, '');
    
    if (value.length > 3 && value.length <= 6) {
        value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
    } else if (value.length > 6) {
        value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 10)}`;
    }
    
    input.value = value;
}

function validateZipCode(input) {
    const zipRegex = /^\d{5}(-\d{4})?$/;
    if (!zipRegex.test(input.value)) {
        showFieldError(input, 'Please enter a valid ZIP code (e.g., 12345 or 12345-6789)');
        return false;
    }
    return true;
}

// Shipping methods
function initShippingMethods() {
    const shippingMethods = document.querySelectorAll('.shipping-method input[type="radio"]');
    
    shippingMethods.forEach(method => {
        method.addEventListener('change', function() {
            updateShippingCost(this.value);
            updateDeliveryEstimate(this.id);
        });
    });
}

function updateShippingCost(method) {
    const shippingCosts = {
        'standard': 15.00,
        'express': 25.00,
        'overnight': 45.00
    };
    
    const shippingRow = document.querySelector('.summary-row:nth-child(2) .amount');
    if (shippingRow) {
        const cost = shippingCosts[method] || 15.00;
        shippingRow.textContent = Fishify.formatPrice(cost);
        updateOrderTotal(cost);
        
        // Save selected shipping method
        localStorage.setItem('selected_shipping', method);
        localStorage.setItem('shipping_cost', cost.toString());
    }
}

function updateDeliveryEstimate(methodId) {
    const deliveryDate = document.querySelector('.delivery-date span');
    if (!deliveryDate) return;
    
    const estimates = {
        'standard': 'Arrives in 3-5 business days',
        'express': 'Arrives in 1-2 business days',
        'overnight': 'Arrives next business day'
    };
    
    const today = new Date();
    const deliveryDays = {
        'standard': 5,
        'express': 2,
        'overnight': 1
    };
    
    const daysToAdd = deliveryDays[methodId] || 5;
    const deliveryDay = new Date(today);
    deliveryDay.setDate(today.getDate() + daysToAdd);
    
    const options = { weekday: 'long', month: 'long', day: 'numeric' };
    const formattedDate = deliveryDay.toLocaleDateString('en-US', options);
    
    deliveryDate.textContent = `Arrives by ${formattedDate}`;
}

// Order summary
function updateOrderSummary() {
    const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    let subtotal = 0;
    
    cart.forEach(item => {
        subtotal += item.price * (item.quantity || 1);
    });
    
    // Update subtotal
    const subtotalElement = document.querySelector('.summary-row .amount:first-child');
    if (subtotalElement) {
        subtotalElement.textContent = Fishify.formatPrice(subtotal);
    }
    
    // Update order items
    updateOrderItems(cart);
    
    // Calculate and update total
    const shippingCost = parseFloat(localStorage.getItem('shipping_cost')) || 15.00;
    updateOrderTotal(shippingCost);
}

function updateOrderItems(cart) {
    const orderItemsContainer = document.querySelector('.order-items');
    if (!orderItemsContainer) return;
    
    // Clear existing items
    orderItemsContainer.innerHTML = '';
    
    // Add current cart items
    cart.forEach((item, index) => {
        const orderItem = document.createElement('div');
        orderItem.className = 'order-item';
        orderItem.innerHTML = `
            <div class="item-info">
                <div class="item-name">${item.name}</div>
                <div class="item-qty">Qty: ${item.quantity || 1}</div>
            </div>
            <div class="item-price">
                ${Fishify.formatPrice(item.price * (item.quantity || 1))}
            </div>
        `;
        orderItemsContainer.appendChild(orderItem);
    });
}

function updateOrderTotal(shippingCost) {
    const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    let subtotal = 0;
    
    cart.forEach(item => {
        subtotal += item.price * (item.quantity || 1);
    });
    
    const tax = subtotal * 0.08; // 8% tax
    const total = subtotal + shippingCost + tax;
    
    // Update total display
    const totalElement = document.querySelector('.total-amount');
    if (totalElement) {
        totalElement.textContent = Fishify.formatPrice(total);
    }
    
    // Save order total for payment page
    localStorage.setItem('order_total', total.toString());
}

// Save and load shipping info
function saveShippingInfo() {
    const shippingInfo = {
        fullName: document.getElementById('full-name').value,
        addressLine1: document.getElementById('address-line1').value,
        addressLine2: document.getElementById('address-line2').value,
        city: document.getElementById('city').value,
        state: document.getElementById('state').value,
        zipCode: document.getElementById('zip-code').value,
        phone: document.getElementById('phone-number').value,
        email: document.getElementById('email').value,
        shippingMethod: document.querySelector('.shipping-method input[type="radio"]:checked')?.id || 'standard'
    };
    
    localStorage.setItem('shipping_info', JSON.stringify(shippingInfo));
    Fishify.showNotification('Shipping information saved!');
}

function loadSavedShippingInfo() {
    const savedInfo = JSON.parse(localStorage.getItem('shipping_info'));
    
    if (savedInfo) {
        // Populate form fields
        document.getElementById('full-name').value = savedInfo.fullName || '';
        document.getElementById('address-line1').value = savedInfo.addressLine1 || '';
        document.getElementById('address-line2').value = savedInfo.addressLine2 || '';
        document.getElementById('city').value = savedInfo.city || '';
        document.getElementById('state').value = savedInfo.state || 'CA';
        document.getElementById('zip-code').value = savedInfo.zipCode || '';
        document.getElementById('phone-number').value = savedInfo.phone || '';
        document.getElementById('email').value = savedInfo.email || '';
        
        // Select shipping method
        const shippingMethod = document.getElementById(savedInfo.shippingMethod);
        if (shippingMethod) {
            shippingMethod.checked = true;
            updateShippingCost(savedInfo.shippingMethod);
            updateDeliveryEstimate(savedInfo.shippingMethod);
        }
    }
}

// Proceed to payment
function proceedToPayment() {
    Fishify.showNotification('Proceeding to payment...', 'info');
    
    // Simulate processing
    setTimeout(() => {
        // In a real app, this would redirect to payment page
        // window.location.href = 'payment.html';
        
        // For demo, show success message
        Fishify.showNotification('Shipping information confirmed! Ready for payment.', 'success');
        
        // Enable continue button animation
        const continueBtn = document.querySelector('.btn-continue');
        if (continueBtn) {
            continueBtn.innerHTML = '<i class="fas fa-check"></i> Ready for Payment';
            continueBtn.style.background = 'linear-gradient(135deg, #27AE60 0%, #2ECC71 100%)';
            
            // After 2 seconds, redirect to payment page
            setTimeout(() => {
                window.location.href = 'payment.html'; // You would create this page
            }, 2000);
        }
    }, 1000);
}

// Back to cart button
const backBtn = document.querySelector('.btn-back');
if (backBtn) {
    backBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Save current form data before leaving
        saveShippingInfo();
        
        // Redirect back to cart
        window.location.href = 'cart.html';
    });
}

// Add shipping page animations
if (!document.querySelector('#shipping-animations')) {
    const style = document.createElement('style');
    style.id = 'shipping-animations';
    style.textContent = `
        @keyframes highlightField {
            0% { background-color: transparent; }
            50% { background-color: rgba(52, 152, 219, 0.1); }
            100% { background-color: transparent; }
        }
        
        .form-group input:focus {
            animation: highlightField 0.5s ease;
        }
        
        .shipping-method input[type="radio"]:checked + label {
            animation: selectShipping 0.3s ease;
        }
        
        @keyframes selectShipping {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
    `;
    document.head.appendChild(style);
}