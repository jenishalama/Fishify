// ===== HOME PAGE JAVASCRIPT =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize home page features
    initHeroSearch();
    initCategoryCards();
    initFeaturedProducts();
    initPopularFish();
    setupAnimations();
    
    // Load featured products from API
    loadFeaturedProducts();
});

// Hero search functionality
function initHeroSearch() {
    const searchInput = document.querySelector('.search-hero input');
    const searchButton = document.querySelector('.search-hero button');
    
    if (searchInput && searchButton) {
        // Search button click
        searchButton.addEventListener('click', function() {
            performHeroSearch(searchInput.value.trim());
        });
        
        // Enter key press
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                performHeroSearch(this.value.trim());
            }
        });
        
        // Add search suggestions
        setupSearchSuggestions(searchInput);
    }
}

function performHeroSearch(term) {
    if (!term) {
        Fishify.showNotification('Please enter a search term', 'error');
        return;
    }
    
    // Show loading state
    const searchButton = document.querySelector('.search-hero button');
    const originalHTML = searchButton.innerHTML;
    searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    searchButton.disabled = true;
    
    // Save search term for results page
    localStorage.setItem('lastSearch', term);
    
    // Simulate search delay
    setTimeout(() => {
        // Reset button
        searchButton.innerHTML = originalHTML;
        searchButton.disabled = false;
        
        // Show success message
        Fishify.showNotification(`Searching for "${term}"...`);
        
        // In a real app, redirect to search results
        // For demo, just filter current page
        highlightSearchResults(term);
        
        // Scroll to results
        const featuredSection = document.querySelector('.featured-products');
        if (featuredSection) {
            featuredSection.scrollIntoView({ behavior: 'smooth' });
        }
    }, 1000);
}

function setupSearchSuggestions(input) {
    const suggestions = [
        'Clownfish',
        'Aquarium Tank',
        'LED Lights',
        'Fish Food',
        'Water Filter',
        'Coral Decor',
        'Air Pump',
        'Heater',
        'Test Kit',
        'Plants'
    ];
    
    let suggestionIndex = 0;
    let interval;
    
    // Show rotating suggestions in placeholder
    function rotateSuggestions() {
        input.placeholder = `Search for ${suggestions[suggestionIndex]}...`;
        suggestionIndex = (suggestionIndex + 1) % suggestions.length;
    }
    
    // Start rotation after a delay
    setTimeout(() => {
        interval = setInterval(rotateSuggestions, 2000);
    }, 1000);
    
    // Stop rotation when user focuses input
    input.addEventListener('focus', function() {
        clearInterval(interval);
        this.placeholder = 'Search for fish, aquariums, accessories...';
    });
    
    // Restart rotation when user leaves input
    input.addEventListener('blur', function() {
        if (!this.value) {
            interval = setInterval(rotateSuggestions, 2000);
        }
    });
}

function highlightSearchResults(term) {
    const productCards = document.querySelectorAll('.product-card, .fish-card');
    const lowerTerm = term.toLowerCase();
    let foundCount = 0;
    
    productCards.forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const description = card.querySelector('.product-description')?.textContent.toLowerCase() || '';
        
        if (title.includes(lowerTerm) || description.includes(lowerTerm)) {
            // Highlight matching card
            card.style.boxShadow = '0 0 0 3px rgba(52, 152, 219, 0.3)';
            card.style.transform = 'translateY(-5px)';
            foundCount++;
            
            // Scroll to first match
            if (foundCount === 1) {
                card.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
            }
            
            // Remove highlight after 3 seconds
            setTimeout(() => {
                card.style.boxShadow = '';
                card.style.transform = '';
            }, 3000);
        }
    });
    
    if (foundCount > 0) {
        Fishify.showNotification(`Found ${foundCount} matching products`, 'success');
    } else {
        Fishify.showNotification('No products found. Try a different search term.', 'error');
    }
}

// Category cards functionality
function initCategoryCards() {
    const categoryCards = document.querySelectorAll('.category-card');
    
    categoryCards.forEach(card => {
        // Add click animation
        card.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Add ripple effect
            createRippleEffect(this, e);
            
            // Get category name
            const category = this.querySelector('h3').textContent;
            
            // Show loading state
            const icon = this.querySelector('.category-icon');
            const originalHTML = icon.innerHTML;
            icon.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            // Simulate category loading
            setTimeout(() => {
                // Reset icon
                icon.innerHTML = originalHTML;
                
                // Show notification
                Fishify.showNotification(`Loading ${category}...`);
                
                // In a real app, this would redirect to the category page
                // For demo, simulate loading
                simulateCategoryLoad(category);
            }, 800);
        });
        
        // Add hover effects
        card.addEventListener('mouseenter', function() {
            const icon = this.querySelector('.category-icon');
            icon.style.transform = 'scale(1.1) rotate(5deg)';
            icon.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            const icon = this.querySelector('.category-icon');
            icon.style.transform = '';
        });
    });
}

