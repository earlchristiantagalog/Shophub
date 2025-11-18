<?php
include 'includes/header.php';
require 'db.php';
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

$generator = new BarcodeGeneratorPNG();

// Fetch all products from products table
$products = $conn->query("SELECT product_id, name, price, stock, category, status, description, created_at, sold FROM products");

while($product = $products->fetch_assoc()) {

    // Skip if product already exists in inventory
    $check = $conn->query("SELECT * FROM inventory WHERE product_id=".$product['product_id']);
    if($check->num_rows > 0) continue;

    // Insert into inventory
    $stmt = $conn->prepare("INSERT INTO inventory (product_id, name, price, stock, category, status, description, created_at, sold) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "isdisissi",
        $product['product_id'],
        $product['name'],
        $product['price'],
        $product['stock'],
        $product['category'],
        $product['status'],
        $product['description'],
        $product['created_at'],
        $product['sold']
    );
    $stmt->execute();

    // Generate Code128 barcode based on product_id
    $barcodeData = $generator->getBarcode($product['product_id'], $generator::TYPE_CODE_128);

    if(!is_dir('uploads/barcodes')) mkdir('uploads/barcodes', 0777, true);
    file_put_contents('uploads/barcodes/'.$product['product_id'].'.png', $barcodeData);
}

echo "<div class='container mt-4'>";
echo "<h3>Products fetched and added to inventory with Code128 barcodes successfully!</h3>";
echo "<a href='inventory.php' class='btn btn-primary mt-3'>Go to Inventory</a>";
echo "</div>";

include 'includes/footer.php';
?>
