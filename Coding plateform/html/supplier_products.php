<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$supplier_id = $_GET['id'] ?? null;
if (!$supplier_id) {
    die("Supplier ID missing.");
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_ids = $_POST['product_ids'] ?? [];

    // Delete old links
    $conn->prepare("DELETE FROM products_suppliers WHERE supplier_id = ?")->execute([$supplier_id]);

    // Insert new links
    if (!empty($product_ids)) {
        $stmt = $conn->prepare("INSERT INTO products_suppliers (product_id, supplier_id) VALUES (?, ?)");
        foreach ($product_ids as $pid) {
            $stmt->execute([$pid, $supplier_id]);
        }
    }

    $message = "✅ Products linked successfully!";
}

// Fetch supplier info
$stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$supplier) {
    die("Supplier not found.");
}

// Fetch all products
$products = $conn->query("SELECT id, product_name FROM products ORDER BY product_name")->fetchAll(PDO::FETCH_ASSOC);

// Fetch linked products
$stmt = $conn->prepare("SELECT product_id FROM products_suppliers WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$linked_products = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Link Products to Supplier - IMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div id="dashboardMainContainer">
    <!-- Sidebar (same as your other pages) -->
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
                <h3 style="color: #2980b9;">Link Products to Supplier: <?= htmlspecialchars($supplier['name']) ?></h3>

                <?php if ($message): ?>
                    <p style="color: green;"><?= htmlspecialchars($message) ?></p>
                <?php endif; ?>

                <form method="POST">
                    <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ccc; padding: 10px;">
                        <?php foreach ($products as $product): ?>
                            <label style="display: block; margin-bottom: 5px;">
                                <input 
                                    type="checkbox" 
                                    name="product_ids[]" 
                                    value="<?= $product['id'] ?>"
                                    <?= in_array($product['id'], $linked_products) ? 'checked' : '' ?>
                                >
                                <?= htmlspecialchars($product['product_name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <br>
                    <button type="submit" class="btn_submit" style="background-color:#2980b9;">
                        <i class="fa fa-link"></i> Save Links
                    </button>
                    &nbsp; <a href="supplier_view.php">Back to Suppliers</a>
                </form>
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
