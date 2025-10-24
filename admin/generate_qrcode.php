<?php
require 'vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

if (isset($_GET['text'])) {
    $text = $_GET['text'];
    $filename = 'qr_' . time() . '.png'; // Unique filename
    $filepath = 'uploads/' . $filename;

    $result = Builder::create()
        ->data($text)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(ErrorCorrectionLevel::High)
        ->size(150)
        ->margin(10)
        ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
        ->foregroundColor(new Color(0, 0, 0))
        ->backgroundColor(new Color(255, 255, 255))
        ->build();

    // Save to file
    file_put_contents($filepath, $result->getString());

    // Return path for use in receipt
    echo json_encode([
        'status' => 'success',
        'filename' => $filename
    ]);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'QR code text missing.']);
}
