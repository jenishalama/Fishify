// accessories.js - Accessories Page Functionality (Matched with Fish Page Logic)
document.addEventListener('DOMContentLoaded', function () {
    initFiltering();
    initSorting();
    initClearFilters();
    initAccessoriesCart();
    initPagination();
});

// ==================== GLOBAL VARIABLES ====================
const itemsPerPage = 8;
let currentPage = 1;

// ==================== HELPER FUNCTION ====================
function formatPrice(price) {
    return `Rs. ${parseInt(price).toLocaleString()}`;
}

// ==================== FILTERING FUNCTIONALITY ====================
function initFiltering() {
    const priceSlider = document.querySelector('.price-slider');
    const availabilityCheckboxes = document.querySelectorAll('input[name="availability"]');

    if (priceSlider) {
        priceSlider.addEventListener('input', () => {
            updatePriceDisplay(priceSlider.value);
            updatePriceSliderColor(priceSlider);
            applyFilters();
        });
        updatePriceSliderColor(priceSlider); // Initial color
    }

    availabilityCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            availabilityCheckboxes.forEach(other => {
                if (other !== cb) other.checked = false;
            });
            applyFilters();
        });
    });

    applyFilters(); 
}

function applyFilters() {
    const slider = document.querySelector('.price-slider');
    const maxPrice = slider ? parseFloat(slider.value) || 10000 : 10000;
    const availabilityCheckboxes = document.querySelectorAll('input[name="availability"]');
    const allCards = Array.from(document.querySelectorAll('.fish-card'));

    let showInStockOnly = false;
    let showOutOfStockOnly = false;

    availabilityCheckboxes.forEach(cb => {
        if (cb.checked) {
            if (cb.value === 'in-stock') showInStockOnly = true;
            if (cb.value === 'out-of-stock') showOutOfStockOnly = true;
        }
    });

    allCards.forEach(card => {
        const price = parseFloat(card.dataset.price) || 0;
        const inStock = (card.dataset.stockStatus || card.dataset.stock) === 'in-stock';
        let visible = price <= maxPrice;

        if (showInStockOnly) visible = visible && inStock;
        if (showOutOfStockOnly) visible = visible && !inStock;

        card.dataset.visible = visible ? 'true' : 'false';
    });

    updatePriceDisplay(maxPrice);
    currentPage = 1; 
    showPage(1);
}

// ==================== UPDATE PRICE DISPLAY ====================
function updatePriceDisplay(maxPrice) {
    const priceRangeSpan = document.querySelector('.price-range span:last-child');
    if (priceRangeSpan) priceRangeSpan.textContent = `Rs. ${parseInt(maxPrice).toLocaleString()}`;
}

// ==================== CLEAR FILTERS ====================
function initClearFilters() {
    const btn = document.querySelector('.btn-clear-filters');
    if (!btn) return;
    btn.addEventListener('click', function() {
        const slider = document.querySelector('.price-slider');
        if (slider) {
            slider.value = 10000;
            updatePriceSliderColor(slider);
        }
        document.querySelectorAll('input[name="availability"]').forEach(cb => cb.checked = false);
        updatePriceDisplay(10000);
        applyFilters();
    });
}

// ==================== SORTING FUNCTIONALITY ====================
function initSorting() {
    const sortSelect = document.querySelector('.sort-by select');
    if (sortSelect) sortSelect.addEventListener('change', applySort);
}

function applySort() {
    const sortSelect = document.querySelector('.sort-by select');
    if (!sortSelect) return;
    const sortBy = sortSelect.value;
    const container = document.querySelector('.fish-grid');
    if (!container) return;

    const allCards = Array.from(document.querySelectorAll('.fish-card'));
    let visibleCards = allCards.filter(c => c.dataset.visible === 'true');

    visibleCards.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price) || 0;
        const priceB = parseFloat(b.dataset.price) || 0;
        if (sortBy === 'price-low') return priceA - priceB;
        if (sortBy === 'price-high') return priceB - priceA;
        return 0;
    });

    visibleCards.forEach(card => container.appendChild(card));
    showPage(1);
}

// ==================== PAGINATION ====================
function initPagination() {
    document.querySelectorAll('.page-number, .page-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const allCards = Array.from(document.querySelectorAll('.fish-card'));
            const visibleCount = allCards.filter(c => c.dataset.visible === 'true').length;
            const totalPages = Math.ceil(visibleCount / itemsPerPage);

            let pageNum = currentPage;
            if (link.classList.contains('page-number')) {
                pageNum = parseInt(link.textContent, 10);
            } else if (link.textContent.trim().includes('Next')) {
                pageNum = currentPage + 1;
            } else if (link.textContent.trim().includes('Previous')) {
                pageNum = currentPage - 1;
            }

            if (pageNum < 1) pageNum = 1;
            if (pageNum > totalPages) pageNum = totalPages;

            showPage(pageNum);
        });
    });
}

function showPage(pageNumber) {
    const allCards = Array.from(document.querySelectorAll('.fish-card'));
    const visibleCards = allCards.filter(c => c.dataset.visible === 'true');
    allCards.forEach(c => c.style.display = 'none');

    const start = (pageNumber - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const toShow = visibleCards.slice(start, end);
    toShow.forEach(c => c.style.display = 'block');

    currentPage = pageNumber;
    updatePaginationButtons();
}

function updatePaginationButtons() {
    const allCards = Array.from(document.querySelectorAll('.fish-card'));
    const visibleCards = allCards.filter(c => c.dataset.visible === 'true');
    const totalPages = Math.ceil(visibleCards.length / itemsPerPage);
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

    const prevLink = document.querySelector('.page-link:first-child');
    const nextLink = document.querySelector('.page-link:last-child');

    if (prevLink) prevLink.classList.toggle('disabled', currentPage === 1 || totalPages === 0);
    if (nextLink) nextLink.classList.toggle('disabled', currentPage === totalPages || totalPages === 0);

    const noResults = document.getElementById('noResults');
    if (noResults) noResults.style.display = visibleCards.length === 0 ? 'block' : 'none';
}

// ==================== ADD TO CART ====================
function initAccessoriesCart() {
    const addToCartButtons = document.querySelectorAll('.fish-card .add-to-cart');

    addToCartButtons.forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const card = this.closest('.fish-card');
            const name = card.querySelector('h3').textContent.trim();
            const price = parseFloat(card.dataset.price) || 0;
            const imgEl = card.querySelector('img');

            const productId = card.dataset.id ? parseInt(card.dataset.id, 10) : null;
            const stock = card.dataset.stock ? parseInt(card.dataset.stock, 10) : 999;
            const product = {
                id: productId !== null && !isNaN(productId) ? productId : 'accessory-' + name.toLowerCase().replace(/[^a-z0-9]/g, '-'),
                name,
                price,
                image: imgEl ? imgEl.src : '',
                description: '',
                category: 'accessories',
                stock: stock
            };

            if (typeof addToCart === 'function') {
                addToCart(product);
            }
        });
    });

    if (typeof updateCartCount === 'function') {
        updateCartCount();
    }
}