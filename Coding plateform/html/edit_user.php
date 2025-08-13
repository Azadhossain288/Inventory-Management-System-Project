<?php
session_start();
date_default_timezone_set('Asia/Dhaka');
include("../database/connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "User ID missing!";
    exit;
}

$id = $_GET['id'];
$message = "";

// Fetch user by ID
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $email      = $_POST['email'];
    $updated_at = date('Y-m-d H:i:s');

    $query = "UPDATE users SET first_name = :first_name, last_name = :last_name, email = :email, updated_at = :updated_at WHERE id = :id";
    $stmt = $conn->prepare($query);

    $result = $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':updated_at' => $updated_at,
        ':id' => $id
    ]);

    if ($result) {
        header("Location: user_add.php");
        exit;
    } else {
        $message = "Failed to update user.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
            padding: 40px;
        }

        .container {
            background: #fff;
            max-width: 500px;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #d63384;
            margin-bottom: 25px;
        }

        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #d63384;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
        }

        button:hover {
            background-color: #c2185b;
        }

        .message {
            margin-top: 15px;
            color: red;
            text-align: center;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            text-decoration: none;
            color: #007bff;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit User</h2>

    <form method="POST">
        <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
        <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <button type="submit">Update User</button>
    </form>

    <?php if ($message): ?>
        <p class="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <a href="user_add.php" class="back-link">← Back to User List</a>
</div>

</body>
</html>
