// ===== AQUARIUM PAGE JAVASCRIPT =====
document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters
    initAquariumFilters();
    setupAquariumCards();
    
    // Load aquariums from API if needed
    loadAquariumsFromAPI();
});

// Filter functionality for aquariums
function initAquariumFilters() {
    // Price range slider
    const priceSlider = document.querySelector('.range-slider[type="range"]:first-of-type');
    const priceInputs = document.querySelectorAll('.price-inputs input');
    
    if (priceSlider && priceInputs.length === 2) {
        priceSlider.addEventListener('input', function() {
            priceInputs[1].value = `$${this.value}`;
            filterAquariums();
        });
    }
    
    // Size range slider
    const sizeSlider = document.querySelector('.range-slider[type="range"]:last-of-type');
    const sizeInputs = document.querySelectorAll('.size-inputs input');
    
    if (sizeSlider && sizeInputs.length === 2) {
        sizeSlider.addEventListener('input', function() {
            sizeInputs[1].value = this.value;
            filterAquariums();
        });
    }
    
    // Checkbox filters
    const checkboxes = document.querySelectorAll('.filter-checkboxes input[type="checkbox"]');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateCheckboxUI(this);
            filterAquariums();
        });
    });
}

function updateCheckboxUI(checkbox) {
    const label = checkbox.closest('.checkbox-item');
    if (checkbox.checked) {
        label.classList.add('selected');
    } else {
        label.classList.remove('selected');
    }
}

function filterAquariums() {
    const aquariumCards = document.querySelectorAll('.aquarium-card');
    const maxPrice = parseInt(document.querySelector('.price-inputs input:last-child')?.value.replace('$', '')) || 750;
    const maxSize = parseInt(document.querySelector('.size-inputs input:last-child')?.value) || 100;
    
    // Get active filters
    const activeFilters = {
        type: getCheckedValues('checkbox', 'filter-checkboxes'),
        material: getCheckedMaterial()
    };
    
    let visibleCount = 0;
    
    aquariumCards.forEach(card => {
        const price = parseFloat(card.dataset.price);
        const size = parseInt(card.dataset.size);
        const material = card.dataset.material;
        const type = card.dataset.type;
        
        let showCard = true;
        
        // Price filter
        if (price > maxPrice) {
            showCard = false;
        }
        
        // Size filter
        if (size > maxSize) {
            showCard = false;
        }
        
        // Material filter
        if (activeFilters.material.length > 0 && !activeFilters.material.includes(material)) {
            showCard = false;
        }
        
        // Type filter
        if (activeFilters.type.length > 0 && !activeFilters.type.includes(type)) {
            showCard = false;
        }
        
        // Show/hide card with animation
        if (showCard) {
            card.style.display = 'block';
            card.style.animation = 'slideUp 0.5s ease';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    updateAquariumResultsCount(visibleCount, aquariumCards.length);
}

function getCheckedValues(inputType, containerClass) {
    const checkboxes = document.querySelectorAll(`.${containerClass} input[type="${inputType}"]:checked`);
    return Array.from(checkboxes).map(cb => {
        const label = cb.nextElementSibling.nextElementSibling;
        return label.textContent.toLowerCase();
    });
}

function getCheckedMaterial() {
    const checkboxes = document.querySelectorAll('.filter-checkboxes input[type="checkbox"]');
    const materials = [];
    
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const label = checkbox.nextElementSibling.nextElementSibling;
            if (label.textContent.toLowerCase() === 'glass' || label.textContent.toLowerCase() === 'acrylic') {
                materials.push(label.textContent.toLowerCase());
            }
        }
    });
    
    return materials;
}

function updateAquariumResultsCount(visible, total) {
    // Create or update results count
    let resultsElement = document.querySelector('.aquarium-results-count');
    if (!resultsElement) {
        const container = document.querySelector('.aquarium-products .container');
        if (container) {
            resultsElement = document.createElement('div');
            resultsElement.className = 'aquarium-results-count';
            resultsElement.style.cssText = `
                text-align: center;
                color: var(--aqua-dark);
                font-size: 1.1rem;
                margin: 2rem 0;
                padding: 1rem;
                background: rgba(0, 188, 212, 0.1);
                border-radius: 8px;
                border-left: 4px solid var(--aqua-secondary);
            `;
            container.insertBefore(resultsElement, container.querySelector('.products-grid').nextSibling);
        }
    }
    
    if (resultsElement) {
        resultsElement.innerHTML = `
            <i class="fas fa-water"></i>
            Showing <strong>${visible}</strong> of <strong>${total}</strong> aquariums
        `;
    }
}

