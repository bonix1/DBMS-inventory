<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['role_id'] != 1) {
    echo "<h3 style='text-align:center;margin-top:20px;'>Access Denied: Admins Only</h3>";
    exit();
}

include "conn.php";

/* tables to include in the database 
CREATE TABLE stock (
    stock_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    supplier_id INT,
    quantity_added INT,
    date_added DATE,
    FOREIGN KEY (product_id) REFERENCES products(product_id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
);

CREATE TABLE suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    contact_info VARCHAR(255)
);
*/

// Insert stock
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'] ?? null;
    $supplier_id = $_POST['supplier_id'] ?? null;
    $quantity = $_POST['quantity'] ?? 0;
    $date = $_POST['date_added'] ?? date('Y-m-d');

    if ($product_id && $supplier_id && $quantity > 0) {
        $stmt = $conn->prepare("INSERT INTO stock (product_id, supplier_id, quantity_added, date_added) VALUES (?, ?, ?, ?)");
        $stmt->execute([$product_id, $supplier_id, $quantity, $date]);
        header("Location: stock.php");
        exit();
    }
}

// Fetch products
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Fetch suppliers
$suppliers = $conn->query("SELECT * FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Fetch stock records
$stockData = $conn->query("
    SELECT s.stock_id, p.name AS product_name, sp.name AS supplier_name, s.quantity_added, s.date_added
    FROM stock s
    JOIN products p ON s.product_id = p.product_id
    JOIN suppliers sp ON s.supplier_id = sp.supplier_id
    ORDER BY s.stock_id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Stock Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f1f1f1; padding-top: 40px; }
        .container { max-width: 900px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Stock Management</h2>

    <!-- Add Stock Form -->
    <form method="post" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Product</label>
                <select name="product_id" class="form-control" required>
                    <option value="">-- Select Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>Supplier</label>
                <select name="supplier_id" class="form-control" required>
                    <option value="">-- Select Supplier --</option>
                    <?php foreach ($suppliers as $supplier): ?>
                        <option value="<?= $supplier['supplier_id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-2">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" required min="1">
            </div>
            <div class="form-group col-md-2">
                <label>Date</label>
                <input type="date" name="date_added" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Stock</button>
    </form>

    <!-- Stock Table -->
    <h5>Recent Stock Entries</h5>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
        <tr>
            <th>Product</th>
            <th>Supplier</th>
            <th>Quantity</th>
            <th>Date</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($stockData): ?>
            <?php foreach ($stockData as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                    <td><?= $row['quantity_added'] ?></td>
                    <td><?= $row['date_added'] ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="4" class="text-center">No stock entries found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="products.php" class="btn btn-secondary">← Back to Products</a>
    </div>
</div>

</body>
</html>
