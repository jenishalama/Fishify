// main.js - Fishify E-commerce Functionality

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all functionality
    initMobileMenu();
    initCart();
    initAddToCartButtons();
    initSearch();
    updateCartCount();
    
    // Add smooth scrolling for anchor links
    initSmoothScrolling();
    
    // Add product hover effects
    initProductHoverEffects();
});

// ==================== MOBILE MENU FUNCTIONALITY ====================
function initMobileMenu() {
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            mainNav.classList.toggle('mobile-active');
            // Toggle icon between bars and X
            const icon = this.querySelector('i');
            if (mainNav.classList.contains('mobile-active')) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-times');
            } else {
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            }
        });
        
        // Close mobile menu when clicking on a link
        const navLinks = mainNav.querySelectorAll('a');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                mainNav.classList.remove('mobile-active');
                const icon = mobileMenuBtn.querySelector('i');
                icon.classList.remove('fa-times');
                icon.classList.add('fa-bars');
            });
        });
    }
}

// ==================== CART FUNCTIONALITY ====================
let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];

function initCart() {
    // Load cart from localStorage
    updateCartCount();
}

function initAddToCartButtons() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productCard = this.closest('.product-card');
            const productTitle = productCard.querySelector('.product-title').textContent;
            const productPrice = productCard.querySelector('.product-price span').textContent;
            const productImage = productCard.querySelector('.product-image img').src;
            
            // Add product to cart
            addToCart({
                id: generateProductId(productTitle),
                name: productTitle,
                price: parseFloat(productPrice),
                image: productImage,
                quantity: 1
            });
            
            // Show feedback to user
            showNotification(`${productTitle} added to cart!`);
        });
    });
}

function addToCart(product) {
    // Check if product already exists in cart
    const existingProductIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingProductIndex > -1) {
        // Update quantity if product exists
        cart[existingProductIndex].quantity += 1;
    } else {
        // Add new product to cart
        cart.push(product);
    }
    
    // Save to localStorage
    localStorage.setItem('fishifyCart', JSON.stringify(cart));
    
    // Update cart count display
    updateCartCount();
    
    // Optional: Trigger cart update event
    document.dispatchEvent(new CustomEvent('cartUpdated'));
}

function updateCartCount() {
    const cartCountElements = document.querySelectorAll('.cart-count');
    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
    
    cartCountElements.forEach(element => {
        element.textContent = totalItems;
        
        // Show/hide based on count
        if (totalItems > 0) {
            element.style.display = 'flex';
        } else {
            element.style.display = 'none';
        }
    });
}

function generateProductId(productName) {
    return productName.toLowerCase().replace(/[^a-z0-9]/g, '-');
}

// ==================== SEARCH FUNCTIONALITY ====================
function initSearch() {
    const searchInputs = document.querySelectorAll('input[type="text"][placeholder*="Search"]');
    const searchButtons = document.querySelectorAll('button i.fa-search');
    
    // Search on button click
    searchButtons.forEach((button, index) => {
        button.addEventListener('click', function() {
            const searchInput = searchInputs[index];
            performSearch(searchInput.value);
        });
    });
    
    // Search on Enter key press
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(this.value);
            }
        });
    });
}

function performSearch(query) {
    if (query.trim()) {
        // Store search term in localStorage for use on search results page
        localStorage.setItem('fishifySearchQuery', query);
        
        // Redirect to search results page or filter current page
        // For now, show an alert and log to console
        console.log('Searching for:', query);
        
        // In a real implementation, you would:
        // 1. Redirect to a search results page: window.location.href = `search.html?q=${encodeURIComponent(query)}`;
        // 2. Or filter current page content
    }
}

// ==================== NOTIFICATION SYSTEM ====================
function showNotification(message, type = 'success') {
    // Remove existing notification if any
    const existingNotification = document.querySelector('.notification');
    if (existingNotification) {
        existingNotification.remove();
    }
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
        <button class="notification-close"><i class="fas fa-times"></i></button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Add close functionality
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.addEventListener('click', () => {
        notification.classList.add('notification-hiding');
        setTimeout(() => notification.remove(), 300);
    });
    
    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.add('notification-hiding');
            setTimeout(() => notification.remove(), 300);
        }
    }, 3000);
    
    // Add CSS for notification if not already present
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #2ecc71;
                color: white;
                padding: 15px 20px;
                border-radius: 5px;
                display: flex;
                align-items: center;
                gap: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                z-index: 1000;
                animation: slideInRight 0.3s ease-out;
                max-width: 350px;
            }
            .notification-error { background: #e74c3c; }
            .notification i { font-size: 1.2em; }
            .notification-close {
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                margin-left: auto;
                padding: 0;
            }
            .notification-hiding {
                animation: slideOutRight 0.3s ease-in forwards;
            }
            @keyframes slideInRight {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
}

// ==================== SMOOTH SCROLLING ====================
function initSmoothScrolling() {
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            // Only handle internal page anchors
            if (href !== '#' && href.startsWith('#')) {
                e.preventDefault();
                const targetElement = document.querySelector(href);
                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
}

// ==================== PRODUCT HOVER EFFECTS ====================
function initProductHoverEffects() {
    const productCards = document.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.1)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
    
    // Category cards hover effect
    const categoryCards = document.querySelectorAll('.category-card');
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const overlay = this.querySelector('.overlay');
            overlay.style.backgroundColor = 'rgba(0, 150, 255, 0.85)';
        });
        
        card.addEventListener('mouseleave', function() {
            const overlay = this.querySelector('.overlay');
            overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
        });
    });
}

// ==================== STICKY HEADER BEHAVIOR ====================
window.addEventListener('scroll', function() {
    const header = document.querySelector('.sticky-header');
    if (window.scrollY > 100) {
        header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        header.style.backgroundColor = 'rgba(255, 255, 255, 0.98)';
    } else {
        header.style.boxShadow = '';
        header.style.backgroundColor = '';
    }
});

// ==================== ADDITIONAL UTILITY FUNCTIONS ====================

// Format currency
function formatCurrency(amount) {
    return 'Rs ' + amount.toLocaleString('en-IN', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

// Get cart total
function getCartTotal() {
    return cart.reduce((total, item) => total + (item.price * item.quantity), 0);
}

// Clear cart (for logout or checkout)
function clearCart() {
    cart = [];
    localStorage.removeItem('fishifyCart');
    updateCartCount();
}

// Export functions for use in other files (if using modules)
// Uncomment if using ES6 modules:
// export { addToCart, getCartTotal, clearCart, cart };