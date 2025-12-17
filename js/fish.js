// // ===== FISH PAGE JAVASCRIPT =====
// document.addEventListener('DOMContentLoaded', function() {
//     // Initialize
//     initFilters();
//     setupSorting();
//     setupPagination();
//     updateCartButtons();
    
//     // Load fish from API if needed
//     if (window.location.pathname.includes('fish.html')) {
//         loadFishFromAPI();
//     }
// });

// // Filter functionality
// function initFilters() {
//     const priceSlider = document.querySelector('.price-slider');
//     const priceMax = document.querySelector('.price-range span:last-child');
//     const checkboxes = document.querySelectorAll('.filter-options input[type="checkbox"]');
//     const clearBtn = document.querySelector('.clear-filters');
    
//     // Price slider
//     if (priceSlider && priceMax) {
//         priceSlider.addEventListener('input', function() {
//             const value = this.value;
//             priceMax.textContent = `$${value}+`;
//             filterFish();
//         });
//     }
    
//     // Checkboxes
//     checkboxes.forEach(checkbox => {
//         checkbox.addEventListener('change', function() {
//             updateActiveFilters();
//             filterFish();
//         });
//     });
    
//     // Clear filters
//     if (clearBtn) {
//         clearBtn.addEventListener('click', clearAllFilters);
//     }
    
//     // Remove filter tags
//     document.addEventListener('click', function(e) {
//         if (e.target.classList.contains('fa-times') && e.target.closest('.filter-tag')) {
//             const filterTag = e.target.closest('.filter-tag');
//             const filterType = filterTag.dataset.type;
//             const filterValue = filterTag.dataset.value;
            
//             // Uncheck corresponding checkbox
//             const checkbox = document.querySelector(`input[type="checkbox"][value="${filterValue}"]`);
//             if (checkbox) {
//                 checkbox.checked = false;
//             }
            
//             filterTag.remove();
//             filterFish();
//         }
//     });
// }

// function updateActiveFilters() {
//     const activeFilters = document.querySelector('.active-filters');
//     if (!activeFilters) return;
    
//     // Clear existing tags
//     activeFilters.innerHTML = '';
    
//     // Get all checked checkboxes
//     const checkedBoxes = document.querySelectorAll('.filter-options input[type="checkbox"]:checked');
    
//     checkedBoxes.forEach(checkbox => {
//         const label = checkbox.nextElementSibling.textContent;
//         const type = checkbox.name;
//         const value = checkbox.value;
        
//         const tag = document.createElement('span');
//         tag.className = 'filter-tag';
//         tag.dataset.type = type;
//         tag.dataset.value = value;
//         tag.innerHTML = `
//             ${label}
//             <i class="fas fa-times"></i>
//         `;
//         activeFilters.appendChild(tag);
//     });
// }

// function filterFish() {
//     const fishCards = document.querySelectorAll('.fish-card');
//     const maxPrice = parseInt(document.querySelector('.price-slider')?.value) || 100;
    
//     // Get active filters
//     const activeFilters = {
//         size: getCheckedValues('size'),
//         behavior: getCheckedValues('behavior'),
//         water: getCheckedValues('water-type'),
//         rarity: getCheckedValues('rarity'),
//         availability: getCheckedValues('availability')
//     };
    
//     let visibleCount = 0;
    
//     fishCards.forEach(card => {
//         const price = parseFloat(card.dataset.price);
//         const size = card.dataset.size;
//         const behavior = card.dataset.behavior;
//         const water = card.dataset.water;
        
//         let showCard = true;
        
//         // Price filter
//         if (price > maxPrice) {
//             showCard = false;
//         }
        
//         // Size filter
//         if (activeFilters.size.length > 0 && !activeFilters.size.includes(size)) {
//             showCard = false;
//         }
        
//         // Behavior filter
//         if (activeFilters.behavior.length > 0 && !activeFilters.behavior.includes(behavior)) {
//             showCard = false;
//         }
        
//         // Water type filter
//         if (activeFilters.water.length > 0 && !activeFilters.water.includes(water)) {
//             showCard = false;
//         }
        
