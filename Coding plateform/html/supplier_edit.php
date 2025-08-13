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

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $contact_number = $_POST['contact_number'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';

    if (empty($name)) {
        $message = "❌ Supplier name is required.";
    } else {
        $stmt = $conn->prepare("UPDATE suppliers SET name=?, contact_number=?, email=?, address=?, updated_at=NOW() WHERE supplier_id=?");
        if ($stmt->execute([$name, $contact_number, $email, $address, $supplier_id])) {
            $message = "✅ Supplier updated successfully!";
        } else {
            $message = "❌ Failed to update supplier.";
        }
    }
}

// Fetch supplier data to pre-fill form
$stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id=?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    die("Supplier not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Supplier - IMS</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div id="dashboardMainContainer">
    <!-- Sidebar (same as before) -->
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

    <!-- Main Content -->
    <div class="dashboard_content_container">
        <header class="dashboard_topNav">
            <a href="#" id="toggleMenu"><i class="fa fa-navicon"></i></a>
            <a href="logout.php"><i class="fa fa-power-off"></i> Log-out</a>
        </header>

        <main class="dashboard_content">
            <div class="dashboard_content_main">
                <h3 style="color:#2980b9;">Edit Supplier</h3>

                <?php if ($message): ?>
                    <p style="color: <?= strpos($message, '✅') === 0 ? 'green' : 'red' ?>">
                        <?= htmlspecialchars($message) ?>
                    </p>
                <?php endif; ?>

                <form method="POST">
                    <label>Supplier Name *</label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($supplier['name']) ?>">

                    <label>Contact Number</label>
                    <input type="text" name="contact_number" value="<?= htmlspecialchars($supplier['contact_number']) ?>">

                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($supplier['email']) ?>">

                    <label>Address</label>
                    <textarea name="address"><?= htmlspecialchars($supplier['address']) ?></textarea><br><br>

                    <button type="submit" class="btn_submit" style="background-color:#2980b9;">
                        <i class="fa fa-save"></i> Update Supplier
                    </button>
                </form>
            </div>
        </main>
    </div>
</div>

<script>
document.getElementById('toggleMenu').addEventListener('click', function(e){
    e.preventDefault();
    document.getElementById('sidebarMenu').classList.toggle('showMenu');
});
</script>
</body>
</html>
