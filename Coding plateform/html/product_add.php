<?php
session_start();
include("../database/connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

date_default_timezone_set('Asia/Dhaka');

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = $_POST['product_name'];
    $unitPrice = $_POST['unit_price'];
    
    $expiryDate = $_POST['expiry_date'];
    $description = $_POST['description'];
    $created_at = date('Y-m-d H:i:s');
    $created_by = $_SESSION['user']['id'] ?? 1;

    $imagePath = "";

    // Handle image upload if provided
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
        $imageDir = "../uploads/";
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        $fileName = basename($_FILES["product_image"]["name"]);
        $targetPath = $imageDir . time() . "_" . $fileName;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO products (product_name, unit_price, expiry_date, description, image_path, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$productName, $unitPrice, $expiryDate, $description, $imagePath, $created_at, $created_by]);

    $message = "Product added successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Product - IMS</title>
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
                <li><a href="product_add.php" class="active"><i class="fa fa-plus"></i> Add Product</a></li>
                <li><a href="product_view.php"><i class="fa fa-eye"></i> View Products</a></li>
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
                <h3 style="color: #27ae60;">Add New Product</h3>

                <?php if ($message): ?>
                    <p style="color: green;"><?= $message ?></p>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <label>PRODUCT NAME</label>
                    <input type="text" name="product_name" required>

                    <label>UNIT PRICE</label>
                    <input type="number" step="0.01" name="unit_price" required>

                    <label>EXPIRY DATE</label>
                    <input type="date" name="expiry_date" required>

                    <label>DESCRIPTION</label>
                    <textarea name="description" required></textarea><br><br>

                    <label>Upload Image (optional):</label>
                    <input type="file" name="product_image"><br><br>

                    <button type="submit" class="btn_submit" style="background-color:#2980b9;">
                        <i class="fa fa-plus-circle"></i> Add Product
                    </button>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
    document.getElementById('toggleMenu').addEventListener('click', function (e) {
        e.preventDefault();
        const menu = document.getElementById('sidebarMenu');
        menu.classList.toggle('showMenu');
    });
</script>
</body>
</html>
