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
    $name = trim($_POST['name']);
    $contact_number = trim($_POST['contact_number']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    // Basic validation
    if (empty($name)) {
        $message = " Supplier name is required.";
    } else {
        // Insert into suppliers table
        $stmt = $conn->prepare("INSERT INTO suppliers (name, contact_number, email, address) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$name, $contact_number, $email, $address])) {
            $message = "Supplier added successfully!";
        } else {
            $message = " Failed to add supplier.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Supplier - IMS</title>
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
                <li><a href="supplier_add.php" class="active"><i class="fa fa-plus"></i> Add Supplier</a></li>
                <li><a href="supplier_view.php"><i class="fa fa-eye"></i> View Suppliers</a></li>
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
                <h3 style="color: #27ae60;">Add New Supplier</h3>

                <?php if ($message): ?>
                    <p style="color: <?= strpos($message, '✅') === 0 ? 'green' : 'red' ?>;">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST">
                    <label>Supplier Name *</label>
                    <input type="text" name="name" required>

                    <label>Contact Number</label>
                    <input type="text" name="contact_number">

                    <label>Email</label>
                    <input type="email" name="email">

                    <label>Address</label>
                    <textarea name="address"></textarea><br><br>

                    <button type="submit" class="btn_submit" style="background-color:#2980b9;">
                        <i class="fa fa-plus-circle"></i> Add Supplier
                    </button>
                </form>
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
