<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fishify </title>

    <style>
        /* ===== Admin Header ===== */
        * {
            margin: 0%;
            padding: 0%;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--dark);
            background: white;
            line-height: 1.6;
        }

        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: linear-gradient(135deg, #0066CC, #00A8E8);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Container */
        .sticky-header .container {
            padding: 0 15px;
        }

        /* Header top layout */
        .header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 70px;
        }

        /* Logo */
        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }

        .logo span {
            letter-spacing: 1px;
        }

        /* Nav */
        nav {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        /* Main nav */
        .main-nav {
            list-style: none;
            display: flex;
            gap: 20px;
            margin: 0;
            padding: 0;
        }

        .main-nav li a {
            color: #eaf0ff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: 6px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        /* Hover */
        .main-nav li a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        /* Active link */
        .main-nav li a.active {
            background: #ffffff;
            color: #0066CC;
            font-weight: 600;
        }

        /* Header actions */
        .header-actions {
            display: flex;
            align-items: center;
        }

        /* Logout button */
        .logout-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #da3e4eff;
            color: #fff;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        /* Logout hover */
        .logout-btn:hover {
            background: #bb2d3b;
            transform: translateY(-1px);
        }

        /* ===== Modal Styles ===== */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 2000;
            animation: fadeIn 0.3s ease;
        }

        .modal-overlay.active {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideDown 0.3s ease;
        }

        .modal-header {
            background: linear-gradient(135deg, #0066CC, #00A8E8);
            color: white;
            padding: 20px 30px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .close-modal {
            background: transparent;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            padding: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background 0.3s ease;
        }

        .close-modal:hover {
            background: rgba(255, 255, 255, 0.2);
        }

        .modal-body {
            padding: 30px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-top {
                flex-direction: column;
                height: auto;
                padding: 10px 0;
            }

            nav {
                flex-direction: column;
                gap: 15px;
            }

            .main-nav {
                flex-direction: column;
                align-items: center;
            }

            .modal-content {
                width: 95%;
                max-height: 95vh;
            }

            .modal-header {
                padding: 15px 20px;
            }

            .modal-body {
                padding: 20px;
            }
        }
    </style>
</head>


<body>

    <header class="sticky-header">
        <div class="container">
            <div class="header-top">
                <a href="" class="logo">
                    <span>Admin Fishify</span>
                </a>

                <nav>
                    <ul class="main-nav">
                        <li><a href="admindashboard.php"
                                class="<?= ($currentPage == 'admindashboard.php') ? 'active' : '' ?>">Dashboard</a></li>
                        <li><a href="products.php"
                                class="<?= ($currentPage == 'products.php') ? 'active' : '' ?>">Products</a></li>
                        <li><a href="orders.php"
                                class="<?= ($currentPage == 'orders.php' || $currentPage == 'order-detail.php') ? 'active' : '' ?>">Orders</a>
                        </li>
                        <li><a href="contacts.php"
                                class="<?= ($currentPage == 'contacts.php' || $currentPage == 'contact-view.php') ? 'active' : '' ?>">Contacts</a>
                        </li>
                        <li><a onclick="openAddProductModal()"
                                class="<?= ($currentPage == 'add_product.php') ? 'active' : '' ?>">Add Products</a></li>

                    </ul>
                </nav>

                <div class="header-actions">
                    <a href="../pages/logout.php" class="logout-btn">
                        <span>Logout</span>
                    </a>
                </div>
    </header>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Product</h2>
                <button class="close-modal" onclick="closeAddProductModal()">&times;</button>
            </div>
            <div class="modal-body">
                <iframe id="addProductFrame" src="" style="width: 100%; height: 500px; border: none;"
                    onload="handleIframeLoad()">
                </iframe>
            </div>
        </div>
    </div>

    <script>
        function openAddProductModal() {
            const modal = document.getElementById('addProductModal');
            const iframe = document.getElementById('addProductFrame');

            // Load the add_product.php page in the iframe
            iframe.src = 'add_product.php';

            // Show the modal
            modal.classList.add('active');

            // Prevent body scroll when modal is open
            document.body.style.overflow = 'hidden';
        }

        function closeAddProductModal() {
            const modal = document.getElementById('addProductModal');
            const iframe = document.getElementById('addProductFrame');

            // Hide the modal
            modal.classList.remove('active');

            // Clear the iframe
            iframe.src = '';

            // Restore body scroll
            document.body.style.overflow = 'auto';
        }

        function handleIframeLoad() {
            // Check if the iframe has redirected to products.php (successful submission)
            const iframe = document.getElementById('addProductFrame');
            try {
                if (iframe.contentWindow.location.href.includes('products.php')) {
                    closeAddProductModal();
                    // Reload the current page if we're on products.php to show the new product
                    if (window.location.href.includes('products.php')) {
                        window.location.reload();
                    }
                }
            } catch (e) {
                // Cross-origin error, ignore
            }
        }

        // Close modal when clicking outside of it
        document.addEventListener('click', function (event) {
            const modal = document.getElementById('addProductModal');
            if (event.target === modal) {
                closeAddProductModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeAddProductModal();
            }
        });
    </script>

</body>

</html>