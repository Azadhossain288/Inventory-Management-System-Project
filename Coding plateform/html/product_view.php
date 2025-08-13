<?php
session_start();
include("../database/connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Get search term if exists
$search = $_GET['search'] ?? '';

if ($search) {
    // Use LIKE operator for partial match, with parameter binding to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, product_name, unit_price, expiry_date, description, image_path 
                            FROM products 
                            WHERE product_name LIKE :search");
    $stmt->execute(['search' => "%$search%"]);
} else {
    // No search, fetch all
    $stmt = $conn->prepare("SELECT id, product_name, unit_price, expiry_date, description, image_path FROM products");
    $stmt->execute();
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Products - IMS</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
</head>
<body>
<div id="dashboardMainContainer">
    <!-- Sidebar -->
    <aside class="dashboard_sidebar">
        <h1 class="dashboard_logo">IMS</h1>
        <div class="dashboard_sidebar_user">
            <img src="https://via.placeholder.com/80" alt="User image.">
            <span>
                <?= htmlspecialchars($_SESSION['user']['username'] ?? 'user') ?>
            </span>
        </div>

        <nav class="dashboard_sidebar_menus" id="sidebarMenu">
            <ul class="dashboard_menu_lists">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="product_add.php"><i class="fa fa-plus"></i> Add Product</a></li>
                <li><a href="product_view.php" class="active"><i class="fa fa-eye"></i> View Products</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="dashboard_content_container">
        <!-- Top Navigation -->
        <header class="dashboard_topNav">
            <a href="#" id="toggleMenu"><i class="fa fa-navicon"></i></a>
            <a href="logout.php"><i class="fa fa-power-off"></i> Log-out</a>
        </header>

        <!-- Page Content -->
        <main class="dashboard_content">
            <div class="dashboard_content_main">
                <h3 style="color: #2980b9;">📦 Product List</h3>

                <form method="GET" action="product_view.php" style="margin-bottom: 20px;">
                    <input type="text" name="search" placeholder="Search products by name" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">Search</button>
                </form>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Unit Price</th>
                            <th>Expiry Date</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($products): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= $product['id'] ?></td>
                                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                                    <td><?= number_format($product['unit_price'], 2) ?></td>
                                    <td><?= htmlspecialchars($product['expiry_date']) ?></td>
                                    <td><?= htmlspecialchars($product['description']) ?></td>
                                    <td>
                                        <?php if (!empty($product['image_path'])): ?>
                                            <img src="<?= $product['image_path'] ?>" width="60" alt="Product Image">
                                        <?php else: ?>
                                            No image
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="product_edit.php?id=<?= $product['id'] ?>" 
                                           style="color: blue; text-decoration:none;">
                                           ✏ Edit
                                        </a> | 
                                        <a href="product_delete.php?id=<?= $product['id'] ?>" 
                                           style="color: red; text-decoration:none;"
                                           onclick="return confirm('Are you sure you want to delete this product?');">
                                           🗑 Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center;">No products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('toggleMenu').addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('sidebarMenu').classList.toggle('showMenu');
    });
</script>
</body>
</html>
