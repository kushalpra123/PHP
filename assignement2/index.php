<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll();
?>

<h2>Product Inventory</h2>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <p><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
    </div>
<?php endif; ?>

<div class="row">
    <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <?php if ($product['image_path']): ?>
                    <img src="<?php echo $product['image_path']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                        No Image
                    </div>
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars(substr($product['description'], 0, 100)); ?>...</p>
                    <p class="card-text"><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
                    <p class="card-text"><strong>Stock:</strong> <?php echo $product['stock_quantity']; ?></p>
                    <a href="product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require_once 'includes/footer.php'; ?>