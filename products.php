<?php
include "conn.php";



/*
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(100),
    price DECIMAL(10,2)
);
*/

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $category = $_POST['category'] ?? '';
    $price = $_POST['price'] ?? 0;

    if (!empty($_POST['product_id'])) {
        // Update product
        $product_id = $_POST['product_id'];
        $sql = "UPDATE products SET name = ?, category = ?, price = ? WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $category, $price, $product_id]);
    } else {
        // Insert product
        $sql = "INSERT INTO products (name, category, price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $category, $price]);
    }

    header("Location: products.php");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$delete_id]);
    header("Location: products.php");
    exit();
}

// Fetch all products
$stmt = $conn->query("SELECT * FROM products ORDER BY product_id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If editing, fetch specific product
$editProduct = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$edit_id]);
    $editProduct = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 40px; }
        .container { max-width: 800px; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
        .form-section { margin-bottom: 40px; }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Product Management</h2>

    <!-- Product Form -->
    <div class="form-section">
        <h5><?= $editProduct ? "Edit Product" : "Add New Product" ?></h5>
        <form method="post">
            <input type="hidden" name="product_id" value="<?= $editProduct['product_id'] ?? '' ?>">
            <div class="form-group">
                <input type="text" name="name" required class="form-control" placeholder="Product Name" value="<?= $editProduct['name'] ?? '' ?>">
            </div>
            <div class="form-group">
                <input type="text" name="category" class="form-control" placeholder="Category" value="<?= $editProduct['category'] ?? '' ?>">
            </div>
            <div class="form-group">
                <input type="number" name="price" step="0.01" required class="form-control" placeholder="Price" value="<?= $editProduct['price'] ?? '' ?>">
            </div>
            <button type="submit" class="btn btn-success"><?= $editProduct ? "Update" : "Add" ?> Product</button>
            <?php if ($editProduct): ?>
                <a href="products.php" class="btn btn-secondary ml-2">Cancel</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Product Table -->
    <h5>Product List</h5>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Price (₱)</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($products): ?>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['category']) ?></td>
                    <td><?= number_format($p['price'], 2) ?></td>
                    <td>
                        <a href="?edit=<?= $p['product_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete=<?= $p['product_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No products found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="insert_user.php" class="btn btn-primary">← Back to Main</a>
    </div>
</div>
</body>
</html>
