<?php
require_once 'includes/config.php';

if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "You don't have permission to access this page";
    redirect('index.php');
}

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$product_id = $_GET['id'];

// Check if product exists
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = "Product not found";
    redirect('index.php');
}

// Delete the product
$stmt = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
if ($stmt->execute([$product_id])) {
    // Delete the associated image if it exists
    if ($product['image_path'] && file_exists($product['image_path'])) {
        unlink($product['image_path']);
    }
    $_SESSION['success'] = "Product deleted successfully";
} else {
    $_SESSION['error'] = "Failed to delete product";
}

redirect('index.php');
?>