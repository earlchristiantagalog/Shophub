<?php
require '../includes/db.php';
require 'vendor/autoload.php';

if (!isset($_GET['order_id']))
    die('No order specified.');
$order_id = $_GET['order_id'];

/* 🧩 Fetch Order Details */
$stmt = $conn->prepare("
    SELECT o.order_id, o.total, o.status, o.order_date, o.payment_method, o.shipping_method,
           oi.quantity, o.shipping_fee,
          a.first_name, a.last_name, u.phone,
           a.address_line_1, a.barangay, a.city, a.province, a.region, a.zip_code
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN addresses a ON o.address_id = a.address_id
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_id = ?
");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0)
    die('Order not found.');
$order = $result->fetch_assoc();
$stmt->close();

/* 🧩 Fetch Tracking Number */
$track_stmt = $conn->prepare("SELECT tracking_number FROM tracking WHERE order_id = ? LIMIT 1");
$track_stmt->bind_param("s", $order_id);
$track_stmt->execute();
$track_result = $track_stmt->get_result();
$tracking_number = $track_result->num_rows > 0 ? $track_result->fetch_assoc()['tracking_number'] : 'PENDING';
$track_stmt->close();

/* 🧩 Seller Info */
$seller_name = "Shophub";
$seller_phone = "09940823693";
$seller_address = "Malunhaw St. Barangay Pulpogan, Consolacion, Cebu";

/* 🧩 Delivery ETA Function */
function getEstimatedDelivery($method, $date)
{
    $method = strtolower(trim($method));
    switch ($method) {
        case 'standard':
        case 'standard delivery':
            return date('M d', strtotime($date . ' +3 days')) . '–' . date('d', strtotime($date . ' +5 days'));
        case 'express':
        case 'express delivery':
            return date('M d', strtotime($date . ' +2 days'));
        default:
            return "N/A";
    }
}
$estimated = getEstimatedDelivery($order['shipping_method'], $order['order_date']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Waybill - <?= htmlspecialchars($order['order_id']) ?></title>
<style>
    @page { size: A6 portrait; margin: 0; }

    html, body {
        width: 105mm;
        height: 148mm;
        margin: 0;
        padding: 0;
        font-family: 'Arial', sans-serif;
        background: #fff;
        font-size: 11px;
        color: #222;
    }

    body { display: flex; justify-content: center; align-items: center; }

    .waybill {
        width: 100%;
        height: 100%;
        border: 1.5px solid #000;
        padding: 6px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    /* HEADER */
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1.5px solid #000;
        padding-bottom: 4px;
        margin-bottom: 4px;
    }

    .header .logo {
        font-size: 16px;
        font-weight: 900;
        color: #d32f2f;
    }

    .header .order-info {
        text-align: right;
        font-size: 10px;
    }

    .order-info div { line-height: 1.2; }

    /* SHIP INFO */
    .ship-info {
        display: flex;
        justify-content: space-between;
        margin-bottom: 4px;
    }

    .ship-box {
        width: 49%;
        border: 1px solid #000;
        border-radius: 3px;
        padding: 4px;
    }

    .ship-box strong { font-size: 12px; }
    .ship-title {
        font-weight: bold;
        font-size: 11px;
        margin-bottom: 2px;
        background: #000;
        color: #fff;
        padding: 2px 4px;
        border-radius: 2px;
    }

    /* BARCODE / QR */
    .barcode-box {
        text-align: center;
        margin: 4px 0;
        border-bottom: 1px solid #000;
        padding-bottom: 4px;
    }

    .barcode-box img {
        width: 130px;
        height: 130px;
        border: 1px solid #000;
        padding: 3px;
        margin-bottom: 2px;
    }

    .tracking-number { font-weight: bold; font-size: 14px; margin-bottom: 2px; }
    .order-id { font-size: 10px; color: #555; }

    /* DETAILS GRID */
    .details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2px 6px;
        border: 1px solid #000;
        border-radius: 3px;
        padding: 4px;
        margin-bottom: 4px;
    }

    .details div strong { display: inline-block; width: 50px; }

    /* FOOTER */
    .footer {
        font-size: 9px;
        border-top: 1px solid #000;
        padding-top: 2px;
        text-align: center;
        color: #555;
    }

    /* BUTTON HIDDEN PRINT */
    @media print { .pdf-button { display: none; } }

    .pdf-button {
        display: block;
        background: #000;
        color: #fff;
        border-radius: 4px;
        padding: 5px;
        text-align: center;
        font-size: 11px;
        margin-top: 4px;
        text-decoration: none;
    }
</style>
</head>
<body>
<div class="waybill" id="content">

    <!-- HEADER -->
    <div class="header">
        <div class="logo">Shophub Express</div>
        <div class="order-info">
            <div>Order ID: <?= htmlspecialchars($order['order_id']) ?></div>
            <div>RTS: SHB-C254-BNA</div>
            <div>Region: CEB-BN</div>
        </div>
    </div>

    <!-- SHIP INFO -->
    <div class="ship-info">
        <div class="ship-box">
            <div class="ship-title">SHIP TO</div>
            <div><strong><?= htmlspecialchars($order['first_name'].' '.$order['last_name']) ?></strong></div>
            <div><?= htmlspecialchars($order['address_line_1']) ?>, <?= htmlspecialchars($order['barangay']) ?></div>
            <div><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?></div>
            <div><?= htmlspecialchars($order['region']) ?> <?= htmlspecialchars($order['zip_code']) ?></div>
            <div style="font-size: large; color: black;"><?= htmlspecialchars($order['phone']) ?></div>
        </div>
        <div class="ship-box">
            <div class="ship-title">SHIP FROM</div>
            <div><strong><?= htmlspecialchars($seller_name) ?></strong></div>
            <div><?= htmlspecialchars($seller_address) ?></div>
            <div style="font-size: large; color: black;"><?= htmlspecialchars($seller_phone) ?></div>
        </div>
    </div>

    <!-- BARCODE / QR -->
    <div class="barcode-box">
        <?php if ($tracking_number !== 'PENDING'): ?>
            <img src="qr.php?code=<?= urlencode($tracking_number) ?>" alt="QR Code">
        <?php else: ?>
            <div style="width:130px;height:130px;border:1px solid #000;margin:0 auto;
            display:flex;align-items:center;justify-content:center;font-size:12px;">NO QR</div>
        <?php endif; ?>
        <div class="tracking-number"><?= htmlspecialchars($tracking_number) ?></div>
        <div class="order-id">Order Date: <?= date('M d, Y', strtotime($order['order_date'])) ?></div>
    </div>

    <!-- DETAILS -->
    <div class="details">
        <div><strong>Method:</strong> <?= htmlspecialchars($order['shipping_method']) ?></div>
        <div><strong>Amount:</strong> ₱<?= number_format($order['total'], 2) ?></div>
        <div><strong>ETA:</strong> <?= htmlspecialchars($estimated) ?></div>
        <div><strong>Fee:</strong> ₱<?= number_format($order['shipping_fee'], 2) ?></div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        Shophub Express • Waybill for delivery
    </div>

    <a href="#" class="pdf-button" id="downloadBtn" data-order-id="<?= htmlspecialchars($order['order_id']) ?>">Download PDF</a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.getElementById("downloadBtn").addEventListener("click", async function(e){
    e.preventDefault();
    const btn = this;
    btn.style.display = "none";

    const content = document.getElementById("content");
    const { jsPDF } = window.jspdf;

    const canvas = await html2canvas(content, { scale: 3, useCORS: true });
    const imgData = canvas.toDataURL("image/png");

    const pdf = new jsPDF({ orientation: "portrait", unit: "mm", format: [105,148] });
    pdf.addImage(imgData, "PNG", 0, 0, 105, 148);
    pdf.save(`Waybill_${btn.dataset.orderId}.pdf`);

    setTimeout(() => btn.style.display = "block", 600);
});
</script>
</body>
</html>
