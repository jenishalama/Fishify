document.addEventListener('DOMContentLoaded', function () {
    initFiltering();
    initSorting();
    initClearFilters();
    initPlantsCart();
    initPagination();
});

// ==================== GLOBAL VARIABLES ====================
const itemsPerPage = 8;
let currentPage = 1;
let allPlantsCards = [];

// ==================== HELPER ====================
function formatPrice(price) {
    return `Rs. ${Math.floor(price).toLocaleString()}`;
}

// ==================== FILTERING ====================
function initFiltering() {
    allPlantsCards = Array.from(document.querySelectorAll('.fish-card'));

    const priceSlider = document.getElementById('priceSlider');
    const checkboxes = document.querySelectorAll('input[name="availability"]');

    if (priceSlider) {
        priceSlider.addEventListener('input', () => {
            updatePriceSliderColor(priceSlider);
            applyFilters();
        });
        updatePriceSliderColor(priceSlider);
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            // ✅ allow only one checkbox at a time
            checkboxes.forEach(other => {
                if (other !== cb) other.checked = false;
            });
            applyFilters();
        });
    });

    applyFilters();
}

function applyFilters() {
    const slider = document.getElementById('priceSlider');
    const maxPrice = slider ? parseFloat(slider.value) || 10000 : 10000;

    const selected = Array.from(document.querySelectorAll('input[name="availability"]:checked')).map(cb => cb.value);

    const inStockOnly = selected.includes('in-stock');
    const outStockOnly = selected.includes('out-of-stock');

    allPlantsCards.forEach(card => {
        const price = Math.floor(parseFloat(card.dataset.price) || 0);

        // ✅ FIXED STOCK CHECK
        const inStock = card.dataset.stockStatus === 'in-stock';

        let visible = price <= maxPrice;

        if (inStockOnly) visible = visible && inStock;
        if (outStockOnly) visible = visible && !inStock;

        card.dataset.visible = visible ? 'true' : 'false';
    });

    updatePriceDisplay(maxPrice);
    showPage(1);
}

// ==================== PRICE DISPLAY ====================
function updatePriceDisplay(maxPrice) {
    const el = document.querySelector('.price-range-max');
    if (el) el.textContent = formatPrice(maxPrice);
}

// ==================== CLEAR FILTERS ====================
function initClearFilters() {
    const btn = document.querySelector('.btn-clear-filters');
    if (!btn) return;

    btn.addEventListener('click', function () {
        const slider = document.getElementById('priceSlider');
        if (slider) {
            slider.value = 10000;
            updatePriceSliderColor(slider);
        }

        document.querySelectorAll('input[name="availability"]').forEach(cb => cb.checked = false);

        const sort = document.getElementById('sortSelect');
        if (sort) sort.value = 'default';

        updatePriceDisplay(10000);
        applyFilters();
    });
}

// ==================== SORTING ====================
function initSorting() {
    const select = document.getElementById('sortSelect');
    if (select) select.addEventListener('change', applySort);
}

function applySort() {
    const select = document.getElementById('sortSelect');
    const container = document.getElementById('plantsGrid');
    if (!select || !container) return;

    let cards = allPlantsCards.filter(c => c.dataset.visible === 'true');

    cards.sort((a, b) => {
        const priceA = Math.floor(parseFloat(a.dataset.price) || 0);
        const priceB = Math.floor(parseFloat(b.dataset.price) || 0);
        const nameA = (a.dataset.name || '').toLowerCase();
        const nameB = (b.dataset.name || '').toLowerCase();

        if (select.value === 'price-low') return priceA - priceB;
        if (select.value === 'price-high') return priceB - priceA;
        if (select.value === 'name') return nameA.localeCompare(nameB);
        return 0;
    });

    cards.forEach(c => container.appendChild(c));
    showPage(1);
}

// ==================== PAGINATION ====================
function initPagination() {
    document.querySelectorAll('.page-number, .page-link').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();

            const visibleCount = allPlantsCards.filter(c => c.dataset.visible === 'true').length;
            const totalPages = Math.ceil(visibleCount / itemsPerPage);

            let page = currentPage;

            if (link.classList.contains('page-number')) {
                page = parseInt(link.textContent);
            } else if (link.textContent.includes('Next')) {
                page++;
            } else if (link.textContent.includes('Previous')) {
                page--;
            }

            page = Math.max(1, Math.min(page, totalPages));
            showPage(page);
        });
    });
}

function showPage(page) {
    const visible = allPlantsCards.filter(c => c.dataset.visible === 'true');

    allPlantsCards.forEach(c => c.style.display = 'none');

    visible
        .slice((page - 1) * itemsPerPage, page * itemsPerPage)
        .forEach(c => c.style.display = 'block');

    currentPage = page;
    updatePaginationButtons();
}

function updatePaginationButtons() {
    const visible = allPlantsCards.filter(c => c.dataset.visible === 'true');
    const totalPages = Math.ceil(visible.length / itemsPerPage);

    document.querySelectorAll('.page-number').forEach((el, i) => {
        if (i < totalPages) {
            el.style.display = 'inline-block';
            el.textContent = i + 1;
            el.classList.toggle('active', currentPage === i + 1);
        } else {
            el.style.display = 'none';
        }
    });

    document.querySelector('.page-link:first-child')?.classList.toggle('disabled', currentPage === 1);
    document.querySelector('.page-link:last-child')?.classList.toggle('disabled', currentPage === totalPages);

    document.getElementById('noResults').style.display = visible.length ? 'none' : 'block';
}

// ==================== CART ====================
function initPlantsCart() {
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            const card = this.closest('.fish-card');

            const product = {
                id: parseInt(card.dataset.id),
                name: card.querySelector('h3').textContent.trim(),
                price: Math.floor(parseFloat(card.dataset.price)),
                image: card.querySelector('img').src,
                category: 'plants'
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