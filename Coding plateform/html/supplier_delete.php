<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$supplier_id = $_GET['id'] ?? null;
if (!$supplier_id) {
    die("Supplier ID is required");
}

// Optional: before deleting, you can check if the supplier exists
$stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id=?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    die("Supplier not found.");
}

// Delete supplier
$stmt = $conn->prepare("DELETE FROM suppliers WHERE supplier_id=?");
if ($stmt->execute([$supplier_id])) {
    // Also delete links in products_suppliers to avoid orphan rows
    $conn->prepare("DELETE FROM products_suppliers WHERE supplier_id=?")->execute([$supplier_id]);

    // Redirect to supplier list with success message (you can pass messages via session)
    $_SESSION['message'] = "Supplier deleted successfully.";
    header("Location: supplier_view.php");
    exit;
} else {
    die("Failed to delete supplier.");
}
