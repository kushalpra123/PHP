<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if (!isset($_GET['id'])) {
    redirect('index.php');
}

$product_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    $_SESSION['error'] = "Product not found";
    redirect('index.php');
}
?>

<h2><?php echo htmlspecialchars($product['name']); ?></h2>

<div class="row">
    <div class="col-md-6">
        <?php if ($product['image_path']): ?>
            <img src="<?php echo $product['image_path']; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <?php else: ?>
            <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 300px;">
                No Image Available
            </div>
        <?php endif; ?>
    </div>
    <div class="col-md-6">
        <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
        <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
        <p><strong>Stock Quantity:</strong> <?php echo $product['stock_quantity']; ?></p>
        
        <?php if (isLoggedIn() && isAdmin()): ?>
            <div class="mt-4">
                <a href="edit-product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-warning">Edit</a>
                <a href="delete-product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>