//         // Show/hide card with animation
//         if (showCard) {
//             card.style.display = 'block';
//             setTimeout(() => {
//                 card.style.opacity = '1';
//                 card.style.transform = 'translateY(0)';
//             }, 50);
//             visibleCount++;
//         } else {
//             card.style.opacity = '0';
//             card.style.transform = 'translateY(10px)';
//             setTimeout(() => {
//                 card.style.display = 'none';
//             }, 300);
//         }
//     });
    
//     // Update results count
//     updateResultsCount(visibleCount, fishCards.length);
// }

// function getCheckedValues(name) {
//     const checkboxes = document.querySelectorAll(`input[name="${name}"]:checked`);
//     return Array.from(checkboxes).map(cb => cb.value);
// }

// function clearAllFilters() {
//     // Uncheck all checkboxes
//     document.querySelectorAll('.filter-options input[type="checkbox"]').forEach(cb => {
//         cb.checked = false;
//     });
    
//     // Reset price slider
//     const priceSlider = document.querySelector('.price-slider');
//     if (priceSlider) {
//         priceSlider.value = 100;
//         document.querySelector('.price-range span:last-child').textContent = '$100+';
//     }
    
//     // Clear filter tags
//     const activeFilters = document.querySelector('.active-filters');
//     if (activeFilters) {
//         activeFilters.innerHTML = '';
//     }
    
//     // Show all fish
//     filterFish();
// }

// function updateResultsCount(visible, total) {
//     const resultsElement = document.querySelector('.results-count');
//     if (!resultsElement) {
//         // Create results count element if it doesn't exist
//         const header = document.querySelector('.products-header');
//         if (header) {
//             const countElement = document.createElement('div');
//             countElement.className = 'results-count';
//             countElement.style.cssText = `
//                 color: var(--gray);
//                 font-size: 0.9rem;
//                 margin-top: 0.5rem;
//             `;
//             header.appendChild(countElement);
//         }
//     }
    
//     const element = document.querySelector('.results-count');
//     if (element) {
//         element.textContent = `Showing ${visible} of ${total} fish`;
//     }
// }

// // Sorting functionality
// function setupSorting() {
//     const sortSelect = document.querySelector('.sort-by select');
//     if (!sortSelect) return;
    
//     sortSelect.addEventListener('change', function() {
//         sortFish(this.value);
//     });
// }

// function sortFish(criteria) {
//     const fishGrid = document.querySelector('.fish-grid');
//     const fishCards = Array.from(document.querySelectorAll('.fish-card'));
    
//     fishCards.sort((a, b) => {
//         const priceA = parseFloat(a.dataset.price);
//         const priceB = parseFloat(b.dataset.price);
//         const nameA = a.querySelector('h3').textContent.toLowerCase();
//         const nameB = b.querySelector('h3').textContent.toLowerCase();
        
//         switch(criteria) {
//             case 'Price: Low to High':
//                 return priceA - priceB;
//             case 'Price: High to Low':
//                 return priceB - priceA;
//             case 'Name: A-Z':
//                 return nameA.localeCompare(nameB);
//             case 'Name: Z-A':
//                 return nameB.localeCompare(nameA);
//             case 'Popularity':
//                 // In a real app, this would use actual popularity data
//                 return Math.random() - 0.5;
//             default:
//                 return 0;
//         }
//     });
    
//     // Reorder in DOM with animation
//     fishCards.forEach((card, index) => {
//         setTimeout(() => {
//             fishGrid.appendChild(card);
//             card.style.animation = 'fadeIn 0.3s ease';
//         }, index * 50);
//     });
    
//     // Add fadeIn animation
//     if (!document.querySelector('#fadeIn-animation')) {
//         const style = document.createElement('style');
//         style.id = 'fadeIn-animation';
//         style.textContent = `
//             @keyframes fadeIn {
//                 from {
//                     opacity: 0;
//                     transform: translateY(20px);
//                 }
//                 to {
//                     opacity: 1;
//                     transform: translateY(0);
//                 }
//             }
//         `;
//         document.head.appendChild(style);
//     }
// }

// // Pagination
// function setupPagination() {
//     const pageLinks = document.querySelectorAll('.page-number, .page-link');
//     pageLinks.forEach(link => {
//         link.addEventListener('click', function(e) {
//             e.preventDefault();
            
//             if (this.classList.contains('disabled')) return;
            
//             // Update active page
//             document.querySelectorAll('.page-number').forEach(page => {
//                 page.classList.remove('active');
//             });
            
