<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Handle search input
$search = isset($_GET['search']) ? trim($_GET['search']) : "";

// Fetch supplier-product links with names (with search filter)
$sql = "
SELECT ps.id, 
       p.product_name, 
       s.name AS supplier_name
FROM products_suppliers ps
JOIN products p ON ps.product_id = p.id
JOIN suppliers s ON ps.supplier_id = s.supplier_id
WHERE (:search = '' OR p.product_name LIKE :searchLike OR s.name LIKE :searchLike)
ORDER BY ps.id ASC
";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', $search, PDO::PARAM_STR);
$stmt->bindValue(':searchLike', "%$search%", PDO::PARAM_STR);
$stmt->execute();
$links = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Supplier-Product Links - IMS</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
    <style>
        .search-container {
            margin-bottom: 15px;
            text-align: right;
        }
        .search-container input[type="text"] {
            padding: 6px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-container button {
            padding: 6px 12px;
            background: #2980b9;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-container button:hover {
            background: #1f6690;
        }
    </style>
</head>
<body>
<div id="dashboardMainContainer">
    <aside class="dashboard_sidebar">
        <h1 class="dashboard_logo">IMS</h1>
        <div class="dashboard_sidebar_user">
            <img src="https://via.placeholder.com/80" alt="User image.">
            <span><?= htmlspecialchars($_SESSION['user']['username'] ?? 'Guest') ?></span>
        </div>
        <nav class="dashboard_sidebar_menus" id="sidebarMenu">
            <ul class="dashboard_menu_lists">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li><a href="supplier_view.php"><i class="fa fa-eye"></i> View Suppliers</a></li>
                <li><a href="supplier_products_view.php" class="active"><i class="fa fa-link"></i> Supplier Links</a></li>
            </ul>
        </nav>
    </aside>

    <div class="dashboard_content_container">
        <header class="dashboard_topNav">
            <a href="#" id="toggleMenu"><i class="fa fa-navicon"></i></a>
            <a href="logout.php"><i class="fa fa-power-off"></i> Log-out</a>
        </header>

        <main class="dashboard_content">
            <div class="dashboard_content_main">
                <h3 style="color: #2980b9;">🔗 Supplier–Product Links</h3>

                <!-- Search Form -->
                <div class="search-container">
                    <form method="GET" action="">
                        <input type="text" name="search" placeholder="Search by product or supplier..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit"><i class="fa fa-search"></i> Search</button>
                    </form>
                </div>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product Name</th>
                            <th>Supplier Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($links): ?>
                            <?php foreach ($links as $row): ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center;">No data found.</td></tr>
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
