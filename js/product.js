// ===== PRODUCT DETAIL PAGE JAVASCRIPT =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize product gallery
    initProductGallery();
    
    // Initialize quantity selector
    initQuantitySelector();
    
    // Initialize add to cart and wishlist
    initProductActions();
    
    // Initialize related items
    initRelatedItems();
    
    // Load product data from API
    loadProductData();
});

// Product gallery functionality
function initProductGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.querySelector('.main-image .image-placeholder');
    
    if (!thumbnails.length || !mainImage) return;
    
    thumbnails.forEach((thumb, index) => {
        thumb.addEventListener('click', function() {
            // Update active thumbnail
            thumbnails.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update main image with animation
            const thumbImage = this.querySelector('.thumb-placeholder');
            const bgColor = window.getComputedStyle(thumbImage).background;
            
            // Add fade animation
            mainImage.style.opacity = '0.5';
            mainImage.style.transition = 'opacity 0.3s ease';
            
            setTimeout(() => {
                mainImage.style.background = bgColor;
                mainImage.style.opacity = '1';
                
                // Update image text
                const imageText = this.querySelector('.thumb-placeholder')?.textContent || `Image ${index + 1}`;
                mainImage.querySelector('span').textContent = imageText;
            }, 150);
            
            // Add special effect based on thumbnail
            updateImageEffect(index);
        });
    });
    
    // Add zoom functionality on hover
    mainImage.addEventListener('mouseenter', function() {
        this.style.transform = 'scale(1.02)';
        this.style.transition = 'transform 0.3s ease';
    });
    
    mainImage.addEventListener('mouseleave', function() {
        this.style.transform = 'scale(1)';
    });
}

