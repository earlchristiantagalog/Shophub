<?php
require '../includes/db.php'; // adjust path to your db.php
require 'vendor/autoload.php'; // Include Composer's autoloader

// Make sure there's no whitespace or echo before this line.
if (!isset($_GET['order_id'])) die('No order specified.');
$order_id = intval($_GET['order_id']);

// Fetch order info from the database
$stmt = $conn->prepare("
    SELECT o.order_id, o.total, o.status, o.order_date, o.payment_method, o.shipping_method,
           oi.quantity, o.shipping_fee,
           u.username, u.phone,
           a.address_line_1, a.barangay, a.city, a.province, a.region, a.zip_code
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN addresses a ON o.address_id = a.address_id
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_id = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$orderResult = $stmt->get_result();
if ($orderResult->num_rows === 0) die('Order not found.');
$order = $orderResult->fetch_assoc();
$stmt->close();

// Seller Info
$seller_name = "Shophub";
$seller_phone = '09940823693';
$seller_address = "Malunhaw St. Barangay Pulpogan, Consolacion, Cebu";

// TCPDF setup
$pdf = new TCPDF();

// Set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Shophub');
$pdf->SetTitle('Waybill Receipt');
$pdf->SetSubject('Waybill for Order #' . $order['order_id']);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 12);

// Title
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Waybill Receipt', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);

// Shipping method
$pdf->Cell(0, 10, 'Shipping Method: ' . $order['shipping_method'], 0, 1, 'C');

// Waybill Number
$pdf->Ln(5);
$pdf->Cell(0, 10, 'Order No.: ' . $order['order_id'], 0, 1, 'C');

// Sender Information
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Sender Information', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Name: ' . $seller_name, 0, 1, 'L');
$pdf->Cell(0, 10, 'Phone: ' . $seller_phone, 0, 1, 'L');
$pdf->Cell(0, 10, 'Address: ' . $seller_address, 0, 1, 'L');

// Recipient Information
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Recipient Information', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Name: ' . $order['username'], 0, 1, 'L');
$pdf->Cell(0, 10, 'Phone: ' . $order['phone'], 0, 1, 'L');
$pdf->Cell(0, 10, 'Address: ' . $order['address_line_1'] . ', ' . $order['barangay'] . ', ' . $order['city'] . ', ' . $order['province'] . ', ' . $order['region'] . ', ' . $order['zip_code'], 0, 1, 'L');

// Package Details
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, 'Package Details', 0, 1, 'L');
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, 'Shipping Fee: ₱' . number_format($order['shipping_fee'], 2), 0, 1, 'L');
$pdf->Cell(0, 10, 'Order Date: ' . date('F d, Y', strtotime($order['order_date'])), 0, 1, 'L');
$pdf->Cell(0, 10, 'Estimated Delivery: ' . date('F d, Y', strtotime($order['order_date'] . ' +2 days')), 0, 1, 'L');

// Terms and Conditions
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 10);
$pdf->MultiCell(0, 10, "Terms & Conditions:\nSubject to carrier's terms and conditions.\nFor inquiries: support@expressdelivery.ph", 0, 'L');

// Output the PDF to browser
$pdf->Output('waybill_receipt_' . $order['order_id'] . '.pdf', 'I');

// Clear output buffer
ob_clean();
flush();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waybill Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            width: 100%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            background-color: #667eea;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .header h1 {
            margin: 0;
        }

        .section {
            margin: 20px 0;
        }

        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .info-box label {
            font-size: 12px;
            font-weight: bold;
            color: #666;
        }

        .info-box p {
            font-size: 14px;
            color: #333;
        }

        .waybill-number {
            text-align: center;
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
            border-radius: 0 0 10px 10px;
        }

        .barcode {
            text-align: center;
            margin-top: 20px;
            font-family: 'Courier New', monospace;
            font-size: 20px;
            letter-spacing: 2px;
            color: #333;
        }

        .pdf-button {
            display: block;
            background-color: #667eea;
            color: white;
            padding: 10px 20px;
            text-align: center;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            margin: 20px 0;
            cursor: pointer;
            text-decoration: none;
        }

        .pdf-button:hover {
            background-color: #5560b2;
        }

        /* Dashed line style for Package Details */
        .dashed-line {
            border-top: 2px dashed #667eea;
            margin: 20px 0;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .container {
                box-shadow: none;
                max-width: 100%;
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>
    <div class="container" id="content">
        <div class="header">
            <h1>Waybill Receipt</h1>
            <p><?= htmlspecialchars($order['shipping_method']) ?> Delivery</p>
        </div>

        <div class="waybill-number">
            <strong>Order ID:</strong><?= $order['order_id'] ?>
        </div>

        <div class="section">
            <div class="section-title">Sender Information</div>
            <div class="info-grid">
                <div class="info-box">
                    <label>Name</label>
                    <p><?= htmlspecialchars($seller_name) ?></p>
                </div>
                <div class="info-box">
                    <label>Phone</label>
                    <p><?= htmlspecialchars($seller_phone) ?></p>
                </div>
                <div class="info-box" style="grid-column: span 2;">
                    <label>Address</label>
                    <p><?= htmlspecialchars($seller_address) ?></p>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-title">Recipient Information</div>
            <div class="info-grid">
                <div class="info-box">
                    <label>Name</label>
                    <p><?= htmlspecialchars($order['username']) ?></p>
                </div>
                <div class="info-box">
                    <label>Phone</label>
                    <p><?= htmlspecialchars($order['phone']) ?></p>
                </div>
                <div class="info-box" style="grid-column: span 2;">
                    <label>Address</label>
                    <p><?= htmlspecialchars($order['address_line_1'] . ', ' . $order['barangay'] . ', ' . $order['city'] . ', ' . $order['province'] . ', ' . $order['region'] . ', ' . $order['zip_code']) ?></p>
                </div>
            </div>
        </div>

        <div class="dashed-line"></div>

        <div class="section">
            <div class="section-title">Package Details</div>
            <div class="info-grid">
                <div class="info-box">
                    <label>Shipping Method</label>
                    <p><?= htmlspecialchars($order['shipping_method']) ?></p>
                </div>
                <div class="info-box">
                    <label>Shipping Fee</label>
                    <p>₱<?= number_format($order['shipping_fee'], 2) ?></p>
                </div>
                <div class="info-box">
                    <label>Order Date</label>
                    <p><?= date('F d, Y', strtotime($order['order_date'])) ?></p>
                </div>
                <div class="info-box">
                    <label>Estimated Delivery</label>
                    <p><?= date('F d, Y', strtotime($order['order_date'] . ' +2 days')) ?></p>
                </div>
            </div>
        </div>

        <div class="barcode">
            <p>WB<?= $order['order_id'] ?></p>
        </div>

        <div class="footer">
            <p><strong>Terms & Conditions:</strong> Subject to carrier's terms and conditions.</p>
            <p>For inquiries: support@expressdelivery.ph</p>
        </div>

        <!-- Updated Download PDF Button -->
        <a href="#" class="pdf-button" id="downloadBtn" data-order-id="<?= $order['order_id'] ?>">Download PDF</a>
    </div>

    <script>
        document.getElementById('downloadBtn').addEventListener('click', function(event) {
            event.preventDefault();
            const orderId = this.getAttribute('data-order-id');
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF();

            // Use jsPDF's html method to capture and render the content into the PDF
            doc.html(document.getElementById('content'), {
                callback: function(doc) {
                    doc.save(`waybill_receipt_${orderId}.pdf`);
                },
                margin: [10, 10, 10, 10],
                x: 10,
                y: 10
            });
        });
    </script>
</body>

</html>