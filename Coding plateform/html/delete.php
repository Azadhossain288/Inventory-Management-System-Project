<?php
session_start();
include("../database/connection.php");

// Redirect to login if not authenticated
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    echo "User ID is missing!";
    exit;
}

$id = $_GET['id'];

// Delete user by ID
$stmt = $conn->prepare("DELETE FROM users WHERE id = :id");
$stmt->execute([':id' => $id]);

// Redirect to user list page
header("Location: user_add.php");
exit;
?>
