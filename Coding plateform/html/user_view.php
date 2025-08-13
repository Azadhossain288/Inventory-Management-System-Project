<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$stmt = $conn->query("SELECT id, first_name, last_name, email, password FROM users ORDER BY id ASC");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$user = $_SESSION['user'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Users - IMS</title>
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
        }
        .logout {
            background:#c0392b; color:#fff; padding:10px; text-align:center; position:absolute; bottom:0; width:220px;
        }
        .logout a { color:#fff; text-decoration:none; }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #ddd; padding: 12px; text-align: left;
        }
        th {
            background-color: #1abc9c; color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .btn {
            padding: 6px 10px;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
        }
        .btn-edit { background-color: #3498db; }
        .btn-delete { background-color: #e74c3c; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Inventory MS</h2>
    <p style="text-align:center;">Welcome, <strong><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'User'; ?></strong></p>

    <h3>User Management</h3>
    <a href="user_add.php"><i class="fa fa-user-plus"></i> Add User</a>
    <a href="user_view.php" style="background:#1abc9c;"><i class="fa fa-eye"></i> View User</a>

    <h3>Product Management</h3>
    <a href="product_add.php"><i class="fa fa-plus"></i> Add Product</a>
    <a href="product_view.php"><i class="fa fa-eye"></i> View Product</a>

    <div class="logout">
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="content">
    <h1>User List</h1>
    <?php if (count($users) === 0): ?>
        <p>No users found.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $index => $u): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($u['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($u['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                    <td><?php echo htmlspecialchars($u['password']); ?></td>
                    <td>
                        <a class="btn btn-edit" href="user_edit.php?id=<?php echo $u['id']; ?>"><i class="fa fa-edit"></i> Edit</a>
                        <a class="btn btn-delete" href="user_delete.php?id=<?php echo $u['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-trash"></i> Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
