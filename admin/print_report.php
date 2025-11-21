<?php
include 'db.php';
include 'vendor/autoload.php';

// Store details
$store_name = "Shophub Electronics and Gadgets";
$store_address = "D4 Malunhaw St., Purok Raphael Palma, Pulpogan, Consolacion, Cebu";
$store_contact = "Phone: 0916-821-8393 | Email: shophub@gmail.com";

// Date range filter
$from = $_GET['from'] ?? date('Y-m-01');
$to   = $_GET['to'] ?? date('Y-m-d');

// Overall Sales Summary
$sales = $conn->query("SELECT 
    COUNT(*) AS total_products,
    SUM(stock) AS total_stock,
    SUM(sold) AS total_sold,
    SUM(sold * price) AS total_revenue
    FROM inventory
    WHERE DATE(created_at) BETWEEN '$from' AND '$to'");
$sales_data = $sales->fetch_assoc();

// Additional Sales Metrics
$today_sales = $conn->query("SELECT SUM(sold * price) AS revenue_today, SUM(sold) AS sold_today
    FROM inventory WHERE DATE(created_at) = CURDATE()");
$today = $today_sales->fetch_assoc();

$week_sales = $conn->query("SELECT SUM(sold * price) AS revenue_week, SUM(sold) AS sold_week
    FROM inventory WHERE YEARWEEK(created_at,1) = YEARWEEK(CURDATE(),1)");
$week = $week_sales->fetch_assoc();

$month_sales = $conn->query("SELECT SUM(sold * price) AS revenue_month, SUM(sold) AS sold_month
    FROM inventory WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
$month = $month_sales->fetch_assoc();

// Inventory Data
$result = $conn->query("SELECT i.*, pi.image_path AS primary_image
    FROM inventory i
    LEFT JOIN product_images pi
    ON i.product_id = pi.product_id AND pi.is_primary = 1
    WHERE DATE(i.created_at) BETWEEN '$from' AND '$to'
    ORDER BY i.product_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sales & Inventory Report</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
/* Landscape A4 Setup */
@page { size: A4 landscape; margin: 15mm; }
body { font-family: Arial, sans-serif; font-size: 12pt; color: #333; padding: 0; }
.no-print { display: none !important; }

/* Header */
.report-header { text-align: center; margin-bottom: 15px; }
.report-header h2 { margin: 0; font-weight: bold; }
.report-header p { margin: 0; }

/* Summary Table */
.table-summary th, .table-summary td { text-align: center; font-weight: bold; padding: 5px; }

/* Inventory Table */
.table-inventory th, .table-inventory td { border: 1px solid #444 !important; padding: 5px; vertical-align: middle; }
.table-inventory th { background-color: #f5f5f5; }
.table-inventory tbody tr:nth-child(even) { background-color: #f9f9f9; }
.table-danger { background-color: #f8d7da !important; }

/* Images */
img { max-width: 60px; height: auto; }

/* Print adjustments */
@media print {
    .no-print { display: none !important; }
    thead { display: table-header-group; }
    tfoot { display: table-footer-group; }
}
</style>
</head>
<body>

<div class="container-fluid">

    <!-- Header / Store Info -->
    <div class="report-header">
        <h2><?= $store_name ?></h2>
        <p><?= $store_address ?></p>
        <p><?= $store_contact ?></p>
        <h4>Sales & Inventory Report</h4>
        <p>From: <?= date('M d, Y', strtotime($from)) ?> To: <?= date('M d, Y', strtotime($to)) ?></p>
        <button class="btn btn-secondary no-print mb-3" onclick="window.print()">Print</button>
    </div>

    <!-- Summary Table -->
    <table class="table table-bordered table-summary mb-4">
        <thead>
            <tr>
                <th>Total Products</th>
                <th>Total Stock</th>
                <th>Total Sold</th>
                <th>Total Revenue</th>
                <th>Sold Today</th>
                <th>Revenue Today</th>
                <th>Sold This Week</th>
                <th>Revenue This Week</th>
                <th>Sold This Month</th>
                <th>Revenue This Month</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= $sales_data['total_products'] ?? 0 ?></td>
                <td><?= $sales_data['total_stock'] ?? 0 ?></td>
                <td><?= $sales_data['total_sold'] ?? 0 ?></td>
                <td>₱<?= number_format($sales_data['total_revenue'] ?? 0,2) ?></td>
                <td><?= $today['sold_today'] ?? 0 ?></td>
                <td>₱<?= number_format($today['revenue_today'] ?? 0,2) ?></td>
                <td><?= $week['sold_week'] ?? 0 ?></td>
                <td>₱<?= number_format($week['revenue_week'] ?? 0,2) ?></td>
                <td><?= $month['sold_month'] ?? 0 ?></td>
                <td>₱<?= number_format($month['revenue_month'] ?? 0,2) ?></td>
            </tr>
        </tbody>
    </table>

    <!-- Inventory Table -->
    <table class="table table-bordered table-inventory">
        <thead>
            <tr>
                <th>#</th>
                <th>Image</th>
                <th>Product</th>
                <th>Category</th>
                <th>Stock</th>
                <th>Sold</th>
                <th>Revenue</th>
                <th>Status</th>
                <th>Barcode</th>
                <th>Date Added</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $count = 1;
        if($result->num_rows > 0):
            while($row = $result->fetch_assoc()):
                $revenue = $row['sold'] * $row['price'];
                $row_class = ($row['stock'] < 5) ? 'table-danger' : '';
        ?>
            <tr class="<?= $row_class ?>">
                <td><?= $count++ ?></td>
                <td>
                    <?php if($row['primary_image'] && file_exists('uploads/'.$row['primary_image'])): ?>
                        <img src="uploads/<?= $row['primary_image'] ?>">
                    <?php else: ?>
                        <span class="text-muted">No image</span>
                    <?php endif; ?>
                </td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['category'] ?></td>
                <td><?= $row['stock'] ?></td>
                <td><?= $row['sold'] ?></td>
                <td>₱<?= number_format($revenue,2) ?></td>
                <td><?= $row['status'] == 0 ? 'Available': 'Not Available'?></td>
                <td><img src="barcode.php?id=<?= $row['product_id'] ?>" width="100"></td>
                <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr>
                <td colspan="10" class="text-center">No items found.</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Prepared By / Reviewed By -->
    <div class="mt-5" style="width: 100%; margin-top: 40px;">
        <table style="width: 100%; border: none; margin-top: 40px;">
            <tr>
                <td style="width: 50%; text-align: center; padding-top: 50px;">
                    ________________________________ <br>
                    <strong>Prepared By</strong><br>
                    <span style="font-size: 11pt;">(Name & Signature)</span>
                </td>
                <td style="width: 50%; text-align: center; padding-top: 50px;">
                    ________________________________ <br>
                    <strong>Reviewed By</strong><br>
                    <span style="font-size: 11pt;">(Name & Signature)</span>
                </td>
            </tr>
        </table>
    </div>

</div> <!-- container-fluid -->

</body>
</html>

