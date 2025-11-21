<?php
require 'db.php';
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

$generator = new BarcodeGeneratorPNG();

// Fetch all products
$products = $conn->query("
    SELECT product_id, name, price, stock, category, status, description, created_at, sold 
    FROM products
");

while ($product = $products->fetch_assoc()) {

    // Skip if already exists
    $check = $conn->query("SELECT 1 FROM inventory WHERE product_id=" . $product['product_id']);
    if ($check->num_rows > 0) continue;

    // Insert new inventory record
    $stmt = $conn->prepare("
        INSERT INTO inventory 
            (product_id, name, price, stock, category, status, description, created_at, sold)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
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

    // Generate Code128 barcode
    $barcodeData = $generator->getBarcode($product['product_id'], $generator::TYPE_CODE_128);

    if (!is_dir('uploads/barcodes')) {
        mkdir('uploads/barcodes', 0777, true);
    }

    file_put_contents('uploads/barcodes/' . $product['product_id'] . '.png', $barcodeData);
}

// Redirect automatically
header("Location: inventory.php");
include 'includes/header.php';
exit;

?>
