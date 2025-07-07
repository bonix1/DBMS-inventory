<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$is_admin = $_SESSION['role_id'] == 1;
$name = $_SESSION['name'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background-color: #f5f5f5; }
        .dashboard {
            margin-top: 60px;
            padding: 30px;
            border-radius: 12px;
            background: white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card:hover {
            transform: scale(1.03);
            transition: 0.3s;
        }
    </style>
</head>
<body>

<div class="container dashboard">
    <h2 class="text-center mb-4">Welcome, <?= htmlspecialchars($name) ?>!</h2>
    <p class="text-center mb-4">Role: <?= $is_admin ? "Admin" : "Staff" ?></p>
    
    <div class="row">

        <div class="col-md-4 mb-4">
            <a href="products.php" class="text-decoration-none">
                <div class="card text-white bg-primary h-100">
                    <div class="card-body text-center">
                        <h4 class="card-title">Product Management</h4>
                        <p>Manage product details and prices.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-4">
            <a href="suppliers.php" class="text-decoration-none">
                <div class="card text-white bg-success h-100">
                    <div class="card-body text-center">
                        <h4 class="card-title">Supplier Management</h4>
                        <p>View and add suppliers.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4 mb-4">
            <a href="stock.php" class="text-decoration-none">
                <div class="card text-white bg-warning h-100">
                    <div class="card-body text-center">
                        <h4 class="card-title">Stock Management</h4>
                        <p>Track stock entries and inventory levels.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 mb-4">
            <a href="sales.php" class="text-decoration-none">
                <div class="card text-white bg-danger h-100">
                    <div class="card-body text-center">
                        <h4 class="card-title">Sales Module</h4>
                        <p>Record and monitor product sales.</p>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-6 mb-4">
            <a href="reports.php" class="text-decoration-none">
                <div class="card text-white bg-dark h-100">
                    <div class="card-body text-center">
                        <h4 class="card-title">Reports & Analytics</h4>
                        <p>View performance reports and trends.</p>
                    </div>
                </div>
            </a>
        </div>

        <?php if ($is_admin): ?>
        <div class="col-md-12 mb-4 text-center">
            <a href="users.php" class="btn btn-outline-secondary">User Management (Admin Only)</a>
        </div>
        <?php endif; ?>
    </div>

    <div class="text-center mt-4">
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

</body>
</html>
