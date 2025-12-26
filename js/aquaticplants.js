// aquaticplants.js - Aquatic Plants Page Functionality

document.addEventListener('DOMContentLoaded', function() {
    initPlantsPage();
});

function initPlantsPage() {
    setupPlantFilters();
    setupPlantSorting();
    setupPlantCart();
    updateCartCount();
}

// ==================== FILTERS ====================
function setupPlantFilters() {
    const priceSlider = document.getElementById('priceSlider');
    const checkboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
    const activeFiltersContainer = document.getElementById('activeFilters');
    
    if (priceSlider) {
        priceSlider.addEventListener('input', function() {
            updatePriceDisplay(this.value);
            filterPlants();
        });
        updatePriceDisplay(priceSlider.value);
    }
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            filterPlants();
            updateActiveFilters();
        });
    });
}

function filterPlants() {
    const plants = document.querySelectorAll('.fish-card');
    const maxPrice = parseInt(document.getElementById('priceSlider').value);
    const selectedTypes = getSelectedValues('input[value*="foreground"], input[value*="midground"], input[value*="background"], input[value*="floating"], input[value*="stem"], input[value*="carpet"]');
    const selectedCare = getSelectedValues('input[value*="easy"], input[value*="medium"], input[value*="difficult"]');
    const selectedLight = getSelectedValues('input[value*="low"], input[value*="medium"], input[value*="high"]');
    
    let visibleCount = 0;
    
    plants.forEach(plant => {
        const price = parseInt(plant.getAttribute('data-price'));
        const type = plant.getAttribute('data-type').split(',');
        const care = plant.getAttribute('data-care');
        const light = plant.getAttribute('data-light');
        
        const pricePass = price <= maxPrice;
        const typePass = selectedTypes.length === 0 || type.some(t => selectedTypes.includes(t));
        const carePass = selectedCare.length === 0 || selectedCare.includes(care);
        const lightPass = selectedLight.length === 0 || selectedLight.includes(light);
        
        const isVisible = pricePass && typePass && carePass && lightPass;
        
        if (isVisible) {
            plant.style.display = 'block';
            visibleCount++;
        } else {
            plant.style.display = 'none';
        }
    });
    
    // Show/hide no results message
    const noResults = document.getElementById('noResults');
    if (noResults) {
        noResults.style.display = visibleCount === 0 ? 'block' : 'none';
    }
}

function getSelectedValues(selector) {
    const selected = [];
    document.querySelectorAll(selector).forEach(checkbox => {
        if (checkbox.checked) {
            selected.push(checkbox.value);
        }
    });
    return selected;
}

function updatePriceDisplay(price) {
    const maxPriceSpan = document.getElementById('maxPrice');
    if (maxPriceSpan) {
        maxPriceSpan.textContent = price >= 10000 ? '10,000+' : price.toLocaleString();
    }
}

function updateActiveFilters() {
    const activeFiltersContainer = document.getElementById('activeFilters');
    const selectedFilters = [];
    
    // Collect all checked checkboxes
    document.querySelectorAll('.filter-options input[type="checkbox"]:checked').forEach(checkbox => {
        selectedFilters.push(checkbox.nextElementSibling.textContent.trim());
    });
    
    // Add price filter if not at max
    const priceSlider = document.getElementById('priceSlider');
    if (priceSlider && parseInt(priceSlider.value) < 10000) {
        selectedFilters.push(`Max: Rs. ${priceSlider.value}`);
    }
    
    // Update display
    if (selectedFilters.length > 0) {
        activeFiltersContainer.innerHTML = `
            <strong>Active Filters:</strong>
            ${selectedFilters.map(filter => 
                `<span class="filter-tag">${filter}</span>`
            ).join('')}
            <button id="clearFilters">Clear All</button>
        `;
        
        // Add clear filters functionality
        document.getElementById('clearFilters').addEventListener('click', clearAllFilters);
    } else {
        activeFiltersContainer.innerHTML = '';
    }
}

function clearAllFilters() {
    // Uncheck all checkboxes
    document.querySelectorAll('.filter-options input[type="checkbox"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Reset price slider
    const priceSlider = document.getElementById('priceSlider');
    if (priceSlider) {
        priceSlider.value = 10000;
        updatePriceDisplay(10000);
    }
    
    // Clear active filters display
    document.getElementById('activeFilters').innerHTML = '';
    
    // Show all plants
    filterPlants();
}

// ==================== SORTING ====================
function setupPlantSorting() {
    const sortSelect = document.getElementById('sortSelect');
    
    if (sortSelect) {
        sortSelect.addEventListener('change', sortPlants);
    }
}

function sortPlants() {
    const container = document.getElementById('plantsGrid');
    const plants = Array.from(document.querySelectorAll('.fish-card'));
    const sortBy = document.getElementById('sortSelect').value;
    
    plants.sort((a, b) => {
        const priceA = parseInt(a.getAttribute('data-price'));
        const priceB = parseInt(b.getAttribute('data-price'));
        const nameA = a.getAttribute('data-name').toLowerCase();
        const nameB = b.getAttribute('data-name').toLowerCase();
        
        switch(sortBy) {
            case 'price-low':
                return priceA - priceB;
            case 'price-high':
                return priceB - priceA;
            case 'name':
                return nameA.localeCompare(nameB);
            default:
                return 0; // Popularity - keep original order
        }
    });
    
    // Re-append sorted plants
    container.innerHTML = '';
    plants.forEach(plant => {
        if (plant.style.display !== 'none') {
            container.appendChild(plant);
        }
    });
}

// ==================== ADD TO CART ====================
function setupPlantCart() {
    const addButtons = document.querySelectorAll('.fish-card .add-to-cart');
    
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const card = this.closest('.fish-card');
            const name = card.querySelector('h3').textContent;
            const price = parseInt(card.getAttribute('data-price'));
            const details = card.querySelector('.plant-details').textContent;
            
            addPlantToCart(name, price, details);
            
            showPlantNotification(`${name} added to cart!`);
        });
    });
}

function addPlantToCart(name, price, details) {
    let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    
    const plant = {
        id: `plant-${name.toLowerCase().replace(/\s+/g, '-')}`,
        name: name,
        price: price,
        details: details,
        quantity: 1,
        type: 'plant'
    };
    
    cart.push(plant);
    localStorage.setItem('fishifyCart', JSON.stringify(cart));
    updateCartCount();
}

// ==================== NOTIFICATION ====================
function showPlantNotification(message) {
    const existing = document.querySelector('.plant-notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = 'plant-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #27ae60;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-weight: 500;
        animation: slideIn 0.3s ease-out;
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => {
            if (notification.parentNode) notification.remove();
        }, 300);
    }, 3000);
    
    // Add animation if not exists
    if (!document.querySelector('#plant-notification-anim')) {
        const style = document.createElement('style');
        style.id = 'plant-notification-anim';
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
}

// ==================== CART COUNT ====================
function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    
    document.querySelectorAll('.cart-count').forEach(count => {
        count.textContent = totalItems;
        count.style.display = totalItems > 0 ? 'flex' : 'none';
    });
}

// ==================== ENHANCEMENTS ====================
// Add hover effects for plants
document.querySelectorAll('.fish-card').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-5px)';
        this.style.boxShadow = '0 10px 25px rgba(46, 204, 113, 0.2)';
        this.style.transition = 'all 0.3s ease';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = 'none';
    });
});