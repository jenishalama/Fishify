<?php
include 'admin_session.php'; // session + db connection

$category = isset($_GET['category']) ? $_GET['category'] : '';

if ($category != '') {
  // Filter by category
  $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY created_at DESC");
  $stmt->bind_param("s", $category);
} else {
  // Show all products
  $stmt = $conn->prepare("SELECT * FROM products ORDER BY created_at DESC");
}

// Execute query
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
  <title>Admin - Products</title>
  <style>
    /* Import Inter Font */
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body {
      font-family: 'Inter', 'Segoe UI', sans-serif;
      background: #f4f7fo;
      margin: 0;
      color: #1e293b;
    }

    .page-content {
      padding: 40px;
      max-width: 1200px;
      margin: 0 auto;
    }

    h1 {
      margin-bottom: 30px;
      font-size: 2rem;
      font-weight: 700;
      color: #1e293b;
    }

    .buttons {
      margin-bottom: 24px;
    }

    .buttons a {
      display: inline-block;
      padding: 10px 20px;
      margin-right: 12px;
      background: rgba(0, 102, 204, 0.1);
      color: #0066CC;
      text-decoration: none;
      border-radius: 8px;
      font-weight: 600;
      text-align: center;
      transition: all 0.2s ease;
    }

    .buttons a.back {
      background: rgba(100, 116, 139, 0.1);
      color: #475569;
    }

    .buttons a:hover {
      background: #0066CC;
      color: #ffffff;
    }

    .buttons a.back:hover {
      background: #475569;
      color: #ffffff;
    }

    table {
      border-collapse: separate;
      border-spacing: 0;
      width: 100%;
      margin-top: 10px;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    table th,
    table td {
      padding: 16px 20px;
      border-bottom: 1px solid #f1f5f9;
      text-align: left;
    }

    table th {
      background: #e6f0fa;
      color: #0052a3;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
    }

    table tr {
      transition: background 0.2s ease;
    }

    table tr:hover td {
      background: #f8fafc;
    }

    table tr:last-child td {
      border-bottom: none;
    }

    table img {
      border-radius: 8px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    table a {
      color: #0066CC;
      text-decoration: none;
      font-weight: 500;
      padding: 4px 8px;
      border-radius: 4px;
      transition: background 0.2s, color 0.2s;
    }

    table a:hover {
      background: rgba(0, 102, 204, 0.1);
      text-decoration: none;
    }
  </style>
</head>

<body>
  <?php include 'adminnavbar.php'; ?>

  <div class="page-content">

    <h1><?php echo $category ? ucfirst($category) . " Products" : "All Products"; ?></h1>

    <div class="buttons">
      <a href="#" onclick="openAddProductModal(); return false;">Add New Product</a>
      <a href="admindashboard.php" class="back">Back</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id']; ?></td>
              <td><?= htmlspecialchars($row['name']); ?></td>
              <td><?= ucfirst(htmlspecialchars($row['category'])); ?></td>
              <td>Rs. <?= number_format($row['price'], 2); ?></td>
              <td><?= $row['stock']; ?></td>
              <td>
                <?php if ($row['image'] != ''): ?>
                  <img src="../uploads/<?= $row['image']; ?>" width="50" alt="<?= htmlspecialchars($row['name']); ?>">
                <?php else: ?>
                  No Image
                <?php endif; ?>
              </td>
              <td>
                <a href="#" onclick="openEditModal(<?= $row['id']; ?>); return false;">Edit</a> |
                <a href="delete_product.php?id=<?= $row['id']; ?>"
                  onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>


  <?php $stmt->close(); ?>

  <!-- Edit Product Modal -->
  <div id="editProductModal" class="modal-overlay">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Edit Product</h2>
        <button class="close-modal" onclick="closeEditModal()">&times;</button>
      </div>
      <div class="modal-body">
        <iframe id="editProductFrame" src="" style="width: 100%; height: 550px; border: none;"
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

    /* Success/Error Messages */
    .alert {
      padding: 15px 20px;
      margin: 20px 0;
      border-radius: 8px;
      font-weight: 500;
    }

    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-error {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
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
        // Check if redirected to products.php (successful update)
        if (iframe.contentWindow.location.href.includes('products.php')) {
          closeEditModal();
          // Reload the page to show updated data
          window.location.reload();
        }
      } catch (e) {
        // Cross-origin error, ignore
      }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function (event) {
      const modal = document.getElementById('editProductModal');
      if (event.target === modal) {
        closeEditModal();
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closeEditModal();
      }
    });

    // Show success/error messages
    window.addEventListener('DOMContentLoaded', function () {
      const urlParams = new URLSearchParams(window.location.search);

      if (urlParams.get('updated') === 'success') {
        showMessage('Product updated successfully!', 'success');
      } else if (urlParams.get('deleted') === 'success') {
        showMessage('Product deleted successfully!', 'success');
      } else if (urlParams.get('deleted') === 'error') {
        showMessage('Error deleting product.', 'error');
      }
    });

    function showMessage(message, type) {
      const alertDiv = document.createElement('div');
      alertDiv.className = 'alert alert-' + type;
      alertDiv.textContent = message;

      const h1 = document.querySelector('h1');
      h1.parentNode.insertBefore(alertDiv, h1.nextSibling);

      // Auto-remove after 5 seconds
      setTimeout(() => {
        alertDiv.style.transition = 'opacity 0.5s';
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 500);
      }, 5000);
    }
  </script>

</body>

</html>