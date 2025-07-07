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

/*
CREATE TABLE suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    contact_info VARCHAR(255)
);
*/

// Add new supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supplier'])) {
    $name = trim($_POST['name']);
    $contact = trim($_POST['contact_info']);

    if (!empty($name) && !empty($contact)) {
        $stmt = $conn->prepare("INSERT INTO suppliers (name, contact_info) VALUES (?, ?)");
        $stmt->execute([$name, $contact]);
        header("Location: suppliers.php");
        exit();
    }
}

// Delete supplier
if (isset($_GET['delete'])) {
    $supplier_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id = ?");
    $stmt->execute([$supplier_id]);
    header("Location: suppliers.php");
    exit();
}

// Get all suppliers
$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY supplier_id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f0f0f0; padding-top: 40px; }
        .container { max-width: 950px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">Supplier Management</h2>

    <!-- Add Supplier Form -->
    <form method="post" class="mb-4">
        <input type="hidden" name="add_supplier" value="1">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Supplier Name</label>
                <input type="text" name="name" class="form-control" required placeholder="e.g., ABC Trading">
            </div>
            <div class="form-group col-md-6">
                <label>Contact Info</label>
                <input type="text" name="contact_info" class="form-control" required placeholder="e.g., 09123456789 / abc@email.com">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Add Supplier</button>
    </form>

    <!-- Supplier List Table -->
    <h5>Existing Suppliers</h5>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Supplier Name</th>
                <th>Contact Info</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($suppliers): ?>
                <?php foreach ($suppliers as $supplier): ?>
                    <tr>
                        <td><?= htmlspecialchars($supplier['name']) ?></td>
                        <td><?= htmlspecialchars($supplier['contact_info']) ?></td>
                        <td>
                            <a href="suppliers.php?delete=<?= $supplier['supplier_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this supplier?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="3" class="text-center">No suppliers found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="stock.php" class="btn btn-secondary">← Back to Stock</a>
    </div>
</div>

</body>
</html>
