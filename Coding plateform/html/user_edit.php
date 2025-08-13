<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Get user ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID.");
}
$id = (int)$_GET['id'];

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    die("User not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($password)) {
        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, password=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$first_name, $last_name, $email, $password, $id]);
    } else {
        $stmt = $conn->prepare("UPDATE users SET first_name=?, last_name=?, email=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$first_name, $last_name, $email, $id]);
    }

    header("Location: user_view.php");
    exit;
}

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User - IMS</title>
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
            min-height: 100vh;
        }
        h1 {
            margin-top:0;
            margin-bottom:20px;
            color:#2c3e50;
        }
        form {
            max-width: 500px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #1abc9c;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            font-size: 16px;
        }
        button:hover {
            background: #16a085;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Inventory MS</h2>
    <p style="text-align:center;">Welcome, <strong><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'User'; ?></strong></p>

    <h3>User Management</h3>
    <a href="user_add.php"><i class="fa fa-user-plus"></i> Add User</a>
    <a href="user_view.php"><i class="fa fa-eye"></i> View User</a>

    <h3>Product Management</h3>
    <a href="product_add.php"><i class="fa fa-plus"></i> Add Product</a>
    <a href="product_view.php"><i class="fa fa-eye"></i> View Product</a>

    <div class="logout" style="background:#c0392b; padding:10px; text-align:center; position:absolute; bottom:0; width:220px;">
        <a href="logout.php" style="color:#fff; text-decoration:none;"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="content">
    <h1><i class="fa fa-edit"></i> Edit User</h1>

    <form method="POST">
        <label>First Name</label>
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>" required>

        <label>Last Name</label>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>

        <label>Password (Leave blank to keep old)</label>
        <input type="text" name="password" placeholder="New Password">

        <button type="submit"><i class="fa fa-save"></i> Update User</button>
    </form>
</div>

</body>
</html>
