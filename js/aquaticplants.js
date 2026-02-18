// aquaticplants.js - Updated Aquatic Plants Page Functionality

document.addEventListener('DOMContentLoaded', function() {
    initPlantsPage();
});

function initPlantsPage() {
    setupFilters();
    setupSorting();
    setupCart();
    updateCartCount();
}

// ==================== FILTERS ====================
function setupFilters() {
    const priceSlider = document.getElementById('priceSlider');
    const checkboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');

    if (priceSlider) {
        priceSlider.addEventListener('input', () => {
            updatePriceDisplay(priceSlider.value);
            applyFilters();
        });
        updatePriceDisplay(priceSlider.value);
    }

    checkboxes.forEach(cb => cb.addEventListener('change', () => {
        applyFilters();
        displayActiveFilters();
    }));
}

function applyFilters() {
    const plants = document.querySelectorAll('.fish-card');
    const maxPrice = parseInt(document.getElementById('priceSlider').value);

    const selectedTypes = getCheckedValues('Plant Type');
    const selectedCare = getCheckedValues('Care Level');
    const selectedLight = getCheckedValues('Light Requirement');

    let visibleCount = 0;

    plants.forEach(plant => {
        const price = parseInt(plant.dataset.price);
        const type = plant.dataset.type.split(',');
        const care = plant.dataset.care;
        const light = plant.dataset.light;

        const pricePass = price <= maxPrice;
        const typePass = selectedTypes.length === 0 || type.some(t => selectedTypes.includes(t));
        const carePass = selectedCare.length === 0 || selectedCare.includes(care);
        const lightPass = selectedLight.length === 0 || selectedLight.includes(light);

        const show = pricePass && typePass && carePass && lightPass;

        plant.style.display = show ? 'block' : 'none';
        if (show) visibleCount++;
    });

    const noResults = document.getElementById('noResults');
    noResults.style.display = visibleCount === 0 ? 'block' : 'none';

    sortPlants(); // keep sorted after filtering
}

function getCheckedValues(groupName) {
    return Array.from(document.querySelectorAll(`.filter-group h3`))
        .filter(h3 => h3.textContent === groupName)
        .map(h3 => h3.nextElementSibling.querySelectorAll('input[type="checkbox"]:checked'))
        .flat()
        .map(cb => cb.value);
}

function updatePriceDisplay(price) {
    const maxPriceSpan = document.getElementById('maxPrice');
    if (maxPriceSpan) {
        maxPriceSpan.textContent = price >= 10000 ? '10,000+' : price.toLocaleString();
    }
}

function displayActiveFilters() {
    const activeContainer = document.getElementById('activeFilters');
    const filters = [];

    document.querySelectorAll('.filter-options input[type="checkbox"]:checked').forEach(cb => {
        filters.push(cb.nextElementSibling.textContent.trim());
    });

    const priceSlider = document.getElementById('priceSlider');
    if (priceSlider.value < 10000) filters.push(`Max: Rs. ${priceSlider.value}`);

    if (filters.length === 0) {
        activeContainer.innerHTML = '';
        return;
    }

    activeContainer.innerHTML = `
        <strong>Active Filters:</strong>
        ${filters.map(f => `<span class="filter-tag">${f} <i class="fas fa-times"></i></span>`).join('')}
        <button id="clearFilters">Clear All</button>
    `;

    // Remove individual filter
    activeContainer.querySelectorAll('.filter-tag i').forEach(icon => {
        icon.addEventListener('click', e => {
            const value = e.target.parentElement.textContent.replace('×','').trim();
            document.querySelectorAll('.filter-options input[type="checkbox"]').forEach(cb => {
                if (cb.nextElementSibling.textContent.trim() === value) cb.checked = false;
            });
            if (value.startsWith('Max:')) priceSlider.value = 10000;
            applyFilters();
            displayActiveFilters();
        });
    });

    document.getElementById('clearFilters').addEventListener('click', clearAllFilters);
}

function clearAllFilters() {
    document.querySelectorAll('.filter-options input[type="checkbox"]').forEach(cb => cb.checked = false);
    const priceSlider = document.getElementById('priceSlider');
    priceSlider.value = 10000;
    updatePriceDisplay(10000);
    applyFilters();
    displayActiveFilters();
}

// ==================== SORTING ====================
function setupSorting() {
    const sortSelect = document.getElementById('sortSelect');
    if (sortSelect) sortSelect.addEventListener('change', sortPlants);
}

function sortPlants() {
    const container = document.getElementById('plantsGrid');
    const plants = Array.from(container.querySelectorAll('.fish-card')).filter(p => p.style.display !== 'none');
    const sortBy = document.getElementById('sortSelect').value;

    plants.sort((a, b) => {
        const priceA = parseInt(a.dataset.price);
        const priceB = parseInt(b.dataset.price);
        const nameA = a.dataset.name.toLowerCase();
        const nameB = b.dataset.name.toLowerCase();

        switch(sortBy) {
            case 'price-low': return priceA - priceB;
            case 'price-high': return priceB - priceA;
            case 'name': return nameA.localeCompare(nameB);
            default: return 0;
        }
    });

    plants.forEach(p => container.appendChild(p));
}

// ==================== CART ====================
function setupCart() {
    document.querySelectorAll('.fish-card .add-to-cart').forEach(button => {
        button.addEventListener('click', e => {
            e.preventDefault();
            const card = button.closest('.fish-card');
            const name = card.querySelector('h3').textContent;
            const price = parseInt(card.dataset.price);
            const details = card.querySelector('.plant-details').textContent;

            addToCart({ name, price, details, type: 'plant' });
            showNotification(`${name} added to cart!`);
        });
    });
}

function addToCart(item) {
    let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    const id = `plant-${item.name.toLowerCase().replace(/\s+/g, '-')}`;

    const existing = cart.find(p => p.id === id);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ ...item, id, quantity: 1 });
    }

    localStorage.setItem('fishifyCart', JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    const total = cart.reduce((sum, item) => sum + item.quantity, 0);
    document.querySelectorAll('.cart-count').forEach(c => {
        c.textContent = total;
        c.style.display = total > 0 ? 'flex' : 'none';
    });
}

// ==================== NOTIFICATIONS ====================
function showNotification(message) {
    const existing = document.querySelector('.plant-notification');
    if (existing) existing.remove();

    const notif = document.createElement('div');
    notif.className = 'plant-notification';
    notif.textContent = message;
    notif.style.cssText = `
        position: fixed; top: 20px; right: 20px; background:#27ae60;
        color:#fff; padding:15px 20px; border-radius:5px; z-index:1000;
        font-weight:500; box-shadow:0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
    `;
    document.body.appendChild(notif);

    setTimeout(() => {
        notif.style.animation = 'slideOut 0.3s ease-out forwards';
        setTimeout(() => notif.remove(), 300);
    }, 3000);

    if (!document.querySelector('#plant-notif-style')) {
        const style = document.createElement('style');
        style.id = 'plant-notif-style';
        style.textContent = `
            @keyframes slideIn { from {transform:translateX(100%);opacity:0;} to {transform:translateX(0);opacity:1;} }
            @keyframes slideOut { from {transform:translateX(0);opacity:1;} to {transform:translateX(100%);opacity:0;} }
        `;
        document.head.appendChild(style);
    }
}

// ==================== HOVER EFFECT ====================
document.querySelectorAll('.fish-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-5px)';
        card.style.boxShadow = '0 10px 25px rgba(46, 204, 113, 0.2)';
        card.style.transition = 'all 0.3s ease';
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0)';
        card.style.boxShadow = 'none';
    });
});