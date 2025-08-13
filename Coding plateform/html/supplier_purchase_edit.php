<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$purchase_id = $_GET['id'] ?? null;
if (!$purchase_id) {
    die("Invalid purchase ID");
}

// Fetch suppliers and products for selects
$suppliers = $conn->query("SELECT supplier_id, name FROM suppliers ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$products = $conn->query("SELECT id, product_name FROM products ORDER BY product_name")->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $supplier_id = $_POST['supplier_id'];
    $product_id  = $_POST['product_id'];
    $quantity    = $_POST['quantity'];
    $price       = $_POST['price'];
    $remarks     = $_POST['remarks'];

    // Fetch product name from products table
    $stmt = $conn->prepare("SELECT product_name FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die("Invalid product selected.");
    }

    $product_name = $product['product_name'];

    // Update the purchase record
    $stmt = $conn->prepare("
        UPDATE supplier_purchases SET 
            supplier_id = ?, product_id = ?, product_name = ?, 
            quantity = ?, price = ?, remarks = ? 
        WHERE purchase_id = ?
    ");
    $stmt->execute([$supplier_id, $product_id, $product_name, $quantity, $price, $remarks, $purchase_id]);

    header("Location: supplier_purchase_view.php");
    exit;
}

// Fetch existing purchase data
$stmt = $conn->prepare("SELECT * FROM supplier_purchases WHERE purchase_id = ?");
$stmt->execute([$purchase_id]);
$purchase = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$purchase) {
    die("Purchase not found.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Supplier Purchase</title>
    <style>
        /* Use the same styles as supplier_purchase_add.php */
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 450px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 10px 0 5px;
        }
        select, input, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        button {
            width: 100%;
            padding: 12px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #0056b3;
        }
        a.back-link {
            display: inline-block;
            margin-top: 10px;
            color: #007BFF;
            text-decoration: none;
        }
        a.back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Supplier Purchase</h2>
    <form method="POST">
        <label>Supplier:</label>
        <select name="supplier_id" required>
            <option value="">-- Select Supplier --</option>
            <?php foreach ($suppliers as $s): ?>
                <option value="<?= $s['supplier_id'] ?>" <?= $purchase['supplier_id'] == $s['supplier_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Product:</label>
        <select name="product_id" required>
            <option value="">-- Select Product --</option>
            <?php foreach ($products as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $purchase['product_id'] == $p['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['product_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Quantity:</label>
        <input type="number" name="quantity" min="1" value="<?= $purchase['quantity'] ?>" required>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" value="<?= $purchase['price'] ?>" required>

        <label>Remarks:</label>
        <textarea name="remarks"><?= htmlspecialchars($purchase['remarks']) ?></textarea>

        <button type="submit">Update Purchase</button>
    </form>
    <a href="supplier_purchase_view.php" class="back-link">← Back to Purchases</a>
</div>
</body>
</html>
