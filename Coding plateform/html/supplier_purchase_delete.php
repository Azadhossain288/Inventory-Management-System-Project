<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$purchase_id = $_GET['id'] ?? null;

if (!$purchase_id) {
    die("Invalid purchase ID");
}

// Delete purchase record
$stmt = $conn->prepare("DELETE FROM supplier_purchases WHERE purchase_id = ?");
$stmt->execute([$purchase_id]);

header("Location: supplier_purchase_view.php");
exit;
