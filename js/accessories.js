// accessories.js - Accessories Page Functionality

document.addEventListener('DOMContentLoaded', function() {
    initAccessoriesPage();
});

function initAccessoriesPage() {
    setupAccessoryFilters();
    setupAccessorySorting();
    setupAccessoryCart();
    setupAccessoryPagination();
    updateCartCount();
}

// ==================== FILTERS ====================
function setupAccessoryFilters() {
    const priceSlider = document.querySelector('.price-slider');
    const categoryCheckboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
    
    if (priceSlider) {
        priceSlider.addEventListener('input', filterAccessories);
        updatePriceDisplay(priceSlider.value);
    }
    
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterAccessories);
    });
}

function filterAccessories() {
    const accessories = document.querySelectorAll('.fish-card');
    const maxPrice = parseInt(document.querySelector('.price-slider').value);
    
    accessories.forEach(accessory => {
        const price = parseInt(accessory.getAttribute('data-price'));
        const isVisible = price <= maxPrice;
        
        accessory.style.display = isVisible ? 'block' : 'none';
    });
    
    updatePriceDisplay(maxPrice);
}

function updatePriceDisplay(price) {
    const priceSpan = document.querySelector('.price-range span:last-child');
    if (priceSpan) {
        priceSpan.textContent = `Rs. ${price.toLocaleString()}+`;
    }
}

// ==================== SORTING ====================
function setupAccessorySorting() {
    const sortSelect = document.querySelector('.sort-by select');
    
    if (sortSelect) {
        sortSelect.addEventListener('change', sortAccessories);
    }
}

function sortAccessories() {
    const container = document.querySelector('.fish-grid');
    const accessories = Array.from(document.querySelectorAll('.fish-card'));
    const sortBy = document.querySelector('.sort-by select').value;
    
    accessories.sort((a, b) => {
        const priceA = parseInt(a.getAttribute('data-price'));
        const priceB = parseInt(b.getAttribute('data-price'));
        
        if (sortBy.includes('Low to High')) {
            return priceA - priceB;
        } else if (sortBy.includes('High to Low')) {
            return priceB - priceA;
        }
        return 0;
    });
    
    // Re-append visible cards
    container.innerHTML = '';
    accessories.forEach(accessory => {
        if (accessory.style.display !== 'none') {
            container.appendChild(accessory);
        }
    });
}

// ==================== ADD TO CART ====================
function setupAccessoryCart() {
    const addButtons = document.querySelectorAll('.fish-card .add-to-cart');
    
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const card = this.closest('.fish-card');
            const name = card.querySelector('h3').textContent;
            const price = parseInt(card.getAttribute('data-price'));
            
            // Add to cart
            addAccessoryToCart(name, price);
            
            // Show notification
            showAccessoryNotification(`${name} added to cart!`);
        });
    });
}

function addAccessoryToCart(name, price) {
    let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    
    // Create accessory item
    const accessory = {
        id: `accessory-${name.toLowerCase().replace(/\s+/g, '-')}`,
        name: name,
        price: price,
        quantity: 1,
        type: 'accessory'
    };
    
    cart.push(accessory);
    localStorage.setItem('fishifyCart', JSON.stringify(cart));
    updateCartCount();
}

// ==================== PAGINATION ====================
function setupAccessoryPagination() {
    const pageLinks = document.querySelectorAll('.pagination a');
    
    pageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Don't proceed if disabled
            if (this.classList.contains('disabled')) return;
            
            // Remove active class from all page numbers
            document.querySelectorAll('.page-number').forEach(page => {
                page.classList.remove('active');
            });
            
            // Add active class to clicked page number
            if (this.classList.contains('page-number')) {
                this.classList.add('active');
            }
            
            // Simulate page change
            console.log(`Navigating to ${this.textContent.trim()} page`);
            
            // Show loading effect
            this.style.opacity = '0.7';
            setTimeout(() => {
                this.style.opacity = '1';
            }, 300);
        });
    });
}

// ==================== NOTIFICATION ====================
function showAccessoryNotification(message) {
    // Remove existing notification
    const existing = document.querySelector('.accessory-notification');
    if (existing) existing.remove();
    
    // Create new notification
    const notification = document.createElement('div');
    notification.className = 'accessory-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #9b59b6;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-weight: 500;
        animation: slideIn 0.3s ease-out;
    `;
    
    // Add animation style
    if (!document.querySelector('#notification-animation')) {
        const style = document.createElement('style');
        style.id = 'notification-animation';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease-out reverse';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 3000);
}

// ==================== CART COUNT ====================
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    const cartCounts = document.querySelectorAll('.cart-count');
    cartCounts.forEach(count => {
        count.textContent = totalItems;
        count.style.display = totalItems > 0 ? 'flex' : 'none';
    });
}

// ==================== ADD HOVER EFFECTS ====================
document.querySelectorAll('.fish-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.02)';
        this.style.transition = 'transform 0.3s ease';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
});