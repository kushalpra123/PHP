<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if (!isLoggedIn() || !isAdmin()) {
    $_SESSION['error'] = "You don't have permission to access this page";
    redirect('index.php');
}

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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);

    $errors = [];
    if (empty($name)) $errors[] = "Product name is required";
    if (empty($price) || !is_numeric($price)) $errors[] = "Valid price is required";
    if (empty($stock) || !is_numeric($stock)) $errors[] = "Valid stock quantity is required";

    // Handle file upload
    $image_path = $product['image_path'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'assets/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $file_name;
        $image_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));

        // Check if image file is a actual image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check === false) {
            $errors[] = "File is not an image";
        }

        // Check file size (max 2MB)
        if ($_FILES['image']['size'] > 2000000) {
            $errors[] = "Image is too large (max 2MB)";
        }

        // Allow certain file formats
        if (!in_array($image_type, ['jpg', 'jpeg', 'png', 'gif'])) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed";
        }

        if (empty($errors)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                // Delete old image if it exists
                if ($image_path && file_exists($image_path)) {
                    unlink($image_path);
                }
                $image_path = $target_path;
            } else {
                $errors[] = "There was an error uploading your file";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, stock_quantity = ?, image_path = ? WHERE product_id = ?");
        if ($stmt->execute([$name, $description, $price, $stock, $image_path, $product_id])) {
            $_SESSION['success'] = "Product updated successfully!";
            redirect('product.php?id=' . $product_id);
        } else {
            $errors[] = "Failed to update product";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2>Edit Product</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stock" class="form-label">Stock Quantity</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="<?php echo htmlspecialchars($product['stock_quantity']); ?>" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="image" name="image">
                <?php if ($product['image_path']): ?>
                    <div class="mt-2">
                        <img src="<?php echo $product['image_path']; ?>" alt="Current Image" style="max-height: 100px;">
                        <p class="text-muted">Current image</p>
                    </div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
            <a href="product.php?id=<?php echo $product_id; ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>