function createRippleEffect(element, event) {
    const ripple = document.createElement('span');
    const rect = element.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = event.clientX - rect.left - size / 2;
    const y = event.clientY - rect.top - size / 2;
    
    ripple.style.cssText = `
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple 0.6s linear;
        width: ${size}px;
        height: ${size}px;
        top: ${y}px;
        left: ${x}px;
        pointer-events: none;
    `;
    
    element.style.position = 'relative';
    element.style.overflow = 'hidden';
    element.appendChild(ripple);
    
    setTimeout(() => {
        ripple.remove();
    }, 600);
}

function simulateCategoryLoad(category) {
    // In a real app, this would redirect
    // For demo, show what would happen
    switch(category.toLowerCase()) {
        case 'fish':
            window.location.href = 'fish.html';
            break;
        case 'aquarium':
            window.location.href = 'aquarium.html';
            break;
        case 'accessories':
            window.location.href = 'accessories.html';
            break;
        case 'plants':
            Fishify.showNotification(`Would load ${category} page`, 'info');
            break;
        default:
            Fishify.showNotification(`Category: ${category}`, 'info');
    }
}

// Featured products functionality
function initFeaturedProducts() {
    const productCards = document.querySelectorAll('.featured-products .product-card');
    
    productCards.forEach((card, index) => {
        // Staggered entrance animation
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
        
        // Add to cart functionality
        const addToCartBtn = card.querySelector('.add-to-cart');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const product = {
                    id: `home-featured-${index}`,
                    name: card.querySelector('h3').textContent,
                    price: parseFloat(card.querySelector('.product-price').textContent.replace('$', '')),
                    category: 'Featured',
                    image: card.querySelector('.product-image')?.style.backgroundImage || ''
                };
                
                Fishify.addToCart(product);
                
                // Add special animation for featured products
                animateFeaturedAddToCart(this, card);
            });
        }
        
        // View details on click
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.add-to-cart')) {
                const productName = this.querySelector('h3').textContent;
                Fishify.showNotification(`Viewing details for ${productName}`, 'info');
                
                // In a real app, redirect to product page
                // For demo, simulate loading product details
                simulateProductView(productName);
            }
        });
    });
}

function animateFeaturedAddToCart(button, card) {
    // Button animation
    const originalHTML = button.innerHTML;
    const originalBg = button.style.background;
    
    button.innerHTML = '<i class="fas fa-check"></i> Added!';
    button.style.background = 'linear-gradient(135deg, #27AE60 0%, #2ECC71 100%)';
    button.style.transform = 'scale(0.95)';
    
    // Card animation
    card.style.boxShadow = '0 8px 25px rgba(39, 174, 96, 0.3)';
    card.style.transform = 'translateY(-5px)';
    
    // Create floating effect
    const floatingEffect = document.createElement('div');
    floatingEffect.innerHTML = '<i class="fas fa-cart-plus"></i>';
    floatingEffect.style.cssText = `
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
        color: #27AE60;
        opacity: 0;
        animation: floatUp 1s ease;
        z-index: 10;
    `;
    
    card.appendChild(floatingEffect);
    
    // Reset after animation
    setTimeout(() => {
        button.innerHTML = originalHTML;
        button.style.background = originalBg;
        button.style.transform = '';
        
        card.style.boxShadow = '';
        card.style.transform = '';
        floatingEffect.remove();
    }, 2000);
}

function simulateProductView(productName) {
    // In a real app, this would redirect to product detail page
    // For demo, show a notification
    Fishify.showNotification(`Would show details for: ${productName}`, 'info');
}

// Popular fish functionality
function initPopularFish() {
    const fishCards = document.querySelectorAll('.fish-card');
    
    fishCards.forEach((card, index) => {
        // Staggered animation
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 150 + 500); // Delay after featured products
        
        // Hover effect
        card.addEventListener('mouseenter', function() {
            const image = this.querySelector('.fish-image');
            if (image) {
                image.style.transform = 'scale(1.05) rotate(1deg)';
                image.style.transition = 'all 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const image = this.querySelector('.fish-image');
            if (image) {
                image.style.transform = '';
            }
        });
        
        // Click to view
        card.addEventListener('click', function() {
            const fishName = this.querySelector('h3').textContent;
            
            // Show loading state
            const image = this.querySelector('.fish-image');
            const originalBg = image.style.background;
            image.style.background = 'linear-gradient(135deg, #3498db 0%, #2ecc71 100%)';
            
            setTimeout(() => {
                image.style.background = originalBg;
                
                // In a real app, redirect to fish page with filter
                // For demo, redirect to fish page
                window.location.href = `fish.html?filter=${encodeURIComponent(fishName)}`;
            }, 300);
        });
    });
}

