// main.js
document.addEventListener('DOMContentLoaded', function() {
    
    // Cart functionality with Nepali rupees
    let cartItems = [];
    let cartTotal = 0;
    const cartCountElement = document.querySelector('.cart-count');
    const cartBtn = document.querySelector('.cart-btn');
    
    // Sample product data with prices
    const productPrices = {
        'Aqua Tank Pro 100': 300,
        'Nano Cube Aquarium Kit': 300,
        'Marine LED Light': 300,
        'Advanced CO2': 300,
        'Premium Plant': 300,
        'Smart Heater': 300,
        'Automatic Fish Feeder': 300,
        'Magnetic Gravel Cleaner': 300
    };
    
    // Add to cart buttons
    const addToCartButtons = document.querySelectorAll('.add-to-cart');
    
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function() {
            const productCard = this.closest('.product-card');
            const productName = productCard.querySelector('.product-title').textContent;
            const productPrice = productPrices[productName] || 300;
            
            // Add item to cart
            addToCart(productName, productPrice);
            
            // Add visual feedback
            this.innerHTML = '<i class="fas fa-check"></i> थपियो!';
            this.style.background = '#28a745';
            
            setTimeout(() => {
                this.innerHTML = '<i class="fas fa-cart-plus"></i> कार्टमा थप्नुहोस्';
                this.style.background = '#009dff';
            }, 1500);
        });
    });
    
    function addToCart(name, price) {
        // Check if item already exists
        const existingItem = cartItems.find(item => item.name === name);
        
        if (existingItem) {
            existingItem.quantity += 1;
            existingItem.total = existingItem.quantity * price;
        } else {
            cartItems.push({
                name: name,
                price: price,
                quantity: 1,
                total: price
            });
        }
        
        cartTotal += price;
        updateCartDisplay();
    }
    
    function updateCartDisplay() {
        // Update cart count
        const totalItems = cartItems.reduce((sum, item) => sum + item.quantity, 0);
        cartCountElement.textContent = totalItems;
        
        // Update cart button to show total
        if (cartBtn) {
            cartBtn.setAttribute('data-total', `रु ${cartTotal}`);
        }
    }
    
    // Search functionality with real results
    const searchButtons = document.querySelectorAll('button i.fa-search');
    const searchInputs = document.querySelectorAll('input[type="text"]');
    
    // Product data for search
    const products = [
        'Aqua Tank Pro 100',
        'Nano Cube Aquarium Kit', 
        'Marine LED Light',
        'Advanced CO2',
        'Premium Plant',
        'Smart Heater',
        'Automatic Fish Feeder',
        'Magnetic Gravel Cleaner',
        'Siamese Fighting Fish',
        'Guppies',
        'Angelfish',
        'Neon Tetra'
    ];
    
    searchButtons.forEach((button, index) => {
        button.closest('button').addEventListener('click', function() {
            const searchInput = this.previousElementSibling;
            performSearch(searchInput.value.trim());
        });
    });
    
    searchInputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performSearch(this.value.trim());
            }
        });
        
        // Make search active with focus
        input.addEventListener('focus', function() {
            this.style.boxShadow = '0 0 0 3px rgba(0, 157, 255, 0.3)';
        });
        
        input.addEventListener('blur', function() {
            this.style.boxShadow = 'none';
        });
    });
    
    function performSearch(searchTerm) {
        if (searchTerm !== '') {
            const matchedProducts = products.filter(product => 
                product.toLowerCase().includes(searchTerm.toLowerCase())
            );
            
            if (matchedProducts.length > 0) {
                // In a real app, you would redirect or filter products
                // For now, show a message
                alert(`तपाईंले खोज्नुभएको: "${searchTerm}"\n\nपाइएको वस्तुहरू: ${matchedProducts.length}\n\n${matchedProducts.join(', ')}`);
                
                // Clear search input
                const activeInput = document.activeElement;
                if (activeInput && activeInput.type === 'text') {
                    activeInput.value = '';
                }
            } else {
                alert(`"${searchTerm}" को लागि कुनै वस्तु भेटिएन\n\nकृपया अरु खोज्नुहोस्।`);
            }
        } else {
            alert('कृपया खोज्नको लागि केहि टाइप गर्नुहोस्');
        }
    }
    
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const mainNav = document.querySelector('.main-nav');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            mainNav.classList.toggle('show');
            this.querySelector('i').classList.toggle('fa-bars');
            this.querySelector('i').classList.toggle('fa-times');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!mobileMenuBtn.contains(event.target) && !mainNav.contains(event.target)) {
                mainNav.classList.remove('show');
                mobileMenuBtn.querySelector('i').classList.add('fa-bars');
                mobileMenuBtn.querySelector('i').classList.remove('fa-times');
            }
        });
    }
    
    // Update prices to show Nepali rupees symbol
    const priceElements = document.querySelectorAll('.product-price');
    priceElements.forEach(priceElement => {
        const span = priceElement.querySelector('span');
        if (span) {
            // Format with Nepali rupees symbol
            priceElement.innerHTML = `रु <span>${span.textContent}</span>`;
        }
    });
    
    // Update cart page link to include cart data
    if (cartBtn) {
        cartBtn.addEventListener('click', function(e) {
            if (cartItems.length === 0) {
                e.preventDefault();
                alert('कार्ट खाली छ। केहि वस्तु थप्नुहोस्।');
                return;
            }
            
            // Store cart data for cart.html page
            localStorage.setItem('cartItems', JSON.stringify(cartItems));
            localStorage.setItem('cartTotal', cartTotal.toString());
        });
    }
    
    // Update current year in footer
    const yearElement = document.querySelector('.footer-bottom p');
    if (yearElement) {
        const currentYear = new Date().getFullYear();
        yearElement.innerHTML = yearElement.innerHTML.replace('2025', currentYear);
    }
    
    // Add tooltip for cart total
    if (cartBtn && cartCountElement) {
        cartCountElement.setAttribute('title', `जम्मा: रु ${cartTotal}`);
    }
});