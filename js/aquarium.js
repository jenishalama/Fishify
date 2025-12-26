// aquarium.js - Aquarium Page Functionality

document.addEventListener('DOMContentLoaded', function() {
    initAquariumPage();
});

function initAquariumPage() {
    setupFilters();
    setupSorting();
    setupAddToCart();
    setupPagination();
    updateCartCount();
}

// ==================== FILTERS ====================
function setupFilters() {
    const priceSlider = document.querySelector('.aquarium-price-slider');
    const materialCheckboxes = document.querySelectorAll('.aquarium-filter-options input[type="checkbox"]');
    
    if (priceSlider) {
        priceSlider.addEventListener('input', filterAquariums);
        updatePriceDisplay(priceSlider.value);
    }
    
    materialCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterAquariums);
    });
}

function filterAquariums() {
    const aquariums = document.querySelectorAll('.aquarium-card');
    const maxPrice = parseInt(document.querySelector('.aquarium-price-slider').value);
    
    aquariums.forEach(aquarium => {
        const priceText = aquarium.querySelector('.aquarium-price').textContent;
        const price = parseInt(priceText.replace(/[^0-9]/g, ''));
        const isVisible = price <= maxPrice;
        
        aquarium.style.display = isVisible ? 'block' : 'none';
    });
    
    // Update price display
    updatePriceDisplay(maxPrice);
}

function updatePriceDisplay(price) {
    const priceSpan = document.querySelector('.aquarium-price-range span:last-child');
    if (priceSpan) {
        priceSpan.textContent = `Rs. ${price.toLocaleString()}+`;
    }
}

// ==================== SORTING ====================
function setupSorting() {
    const sortSelect = document.querySelector('.aquarium-sort select');
    
    if (sortSelect) {
        sortSelect.addEventListener('change', sortAquariums);
    }
}

function sortAquariums() {
    const container = document.querySelector('.aquarium-grid');
    const aquariums = Array.from(document.querySelectorAll('.aquarium-card'));
    const sortBy = document.querySelector('.aquarium-sort select').value;
    
    aquariums.sort((a, b) => {
        const priceA = getPriceFromCard(a);
        const priceB = getPriceFromCard(b);
        
        if (sortBy.includes('Low to High')) {
            return priceA - priceB;
        } else if (sortBy.includes('High to Low')) {
            return priceB - priceA;
        }
        return 0; // Keep original order for popularity
    });
    
    // Re-append sorted cards
    container.innerHTML = '';
    aquariums.forEach(aquarium => {
        if (aquarium.style.display !== 'none') {
            container.appendChild(aquarium);
        }
    });
}

function getPriceFromCard(card) {
    const priceText = card.querySelector('.aquarium-price').textContent;
    return parseInt(priceText.replace(/[^0-9]/g, ''));
}

// ==================== ADD TO CART ====================
function setupAddToCart() {
    const addButtons = document.querySelectorAll('.aquarium-card .btn-primary');
    
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const card = this.closest('.aquarium-card');
            const name = card.querySelector('h3').textContent;
            const priceText = card.querySelector('.aquarium-price').textContent;
            const price = parseInt(priceText.replace(/[^0-9]/g, ''));
            
            // Add to cart
            addAquariumToCart(name, price);
            
            // Show notification
            showAquariumNotification(`${name} added to cart!`);
        });
    });
}

function addAquariumToCart(name, price) {
    let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    
    // Create aquarium item
    const aquarium = {
        id: `aquarium-${name.toLowerCase().replace(/\s+/g, '-')}`,
        name: name,
        price: price,
        quantity: 1,
        type: 'aquarium'
    };
    
    cart.push(aquarium);
    localStorage.setItem('fishifyCart', JSON.stringify(cart));
    updateCartCount();
}

// ==================== PAGINATION ====================
function setupPagination() {
    const pageLinks = document.querySelectorAll('.aquarium-pagination a');
    
    pageLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all page numbers
            document.querySelectorAll('.page-number').forEach(page => {
                page.classList.remove('active');
            });
            
            // Add active class to clicked page number
            if (this.classList.contains('page-number')) {
                this.classList.add('active');
            }
            
            // Simple page navigation
            console.log(`Navigating to ${this.textContent} page`);
            // In real app, you would load new data here
        });
    });
}

// ==================== NOTIFICATION ====================
function showAquariumNotification(message) {
    // Create notification
    const notification = document.createElement('div');
    notification.className = 'aquarium-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #3498db;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-weight: 500;
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
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