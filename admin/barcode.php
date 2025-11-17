<?php
// MUST be first — no spaces or BOM before this line!
declare(strict_types=1);

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../vendor/autoload.php'; 

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Disable warnings; output only PNG
error_reporting(E_ERROR | E_PARSE);
ob_clean();
header('Content-Type: image/png');

// Get tracking code
$trackingCode = $_GET['code'] ?? '';
$trackingCode = preg_replace('/[^A-Za-z0-9\-_]/', '', $trackingCode);

// If missing tracking code → return simple error image
if (empty($trackingCode)) {
    $im = imagecreatetruecolor(200, 200);
    $bg = imagecolorallocate($im, 255, 255, 255);
    $textc = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 200, 200, $bg);
    imagestring($im, 4, 20, 90, 'Invalid QR Code', $textc);
    imagepng($im);
    imagedestroy($im);
    exit;
}

try {

    $options = new QROptions([
        'version' => 7,
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => QRCode::ECC_M,
        'scale' => 6,     // QR size
        'imageBase64' => false, // we output raw PNG
    ]);

    $qr = (new QRCode($options))->render($trackingCode);

    echo $qr; // Output PNG
    exit;

} catch (Exception $e) {

    // Fallback error QR image
    $im = imagecreatetruecolor(200, 200);
    $bg = imagecolorallocate($im, 255, 255, 255);
    $textc = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 200, 200, $bg);
    imagestring($im, 4, 20, 90, 'QR Error', $textc);
    imagepng($im);
    imagedestroy($im);
}
