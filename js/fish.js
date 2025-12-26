// fish.js - Fish Page Functionality

document.addEventListener('DOMContentLoaded', function() {
    // Initialize fish page features
    initFiltering();
    initSorting();
    initFishCart();
});

// ==================== FILTERING FUNCTIONALITY ====================
function initFiltering() {
    const priceSlider = document.querySelector('.price-slider');
    const availabilityCheckboxes = document.querySelectorAll('input[name="availability"]');
    
    if (priceSlider) {
        priceSlider.addEventListener('input', filterFish);
    }
    
    availabilityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', filterFish);
    });
}

function filterFish() {
    const fishCards = document.querySelectorAll('.fish-card');
    const maxPrice = document.querySelector('.price-slider').value;
    
    fishCards.forEach(card => {
        const fishPrice = parseInt(card.getAttribute('data-price'));
        const isInStock = true; // You can add stock data to your fish cards
        
        // Check price filter
        const pricePass = fishPrice <= maxPrice;
        
        // Show/hide based on filters
        if (pricePass) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
    
    updatePriceDisplay(maxPrice);
}

function updatePriceDisplay(maxPrice) {
    const priceRangeSpan = document.querySelector('.price-range span:last-child');
    if (priceRangeSpan) {
        priceRangeSpan.textContent = `Rs. ${maxPrice.toLocaleString()}`;
    }
}

// ==================== SORTING FUNCTIONALITY ====================
function initSorting() {
    const sortSelect = document.querySelector('.sort-by select');
    
    if (sortSelect) {
        sortSelect.addEventListener('change', sortFish);
    }
}

function sortFish() {
    const sortBy = document.querySelector('.sort-by select').value;
    const fishContainer = document.querySelector('.fish-grid');
    const fishCards = Array.from(document.querySelectorAll('.fish-card'));
    
    fishCards.sort((a, b) => {
        const priceA = parseInt(a.getAttribute('data-price'));
        const priceB = parseInt(b.getAttribute('data-price'));
        
        if (sortBy.includes('Low to High')) {
            return priceA - priceB;
        } else if (sortBy.includes('High to Low')) {
            return priceB - priceA;
        }
        return 0; // For popularity, keep original order
    });
    
    // Clear and re-append sorted cards
    fishContainer.innerHTML = '';
    fishCards.forEach(card => {
        fishContainer.appendChild(card);
    });
}

// ==================== ADD TO CART FOR FISH ====================
function initFishCart() {
    const addToCartButtons = document.querySelectorAll('.fish-card .add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const fishCard = this.closest('.fish-card');
            const fishName = fishCard.querySelector('h3').textContent;
            const fishPrice = fishCard.querySelector('.amount').textContent;
            
            // Get cart from localStorage or create new
            let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
            
            // Create fish object
            const fishItem = {
                id: generateFishId(fishName),
                name: fishName,
                price: parseFloat(fishPrice),
                quantity: 1,
                type: 'fish'
            };
            
            // Add to cart
            cart.push(fishItem);
            localStorage.setItem('fishifyCart', JSON.stringify(cart));
            
            // Update cart count
            updateCartCount();
            
            // Show success message
            showFishNotification(`${fishName} added to cart!`);
        });
    });
}

function generateFishId(name) {
    return 'fish-' + name.toLowerCase().replace(/[^a-z0-9]/g, '-');
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    const totalItems = cart.length;
    
    const cartCountElements = document.querySelectorAll('.cart-count');
    cartCountElements.forEach(element => {
        element.textContent = totalItems;
        element.style.display = totalItems > 0 ? 'flex' : 'none';
    });
}

function showFishNotification(message) {
    // Create simple notification
    const notification = document.createElement('div');
    notification.className = 'fish-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #2ecc71;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;
    
    document.body.appendChild(notification);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// ==================== PAGINATION ====================
document.querySelectorAll('.page-number, .page-link').forEach(link => {
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
        
        // Simple pagination simulation
        const pageNum = this.textContent;
        if (!isNaN(pageNum)) {
            console.log(`Loading page ${pageNum}...`);
            // In real implementation, you would load new fish data here
        }
    });
});

// ==================== INITIAL SETUP ====================
// Update cart count on page load
updateCartCount();

// Initialize price display
const priceSlider = document.querySelector('.price-slider');
if (priceSlider) {
    updatePriceDisplay(priceSlider.value);
}