//             if (this.classList.contains('page-number')) {
//                 this.classList.add('active');
//             }
            
//             // Simulate page loading
//             simulatePageLoad();
//         });
//     });
// }

// function simulatePageLoad() {
//     const fishGrid = document.querySelector('.fish-grid');
//     if (!fishGrid) return;
    
//     // Add loading animation
//     fishGrid.style.opacity = '0.5';
//     fishGrid.style.transition = 'opacity 0.3s ease';
    
//     setTimeout(() => {
//         fishGrid.style.opacity = '1';
        
//         // Scroll to top
//         window.scrollTo({
//             top: 0,
//             behavior: 'smooth'
//         });
//     }, 500);
// }

// // Load fish from API
// async function loadFishFromAPI() {
//     try {
//         const response = await fetch('/api/products.php?category=fish');
//         const fish = await response.json();
        
//         if (fish.length > 0) {
//             populateFishGrid(fish);
//         }
//     } catch (error) {
//         console.error('Error loading fish:', error);
//     }
// }

// function populateFishGrid(fish) {
//     const fishGrid = document.querySelector('.fish-grid');
//     if (!fishGrid) return;
    
//     // Clear existing content
//     fishGrid.innerHTML = '';
    
//     // Add fish cards
//     fish.forEach(fish => {
//         const card = createFishCard(fish);
//         fishGrid.appendChild(card);
//     });
    
//     // Update cart buttons
//     updateCartButtons();
// }

// function createFishCard(fish) {
//     const card = document.createElement('div');
//     card.className = 'fish-card';
//     card.dataset.price = fish.price;
//     card.dataset.size = fish.size || 'medium';
//     card.dataset.behavior = fish.behavior || 'peaceful';
//     card.dataset.water = fish.water_type || 'freshwater';
    
//     card.innerHTML = `
//         <div class="fish-image" style="background: linear-gradient(135deg, ${getRandomColor()})">
//             <div class="fish-effect"></div>
//             <span>${fish.name}</span>
//         </div>
//         <div class="fish-info">
//             <h3>${fish.name}</h3>
//             <div class="fish-price">${Fishify.formatPrice(fish.price)}</div>
//             <div class="fish-meta">
//                 ${fish.size ? `<span class="meta-tag">${fish.size}</span>` : ''}
//                 ${fish.behavior ? `<span class="meta-tag">${fish.behavior}</span>` : ''}
//                 ${fish.water_type ? `<span class="meta-tag">${fish.water_type}</span>` : ''}
//             </div>
//             <div class="seller-info">
//                 <span class="seller-badge">${fish.seller || 'Fishify'}</span>
//                 <button class="btn btn-primary add-to-cart">
//                     <i class="fas fa-cart-plus"></i> Add to Cart
//                 </button>
//             </div>
//         </div>
//     `;
    
//     return card;
// }

// function getRandomColor() {
//     const colors = [
//         '#667eea, #764ba2',
//         '#f093fb, #f5576c',
//         '#4facfe, #00f2fe',
//         '#43e97b, #38f9d7',
//         '#fa709a, #fee140',
//         '#30cfd0, #330867',
//         '#5ee7df, #b490ca',
//         '#d299c2, #fef9d7'
//     ];
//     return colors[Math.floor(Math.random() * colors.length)];
// }

// function updateCartButtons() {
//     document.querySelectorAll('.fish-card .add-to-cart').forEach(button => {
//         button.addEventListener('click', function(e) {
//             e.preventDefault();
//             e.stopPropagation();
            
//             const card = this.closest('.fish-card');
//             const name = card.querySelector('h3').textContent;
//             const price = parseFloat(card.querySelector('.fish-price').textContent.replace('$', '').replace(',', ''));
            
//             Fishify.addToCart({
//                 id: Date.now().toString(),
//                 name: name,
//                 price: price,
//                 image: '',
//                 category: 'Fish'
//             });
            
//             // Add button feedback
//             this.innerHTML = '<i class="fas fa-check"></i> Added!';
//             this.style.background = '#27AE60';
            
//             setTimeout(() => {
//                 this.innerHTML = '<i class="fas fa-cart-plus"></i> Add to Cart';
//                 this.style.background = '';
//             }, 2000);
//         });
//     });
// }