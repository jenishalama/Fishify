// fish.js - Fish Page Functionality
document.addEventListener('DOMContentLoaded', function() {
    initFiltering();
    initSorting();
    initFishCart();
    initPagination();
});

// ==================== GLOBAL VARIABLES ====================
const itemsPerPage = 8; // 8 cards per page
let currentPage = 1;
let allFishCards = Array.from(document.querySelectorAll('.fish-card'));

// ==================== FILTERING FUNCTIONALITY ====================
function initFiltering() {
    const priceSlider = document.querySelector('.price-slider');
    const availabilityCheckboxes = document.querySelectorAll('input[name="availability"]');

    if (priceSlider) priceSlider.addEventListener('input', applyFilters);

    availabilityCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            // Only one checkbox can be selected at a time
            availabilityCheckboxes.forEach(other => {
                if (other !== cb) other.checked = false;
            });
            applyFilters();
        });
    });

    applyFilters(); // initial filter
}

function applyFilters() {
    const maxPrice = parseInt(document.querySelector('.price-slider').value);
    const availabilityCheckboxes = document.querySelectorAll('input[name="availability"]');
    let showInStockOnly = false;
    let showOutOfStockOnly = false;

    availabilityCheckboxes.forEach(cb => {
        if (cb.checked) {
            if (cb.value === 'in-stock') showInStockOnly = true;
            if (cb.value === 'pre-order') showOutOfStockOnly = true;
        }
    });

    allFishCards.forEach(card => {
        const price = parseInt(card.getAttribute('data-price'));
        const inStock = card.dataset.stock === 'in-stock'; // Use data-stock attribute
        let visible = price <= maxPrice;

        if (showInStockOnly) visible = visible && inStock;
        if (showOutOfStockOnly) visible = visible && !inStock;

        card.dataset.visible = visible ? 'true' : 'false';

        // Grey out out-of-stock fish and disable Add to Cart
      const addBtn = card.querySelector('.add-to-cart');

// Badge handling
let badge = card.querySelector('.out-of-stock-badge');

if (!inStock) {
    addBtn.disabled = true;
    addBtn.textContent = 'Out of Stock';

    if (!badge) {
        badge = document.createElement('span');
        badge.className = 'out-of-stock-badge';
        badge.textContent = 'Out of Stock';
        card.style.position = 'relative';
        card.appendChild(badge);
    }
} else {
    addBtn.disabled = false;
    addBtn.innerHTML = `<i class="fas fa-cart-plus"></i> Add to Cart`;

    if (badge) badge.remove();
}
    });

    updatePriceDisplay(maxPrice);
    showPage(1); // Reset to first page after filtering
}
out
// ==================== UPDATE PRICE DISPLAY ====================
function updatePriceDisplay(maxPrice) {
    const priceRangeSpan = document.querySelector('.price-range span:last-child');
    if (priceRangeSpan) priceRangeSpan.textContent = `Rs. ${maxPrice.toLocaleString()}`;
}

// ==================== SORTING FUNCTIONALITY ====================
function initSorting() {
    const sortSelect = document.querySelector('.sort-by select');
    if (sortSelect) sortSelect.addEventListener('change', applySort);
}

function applySort() {
    const sortBy = document.querySelector('.sort-by select').value;
    const container = document.querySelector('.fish-grid');

    let visibleCards = allFishCards.filter(c => c.dataset.visible === 'true');

    visibleCards.sort((a, b) => {
        const priceA = parseInt(a.getAttribute('data-price'));
        const priceB = parseInt(b.getAttribute('data-price'));

        if (sortBy.includes('Low to High')) return priceA - priceB;
        if (sortBy.includes('High to Low')) return priceB - priceA;
        return 0; // popularity or default
    });

    visibleCards.forEach(card => container.appendChild(card));
    showPage(1);
}

// ==================== PAGINATION ====================
function initPagination() {
    updatePaginationButtons();

    document.querySelectorAll('.page-number, .page-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            let pageNum = currentPage;

            if (link.classList.contains('page-number')) {
                pageNum = parseInt(link.textContent);
            } else if (link.textContent.includes('Next')) {
                pageNum = currentPage + 1;
            } else if (link.textContent.includes('Previous')) {
                pageNum = currentPage - 1;
            }

            const totalPages = Math.ceil(allFishCards.filter(c => c.dataset.visible === 'true').length / itemsPerPage);
            if (pageNum < 1) pageNum = 1;
            if (pageNum > totalPages) pageNum = totalPages;

            showPage(pageNum);
        });
    });

    showPage(1);
}

function showPage(pageNumber) {
    const visibleCards = allFishCards.filter(c => c.dataset.visible === 'true');
    allFishCards.forEach(c => c.style.display = 'none');

    const start = (pageNumber - 1) * itemsPerPage;
    const end = start + itemsPerPage;

    visibleCards.slice(start, end).forEach(c => c.style.display = 'block');
    currentPage = pageNumber;

    updatePaginationButtons();
}

function updatePaginationButtons() {
    const totalPages = Math.ceil(allFishCards.filter(c => c.dataset.visible === 'true').length / itemsPerPage);
    const pageNumbers = document.querySelectorAll('.page-number');

    pageNumbers.forEach((el, index) => {
        if (index < totalPages) {
            el.style.display = 'inline-block';
            el.textContent = index + 1;
            el.classList.toggle('active', currentPage === index + 1);
        } else {
            el.style.display = 'none';
        }
    });

    // Enable/disable prev & next
    const prevLink = document.querySelector('.page-link:first-child');
    const nextLink = document.querySelector('.page-link:last-child');

    if (prevLink) prevLink.classList.toggle('disabled', currentPage === 1);
    if (nextLink) nextLink.classList.toggle('disabled', currentPage === totalPages);
}

// ==================== ADD TO CART ====================
function initFishCart() {
    const addToCartButtons = document.querySelectorAll('.fish-card .add-to-cart');

    addToCartButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();

            const card = this.closest('.fish-card');
            const name = card.querySelector('h3').textContent;
            const price = parseFloat(card.querySelector('.amount').textContent);

            let cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
            const existing = cart.find(item => item.name === name);

            if (existing) existing.quantity += 1;
            else cart.push({ id: generateId(name), name, price, quantity: 1, type: 'fish' });

            localStorage.setItem('fishifyCart', JSON.stringify(cart));
            updateCartCount();
            showNotification(`${name} added to cart!`);
        });
    });

    updateCartCount();
}

function generateId(name) {
    return 'fish-' + name.toLowerCase().replace(/[^a-z0-9]/g, '-');
}

function updateCartCount() {
    const cart = JSON.parse(localStorage.getItem('fishifyCart')) || [];
    const total = cart.reduce((sum, item) => sum + item.quantity, 0);

    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = total;
        el.style.display = total > 0 ? 'flex' : 'none';
    });
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'fish-notification';
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
    `;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}