// search.js

document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const query = params.get("q")?.toLowerCase() || "";
    document.getElementById("search-keyword").textContent = query;

    // All available products with images, stock status
    const products = [
        { name: "Clownfish - Ocellaris", type: "fish", price: 3700, image: "../Images/Homepage/bgfishify.jpg", stock: "in-stock" },
        { name: "German Blue Ram", type: "fish", price: 3300, image: "../Images/Homepage/betta.jpg", stock: "in-stock" },
        { name: "Dragon Goby", type: "fish", price: 5300, image: "../Images/Homepage/bgfishify.jpg", stock: "out-of-stock" },
        { name: "Noon Tetra", type: "fish", price: 4300, image: "../Images/Homepage/bgfishify.jpg", stock: "in-stock" },
        { name: "Energy Shrimp", type: "fish", price: 800, image: "../Images/Homepage/bgfishify.jpg", stock: "in-stock" },
        { name: "Harlequin Rasbora", type: "fish", price: 300, image: "../Images/Homepage/bgfishify.jpg", stock: "out-of-stock" },
        { name: "Fire Shrimp", type: "fish", price: 3200, image: "../Images/Homepage/bgfishify.jpg", stock: "in-stock" },
        { name: "Royal Gramma", type: "fish", price: 5000, image: "../Images/Homepage/bgfishify.jpg", stock: "in-stock" },

        // Aquariums
        { name: "Aqua Tank Pro 100", type: "aquarium", price: 3000, image: "../Images/Homepage/aquarium1.jpg", stock: "in-stock" },
        { name: "Nano Cube Aquarium Kit", type: "aquarium", price: 3500, image: "../Images/Homepage/aquarium2.jpg", stock: "in-stock" },
        { name: "Marine LED Light", type: "aquarium", price: 1200, image: "../Images/Homepage/aquarium3.jpg", stock: "out-of-stock" },
        { name: "Automatic Fish Feeder", type: "aquarium", price: 900, image: "../Images/Homepage/aquarium4.jpg", stock: "in-stock" },
        { name: "Magnetic Gravel Cleaner", type: "aquarium", price: 800, image: "../Images/Homepage/aquarium5.jpg", stock: "in-stock" },

        // Accessories
        { name: "Fish Net", type: "accessory", price: 200, image: "../Images/Homepage/accessory1.jpg", stock: "in-stock" },
        { name: "Aquarium Heater", type: "accessory", price: 1500, image: "../Images/Homepage/accessory2.jpg", stock: "in-stock" },
        { name: "Water Test Kit", type: "accessory", price: 700, image: "../Images/Homepage/accessory3.jpg", stock: "out-of-stock" },
    ];

    const resultsContainer = document.getElementById("search-results");
    const noResults = document.getElementById("no-results");
    resultsContainer.innerHTML = "";

    const matched = products.filter(p => p.name.toLowerCase().includes(query));

    if (matched.length > 0) {
        noResults.style.display = "none";

        matched.forEach(product => {
            const card = document.createElement("div");
            card.className = "product-card";
            card.innerHTML = `
                <div class="product-image">
                    <img src="${product.image}" alt="${product.name}">
                </div>
                <div class="product-info">
                    <h3 class="product-title">${product.name}</h3>
                    <div class="product-price">Rs <span>${product.price}</span></div>
                    <div class="product-stock ${product.stock}">${product.stock === 'in-stock' ? 'In Stock' : 'Out of Stock'}</div>
                    <button class="btn add-to-cart" ${product.stock === 'out-of-stock' ? 'disabled' : ''}>
                        <i class="fas fa-cart-plus"></i> Add to Cart
                    </button>
                </div>
            `;
            resultsContainer.appendChild(card);
        });

        initCartButtons();
    } else {
        noResults.style.display = "block";
    }

    // Allow search again
    const searchInput = document.getElementById("search-input");
    const searchBtn = document.getElementById("search-btn");

    searchBtn.addEventListener("click", () => {
        const newQuery = searchInput.value.trim();
        if (newQuery) {
            window.location.href = `search.html?q=${encodeURIComponent(newQuery)}`;
        }
    });

    searchInput.addEventListener("keypress", (e) => {
        if (e.key === "Enter") searchBtn.click();
    });

    // Add to cart functionality
    function initCartButtons() {
        const buttons = document.querySelectorAll(".btn.add-to-cart");
        buttons.forEach(btn => {
            btn.addEventListener("click", () => {
                const card = btn.closest(".product-card");
                const name = card.querySelector(".product-title").textContent;
                const price = parseFloat(card.querySelector(".product-price span").textContent);

                let cart = JSON.parse(localStorage.getItem("fishifyCart")) || [];
                const existing = cart.find(item => item.name === name);

                if (existing) existing.quantity += 1;
                else cart.push({ id: generateId(name), name, price, quantity: 1, type: "product" });

                localStorage.setItem("fishifyCart", JSON.stringify(cart));
                updateCartCount();
                showNotification(`${name} added to cart!`);
            });
        });
    }

    function generateId(name) {
        return 'product-' + name.toLowerCase().replace(/[^a-z0-9]/g, '-');
    }

    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem("fishifyCart")) || [];
        const total = cart.reduce((sum, item) => sum + item.quantity, 0);
        document.querySelectorAll(".cart-count").forEach(el => {
            el.textContent = total;
            el.style.display = total > 0 ? "flex" : "none";
        });
    }

    function showNotification(message) {
        const notification = document.createElement("div");
        notification.className = "fish-notification";
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #2ecc71;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
});