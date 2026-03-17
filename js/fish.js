document.addEventListener('DOMContentLoaded', function() {

    // ==================== GLOBAL VARIABLES ====================
    const itemsPerPage = 8;
    let currentPage = 1;
    let allFishCards = Array.from(document.querySelectorAll('.fish-card'));

    // ==================== HELPER FUNCTION ====================
    function formatPrice(price) {
        return `Rs. ${Math.floor(price).toLocaleString()}`;
    }

    // ==================== FILTERING ====================
    function initFiltering() {
        const priceSlider = document.querySelector('.price-slider');
        const availabilityCheckboxes = document.querySelectorAll('input[name="availability"]');

        if (priceSlider) {
            priceSlider.addEventListener('input', function() {
                updatePriceSliderColor(this);
                applyFilters();
            });
            updatePriceSliderColor(priceSlider);
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
        let showInStockOnly = false;
        let showOutOfStockOnly = false;

        availabilityCheckboxes.forEach(cb => {
            if (cb.checked) {
                if (cb.value === 'in-stock') showInStockOnly = true;
                if (cb.value === 'out-of-stock') showOutOfStockOnly = true;
            }
        });

        allFishCards.forEach(card => {
            const price = Math.floor(parseFloat(card.dataset.price) || 0);
            const inStock = (card.dataset.stockStatus || '') === 'in-stock';

            let visible = price <= maxPrice;

            if (showInStockOnly) visible = visible && inStock;
            if (showOutOfStockOnly) visible = visible && !inStock;

            card.dataset.visible = visible ? 'true' : 'false';
            card.style.display = '';
        });

        updatePriceDisplay(maxPrice);
        showPage(1);
    }

    // ==================== PRICE DISPLAY ====================
    function updatePriceDisplay(maxPrice) {
        const span = document.querySelector('.price-range-max, .price-range span:last-child');
        if (span) span.textContent = formatPrice(maxPrice);
    }

    // ==================== CLEAR FILTER ====================
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

    // ==================== SORTING ====================
    function initSorting() {
        const sortSelect = document.querySelector('.sort-by select');
        if (sortSelect) sortSelect.addEventListener('change', applySort);
    }

    function applySort() {
        const sortSelect = document.querySelector('.sort-by select');
        const container = document.querySelector('.fish-grid');
        if (!sortSelect || !container) return;

        let visibleCards = allFishCards.filter(c => c.dataset.visible === 'true');

        visibleCards.sort((a, b) => {
            const priceA = parseFloat(a.dataset.price) || 0;
            const priceB = parseFloat(b.dataset.price) || 0;

            if (sortSelect.value === 'price-low') return priceA - priceB;
            if (sortSelect.value === 'price-high') return priceB - priceA;
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

                let pageNum = currentPage;

                if (link.classList.contains('page-number')) {
                    pageNum = parseInt(link.textContent);
                } else if (link.textContent.includes('Next')) {
                    pageNum++;
                } else if (link.textContent.includes('Previous')) {
                    pageNum--;
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
        const visibleCards = allFishCards.filter(c => c.dataset.visible === 'true');
        const totalPages = Math.ceil(visibleCards.length / itemsPerPage);

        document.querySelectorAll('.page-number').forEach((el, i) => {
            el.style.display = i < totalPages ? 'inline-block' : 'none';
            el.textContent = i + 1;
            el.classList.toggle('active', currentPage === i + 1);
        });

        const noResults = document.getElementById('noResults');
        if (noResults) noResults.style.display = visibleCards.length === 0 ? 'block' : 'none';
    }

    // ==================== CART ====================
    function initFishCart() {
        document.querySelectorAll('.fish-card .add-to-cart').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const card = this.closest('.fish-card');

                const product = {
                    id: card.dataset.id || 'fish-' + Date.now(),
                    name: card.querySelector('h3').textContent.trim(),
                    price: Math.floor(parseFloat(card.dataset.price) || 0),
                    image: card.querySelector('img')?.src || '',
                    category: 'fish',
                    stock: parseInt(card.dataset.stock) || 999
                };

                addToCart(product);
            });
        });

        if (typeof updateCartCount === 'function') updateCartCount();
    }

    // ==================== INIT ====================
    initFiltering();
    initSorting();
    initClearFilters();
    initFishCart();
    initPagination();

});