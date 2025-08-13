<?php
session_start();
include("../database/connection.php"); // DB connection

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$user = $_SESSION['user'];

// Get total products count
$stmt = $conn->prepare("SELECT COUNT(*) AS total_products FROM products");
$stmt->execute();
$total_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_products'];

// Get total suppliers count
$stmt = $conn->prepare("SELECT COUNT(*) AS total_suppliers FROM suppliers");
$stmt->execute();
$total_suppliers = $stmt->fetch(PDO::FETCH_ASSOC)['total_suppliers'];

// Get total supplier-product links count
$stmt = $conn->prepare("SELECT COUNT(*) AS total_supplier_products FROM products_suppliers");
$stmt->execute();
$total_supplier_products = $stmt->fetch(PDO::FETCH_ASSOC)['total_supplier_products'];

// Get total supplier purchases count
$stmt = $conn->prepare("SELECT COUNT(*) AS total_supplier_purchases FROM supplier_purchases");
$stmt->execute();
$total_supplier_purchases = $stmt->fetch(PDO::FETCH_ASSOC)['total_supplier_purchases'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - IMS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f4f4;
        }
        .sidebar {
            width: 300px; 
            background: #2c3e50;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            color: #fff;
            display: flex;
            flex-direction: column;
        }
        .menu {
            overflow-y: auto;
            flex: 1;
            padding-top: 20px;
            scrollbar-width: thin;
        }
        .menu h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .menu a {
            display: block;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
        }
        .menu a:hover {
            background: #1abc9c;
        }
        h3 {
            padding-left: 20px;
            margin-top: 25px;
            margin-bottom: 8px;
            font-weight: normal;
            font-size: 1.1em;
            border-bottom: 1px solid #444;
            padding-bottom: 5px;
        }
        .logout {
            background: #c0392b;
            padding: 10px;
            text-align: center;
        }
        .logout a {
            color: #fff;
            text-decoration: none;
        }
        .content {
            margin-left: 300px;
            padding: 20px;
            background-color:#fff;
            min-height: 100vh;
        }

        /* Dashboard cards container with wrap */
        .dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 30px;
        }

        /* Each card container: 2 cards per row */
        .card-link {
            flex: 0 0 48%;
            text-decoration: none;
        }

        .card {
            background: #1abc9c;
            color: #fff;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        .card h2 {
            font-size: 40px;
            margin: 0;
        }
        .card p {
            margin: 10px 0 0;
            font-size: 18px;
        }

        /* Special background colors for some cards */
        .card.suppliers {
            background: #3498db;
        }
        .card.supplier-links {
            background: #2980b9;
        }
        .card.supplier-purchases {
            background: #9b59b6;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="menu">
        <h2>Inventory MS</h2>
        <p style="text-align:center;">Welcome, 
            <strong><?= isset($user['username']) ? htmlspecialchars($user['username']) : 'User' ?></strong>
        </p>

        <!-- User Management -->
        <h3>User Management</h3>
        <a href="user_add.php"><i class="fa fa-user-plus"></i> Add Employee</a>
        <a href="user_view.php"><i class="fa fa-eye"></i> View Employee</a>

        <!-- Product Management -->
        <h3>Product Management</h3>
        <a href="product_add.php"><i class="fa fa-plus"></i> Add Product</a>
        <a href="product_view.php"><i class="fa fa-eye"></i> View Product</a>

        <!-- Supplier Management -->
        <h3>Supplier Management</h3>
        <a href="supplier_add.php"><i class="fa fa-truck"></i> Add Supplier</a>
        <a href="supplier_view.php"><i class="fa fa-eye"></i> View Suppliers</a>
        <a href="supplier_products_view.php"><i class="fa fa-table"></i> View Database</a>

        <!-- Supplier Purchase -->
        <h3>Supplier Purchase</h3>
        <a href="supplier_purchase_add.php"><i class="fa fa-plus"></i> Add Purchase</a>
        <a href="supplier_purchase_view.php"><i class="fa fa-history"></i> View Purchase History</a>
    </div>

    <!-- Logout Button -->
    <div class="logout">
        <a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="content">
    <h1>Dashboard</h1>

    <div class="dashboard-cards">
        <a href="product_view.php" class="card-link">
            <div class="card">
                <h2><?= $total_products ?></h2>
                <p>Total Products</p>
            </div>
        </a>

        <a href="supplier_view.php" class="card-link">
            <div class="card suppliers">
                <h2><?= $total_suppliers ?></h2>
                <p>Total Suppliers</p>
            </div>
        </a>

        <a href="supplier_products_view.php" class="card-link">
            <div class="card supplier-links">
                <h2><?= $total_supplier_products ?></h2>
                <p>Total Supplier-Product Links</p>
            </div>
        </a>

        <a href="supplier_purchase_view.php" class="card-link">
            <div class="card supplier-purchases">
                <h2><?= $total_supplier_purchases ?></h2>
                <p>Supplier Purchase History</p>
            </div>
        </a>
    </div>
</div>

</body>
</html>
