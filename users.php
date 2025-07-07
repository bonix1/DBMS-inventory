<?php
session_start();
include "conn.php";

// 🔒 Restrict access to Admins only
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: dashboard.php");
    exit();
}

// Fetch all users with role names
$users = $conn->query("
    SELECT u.user_id, u.name, u.email, u.id_number, u.course, r.role_name 
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch roles for dropdown
$roles = $conn->query("SELECT * FROM roles")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission to add user
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $id_number = $_POST['id_number'];
    $course = $_POST['course'];
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];

    // Basic validation
    if ($name && $email && $id_number && $course && $password && $role_id) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, id_number, course, password, role_id) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $id_number, $course, $hashedPassword, $role_id]);

        $success = "User added successfully.";
        header("Refresh:1");
    } else {
        $error = "All fields are required.";
    }
}

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $deleteId = $_GET['delete'];

    // Prevent self-deletion
    if ($deleteId != $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
        $stmt->execute([$deleteId]);
        header("Location: users.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { background: #f8f9fa; padding-top: 40px; }
        .container { max-width: 900px; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="container">
    <h2 class="mb-4 text-center">User Management (Admin Only)</h2>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <!-- Add New User Form -->
    <form method="POST" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-3"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
            <div class="form-group col-md-3"><input type="email" name="email" class="form-control" placeholder="Email" required></div>
            <div class="form-group col-md-2"><input type="text" name="id_number" class="form-control" placeholder="ID No." required></div>
            <div class="form-group col-md-2"><input type="text" name="course" class="form-control" placeholder="Course" required></div>
            <div class="form-group col-md-2"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <select name="role_id" class="form-control" required>
                    <option value="">Select Role</option>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-3">
                <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
            </div>
        </div>
    </form>

    <!-- User Table -->
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>ID Number</th>
                <th>Course</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($users): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['id_number']) ?></td>
                        <td><?= htmlspecialchars($user['course']) ?></td>
                        <td><?= htmlspecialchars($user['role_name']) ?></td>
                        <td>
                            <?php if ($_SESSION['user_id'] != $user['user_id']): ?>
                                <a href="users.php?delete=<?= $user['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
                            <?php else: ?>
                                <span class="text-muted">Logged In</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center">No users found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>
</body>
</html>
