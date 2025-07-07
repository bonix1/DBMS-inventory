<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include "conn.php";

// Fetch all products
$products = $conn->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);

// Insert Sale (with stock validation)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity_sold = (int)$_POST['quantity_sold'];
    $sale_date = $_POST['sale_date'] ?? date('Y-m-d');

    // Get product price
    $stmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get available stock
    $stockStmt = $conn->prepare("SELECT SUM(quantity_added) as total FROM stock WHERE product_id = ?");
    $stockStmt->execute([$product_id]);
    $stock = $stockStmt->fetch(PDO::FETCH_ASSOC);

    if ($product && $stock && $quantity_sold > 0 && $stock['total'] >= $quantity_sold) {
        $total = $product['price'] * $quantity_sold;

        // Record sale
        $insertSale = $conn->prepare("INSERT INTO sales (product_id, quantity_sold, sale_date, total_amount) VALUES (?, ?, ?, ?)");
        $insertSale->execute([$product_id, $quantity_sold, $sale_date, $total]);

        // Deduct stock
        $updateStock = $conn->prepare("UPDATE stock SET quantity_added = quantity_added - ? WHERE product_id = ?");
        $updateStock->execute([$quantity_sold, $product_id]);

        header("Location: sales.php");
        exit();
    } else {
        echo "<script>alert('Insufficient stock or invalid input.');</script>";
    }
}

// Get sales records
$sales = $conn->query("
    SELECT s.sale_id, p.name AS product_name, s.quantity_sold, s.total_amount, s.sale_date
    FROM sales s
    JOIN products p ON s.product_id = p.product_id
    ORDER BY s.sale_id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Module</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f0f0f0; padding-top: 40px; }
        .container { max-width: 900px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Sales Management</h2>

    <!-- Sale Form -->
    <form method="post" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-5">
                <label>Product</label>
                <select name="product_id" class="form-control" required>
                    <option value="">-- Select Product --</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <label>Quantity Sold</label>
                <input type="number" name="quantity_sold" class="form-control" min="1" required>
            </div>
            <div class="form-group col-md-4">
                <label>Sale Date</label>
                <input type="date" name="sale_date" class="form-control" value="<?= date('Y-m-d') ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-success">Record Sale</button>
    </form>

    <!-- Sales Table -->
    <h5>Sales Records</h5>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Total (₱)</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($sales): ?>
                <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td><?= htmlspecialchars($sale['product_name']) ?></td>
                        <td><?= $sale['quantity_sold'] ?></td>
                        <td><?= number_format($sale['total_amount'], 2) ?></td>
                        <td><?= $sale['sale_date'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">No sales yet.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
