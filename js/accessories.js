// ===== ACCESSORIES PAGE JAVASCRIPT =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize sort dropdown
    initSortDropdown();
    
    // Initialize category filters
    initCategoryFilters();
    
    // Setup accessory cards
    setupAccessoryCards();
    
    // Load accessories from API
    loadAccessoriesFromAPI();
});

// Sort dropdown functionality
function initSortDropdown() {
    const sortBtn = document.querySelector('.sort-btn');
    const sortOptions = document.querySelector('.sort-options');
    
    if (sortBtn && sortOptions) {
        // Toggle dropdown
        sortBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            sortOptions.style.display = sortOptions.style.display === 'block' ? 'none' : 'block';
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            sortOptions.style.display = 'none';
        });
        
        // Sort option selection
        sortOptions.querySelectorAll('a').forEach(option => {
            option.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Update button text
                sortBtn.innerHTML = `${this.textContent} <i class="fas fa-chevron-down"></i>`;
                
                // Update active state
                sortOptions.querySelectorAll('a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
                
                // Sort accessories
                sortAccessories(this.textContent);
                
                // Close dropdown
                sortOptions.style.display = 'none';
            });
        });
        
        // Prevent dropdown close when clicking inside
        sortOptions.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

function sortAccessories(sortBy) {
    const accessoriesGrid = document.querySelector('.accessories-grid');
    const accessoryCards = Array.from(document.querySelectorAll('.accessory-card'));
    
    accessoryCards.sort((a, b) => {
        const priceA = parseFloat(a.querySelector('.accessory-price').textContent.replace('$', ''));
        const priceB = parseFloat(b.querySelector('.accessory-price').textContent.replace('$', ''));
        const nameA = a.querySelector('h3').textContent.toLowerCase();
        const nameB = b.querySelector('h3').textContent.toLowerCase();
        
        switch(sortBy) {
            case 'Price: Low to High':
                return priceA - priceB;
            case 'Price: High to Low':
                return priceB - priceA;
            case 'Name: A-Z':
                return nameA.localeCompare(nameB);
            case 'Name: Z-A':
                return nameB.localeCompare(nameA);
            case 'New Arrivals':
                // In real app, use actual date data
                return Math.random() - 0.5;
            case 'Best Sellers':
                // In real app, use sales data
                return Math.random() - 0.5;
            default:
                return 0;
        }
    });
    
    // Reorder with animation
    accessoriesGrid.innerHTML = '';
    accessoryCards.forEach((card, index) => {
        setTimeout(() => {
            accessoriesGrid.appendChild(card);
            card.style.animation = 'fadeInScale 0.4s ease';
        }, index * 50);
    });
}

// Category filter functionality
function initCategoryFilters() {
    const categoryBtns = document.querySelectorAll('.category-btn');
    
    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Update active state
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter accessories by category
            filterByCategory(this.textContent);
        });
    });
}

function filterByCategory(category) {
    const accessoryCards = document.querySelectorAll('.accessory-card');
    const lowerCategory = category.toLowerCase();
    
    accessoryCards.forEach(card => {
        const cardCategory = card.querySelector('.accessory-category').textContent.toLowerCase();
        
        if (lowerCategory === 'all' || cardCategory.includes(lowerCategory) || 
            (lowerCategory === 'best sellers' && card.classList.contains('best-seller')) ||
            (lowerCategory === 'new arrivals' && card.classList.contains('new-arrival'))) {
            card.style.display = 'block';
            card.style.animation = 'slideIn 0.5s ease';
        } else {
            card.style.display = 'none';
        }
    });
    
    updateAccessoryCount(category);
}

function updateAccessoryCount(category) {
    const visibleCards = document.querySelectorAll('.accessory-card[style*="display: block"]').length;
    const totalCards = document.querySelectorAll('.accessory-card').length;
    
    let countElement = document.querySelector('.accessory-count');
    if (!countElement) {
        const controls = document.querySelector('.controls-grid');
        if (controls) {
            countElement = document.createElement('div');
            countElement.className = 'accessory-count';
            countElement.style.cssText = `
                color: var(--accessory-indigo);
                font-size: 0.95rem;
                font-weight: 500;
                margin-left: auto;
            `;
            controls.appendChild(countElement);
        }
    }
    
    if (countElement) {
        if (category.toLowerCase() === 'all') {
            countElement.textContent = `Showing all ${visibleCards} accessories`;
        } else {
            countElement.textContent = `${category}: ${visibleCards} items`;
        }
    }
}

