<?php
session_start();
include "conn.php";

$error = "";
/*
CREATE TABLE roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL
);
INSERT INTO roles (role_name) VALUES ('Admin'), ('Staff');
ALTER TABLE users 
ADD COLUMN password VARCHAR(255) NOT NULL,
ADD COLUMN role_id INT,
ADD FOREIGN KEY (role_id) REFERENCES roles(role_id);
<?php
echo password_hash("admin123", PASSWORD_DEFAULT);
?>
$2y$10$FjbU6s9YErEXAMPLEloXxUrKUbFzh4USfRovYnnEmmHkYrE6P9v2dC
INSERT INTO users (name, email, id_number, course, password, role_id) VALUES
('Admin User', 'admin@example.com', '0001', 'IT', '$2y$10$FjbU6s9YErEXAMPLEloXxUrKUbFzh4USfRovYnnEmmHkYrE6P9v2dC', 1),
('Staff User', 'staff@example.com', '0002', 'IT', '$2y$10$FjbU6s9YErEXAMPLEloXxUrKUbFzh4USfRovYnnEmmHkYrE6P9v2dC', 2);
*/ 

// Process login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role_id'] = $user['role_id'];
        $_SESSION['name'] = $user['name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Inventory System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <h3 class="text-center mb-4">Inventory System Login</h3>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Email" required autofocus>
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button class="btn btn-primary btn-block">Login</button>
    </form>
</div>

</body>
</html>
