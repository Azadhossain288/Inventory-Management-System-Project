<?php
session_start();
include("../database/connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Get supplier ID from URL
if (!isset($_GET['id'])) {
    die("Supplier ID not provided.");
}
$supplier_id = $_GET['id'];

// Fetch supplier details
$stmt = $conn->prepare("SELECT * FROM suppliers WHERE supplier_id = ?");
$stmt->execute([$supplier_id]);
$supplier = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$supplier) {
    die("Supplier not found.");
}

// Fetch all products
$stmt = $conn->prepare("SELECT * FROM products ORDER BY product_name ASC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['products'])) {
        // First delete old links
        $conn->prepare("DELETE FROM product_supplier WHERE supplier_id = ?")->execute([$supplier_id]);

        // Insert new links
        $stmt = $conn->prepare("INSERT INTO product_supplier (supplier_id, product_id) VALUES (?, ?)");
        foreach ($_POST['products'] as $product_id) {
            $stmt->execute([$supplier_id, $product_id]);
        }
        echo "<p style='color:green;'>Products linked successfully!</p>";
    } else {
        echo "<p style='color:red;'>Please select at least one product.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Link Products - <?php echo htmlspecialchars($supplier['name']); ?></title>
</head>
<body>
<h2>Link Products to <?php echo htmlspecialchars($supplier['name']); ?></h2>

<form method="post">
    <?php foreach ($products as $product): ?>
        <label>
            <input type="checkbox" name="products[]" value="<?php echo $product['id']; ?>">
            <?php echo htmlspecialchars($product['product_name']); ?>
        </label><br>
    <?php endforeach; ?>
    <br>
    <button type="submit">Save Links</button>
</form>

</body>
</html>
