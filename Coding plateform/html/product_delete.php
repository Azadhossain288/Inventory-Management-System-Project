<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: product_view.php");
    exit;
}

$id = $_GET['id'];

// Optional: delete image file too
$stmt = $conn->prepare("SELECT image_path FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product && !empty($product['image_path']) && file_exists($product['image_path'])) {
    unlink($product['image_path']);
}

// Delete the product from DB
$delete = $conn->prepare("DELETE FROM products WHERE id = ?");
$delete->execute([$id]);

header("Location: product_view.php");
exit;
?>
