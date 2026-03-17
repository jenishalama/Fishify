<?php
include 'admin_session.php';

// Count total products
$sql = "SELECT COUNT(*) as total FROM products";
$res = $conn->query($sql);
$totalProducts = $res->fetch_assoc()['total'];

// Count products by category
$categories = ['fish', 'aquarium', 'accessories', 'plants'];
$categoryCount = [];

foreach($categories as $cat){
    $sql = "SELECT COUNT(*) as cnt FROM products WHERE category='$cat'";
    $res = $conn->query($sql);
    $categoryCount[$cat] = $res->fetch_assoc()['cnt'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Fishify</title>

    <style>
        /* Import Inter Font */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f4f7fo;
            color: #1e293b;
        }

        .page-content {
            padding: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .heading1 {
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .card {
            background: #ffffff;
            padding: 24px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; height: 4px;
            background: linear-gradient(135deg, #0066CC, #00A8E8);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .card h2 {
            font-size: 1.1rem;
            margin-bottom: 8px;
            color: #475569;
            font-weight: 600;
        }

        .card p {
            font-size: 2.5rem;
            font-weight: 700;
            color: #0066CC;
            margin: 10px 0 20px 0;
        }

        a.button {
            display: inline-block;
            padding: 10px 20px;
            background: rgba(0, 102, 204, 0.1);
            color: #0066CC;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        a.button:hover {
            background: #0066CC;
            color: #ffffff;
        }

        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            font-size: 1.5rem;
            color: #1e293b;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 10px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        th, td {
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
            text-align: left;
        }

        th {
            background: #e6f0fa;
            color: #0052a3;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr {
            transition: background 0.2s ease;
        }
        
        tr:hover td {
            background: #f8fafc;
        }

        td a {
            color: #0066CC;
            text-decoration: none;
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 4px;
            transition: background 0.2s, color 0.2s;
        }

        td a:hover {
            background: rgba(0, 102, 204, 0.1);
            text-decoration: none;
        }

    </style>
</head>

<body>

<?php include 'adminnavbar.php'; ?>

<div class="page-content">

    <h1 class="heading1">Admin Dashboard</h1>

    <div class="dashboard">
        <div class="card">
            <h2>Total Products</h2>
            <p><?php echo $totalProducts; ?></p>
            <a class="button" href="products.php">Manage Products</a>
        </div>

        <?php foreach($categories as $cat): ?>
        <div class="card">
            <h2><?php echo ucfirst($cat); ?></h2>
            <p><?php echo $categoryCount[$cat]; ?></p>
            <a class="button" href="products.php?category=<?php echo $cat; ?>">
                View <?php echo ucfirst($cat); ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <h2 class="section-title">Latest Products</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>

        <?php
        $sql = "SELECT * FROM products ORDER BY created_at DESC LIMIT 5";
        $res = $conn->query($sql);
        while($row = $res->fetch_assoc()):
        ?>
        <tr>
            <td><?= $row['id']; ?></td>
            <td><?= $row['name']; ?></td>
            <td><?= ucfirst($row['category']); ?></td>
            <td>Rs. <?= $row['price']; ?></td>
            <td><?= $row['stock']; ?></td>
            <td>
                <a href="#" onclick="openEditModal(<?= $row['id']; ?>); return false;">Edit</a> |
                <a href="delete-product.php?id=<?= $row['id']; ?>"
                   onclick="return confirm('Are you sure?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

<!-- Edit Product Modal -->
<div id="editProductModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Product</h2>
            <button class="close-modal" onclick="closeEditModal()">&times;</button>
        </div>
        <div class="modal-body">
            <iframe 
                id="editProductFrame" 
                src="" 
                style="width: 100%; height: 550px; border: none;"
                onload="handleEditIframeLoad()">
            </iframe>
        </div>
    </div>
</div>

<style>
/* Modal Styles */
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
    max-width: 650px;
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
    from { opacity: 0; }
    to { opacity: 1; }
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
</style>

<script>
function openEditModal(productId) {
    const modal = document.getElementById('editProductModal');
    const iframe = document.getElementById('editProductFrame');
    
    // Load the edit_product.php page with product ID
    iframe.src = 'edit_product.php?id=' + productId;
    
    // Show the modal
    modal.classList.add('active');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeEditModal() {
    const modal = document.getElementById('editProductModal');
    const iframe = document.getElementById('editProductFrame');
    
    // Hide the modal
    modal.classList.remove('active');
    
    // Clear the iframe
    iframe.src = '';
    
    // Restore body scroll
    document.body.style.overflow = 'auto';
}

function handleEditIframeLoad() {
    const iframe = document.getElementById('editProductFrame');
    try {
        // Check if redirected to adnimdashboard.php or products.php (successful update)
        if (iframe.contentWindow.location.href.includes('products.php') || iframe.contentWindow.location.href.includes('admindashboard.php')) {
            closeEditModal();
            // Reload the page to show updated data
            window.location.reload();
        }
    } catch (e) {
        // Cross-origin error, ignore
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('editProductModal');
    if (event.target === modal) {
        closeEditModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEditModal();
    }
});
</script>

</body>
</html>