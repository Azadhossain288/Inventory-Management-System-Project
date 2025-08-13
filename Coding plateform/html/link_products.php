<?php
session_start();
include("../database/connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Fetch suppliers
$suppliers = $conn->query("SELECT supplier_id, name FROM suppliers")->fetchAll(PDO::FETCH_ASSOC);

// Fetch products
$products = $conn->query("SELECT product_id, product_name FROM products")->fetchAll(PDO::FETCH_ASSOC);

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $product_id = $_POST['product_id'];

    $stmt = $conn->prepare("INSERT INTO product_supplier (supplier_id, product_id) VALUES (?, ?)");
    $stmt->execute([$supplier_id, $product_id]);

    echo "<p>Product linked to supplier successfully!</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Link Product to Supplier</title>
</head>
<body>
    <h2>Link Product to Supplier</h2>
    <form method="POST">
        <label for="supplier_id">Select Supplier:</label>
        <select name="supplier_id" required>
            <?php foreach ($suppliers as $supplier): ?>
                <option value="<?= $supplier['supplier_id'] ?>"><?= htmlspecialchars($supplier['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="product_id">Select Product:</label>
        <select name="product_id" required>
            <?php foreach ($products as $product): ?>
                <option value="<?= $product['product_id'] ?>"><?= htmlspecialchars($product['product_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <button type="submit">Link Product</button>
    </form>

    <hr>
    <h3>Existing Links</h3>
    <table border="1" cellpadding="5">
        <tr>
            <th>Supplier</th>
            <th>Product</th>
        </tr>
        <?php
        $links = $conn->query("
            SELECT s.name AS supplier_name, p.product_name
            FROM product_supplier ps
            JOIN suppliers s ON ps.supplier_id = s.supplier_id
            JOIN products p ON ps.product_id = p.product_id
        ")->fetchAll(PDO::FETCH_ASSOC);

        foreach ($links as $link) {
            echo "<tr>
                    <td>" . htmlspecialchars($link['supplier_name']) . "</td>
                    <td>" . htmlspecialchars($link['product_name']) . "</td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>
