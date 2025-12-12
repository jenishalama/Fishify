// Fish Page Specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Price Slider
    const priceSlider = document.querySelector('.price-slider');
    const priceMax = document.querySelector('.price-range span:last-child');
    
    if (priceSlider) {
        priceSlider.addEventListener('input', function() {
            priceMax.textContent = `$${this.value}+`;
            filterFish();
        });
    }
    
    // Filter Checkboxes
    const checkboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterFish);
    });
    
    // Clear Filters Button
    const clearFiltersBtn = document.querySelector('.clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearAllFilters);
    }
    
    // Remove Filter Tags
    document.querySelectorAll('.filter-tag i').forEach(icon => {
        icon.addEventListener('click', function() {
            const tag = this.parentElement;
            tag.remove();
        });
    });
    
    // Add to Cart Buttons
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const fishCard = this.closest('.fish-card');
            const fishName = fishCard.querySelector('h3').textContent;
            const fishPrice = fishCard.querySelector('.fish-price').textContent;
            
            addToCart(fishName, fishPrice);
            
            // Show notification
            showNotification(`${fishName} added to cart!`);
        });
    });
    
    // Sort Functionality
    const sortSelect = document.querySelector('.sort-by select');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortFish(this.value);
        });
    }
});

function filterFish() {
    const fishCards = document.querySelectorAll('.fish-card');
    const maxPrice = parseInt(document.querySelector('.price-slider').value);
    
    fishCards.forEach(card => {
        const price = parseFloat(card.dataset.price);
        const size = card.dataset.size;
        const behavior = card.dataset.behavior;
        const water = card.dataset.water;
        
        // Check selected filters
        const selectedSizes = getSelectedValues('size');
        const selectedBehaviors = getSelectedValues('behavior');
        const selectedWaters = getSelectedValues('water-type');
        
        let showCard = true;
        
        // Price filter
        if (price > maxPrice) {
            showCard = false;
        }
        
        // Size filter
        if (selectedSizes.length > 0 && !selectedSizes.includes(size)) {
            showCard = false;
        }
        
        // Behavior filter
        if (selectedBehaviors.length > 0 && !selectedBehaviors.includes(behavior)) {
            showCard = false;
        }
        
        // Water type filter
        if (selectedWaters.length > 0 && !selectedWaters.includes(water)) {
            showCard = false;
        }
        
        // Show/hide card
        card.style.display = showCard ? 'block' : 'none';
    });
}

function getSelectedValues(name) {
    const checkboxes = document.querySelectorAll(`input[name="${name}"]:checked`);
    return Array.from(checkboxes).map(cb => cb.value);
}

function clearAllFilters() {
    // Uncheck all checkboxes
    document.querySelectorAll('.filter-options input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    
    // Reset price slider
    const priceSlider = document.querySelector('.price-slider');
    if (priceSlider) {
        priceSlider.value = 100;
        document.querySelector('.price-range span:last-child').textContent = '$100+';
    }
    
    // Clear filter tags
    document.querySelector('.active-filters').innerHTML = '';
    
    // Show all fish
    document.querySelectorAll('.fish-card').forEach(card => {
        card.style.display = 'block';
    });
}

function addToCart(name, price) {
    // Get existing cart or create new one
    let cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    
    // Check if item already exists
    const existingItem = cart.find(item => item.name === name);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            name: name,
            price: parseFloat(price.replace('$', '')),
            quantity: 1,
            image: '' // You can add image URLs here
        });
    }
    
    // Save to localStorage
    localStorage.setItem('fishify_cart', JSON.stringify(cart));
    
    // Update cart count
    updateCartCount();
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        cartCount.textContent = totalItems;
    }
}

function showNotification(message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-check-circle"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: var(--success);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 1000;
        animation: slideIn 0.3s ease;
        font-weight: 500;
    `;
    
    notification.querySelector('.notification-content').style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.75rem;
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

function sortFish(criteria) {
    const fishGrid = document.querySelector('.fish-grid');
    const fishCards = Array.from(document.querySelectorAll('.fish-card'));
    
    fishCards.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price);
        const priceB = parseFloat(b.dataset.price);
        
        switch(criteria) {
            case 'Price: Low to High':
                return priceA - priceB;
            case 'Price: High to Low':
                return priceB - priceA;
            default:
                return 0;
        }
    });
    
    // Reorder in DOM
    fishCards.forEach(card => {
        fishGrid.appendChild(card);
    });
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Initialize cart count on page load
updateCartCount();