// Setup animations
function setupAnimations() {
    // Add animation styles
    if (!document.querySelector('#home-animations')) {
        const style = document.createElement('style');
        style.id = 'home-animations';
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
            
            @keyframes floatUp {
                0% {
                    opacity: 0.8;
                    transform: translate(-50%, -50%) scale(1);
                }
                100% {
                    opacity: 0;
                    transform: translate(-50%, -150%) scale(1.5);
                }
            }
            
            /* Initial states for animations */
            .product-card, .fish-card, .category-card {
                opacity: 0;
                transform: translateY(20px);
                transition: all 0.5s ease;
            }
            
            /* Hero text animation */
            .hero-content h1 {
                animation: fadeInUp 1s ease;
            }
            
            .hero-content p {
                animation: fadeInUp 1s ease 0.3s both;
            }
            
            .search-hero {
                animation: fadeInUp 1s ease 0.6s both;
            }
            
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            /* Section title animations */
            .section-title {
                position: relative;
                overflow: hidden;
            }
            
            .section-title::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 0;
                width: 100%;
                height: 2px;
                background: linear-gradient(90deg, transparent, var(--primary), transparent);
                animation: slideIn 2s ease infinite;
            }
            
            @keyframes slideIn {
                0% {
                    transform: translateX(-100%);
                }
                100% {
                    transform: translateX(100%);
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Add scroll animations
    setupScrollAnimations();
}

function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                
                // Special animation for featured products grid
                if (entry.target.classList.contains('products-grid')) {
                    animateGridItems(entry.target);
                }
            }
        });
    }, observerOptions);
    
    // Observe sections
    const sections = document.querySelectorAll('section');
    sections.forEach(section => observer.observe(section));
}

function animateGridItems(grid) {
    const items = grid.querySelectorAll('.product-card, .fish-card');
    items.forEach((item, index) => {
        setTimeout(() => {
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Load featured products from API
async function loadFeaturedProducts() {
    try {
        const response = await fetch('/api/products.php?featured=true');
        const products = await response.json();
        
        if (products.length > 0) {
            updateFeaturedProducts(products);
        }
    } catch (error) {
        console.error('Error loading featured products:', error);
        // Continue with existing demo products
    }
}

function updateFeaturedProducts(products) {
    const featuredGrid = document.querySelector('.featured-products .products-grid');
    if (!featuredGrid) return;
    
    // Clear existing products
    featuredGrid.innerHTML = '';
    
    // Add featured products from API
    products.slice(0, 8).forEach((product, index) => {
        const productCard = createFeaturedProductCard(product, index);
        featuredGrid.appendChild(productCard);
    });
    
    // Reinitialize event listeners
    initFeaturedProducts();
}

function createFeaturedProductCard(product, index) {
    const colors = [
        ['#4facfe', '#00f2fe'],
        ['#43e97b', '#38f9d7'],
        ['#fa709a', '#fee140'],
        ['#30cfd0', '#330867'],
        ['#667eea', '#764ba2'],
        ['#f093fb', '#f5576c'],
        ['#5ee7df', '#b490ca'],
        ['#d299c2', '#fef9d7']
    ];
    
    const colorPair = colors[index % colors.length];
    
    const card = document.createElement('div');
    card.className = 'product-card';
    card.style.opacity = '0';
    card.style.transform = 'translateY(20px)';
    
    card.innerHTML = `
        <div class="product-image" style="background: linear-gradient(135deg, ${colorPair[0]} 0%, ${colorPair[1]} 100%)">
            <div class="product-badge">FEATURED</div>
            <span>${product.name}</span>
        </div>
        <div class="product-info">
            <h3 class="product-title">${product.name}</h3>
            <div class="product-price">${Fishify.formatPrice(product.price)}</div>
            <button class="btn btn-primary add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
        </div>
    `;
    
    return card;
}

// Add auto-slide for hero background (optional)
function initHeroBackgroundSlide() {
    const hero = document.querySelector('.hero');
    if (!hero) return;
    
    const colors = [
        'linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%)',
        'linear-gradient(135deg, #006994 0%, #00BCD4 100%)',
        'linear-gradient(135deg, #2D9CDB 0%, #2F80ED 100%)',
        'linear-gradient(135deg, #1E88E5 0%, #00ACC1 100%)',
        'linear-gradient(135deg, #3498DB 0%, #1ABC9C 100%)'
    ];
    
    let currentIndex = 0;
    
    setInterval(() => {
        currentIndex = (currentIndex + 1) % colors.length;
        hero.style.background = colors[currentIndex];
        hero.style.transition = 'background 1.5s ease';
    }, 8000); // Change every 8 seconds
}

// Initialize hero background slide if desired
// initHeroBackgroundSlide();

// Add newsletter signup (if you add one to home page)
function initNewsletterSignup() {
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            
            if (FishifyUtils.isValidEmail(email)) {
                // Save to localStorage for demo
                let subscribers = JSON.parse(localStorage.getItem('newsletter_subscribers')) || [];
                if (!subscribers.includes(email)) {
                    subscribers.push(email);
                    localStorage.setItem('newsletter_subscribers', JSON.stringify(subscribers));
                    
                    Fishify.showNotification('Thanks for subscribing!', 'success');
                    this.reset();
                } else {
                    Fishify.showNotification('You are already subscribed!', 'info');
                }
            } else {
                Fishify.showNotification('Please enter a valid email address', 'error');
            }
        });
    }
}

// Call this if you add newsletter form
// initNewsletterSignup();