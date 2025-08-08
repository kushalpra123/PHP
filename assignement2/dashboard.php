<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "You don't have permission to access this page";
    redirect('index.php');
}

// Get all products
$stmt = $pdo->query("SELECT p.*, u.username FROM products p JOIN users u ON p.created_by = u.user_id ORDER BY p.created_at DESC");
$products = $stmt->fetchAll();

// Get all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<h2>Admin Dashboard</h2>

<div class="row mt-4">
    <div class="col-md-6">
        <h4>Recent Products</h4>
        <div class="list-group">
            <?php foreach ($products as $product): ?>
                <a href="product.php?id=<?php echo $product['product_id']; ?>" class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <small>$<?php echo number_format($product['price'], 2); ?></small>
                    </div>
                    <p class="mb-1"><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</p>
                    <small>Added by <?php echo htmlspecialchars($product['username']); ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="col-md-6">
        <h4>Recent Users</h4>
        <div class="list-group">
            <?php foreach ($users as $user): ?>
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1"><?php echo htmlspecialchars($user['username']); ?></h5>
                        <small class="text-<?php echo $user['role'] === 'admin' ? 'danger' : 'primary'; ?>">
                            <?php echo ucfirst($user['role']); ?>
                        </small>
                    </div>
                    <p class="mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                    <small>Joined on <?php echo date('M j, Y', strtotime($user['created_at'])); ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>