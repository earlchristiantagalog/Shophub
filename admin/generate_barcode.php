<?php
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

if (isset($_GET['text'])) {
    $text = $_GET['text'];
    $filename = 'barcode_' . time() . '.png';
    $filepath = 'barcodes/' . $filename;

    $generator = new BarcodeGeneratorPNG();
    $barcode = $generator->getBarcode($text, $generator::TYPE_CODE_128, 2, 50);

    // Save the image to the file
    file_put_contents($filepath, $barcode);

    echo json_encode([
        'status' => 'success',
        'filename' => $filename
    ]);
} else {
    echo json_encode(['status' => 'error']);
}
