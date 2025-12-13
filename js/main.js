// ===== MAIN JAVASCRIPT =====
// Cart functionality
let cart = JSON.parse(localStorage.getItem('fishify_cart')) || [];

// Initialize cart count
function updateCartCount() {
    const totalItems = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
    const cartCountElements = document.querySelectorAll('.cart-count');
    
    cartCountElements.forEach(element => {
        element.textContent = totalItems;
    });
}

// Add to cart functionality
function addToCart(product) {
    const existingItem = cart.find(item => 
        item.id === product.id || 
        (item.name === product.name && item.price === product.price)
    );
    
    if (existingItem) {
        existingItem.quantity = (existingItem.quantity || 1) + 1;
    } else {
        product.quantity = 1;
        cart.push(product);
    }
    
    localStorage.setItem('fishify_cart', JSON.stringify(cart));
    updateCartCount();
    showNotification(`${product.name} added to cart!`);
}

// Show notification
function showNotification(message, type = 'success') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => {
        notification.remove();
    });
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#27AE60' : '#E74C3C'};
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease;
        font-weight: 500;
        max-width: 350px;
    `;
    
    notification.querySelector('.notification-content').style.cssText = `
        display: flex;
        align-items: center;
        gap: 0.75rem;
    `;
    
    // Add animation styles
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
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
            
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
            }
        `;
        document.head.appendChild(style);
    }
    
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

// Product card click handlers
function setupProductCards() {
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const card = this.closest('.product-card, .fish-card, .aquarium-card, .accessory-card, .related-card');
            if (!card) return;
            
            const product = {
                id: card.dataset.id || Date.now().toString(),
                name: card.querySelector('h3, .product-title, .fish-info h3, .aquarium-info h3, .accessory-info h3').textContent,
                price: parseFloat(card.querySelector('.product-price, .fish-price, .aquarium-price, .accessory-price, .related-price').textContent.replace('$', '').replace(',', '')),
                image: card.querySelector('.product-image, .fish-image, .aquarium-image, .accessory-image, .related-image')?.style.backgroundImage || '',
                category: card.dataset.category || 'Unknown'
            };
            
            addToCart(product);
        });
    });
}

// Search functionality
function setupSearch() {
    const searchInputs = document.querySelectorAll('.search-bar input, .search-hero input');
    const searchButtons = document.querySelectorAll('.search-bar button, .search-hero button');
    
    searchButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            const searchTerm = searchInputs[index].value.trim();
            if (searchTerm) {
                performSearch(searchTerm);
            }
        });
    });
    
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const searchTerm = this.value.trim();
                if (searchTerm) {
                    performSearch(searchTerm);
                }
            }
        });
    });
}

function performSearch(term) {
    // Store search term for results page
    localStorage.setItem('lastSearch', term);
    
    // Show loading/notification
    showNotification(`Searching for "${term}"...`, 'info');
    
    // In a real app, this would redirect to search results page
    // For now, we'll just filter on current page
    filterBySearchTerm(term);
}

function filterBySearchTerm(term) {
    const productCards = document.querySelectorAll('.product-card, .fish-card, .aquarium-card, .accessory-card');
    const lowerTerm = term.toLowerCase();
    
    productCards.forEach(card => {
        const title = card.querySelector('h3, .product-title').textContent.toLowerCase();
        const description = card.querySelector('.product-description, .item-description')?.textContent.toLowerCase() || '';
        
        if (title.includes(lowerTerm) || description.includes(lowerTerm)) {
            card.style.display = 'block';
            card.style.animation = 'highlight 0.5s ease';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Add highlight animation
    if (!document.querySelector('#search-highlight')) {
        const style = document.createElement('style');
        style.id = 'search-highlight';
        style.textContent = `
            @keyframes highlight {
                0% { background-color: transparent; }
                50% { background-color: rgba(52, 152, 219, 0.1); }
                100% { background-color: transparent; }
            }
        `;
        document.head.appendChild(style);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    setupProductCards();
    setupSearch();
    
    // Mobile menu toggle (if needed)
    setupMobileMenu();
    
    // Form validation
    setupFormValidation();
});

// Mobile menu
function setupMobileMenu() {
    const mobileMenuBtn = document.createElement('button');
    mobileMenuBtn.className = 'mobile-menu-btn';
    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
    mobileMenuBtn.style.cssText = `
        display: none;
        background: none;
        border: none;
        font-size: 1.5rem;
        color: var(--primary);
        cursor: pointer;
        padding: 0.5rem;
    `;
    
    const nav = document.querySelector('nav .container');
    if (nav && window.innerWidth <= 768) {
        const headerTop = document.querySelector('.header-top');
        if (headerTop) {
            headerTop.appendChild(mobileMenuBtn);
        }
        
        const mainNav = document.querySelector('.main-nav');
        if (mainNav) {
            mainNav.style.cssText = `
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                flex-direction: column;
                padding: 1rem;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                z-index: 1000;
            `;
            
            mobileMenuBtn.addEventListener('click', function() {
                if (mainNav.style.display === 'flex') {
                    mainNav.style.display = 'none';
                    mobileMenuBtn.innerHTML = '<i class="fas fa-bars"></i>';
                } else {
                    mainNav.style.display = 'flex';
                    mobileMenuBtn.innerHTML = '<i class="fas fa-times"></i>';
                }
            });
        }
    }
}

// Form validation
function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredInputs = form.querySelectorAll('input[required], select[required]');
            let isValid = true;
            
            requiredInputs.forEach(input => {
                if (!input.value.trim()) {
                    input.style.borderColor = '#E74C3C';
                    isValid = false;
                    
                    // Add error message
                    let errorMsg = input.nextElementSibling;
                    if (!errorMsg || !errorMsg.classList.contains('error-message')) {
                        errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.textContent = 'This field is required';
                        errorMsg.style.cssText = `
                            color: #E74C3C;
                            font-size: 0.85rem;
                            margin-top: 0.25rem;
                        `;
                        input.parentNode.appendChild(errorMsg);
                    }
                } else {
                    input.style.borderColor = '';
                    const errorMsg = input.nextElementSibling;
                    if (errorMsg && errorMsg.classList.contains('error-message')) {
                        errorMsg.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                showNotification('Please fill in all required fields', 'error');
            }
        });
    });
}

// Price formatter
function formatPrice(price) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(price);
}

// Export functions for use in other files
window.Fishify = {
    addToCart,
    updateCartCount,
    showNotification,
    formatPrice,
    cart
};