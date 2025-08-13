<?php
session_start();
include("../database/connection.php");

if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

$sql = "
    SELECT sp.purchase_id, s.name AS supplier_name, sp.product_name,
           sp.quantity, sp.price, sp.total_cost, sp.purchase_date, sp.remarks
    FROM supplier_purchases sp
    JOIN suppliers s ON sp.supplier_id = s.supplier_id
    ORDER BY sp.purchase_date ASC
";
$purchases = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Supplier Purchases</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 95%;
            max-width: 1100px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table thead {
            background: #007BFF;
            color: white;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table tr:nth-child(even) {
            background: #f9f9f9;
        }
        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 0 2px;
            background: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-delete {
            background: #c0392b;
        }
        .btn-delete:hover {
            background: #922b21;
        }
        .top-actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="top-actions">
        <a href="supplier_purchase_add.php" class="btn"> Add Purchase</a>
        <a href="dashboard.php">Dashboard</a>

    </div>
    <h2>Supplier Purchases</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Supplier</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
                <th>Date</th>
                <th>Remarks</th>
                <th>Actions</th>
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
                    <td>
                        <a href="supplier_purchase_edit.php?id=<?= $row['purchase_id'] ?>" class="btn">✏️ Edit</a>
                        <a href="supplier_purchase_delete.php?id=<?= $row['purchase_id'] ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this purchase?');">🗑 Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align:center;">No purchases found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
