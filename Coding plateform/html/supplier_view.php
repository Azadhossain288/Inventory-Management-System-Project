<?php
session_start();
include("../database/connection.php");
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$search = $_GET['search'] ?? '';

if ($search) {
    $stmt = $conn->prepare("SELECT * FROM suppliers WHERE name LIKE :search ORDER BY supplier_id ASC");
    $stmt->execute(['search' => "%$search%"]);
} else {
    $stmt = $conn->prepare("SELECT * FROM suppliers ORDER BY supplier_id ASC");
    $stmt->execute();
}

$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Suppliers - IMS</title>
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
            <span><?= htmlspecialchars($_SESSION['user']['username'] ?? 'user') ?></span>
        </div>

        <nav class="dashboard_sidebar_menus" id="sidebarMenu">
            <ul class="dashboard_menu_lists">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="supplier_add.php"><i class="fa fa-plus"></i> Add Supplier</a></li>
                <li><a href="supplier_view.php" class="active"><i class="fa fa-eye"></i> View Suppliers</a></li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="dashboard_content_container">
        <header class="dashboard_topNav">
            <a href="#" id="toggleMenu"><i class="fa fa-navicon"></i></a>
            <a href="logout.php"><i class="fa fa-power-off"></i> Log-out</a>
        </header>

        <main class="dashboard_content">
            <div class="dashboard_content_main">
                <h3 style="color: #2980b9;">📋 Supplier List</h3>

                <form method="GET" action="supplier_view.php" style="margin-bottom: 20px;">
                    <input type="text" name="search" placeholder="Search suppliers by name" value="<?= htmlspecialchars($search) ?>">
                    <button type="submit">Search</button>
                </form>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Supplier ID</th>
                            <th>Name</th>
                            <th>Contact Number</th>
                            <th>Email</th>
                            <th>Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($suppliers): ?>
                            <?php foreach ($suppliers as $supplier): ?>
                                <tr>
                                    <td><?= $supplier['supplier_id'] ?></td>
                                    <td><?= htmlspecialchars($supplier['name']) ?></td>
                                    <td><?= htmlspecialchars($supplier['contact_number']) ?></td>
                                    <td><?= htmlspecialchars($supplier['email']) ?></td>
                                    <td><?= htmlspecialchars($supplier['address']) ?></td>
                                    <td>
                                        <a href="supplier_edit.php?id=<?= $supplier['supplier_id'] ?>" 
                                           style="color: blue; text-decoration:none;">
                                           ✏ Edit
                                        </a> | 
                                        <a href="supplier_delete.php?id=<?= $supplier['supplier_id'] ?>" 
                                           style="color: red; text-decoration:none;"
                                           onclick="return confirm('Are you sure you want to delete this supplier?');">
                                           🗑 Delete
                                        </a> | 
                                       <a href="supplier_products.php?id=<?= $supplier['supplier_id'] ?>" style="color: green; text-decoration:none;">
                                      🔗 Link Products
                                    </a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center;">No suppliers found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('toggleMenu').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('sidebarMenu').classList.toggle('showMenu');
    });
</script>
</body>
</html>
