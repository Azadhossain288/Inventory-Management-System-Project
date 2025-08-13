<?php
session_start();
include("../database/connection.php");

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: log.php");
    exit;
}

// Get product by ID
if (!isset($_GET['id'])) {
    die("Product ID not provided.");
}

$id = $_GET['id'];

// Fetch the existing product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product not found.");
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $unit_price   = $_POST['unit_price'];
    $expiry_date  = $_POST['expiry_date'];
    $description  = $_POST['description'];

    // Default keep existing image path
    $image_path = $product['image_path'];

    // Check if a new file is uploaded without errors
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileSize = $_FILES['product_image']['size'];
        $fileType = $_FILES['product_image']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed file extensions
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Sanitize file name and create new unique file name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;

            // Directory where images will be saved
            $uploadFileDir = '../uploads/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $destPath = $uploadFileDir . $newFileName;

            // Move the file from temp to uploads folder
            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $image_path = 'uploads/' . $newFileName; // Save relative path for DB
            } else {
                echo "<p style='color:red;'>There was an error moving the uploaded file.</p>";
            }
        } else {
            echo "<p style='color:red;'>Upload failed. Allowed file types: " . implode(', ', $allowedExtensions) . "</p>";
        }
    }

    // Update query including image path
    $update = $conn->prepare("UPDATE products 
                              SET product_name = ?, unit_price = ?, expiry_date = ?, description = ?, image_path = ? 
                              WHERE id = ?");
    $update->execute([$product_name, $unit_price, $expiry_date, $description, $image_path, $id]);

    header("Location: product_view.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Edit Product</h2>
    <form method="POST" enctype="multipart/form-data">
        <label>Product Name:</label>
        <input type="text" name="product_name" value="<?= htmlspecialchars($product['product_name']) ?>" required><br><br>

        <label>Unit Price:</label>
        <input type="number" step="0.01" name="unit_price" value="<?= htmlspecialchars($product['unit_price']) ?>" required><br><br>

        <label>Expiry Date:</label>
        <input type="date" name="expiry_date" value="<?= htmlspecialchars($product['expiry_date']) ?>"><br><br>

        <label>Description:</label><br>
        <textarea name="description"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

        <label>Product Image:</label><br>
        <?php if (!empty($product['image_path'])): ?>
            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image" style="max-width:150px; max-height:150px;"><br>
        <?php endif; ?>
        <input type="file" name="product_image" accept="image/*"><br><br>

        <button type="submit">Update Product</button>
        <a href="product_view.php">Cancel</a>
    </form>
</div>
</body>
</html>
