// aquarium.js - Aquarium Page Functionality
document.addEventListener('DOMContentLoaded', function () {
    initAquariumPage();
});

// ==================== GLOBAL VARIABLES ====================
const itemsPerPage = 8; // 8 cards per page
let currentPage = 1;
let allAquariumCards = []; // Will initialize after DOM loaded

function initAquariumPage() {
    allAquariumCards = Array.from(document.querySelectorAll('.aquarium-card'));
    setupFilters();
    setupSorting();
    setupAddToCart();
    setupPagination();
    updateCartCount();
}

// ==================== FILTERS ====================
function setupFilters() {
    const priceSlider = document.querySelector('.aquarium-price-slider');
    const availabilityCheckboxes = document.querySelectorAll('input[name="availability"]');

    if (priceSlider) priceSlider.addEventListener('input', applyFilters);

    availabilityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            // Only one checkbox active at a time
            availabilityCheckboxes.forEach(other => {
                if (other !== checkbox) other.checked = false;
            });
            applyFilters();
        });
    });

    applyFilters(); // initial filter
}

function applyFilters() {
    const maxPrice = parseInt(document.querySelector('.aquarium-price-slider').value) || 0;
    const availabilityCheckboxes = document.querySelectorAll('input[name="availability"]');
    let showInStockOnly = false;
    let showOutOfStockOnly = false;

    availabilityCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            if (checkbox.nextElementSibling.textContent.toLowerCase() === 'in stock') showInStockOnly = true;
            if (checkbox.nextElementSibling.textContent.toLowerCase() === 'out of stock') showOutOfStockOnly = true;
        }
    });

    allAquariumCards.forEach(card => {
        const price = parseInt(card.dataset.price) || 0;
        const inStock = card.dataset.stock === 'in-stock';

        let visible = price <= maxPrice;
        if (showInStockOnly) visible = visible && inStock;
        if (showOutOfStockOnly) visible = visible && !inStock;

        card.dataset.visible = visible ? 'true' : 'false';

        // Out-of-stock badge & disable add-to-cart
        const addBtn = card.querySelector('.add-to-cart');
        let badge = card.querySelector('.out-of-stock-badge');


        if (!inStock) {

            if (addBtn) {
                addBtn.disabled = true;
                addBtn.textContent = 'Out of Stock';
            }
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'out-of-stock-badge';
                badge.textContent = 'Out of Stock';
                card.style.position = 'relative';
                card.appendChild(badge);
            }
        } else {
       
            if (addBtn) {
                addBtn.disabled = false;
                addBtn.innerHTML = `<i class="fas fa-cart-plus"></i> Add to Cart`;
            }
            if (badge) badge.remove();
        }
    });

    updatePriceDisplay(maxPrice);
    showPage(1);
}

function updatePriceDisplay(price) {
    const priceSpan = document.querySelector('.aquarium-price-range span:last-child');
    if (priceSpan) priceSpan.textContent = `Rs. ${price.toLocaleString()}+`;
}

// ==================== SORTING ====================
function setupSorting() {
    const sortSelect = document.querySelector('.aquarium-sort select');
    if (sortSelect) sortSelect.addEventListener('change', applySort);
}

function applySort() {
    const sortBy = document.querySelector('.aquarium-sort select').value;
    const container = document.querySelector('.aquarium-grid');

    let visibleCards = allAquariumCards.filter(c => c.dataset.visible === 'true');

    visibleCards.sort((a, b) => {
        const priceA = parseInt(a.dataset.price) || 0;
        const priceB = parseInt(b.dataset.price) || 0;

        if (sortBy.includes('Low to High')) return priceA - priceB;
        if (sortBy.includes('High to Low')) return priceB - priceA;
        return 0; // popularity/default
    });

    visibleCards.forEach(card => container.appendChild(card));
    showPage(1);
}

// ==================== PAGINATION ====================
function setupPagination() {
    document.querySelectorAll('.page-number, .page-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            let pageNum = currentPage;

            if (link.classList.contains('page-number')) pageNum = parseInt(link.textContent);
            else if (link.textContent.includes('Next')) pageNum = currentPage + 1;
            else if (link.textContent.includes('Previous')) pageNum = currentPage - 1;

            const totalPages = Math.ceil(allAquariumCards.filter(c => c.dataset.visible === 'true').length / itemsPerPage);
            if (pageNum < 1) pageNum = 1;
            if (pageNum > totalPages) pageNum = totalPages;

            showPage(pageNum);
        });
    });

    showPage(1);
}

function showPage(pageNumber) {
    const visibleCards = allAquariumCards.filter(c => c.dataset.visible === 'true');
    allAquariumCards.forEach(c => c.style.display = 'none');

    const start = (pageNumber - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    visibleCards.slice(start, end).forEach(c => c.style.display = 'block');
    currentPage = pageNumber;

    updatePaginationButtons();
}

function updatePaginationButtons() {
    const totalPages = Math.ceil(allAquariumCards.filter(c => c.dataset.visible === 'true').length / itemsPerPage);
    const pageNumbers = document.querySelectorAll('.page-number');

    pageNumbers.forEach((el, index) => {
        if (index < totalPages) {
            el.style.display = 'inline-block';
            el.textContent = index + 1;
            el.classList.toggle('active', currentPage === index + 1);
        } else el.style.display = 'none';
    });

    const prevLink = document.querySelector('.page-link:first-child');
    const nextLink = document.querySelector('.page-link:last-child');

    if (prevLink) prevLink.classList.toggle('disabled', currentPage === 1);
    if (nextLink) nextLink.classList.toggle('disabled', currentPage === totalPages);
}

// ==================== ADD TO CART ====================
function setupAddToCart() {
    const addButtons = document.querySelectorAll('.aquarium-card .add-to-cart');

    addButtons.forEach(button => {
        button.addEventListener('click', e => {
            e.preventDefault();

            const card = button.closest('.aquarium-card');
            const name = card.querySelector('h3').textContent;
            const price = parseInt(card.querySelector('.aquarium-price').textContent.replace(/[^0-9]/g, '')) || 0;

            let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
            const existing = cart.find(item => item.name === name);

            if (existing) existing.quantity += 1;
            else cart.push({
                id: `aquarium-${name.toLowerCase().replace(/\s+/g, '-')}`,
                name,
                price,
                quantity: 1,
                type: 'aquarium'
            });

            localStorage.setItem('fishifyCart', JSON.stringify(cart));
            updateCartCount();
            showNotification(`${name} added to cart!`);
        });
    });
}

// ==================== NOTIFICATION ====================
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'aquarium-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #3498db;
        color: white;
        padding: 15px 20px;
        border-radius: 5px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-weight: 500;
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
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