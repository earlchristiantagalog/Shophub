<?php
// MUST be first — no spaces or BOM before this line!
declare(strict_types=1);

require __DIR__ . 'db.php';
require __DIR__ . 'vendor/autoload.php'; // <-- FIXED: correct vendor path

use Picqer\Barcode\BarcodeGeneratorPNG;

// Disable all output except the image
error_reporting(E_ERROR | E_PARSE);
ob_clean();  
header('Content-Type: image/png');

// Get tracking code
$trackingCode = $_GET['code'] ?? '';
$trackingCode = preg_replace('/[^A-Za-z0-9\-_]/', '', $trackingCode);

// Handle missing tracking code
if (empty($trackingCode)) {
    $im = imagecreatetruecolor(300, 60);
    $bg = imagecolorallocate($im, 255, 255, 255);
    $textc = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 300, 60, $bg);
    imagestring($im, 3, 10, 20, 'Invalid tracking code', $textc);
    imagepng($im);
    imagedestroy($im);
    exit;
}

try {
    $generator = new BarcodeGeneratorPNG();

    // CODE 128 always works with A-Z, 0-9, -, _
    $barcode = $generator->getBarcode(
        $trackingCode,
        $generator::TYPE_CODE_128,
        2,   // scale
        60   // height
    );

    echo $barcode;
    exit;

} catch (Exception $e) {
    // Fallback: display error text
    $im = imagecreatetruecolor(300, 60);
    $bg = imagecolorallocate($im, 255, 255, 255);
    $textc = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 300, 60, $bg);
    imagestring($im, 3, 10, 20, 'Error generating barcode', $textc);
    imagepng($im);
    imagedestroy($im);
}
