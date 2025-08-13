<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Fetch all suppliers for dropdown
$suppliersStmt = $conn->prepare("SELECT supplier_id, name FROM suppliers ORDER BY name");
$suppliersStmt->execute();
$suppliersList = $suppliersStmt->fetchAll(PDO::FETCH_ASSOC);

// Get selected supplier id from GET param (optional)
$selectedSupplierId = $_GET['supplier_id'] ?? '';

// Prepare SQL for purchase history with optional supplier filter
$sql = "
SELECT sp.purchase_id, 
       s.name AS supplier_name, 
       p.product_name, 
       sp.quantity, 
       sp.price, 
       sp.total_cost, 
       sp.purchase_date, 
       sp.remarks
FROM supplier_purchases sp
JOIN suppliers s ON sp.supplier_id = s.supplier_id
JOIN products p ON sp.product_id = p.id
";

if ($selectedSupplierId) {
    $sql .= " WHERE sp.supplier_id = :supplier_id ";
}

$sql .= " ORDER BY sp.purchase_date DESC";

$stmt = $conn->prepare($sql);

if ($selectedSupplierId) {
    $stmt->execute(['supplier_id' => $selectedSupplierId]);
} else {
    $stmt->execute();
}

$purchases = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Supplier Purchase History - IMS</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://use.fontawesome.com/0c7a3095b5.js"></script>
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
                <li><a href="supplier_add.php"><i class="fa fa-plus"></i> Add Supplier</a></li>
                <li><a href="supplier_view.php"><i class="fa fa-eye"></i> View Suppliers</a></li>
                <li><a href="supplier_history.php" class="active"><i class="fa fa-history"></i> Supplier Purchase History</a></li>
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
                <h3 style="color: #2980b9;">📦 Supplier Purchase History</h3>

                <!-- Supplier filter form -->
                <form method="GET" action="supplier_history.php" style="margin-bottom: 20px;">
                    <label for="supplier_id">Select Supplier: </label>
                    <select name="supplier_id" id="supplier_id" required>
                        <option value="">-- Select Supplier --</option>
                        <?php foreach ($suppliersList as $supplier): ?>
                            <option value="<?= $supplier['supplier_id'] ?>" <?= $selectedSupplierId == $supplier['supplier_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($supplier['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit">View Purchases</button>
                </form>

                <table border="1" cellpadding="10" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Purchase ID</th>
                            <th>Supplier Name</th>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Price (per unit)</th>
                            <th>Total Cost</th>
                            <th>Purchase Date</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($purchases): ?>
                            <?php foreach ($purchases as $row): ?>
                                <tr>
                                    <td><?= $row['purchase_id'] ?></td>
                                    <td><?= htmlspecialchars($row['supplier_name']) ?></td>
                                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                                    <td><?= $row['quantity'] ?></td>
                                    <td><?= number_format($row['price'], 2) ?></td>
                                    <td><?= number_format($row['total_cost'], 2) ?></td>
                                    <td><?= $row['purchase_date'] ?></td>
                                    <td><?= htmlspecialchars($row['remarks']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="8" style="text-align:center;">No purchase history found for this supplier.</td></tr>
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
