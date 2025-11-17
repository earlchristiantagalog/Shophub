<?php
declare(strict_types=1); // MUST be first

include 'db.php'; 
require __DIR__ . '/vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

header('Content-Type: image/png');
header('Access-Control-Allow-Origin: *'); // Needed for html2canvas & PDF

$code = $_GET['code'] ?? '';
$code = trim($code);

// If no code or PENDING, generate placeholder
if ($code === '' || strtoupper($code) === 'PENDING') {
    $im = imagecreatetruecolor(200, 200);
    $white = imagecolorallocate($im, 255, 255, 255);
    $black = imagecolorallocate($im, 0, 0, 0);
    imagefilledrectangle($im, 0, 0, 200, 200, $white);
    imagestring($im, 5, 60, 90, "NO QR", $black);
    imagepng($im);
    imagedestroy($im);
    exit;
}

$options = new QROptions([
    'version'     => 6,
    'outputType'  => QRCode::OUTPUT_IMAGE_PNG,
    'eccLevel'    => QRCode::ECC_M,
    'scale'       => 6,
    'imageBase64' => false,
]);

echo (new QRCode($options))->render($code);
exit;
