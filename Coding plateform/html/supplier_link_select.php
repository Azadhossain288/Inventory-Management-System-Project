<?php
session_start();
include("../database/connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Fetch all suppliers
$stmt = $conn->prepare("SELECT supplier_id, name FROM suppliers ORDER BY name ASC");
$stmt->execute();
$suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Select Supplier - Link Products</title>
</head>
<body>
<h2>Select a Supplier to Link Products</h2>
<form method="get" action="supplier_link_products.php">
    <select name="id" required>
        <option value="">-- Select Supplier --</option>
        <?php foreach ($suppliers as $supplier): ?>
            <option value="<?php echo $supplier['supplier_id']; ?>">
                <?php echo htmlspecialchars($supplier['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Proceed</button>
</form>
</body>
</html>
