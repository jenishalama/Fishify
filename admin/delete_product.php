<?php
include 'admin_session.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // First, get the image name to delete the file
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image = $row['image'];

        // Delete the product from database
        $deleteStmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            // Delete the image file if it exists
            if ($image != '' && file_exists("../uploads/$image")) {
                unlink("../uploads/$image");
            }
            header("Location: products.php?deleted=success");
        } else {
            header("Location: products.php?deleted=error");
        }

        $deleteStmt->close();
    } else {
        header("Location: products.php?deleted=notfound");
    }

    $stmt->close();
} else {
    header("Location: products.php");
}

$conn->close();
?>