function updateImageEffect(index) {
    const mainImage = document.querySelector('.main-image .image-placeholder');
    let effectDiv = mainImage.querySelector('.dynamic-effect');
    
    if (!effectDiv) {
        effectDiv = document.createElement('div');
        effectDiv.className = 'dynamic-effect';
        mainImage.appendChild(effectDiv);
    }
    
    const effects = [
        'radial-gradient(circle at 30% 30%, rgba(255,255,255,0.3) 2%, transparent 15%)',
        'linear-gradient(45deg, transparent 49%, rgba(255,255,255,0.2) 49%, rgba(255,255,255,0.2) 51%, transparent 51%)',
        'radial-gradient(circle, rgba(255,255,255,0.4) 1px, transparent 1px)',
        'repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px)'
    ];
    
    effectDiv.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: ${effects[index % effects.length]};
        background-size: 200px 200px;
        pointer-events: none;
    `;
}

// Quantity selector
function initQuantitySelector() {
    const minusBtn = document.querySelector('.qty-btn.minus');
    const plusBtn = document.querySelector('.qty-btn.plus');
    const qtyInput = document.querySelector('.qty-input');
    
    if (!minusBtn || !plusBtn || !qtyInput) return;
    
    minusBtn.addEventListener('click', function() {
        let value = parseInt(qtyInput.value) || 1;
        if (value > 1) {
            value--;
            qtyInput.value = value;
            animateQuantityChange();
        }
    });
    
    plusBtn.addEventListener('click', function() {
        let value = parseInt(qtyInput.value) || 1;
        value++;
        qtyInput.value = value;
        animateQuantityChange();
    });
    
    // Input validation
    qtyInput.addEventListener('input', function() {
        let value = parseInt(this.value) || 1;
        if (value < 1) value = 1;
        if (value > 99) value = 99;
        this.value = value;
    });
    
    qtyInput.addEventListener('change', function() {
        let value = parseInt(this.value) || 1;
        if (value < 1) value = 1;
        if (value > 99) value = 99;
        this.value = value;
        animateQuantityChange();
    });
}

function animateQuantityChange() {
    const qtyInput = document.querySelector('.qty-input');
    if (qtyInput) {
        qtyInput.style.transform = 'scale(1.1)';
        qtyInput.style.backgroundColor = 'rgba(0, 105, 148, 0.1)';
        
        setTimeout(() => {
            qtyInput.style.transform = '';
            qtyInput.style.backgroundColor = '';
        }, 300);
    }
}

// Product actions (add to cart, wishlist)
function initProductActions() {
    const addToCartBtn = document.querySelector('.btn-add-to-cart');
    const wishlistBtn = document.querySelector('.btn-wishlist');
    
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            const product = getCurrentProduct();
            const quantity = parseInt(document.querySelector('.qty-input').value) || 1;
            
            // Add multiple items based on quantity
            for (let i = 0; i < quantity; i++) {
                Fishify.addToCart({
                    ...product,
                    id: `${product.id}_${i}`
                });
            }
            
            // Button animation
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> Added to Cart!';
            this.style.background = 'linear-gradient(135deg, #27AE60 0%, #2ECC71 100%)';
            
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.style.background = '';
            }, 2000);
        });
    }
    
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', function() {
            const product = getCurrentProduct();
            
            // Add to wishlist (in localStorage for demo)
            let wishlist = JSON.parse(localStorage.getItem('fishify_wishlist')) || [];
            const exists = wishlist.some(item => item.id === product.id);
            
            if (!exists) {
                wishlist.push(product);
                localStorage.setItem('fishify_wishlist', JSON.stringify(wishlist));
                
                // Button animation
                const originalHTML = this.innerHTML;
                this.innerHTML = '<i class="fas fa-heart"></i> Added to Wishlist!';
                this.style.color = '#E74C3C';
                this.style.borderColor = '#E74C3C';
                
                Fishify.showNotification('Added to wishlist!');
                
                setTimeout(() => {
                    this.innerHTML = originalHTML;
                    this.style.color = '';
                    this.style.borderColor = '';
                }, 2000);
            } else {
                Fishify.showNotification('Already in wishlist!', 'info');
            }
        });
    }
}

function getCurrentProduct() {
    return {
        id: 'pacific-blue-tang',
        name: document.querySelector('.product-header h1').textContent,
        price: parseFloat(document.querySelector('.current-price').textContent.replace('$', '')),
        category: 'Fish',
        subcategory: 'Saltwater',
        description: document.querySelector('.product-description').textContent,
        image: document.querySelector('.main-image .image-placeholder')?.style.backgroundImage || ''
    };
}

// Related items functionality
function initRelatedItems() {
    const relatedCards = document.querySelectorAll('.related-card');
    
    relatedCards.forEach(card => {
        // Add to cart button
        const addToCartBtn = card.querySelector('.btn-related');
        if (addToCartBtn) {
            addToCartBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const product = {
                    id: Date.now().toString(),
                    name: card.querySelector('h3').textContent,
                    price: parseFloat(card.querySelector('.related-price').textContent.replace('$', '').replace(',', '')),
                    category: 'Related',
                    image: card.querySelector('.related-image')?.style.backgroundImage || ''
                };
                
                Fishify.addToCart(product);
                
                // Button feedback
                const originalText = this.textContent;
                this.textContent = 'Added!';
                this.style.background = '#27AE60';
                this.style.color = 'white';
                
                setTimeout(() => {
                    this.textContent = originalText;
                    this.style.background = '';
                    this.style.color = '';
                }, 1500);
            });
        }
        
        // Click to view product
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.btn-related')) {
                const productName = this.querySelector('h3').textContent;
                Fishify.showNotification(`Viewing ${productName} details`, 'info');
                
                // In a real app, this would redirect to the product page
                // window.location.href = `product.html?product=${encodeURIComponent(productName)}`;
            }
        });
        
        // Hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 12px 30px rgba(0, 86, 204, 0.25)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = '';
            this.style.boxShadow = '';
        });
    });
}

// Load product data from API
async function loadProductData() {
    const productId = getProductIdFromURL();
    
    if (productId) {
        try {
            const response = await fetch(`/api/products.php?id=${productId}`);
            const product = await response.json();
            
            if (product) {
                updateProductDisplay(product);
            }
        } catch (error) {
            console.error('Error loading product data:', error);
        }
    }
}

function getProductIdFromURL() {
    const params = new URLSearchParams(window.location.search);
    return params.get('id') || 'pacific-blue-tang'; // Default for demo
}

function updateProductDisplay(product) {
    // Update basic info
    const titleElement = document.querySelector('.product-header h1');
    const priceElement = document.querySelector('.current-price');
    const descriptionElement = document.querySelector('.product-description');
    
    if (titleElement) titleElement.textContent = product.name;
    if (priceElement) priceElement.textContent = Fishify.formatPrice(product.price);
    if (descriptionElement && product.description) {
        descriptionElement.innerHTML = `<p>${product.description}</p>`;
    }
    
    // Update specifications
    updateSpecifications(product);
    
    // Update seller info
    updateSellerInfo(product);
    
    // Update main image
    updateMainImage(product);
}

function updateSpecifications(product) {
    const specsGrid = document.querySelector('.specs-grid');
    if (!specsGrid) return;
    
    const specifications = {
        'Species': product.species || 'Paracanthurus hepatus',
        'Size': product.size || 'Up to 12 inches',
        'Temperament': product.temperament || 'Semi-aggressive, territorial',
        'Diet': product.diet || 'Herbivore (algae, spirulina)',
        'Minimum Tank Size': product.tank_size || '100 gallons',
        'Reef Compatible': product.reef_compatible || 'Yes, with caution',
        'Water Type': product.water_type || 'Saltwater',
        'pH Range': product.ph_range || '8.1-8.4',
        'Temperature': product.temperature || '72-78°F'
    };
    
    specsGrid.innerHTML = '';
    
    Object.entries(specifications).forEach(([label, value]) => {
        const specItem = document.createElement('div');
        specItem.className = 'spec-item';
        specItem.innerHTML = `
            <span class="spec-label">${label}:</span>
            <span class="spec-value">${value}</span>
        `;
        specsGrid.appendChild(specItem);
    });
}

function updateSellerInfo(product) {
    const sellerName = document.querySelector('.seller-name');
    const sellerRating = document.querySelector('.seller-rating .stars');
    
    if (sellerName && product.seller) {
        sellerName.textContent = product.seller;
    }
    
    if (sellerRating && product.rating) {
        const rating = parseFloat(product.rating);
        const fullStars = Math.floor(rating);
        const hasHalfStar = rating % 1 >= 0.5;
        
        let starsHTML = '';
        for (let i = 0; i < 5; i++) {
            if (i < fullStars) {
                starsHTML += '<i class="fas fa-star"></i>';
            } else if (i === fullStars && hasHalfStar) {
                starsHTML += '<i class="fas fa-star-half-alt"></i>';
            } else {
                starsHTML += '<i class="far fa-star"></i>';
            }
        }
        
        sellerRating.innerHTML = starsHTML;
    }
}

function updateMainImage(product) {
    const mainImage = document.querySelector('.main-image .image-placeholder');
    if (!mainImage || !product.image_url) return;
    
    // Update background image
    mainImage.style.backgroundImage = `url('${product.image_url}')`;
    mainImage.style.backgroundSize = 'cover';
    mainImage.style.backgroundPosition = 'center';
    
    // Clear text if image loads
    const imageText = mainImage.querySelector('span');
    if (imageText) {
        imageText.style.display = 'none';
    }
}

// Add product page specific animations
if (!document.querySelector('#product-animations')) {
    const style = document.createElement('style');
    style.id = 'product-animations';
    style.textContent = `
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .product-actions button:active {
            animation: pulse 0.3s ease;
        }
        
        .care-card:hover .care-icon {
            animation: bounce 0.5s ease;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
    `;
    document.head.appendChild(style);
}