// Setup aquarium card interactions
function setupAquariumCards() {
    // Add to cart functionality
    document.querySelectorAll('.aquarium-card .add-to-cart').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const card = this.closest('.aquarium-card');
            const name = card.querySelector('h3').textContent;
            const price = parseFloat(card.querySelector('.aquarium-price').textContent.replace('$', '').replace(',', ''));
            const size = card.querySelector('.spec:nth-child(1) span').textContent.replace('Size: ', '');
            const material = card.querySelector('.spec:nth-child(2) span').textContent.replace('Material: ', '');
            
            const product = {
                id: Date.now().toString(),
                name: name,
                price: price,
                size: size,
                material: material,
                category: 'Aquarium',
                image: card.querySelector('.aquarium-image')?.style.backgroundImage || ''
            };
            
            Fishify.addToCart(product);
            
            // Button feedback animation
            const originalHTML = this.innerHTML;
            this.innerHTML = '<i class="fas fa-check"></i> Added!';
            this.style.background = 'linear-gradient(135deg, #27AE60 0%, #2ECC71 100%)';
            
            setTimeout(() => {
                this.innerHTML = originalHTML;
                this.style.background = '';
            }, 2000);
        });
    });
    
    // Card hover effects
    document.querySelectorAll('.aquarium-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            const image = this.querySelector('.aquarium-image');
            if (image) {
                image.style.transform = 'scale(1.05)';
                image.style.transition = 'transform 0.3s ease';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const image = this.querySelector('.aquarium-image');
            if (image) {
                image.style.transform = 'scale(1)';
            }
        });
        
        // Click to view details
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.add-to-cart')) {
                const name = this.querySelector('h3').textContent;
                const price = this.querySelector('.aquarium-price').textContent;
                
                // In a real app, this would redirect to product detail page
                Fishify.showNotification(`Viewing details for ${name} - ${price}`, 'info');
            }
        });
    });
}

// Load aquariums from API
async function loadAquariumsFromAPI() {
    try {
        const response = await fetch('/api/products.php?category=aquarium');
        const aquariums = await response.json();
        
        if (aquariums.length > 0) {
            populateAquariumGrid(aquariums);
        }
    } catch (error) {
        console.error('Error loading aquariums:', error);
        // Use fallback data
        useFallbackAquariumData();
    }
}

function populateAquariumGrid(aquariums) {
    const grid = document.querySelector('.products-grid');
    if (!grid) return;
    
    // Clear existing content (keep first few for demo)
    const existingCards = grid.querySelectorAll('.aquarium-card');
    if (existingCards.length > 8) {
        grid.innerHTML = '';
    }
    
    // Add aquarium cards from API
    aquariums.forEach(aquarium => {
        const card = createAquariumCardFromAPI(aquarium);
        if (existingCards.length <= 8) {
            grid.appendChild(card);
        }
    });
    
    // Re-setup event listeners
    setupAquariumCards();
}

function createAquariumCardFromAPI(aquarium) {
    const card = document.createElement('div');
    card.className = 'aquarium-card';
    card.dataset.price = aquarium.price;
    card.dataset.size = aquarium.size ? parseInt(aquarium.size) : 30;
    card.dataset.material = aquarium.material?.toLowerCase() || 'glass';
    card.dataset.type = aquarium.type?.toLowerCase() || 'freshwater';
    
    const colorPairs = [
        ['#4facfe', '#00f2fe'],
        ['#43e97b', '#38f9d7'],
        ['#fa709a', '#fee140'],
        ['#30cfd0', '#330867'],
        ['#667eea', '#764ba2'],
        ['#f093fb', '#f5576c'],
        ['#5ee7df', '#b490ca'],
        ['#d299c2', '#fef9d7']
    ];
    
    const randomColor = colorPairs[Math.floor(Math.random() * colorPairs.length)];
    
    card.innerHTML = `
        <div class="aquarium-image">
            <div class="img-placeholder" style="background: linear-gradient(135deg, ${randomColor[0]} 0%, ${randomColor[1]} 100%)">
                <div class="glass-effect"></div>
                <span>${aquarium.name}</span>
            </div>
        </div>
        <div class="aquarium-info">
            <h3>${aquarium.name}</h3>
            <div class="aquarium-price">${Fishify.formatPrice(aquarium.price)}</div>
            <div class="aquarium-specs">
                <div class="spec">
                    <i class="fas fa-ruler-combined"></i>
                    <span>Size: ${aquarium.size || '30'} Gallons</span>
                </div>
                <div class="spec">
                    <i class="fas fa-gem"></i>
                    <span>Material: ${aquarium.material || 'Glass'}</span>
                </div>
            </div>
            <button class="btn btn-aqua add-to-cart">
                <i class="fas fa-cart-plus"></i> Add to Cart
            </button>
        </div>
    `;
    
    return card;
}

function useFallbackAquariumData() {
    // Fallback data in case API fails
    const fallbackAquariums = [
        {
            name: "Professional Marine Aquarium",
            price: 1299.99,
            size: "120",
            material: "Glass",
            type: "saltwater"
        },
        {
            name: "Compact Betta Tank",
            price: 79.99,
            size: "5",
            material: "Acrylic",
            type: "freshwater"
        },
        {
            name: "Reef Ready Cube",
            price: 499.99,
            size: "45",
            material: "Glass",
            type: "saltwater"
        },
        {
            name: "Breeding Rack System",
            price: 299.99,
            size: "20",
            material: "Acrylic",
            type: "breeding"
        }
    ];
    
    populateAquariumGrid(fallbackAquariums);
}

// Add animation for slideUp
if (!document.querySelector('#aquarium-animations')) {
    const style = document.createElement('style');
    style.id = 'aquarium-animations';
    style.textContent = `
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .aquarium-card {
            animation: slideUp 0.5s ease;
        }
    `;
    document.head.appendChild(style);
}