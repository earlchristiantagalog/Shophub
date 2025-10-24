<?php
require 'vendor/autoload.php';
require 'db.php';

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Picqer\Barcode\BarcodeGeneratorPNG;

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order info
    $sql = "SELECT o.*, u.username, u.email, a.phone, a.address_line_1, a.barangay, a.city, a.province, a.region
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN addresses a ON o.address_id = a.address_id
            WHERE o.order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Fetch ordered products
    $stmt = $conn->prepare("SELECT product_name, quantity, price FROM order_items WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Generate QR Code (for tracking)
    $qrCode = QrCode::create("http://yourdomain.com/track_order.php?order_id=$order_id")
        ->setSize(100)
        ->setMargin(0);
    $writer = new PngWriter();
    $qrImage = $writer->write($qrCode);
    $qrDataUri = $qrImage->getDataUri();

    // Generate Barcode (tracking number)
    $generator = new BarcodeGeneratorPNG();
    $barcode = base64_encode($generator->getBarcode($order['tracking_number'], $generator::TYPE_CODE_128));

    // Totals
    $totalQty = array_sum(array_column($products, 'quantity'));
    $totalWeight = $totalQty * 1; // Assume 1kg per item for example
?>
<!DOCTYPE html>
<html>
<head>
    <title>Delivery Label - <?= htmlspecialchars($order_id) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            width: 1024px;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 10px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        .header-left {
            font-size: 14px;
        }
        .header-right {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
        }
        .barcode {
            text-align: center;
            margin: 10px 0;
        }
        .barcode img {
            height: 60px;
        }
        .buyer-box {
            border: 1px solid #000;
            padding: 10px;
            margin: 10px 0;
            font-size: 14px;
        }
        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            background: #f3f3f3;
            padding: 5px;
            border-bottom: 1px solid #000;
        }
        .info {
            margin-top: 3px;
            line-height: 1.4;
        }
        .cod-box {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
            padding: 8px 5px;
            font-weight: bold;
            margin-top: 10px;
        }
        .attempt-box {
            display: flex;
            justify-content: space-around;
            margin-top: 10px;
            border-top: 1px solid #000;
            padding-top: 8px;
        }
        .attempt-box div {
            border: 1px solid #000;
            padding: 10px 20px;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 15px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <div class="header-left">
            <img src="Shophub.png" alt="Logo" style="height:40px;"><br>
            <strong><?= htmlspecialchars($order['region']) ?></strong><br>
            Send Date: <?= date('Y-m-d', strtotime($order['order_date'])) ?>
        </div>
        <div class="header-right">
            <?= htmlspecialchars($order['tracking_number']) ?><br>
            <span style="font-size:12px;">Order ID: <?= htmlspecialchars($order['order_id']) ?></span>
        </div>
    </div>

    <div class="barcode">
        <img src="data:image/png;base64,<?= $barcode ?>" alt="Barcode"><br>
        <?= htmlspecialchars($order['tracking_number']) ?>
    </div>

    <div class="buyer-box">
        <div class="section-title">BUYER</div>
        <div class="info">
            <strong><?= htmlspecialchars($order['username']) ?></strong><br>
            <?= htmlspecialchars($order['address_line_1']) ?>, <?= htmlspecialchars($order['barangay']) ?><br>
            <?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?><br>
            <?= htmlspecialchars($order['region']) ?><br>
            📞 <?= htmlspecialchars($order['phone']) ?>
        </div>
    </div>

    <div class="cod-box">
        <div>Product Quantity: <?= $totalQty ?></div>
        <div>Weight: <?= $totalWeight ?> kg</div>
        <div>COD Amount: ₱<?= number_format($order['total'], 2) ?></div>
    </div>

    <div class="barcode" style="margin-top:15px;">
        <img src="<?= $qrDataUri ?>" alt="QR Code"><br>
        <small>Scan to track order</small>
    </div>

    <div class="attempt-box">
        <div>
            <div>Delivery Attempt</div>
            <div>1</div>
            <div>2</div>
        </div>
        <div>
            <div>Return Attempt</div>
            <div>1</div>
            <div>2</div>
        </div>
    </div>

    <div class="footer">
        <p>Thank you for shopping with <strong>Shophub</strong>!</p>
    </div>
</body>
</html>
<?php } ?>
