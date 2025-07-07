<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: dashboard.php");
    exit();
}

include "conn.php";

// Sales per product
$salesData = $conn->query("
    SELECT p.name AS product_name, SUM(s.quantity_sold) AS total_qty, SUM(s.total_amount) AS total_sales
    FROM sales s
    JOIN products p ON s.product_id = p.product_id
    GROUP BY s.product_id
    ORDER BY total_sales DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Products per supplier
$supplierProducts = $conn->query("
    SELECT sp.name AS supplier_name, COUNT(DISTINCT p.product_id) AS product_count
    FROM suppliers sp
    JOIN stock s ON sp.supplier_id = s.supplier_id
    JOIN products p ON s.product_id = p.product_id
    GROUP BY sp.supplier_id
    ORDER BY product_count DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports & Analytics</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center mb-4">Reports & Analytics</h2>

    <!-- Chart: Sales Per Product -->
    <div class="mb-5">
        <h5 class="text-center">Top Selling Products</h5>
        <canvas id="salesChart" height="120"></canvas>
    </div>

    <!-- Chart: Products Supplied per Supplier -->
    <div class="mb-5">
        <h5 class="text-center">Products Supplied by Supplier</h5>
        <canvas id="supplierChart" height="120"></canvas>
    </div>

    <div class="text-center">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>

<script>
    const salesChart = document.getElementById('salesChart').getContext('2d');
    const supplierChart = document.getElementById('supplierChart').getContext('2d');

    new Chart(salesChart, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($salesData, 'product_name')) ?>,
            datasets: [{
                label: 'Total Sales (₱)',
                data: <?= json_encode(array_column($salesData, 'total_sales')) ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        }
    });

    new Chart(supplierChart, {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($supplierProducts, 'supplier_name')) ?>,
            datasets: [{
                label: 'Product Count',
                data: <?= json_encode(array_column($supplierProducts, 'product_count')) ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.6)'
            }]
        }
    });
</script>
</body>
</html>