// Setup accessory cards
function setupAccessoryCards() {
    // Add to cart functionality
    document.querySelectorAll('.accessory-card .add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const card = this.closest('.accessory-card');
            const name = card.querySelector('h3').textContent;
            const price = parseFloat(card.querySelector('.accessory-price').textContent.replace('$', ''));
            const category = card.querySelector('.accessory-category').textContent;
            
            const product = {
                id: Date.now().toString(),
                name: name,
                price: price,
                category: 'Accessory',
                subcategory: category,
                image: card.querySelector('.accessory-image')?.style.backgroundImage || ''
            };
            
            Fishify.addToCart(product);
            
            // Button animation
            const originalHTML = this.innerHTML;
            const originalBg = this.style.background;
            
            this.innerHTML = '<i class="fas fa-check"></i> Added!';
            this.style.background = 'linear-gradient(135deg, #27AE60 0%, #2ECC71 100%)';
            this.style.transform = 'scale(0.95)';
            
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.style.background = originalBg;
                this.style.transform = '';
            }, 1500);
        });
    });
    
    // Hover effects
    document.querySelectorAll('.accessory-card').forEach(card => {
        // Add random badges
        if (Math.random() > 0.7) {
            const badge = document.createElement('div');
            badge.className = 'accessory-badge';
            badge.textContent = Math.random() > 0.5 ? 'BEST SELLER' : 'NEW';
            badge.style.cssText = `
                position: absolute;
                top: 10px;
                right: 10px;
                background: ${Math.random() > 0.5 ? 'var(--accessory-pink)' : 'var(--accessory-green)'};
                color: white;
                padding: 0.25rem 0.75rem;
                border-radius: 4px;
                font-size: 0.75rem;
                font-weight: 600;
                z-index: 2;
            `;
            
            const imageContainer = card.querySelector('.accessory-image');
            if (imageContainer) {
                imageContainer.style.position = 'relative';
                imageContainer.appendChild(badge);
            }
        }
        
        // Add hover animation
        card.addEventListener('mouseenter', function() {
            const image = this.querySelector('.accessory-image');
            if (image) {
                image.style.transform = 'translateY(-5px)';
                image.style.transition = 'transform 0.3s ease';
            }
            
            const button = this.querySelector('.btn-accessory');
            if (button) {
                button.style.transform = 'translateY(-2px)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const image = this.querySelector('.accessory-image');
            if (image) {
                image.style.transform = 'translateY(0)';
            }
            
            const button = this.querySelector('.btn-accessory');
            if (button) {
                button.style.transform = '';
            }
        });
    });
}

// Load accessories from API
async function loadAccessoriesFromAPI() {
    try {
        const response = await fetch('/api/products.php?category=accessory');
        const accessories = await response.json();
        
        if (accessories.length > 0) {
            populateAccessoryGrid(accessories);
        }
    } catch (error) {
        console.error('Error loading accessories:', error);
        useFallbackAccessoryData();
    }
}

function populateAccessoryGrid(accessories) {
    const grid = document.querySelector('.accessories-grid');
    if (!grid) return;
    
    // Clear existing content if we have API data
    const existingCards = grid.querySelectorAll('.accessory-card');
    if (existingCards.length > 0 && accessories.length > existingCards.length) {
        grid.innerHTML = '';
    }
    
    // Add accessories from API
    accessories.forEach(accessory => {
        const card = createAccessoryCardFromAPI(accessory);
        grid.appendChild(card);
    });
    
    // Re-setup event listeners
    setupAccessoryCards();
    updateAccessoryCount('All');
}

function createAccessoryCardFromAPI(accessory) {
    const card = document.createElement('div');
    card.className = 'accessory-card';
    
    // Random color for the card
    const colorPairs = [
        ['#2196F3', '#21CBF3'],
        ['#00BCD4', '#00E5FF'],
        ['#4CAF50', '#81C784'],
        ['#FF9800', '#FFB74D'],
        ['#9C27B0', '#BA68C8'],
        ['#3F51B5', '#7986CB'],
        ['#009688', '#4DB6AC'],
        ['#F44336', '#E57373'],
        ['#795548', '#A1887F'],
        ['#607D8B', '#90A4AE'],
        ['#FF5722', '#FF8A65'],
        ['#8D6E63', '#A1887F']
    ];
    
    const randomColor = colorPairs[Math.floor(Math.random() * colorPairs.length)];
    
    card.innerHTML = `
        <div class="accessory-image">
            <div class="img-placeholder" style="background: linear-gradient(135deg, ${randomColor[0]} 0%, ${randomColor[1]} 100%)">
                <div class="filter-effect"></div>
                <span>${accessory.name}</span>
            </div>
        </div>
        <div class="accessory-info">
            <h3>${accessory.name}</h3>
            <div class="accessory-price">${Fishify.formatPrice(accessory.price)}</div>
            <div class="accessory-category">${accessory.type || 'Equipment'}</div>
            <button class="btn btn-accessory add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
        </div>
    `;
    
    return card;
}

function useFallbackAccessoryData() {
    // Fallback accessory data
    const fallbackAccessories = [
        {
            name: "Premium Filter Media",
            price: 29.99,
            type: "Filtration"
        },
        {
            name: "LED Moonlight",
            price: 34.99,
            type: "Lighting"
        },
        {
            name: "Aquarium Background",
            price: 19.99,
            type: "Decor"
        },
        {
            name: "Water Changer System",
            price: 49.99,
            type: "Maintenance"
        },
        {
            name: "Fish Net Set",
            price: 14.99,
            type: "Supplies"
        },
        {
            name: "Aquarium Stand",
            price: 159.99,
            type: "Equipment"
        }
    ];
    
    populateAccessoryGrid(fallbackAccessories);
}


if (!document.querySelector('#accessory-animations')) {
    const style = document.createElement('style');
    style.id = 'accessory-animations';
    style.textContent = `
        @keyframes fadeInScale {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .accessory-card {
            animation: fadeInScale 0.4s ease;
        }
    `;
    document.head.appendChild(style);
}