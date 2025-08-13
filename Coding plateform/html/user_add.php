<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($first_name && $last_name && $email && $password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$first_name, $last_name, $email, $hashed_password])) {
            $message = "User created successfully!";
        } else {
            $message = "Failed to create user.";
        }
    } else {
        $message = "Please fill all fields.";
    }
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add User - IMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin:0; background:#f4f4f4; }
        .sidebar {
            width: 220px; background:#2c3e50; height:100vh; position:fixed; top:0; left:0; padding-top:20px; color:#fff;
        }
        .sidebar h2 { text-align:center; margin-bottom:30px; }
        .sidebar a {
            display:block; padding:12px 20px; color:#fff; text-decoration:none;
        }
        .sidebar a:hover { background:#1abc9c; }
        .content {
            margin-left: 220px; padding: 20px;
            background:#fff;
            border-radius: 6px;
            max-width: 500px;
        }
        .logout {
            background:#c0392b; color:#fff; padding:10px; text-align:center; position:absolute; bottom:0; width:220px;
        }
        .logout a { color:#fff; text-decoration:none; }
        input, button {
            width: 100%; padding:10px; margin:8px 0; border-radius:4px; border:1px solid #ccc;
            font-size: 16px;
        }
        button {
            background:#27ae60; color:#fff; border:none; cursor:pointer;
        }
        button:hover {
            background:#219150;
        }
        .message {
            margin: 10px 0; font-weight: bold; color: green;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Inventory MS</h2>
    <p style="text-align:center;">
        Welcome, <strong><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'User'; ?></strong>
    </p>

    <h3>User Management</h3>
    <a href="user_add.php" style="background:#1abc9c;"> <i class="fa fa-user-plus"></i> Add User</a>
    <a href="user_view.php"><i class="fa fa-eye"></i> View User</a>

    <h3>Product Management</h3>
    <a href="product_add.php"><i class="fa fa-plus"></i> Add Product</a>
    <a href="product_view.php"><i class="fa fa-eye"></i> View Product</a>

    <div class="logout">
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="content">
    <h1>Add User</h1>
    <?php if ($message): ?>
        <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <form method="POST" action="">
        <input type="text" name="first_name" placeholder="First Name" required autofocus>
        <input type="text" name="last_name" placeholder="Last Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Create User</button>
    </form>
</div>

</body>
</html>
