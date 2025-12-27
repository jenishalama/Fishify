// accessories.js - Accessories Page Functionality

document.addEventListener('DOMContentLoaded', function() {
    initAccessoriesPage();
});

// ==================== GLOBAL VARIABLES ====================
const itemsPerPage = 8; // change as needed
let currentPage = 1;
let allAccessoryCards = Array.from(document.querySelectorAll('.fish-card')); // make sure each card has class 'fish-card'

// ==================== INIT FUNCTION ====================
function initAccessoriesPage() {
    setupAccessoryFilters();
    setupAccessorySorting();
    setupAccessoryCart();
    setupAccessoryPagination();
    showPage(1);
    updateCartCount();
}

// ==================== FILTERS ====================
function setupAccessoryFilters() {
    const priceSlider = document.querySelector('.price-slider');
    const categoryCheckboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
    
    if (priceSlider) {
        priceSlider.addEventListener('input', applyAccessoryFilters);
        updatePriceDisplay(priceSlider.value);
    }
    
    categoryCheckboxes.forEach(cb => cb.addEventListener('change', applyAccessoryFilters));
    applyAccessoryFilters(); // initial
}

function applyAccessoryFilters() {
    const maxPrice = parseInt(document.querySelector('.price-slider').value);
    const categoryCheckboxes = document.querySelectorAll('.filter-options input[type="checkbox"]:checked');
    const selectedCategories = Array.from(categoryCheckboxes).map(cb => cb.value.toLowerCase());

    allAccessoryCards.forEach(card => {
        const price = parseInt(card.getAttribute('data-price'));
        const category = card.dataset.category ? card.dataset.category.toLowerCase() : '';

        let visible = price <= maxPrice;

        if (selectedCategories.length > 0) visible = visible && selectedCategories.includes(category);

        card.dataset.visible = visible ? 'true' : 'false';
    });

    updatePriceDisplay(maxPrice);
    showPage(1);
}

function updatePriceDisplay(price) {
    const priceSpan = document.querySelector('.price-range span:last-child');
    if (priceSpan) priceSpan.textContent = `Rs. ${price.toLocaleString()}+`;
}

// ==================== SORTING ====================
function setupAccessorySorting() {
    const sortSelect = document.querySelector('.sort-by select');
    if (sortSelect) sortSelect.addEventListener('change', applyAccessorySort);
}

function applyAccessorySort() {
    const sortBy = document.querySelector('.sort-by select').value;
    const container = document.querySelector('.fish-grid');

    let visibleCards = allAccessoryCards.filter(c => c.dataset.visible === 'true');

    visibleCards.sort((a, b) => {
        const priceA = parseInt(a.getAttribute('data-price'));
        const priceB = parseInt(b.getAttribute('data-price'));

        if (sortBy.includes('Low to High')) return priceA - priceB;
        if (sortBy.includes('High to Low')) return priceB - priceA;
        return 0;
    });

    visibleCards.forEach(card => container.appendChild(card));
    showPage(1);
}

// ==================== PAGINATION ====================
function setupAccessoryPagination() {
    document.querySelectorAll('.page-number, .page-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            let pageNum = currentPage;

            if (link.classList.contains('page-number')) pageNum = parseInt(link.textContent);
            else if (link.textContent.includes('Next')) pageNum = currentPage + 1;
            else if (link.textContent.includes('Previous')) pageNum = currentPage - 1;

            const totalPages = Math.ceil(allAccessoryCards.filter(c => c.dataset.visible === 'true').length / itemsPerPage);
            if (pageNum < 1) pageNum = 1;
            if (pageNum > totalPages) pageNum = totalPages;

            showPage(pageNum);
        });
    });
}

function showPage(pageNumber) {
    const visibleCards = allAccessoryCards.filter(c => c.dataset.visible === 'true');
    allAccessoryCards.forEach(c => c.style.display = 'none');

    const start = (pageNumber - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    visibleCards.slice(start, end).forEach(c => c.style.display = 'block');
    currentPage = pageNumber;

    updatePaginationButtons();
}

function updatePaginationButtons() {
    const totalPages = Math.ceil(allAccessoryCards.filter(c => c.dataset.visible === 'true').length / itemsPerPage);
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
function setupAccessoryCart() {
    const addButtons = document.querySelectorAll('.fish-card .add-to-cart');
    
    addButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const card = this.closest('.fish-card');
            const name = card.querySelector('h3').textContent;
            const price = parseInt(card.getAttribute('data-price'));

            let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
            const existing = cart.find(item => item.name === name);

            if (existing) existing.quantity += 1;
            else cart.push({
                id: `accessory-${name.toLowerCase().replace(/\s+/g, '-')}`,
                name, price, quantity: 1, type: 'accessory'
            });

            localStorage.setItem('fishifyCart', JSON.stringify(cart));
            updateCartCount();
            showAccessoryNotification(`${name} added to cart!`);
        });
    });
}

// ==================== NOTIFICATION ====================
function showAccessoryNotification(message) {
    const existing = document.querySelector('.accessory-notification');
    if (existing) existing.remove();

    const notification = document.createElement('div');
    notification.className = 'accessory-notification';
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #9b59b6;
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

    const cartCounts = document.querySelectorAll('.cart-count');
    cartCounts.forEach(count => {
        count.textContent = totalItems;
        count.style.display = totalItems > 0 ? 'flex' : 'none';
    });
}