<?php
require 'db.php';
require 'vendor/autoload.php';

use Picqer\Barcode\BarcodeGeneratorPNG;

if(!isset($_GET['id'])) exit;

$product_id = $_GET['id'];

$generator = new BarcodeGeneratorPNG();
header('Content-Type: image/png');

// Output the barcode directly
echo $generator->getBarcode($product_id, $generator::TYPE_CODE_128);
