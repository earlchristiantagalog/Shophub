<?php
include 'db.php';
include 'vendor/autoload.php';

// Store details
$store_name = "Shophub Electronics and Gadgets";
$store_address = "D4 Malunhaw St., Purok Raphael Palma, Pulpogan, Consolacion, Cebu";
$store_contact = "Phone: 0916-821-8393 | Email: shophub@gmail.com";

// Date range filter
$from = $_GET['from'] ?? date('Y-m-01');
$to = $_GET['to'] ?? date('Y-m-d');

/* Overall totals */
$sales_q = "
    SELECT
        COUNT(*) AS total_products,
        IFNULL(SUM(stock),0) AS total_stock,
        IFNULL(SUM(sold),0) AS total_sold,
        IFNULL(SUM(sold * price),0) AS total_revenue
    FROM products
";
$sales_res = $conn->query($sales_q);
$sales_data = $sales_res ? $sales_res->fetch_assoc() : [
    'total_products' => 0,
    'total_stock' => 0,
    'total_sold' => 0,
    'total_revenue' => 0
];


// Additional metrics
/* Today */
$today = "
    SELECT
        IFNULL(SUM(oi.quantity),0) AS sold_today,
        IFNULL(SUM(oi.quantity * oi.price),0) AS revenue_today
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    WHERE o.status = 'Delivered'
      AND DATE(o.order_date) = CURDATE()
";
$today_res = $conn->query($today);
$today = $today_res ? $today_res->fetch_assoc() : ['sold_today' => 0, 'revenue_today' => 0];

/* This week (ISO week, YEARWEEK with mode 1) */
$week = "
    SELECT
        IFNULL(SUM(oi.quantity),0) AS sold_week,
        IFNULL(SUM(oi.quantity * oi.price),0) AS revenue_week
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    WHERE o.status = 'Delivered'
      AND YEARWEEK(o.order_date, 1) = YEARWEEK(CURDATE(), 1)
";
$week_res = $conn->query($week);
$week = $week_res ? $week_res->fetch_assoc() : ['sold_week' => 0, 'revenue_week' => 0];

/* This month */
$month = "
    SELECT
        IFNULL(SUM(oi.quantity),0) AS sold_month,
        IFNULL(SUM(oi.quantity * oi.price),0) AS revenue_month
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    WHERE o.status = 'Delivered'
      AND MONTH(o.order_date) = MONTH(CURDATE())
      AND YEAR(o.order_date) = YEAR(CURDATE())
";
$month_res = $conn->query($month);
$month = $month_res ? $month_res->fetch_assoc() : ['sold_month' => 0, 'revenue_month' => 0];

$result = $conn->query("
    SELECT p.*, pi.image_path AS primary_image
    FROM products p
    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_primary = 1
    ORDER BY p.created_at DESC
");


// Recently added products
$recent_added = $conn->query("SELECT i.*, pi.image_path AS primary_image FROM inventory i LEFT JOIN product_images pi ON i.product_id = pi.product_id AND pi.is_primary = 1 WHERE DATE(i.created_at) BETWEEN '$from' AND '$to' ORDER BY i.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Sales & Inventory Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Landscape setup */
        @page {
            size: A4 landscape;
            margin: 15mm;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12pt;
            color: #333;
        }

        .no-print {
            display: none !important;
        }

        /* Header */
        .report-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .report-header h2 {
            font-weight: bold;
            color: #2c3e50;
        }

        .report-header p {
            margin: 0;
            font-size: 10pt;
            color: #555;
        }

        /* Summary Cards */
        .summary-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 30px;
        }

        .summary-card {
            flex: 1;
            min-width: 150px;
            padding: 20px;
            border-radius: 12px;
            color: #fff;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .summary-card i {
            font-size: 2rem;
            position: absolute;
            top: 10px;
            right: 10px;
            opacity: 0.2;
        }

        .summary-card h5 {
            margin-bottom: 8px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .summary-card p {
            margin: 0;
            font-size: 1.4rem;
            font-weight: bold;
        }

        /* Gradient colors */
        .bg-primary {
            background: linear-gradient(135deg, #1d62f0, #6a8ff2);
        }

        .bg-success {
            background: linear-gradient(135deg, #28a745, #6dd77f);
        }

        .bg-warning {
            background: linear-gradient(135deg, #ffc107, #ffe082);
            color: #212529;
        }

        .bg-danger {
            background: linear-gradient(135deg, #dc3545, #e57373);
        }

        .bg-info {
            background: linear-gradient(135deg, #17a2b8, #5bc0de);
        }

        /* Inventory Table */
        .table-inventory {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 40px;
            transition: all 0.3s ease;
        }

        .table-inventory th,
        .table-inventory td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: middle;
            text-align: center;
        }

        .table-inventory th {
            background-color: #f8f9fa;
            border-radius: 6px;
        }

        .table-inventory tbody tr:hover {
            background-color: #f1f1f1;
            transition: 0.3s;
        }

        .table-danger {
            background-color: #f8d7da !important;
        }

        /* Status badge */
        .badge-available {
            background-color: #28a745;
            color: #fff;
            padding: 5px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .badge-unavailable {
            background-color: #dc3545;
            color: #fff;
            padding: 5px 8px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        /* Recently Added Products Card */
        .recent-card {
            border: 1px solid #ccc;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            background-color: #fefefe;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .recent-card h5 {
            margin-bottom: 15px;
            font-weight: 600;
            color: #2c3e50;
        }

        /* Images */
        img {
            max-width: 60px;
            height: auto;
            border-radius: 6px;
        }

        /* Print adjustments */
        @media print {
            .no-print {
                display: none !important;
            }

            thead {
                display: table-header-group;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
    <div class="container-fluid p-4">

        <!-- Header -->
        <div class="report-header">
            <h2><?= $store_name ?></h2>
            <p><?= $store_address ?></p>
            <p><?= $store_contact ?></p>
            <h4>Sales & Inventory Report</h4>
            <p>From <?= date('M d, Y', strtotime($from)) ?> to <?= date('M d, Y', strtotime($to)) ?></p>
        </div>

        <!-- Summary Table -->
        <div class="table-responsive">
            <table class="table table-bordered text-center" style="font-weight: bold; font-size: 14px;">
                <thead class="table-dark">
                    <tr>
                        <th>Total Products</th>
                        <th>Total Stock</th>
                        <th>Sold Today</th>
                        <th>Sold This Week</th>
                        <th>Sold This Month</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?= $sales_data['total_products'] ?? 0 ?></td>
                        <td><?= $sales_data['total_stock'] ?? 0 ?></td>
                        <td>
                            <?= $today['sold_today'] ?? 0 ?><br>
                            <small>₱<?= number_format($today['revenue_today'] ?? 0, 2) ?></small>
                        </td>
                        <td>
                            <?= $week['sold_week'] ?? 0 ?><br>
                            <small>₱<?= number_format($week['revenue_week'] ?? 0, 2) ?></small>
                        </td>
                        <td>
                            <?= $month['sold_month'] ?? 0 ?><br>
                            <small>₱<?= number_format($month['revenue_month'] ?? 0, 2) ?></small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <style>
            .table thead th {
                font-weight: bold;
                font-size: 15px;
                background-color: #343a40;
                /* dark header */
                color: #fff;
            }

            .table tbody td {
                font-weight: bold;
                font-size: 14px;
            }

            .table tbody small {
                font-weight: normal;
                font-size: 12px;
                color: #555;
            }

            .table-bordered {
                border: 2px solid #333;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #333 !important;
            }
        </style>
        <!-- Full Inventory Table -->
        <table class="table table-inventory">
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
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                        $revenue = $row['sold'] * $row['price'];
                        $row_class = ($row['stock'] < 5) ? 'table-danger' : '';
                        $status_badge = $row['status'] == 'active' ? '<span class="badge-available">Available</span>' : '<span class="badge-unavailable">Not Available</span>';
                        ?>
                        <tr class="<?= $row_class ?>">
                            <td><?= $count++ ?></td>
                            <td>
                                <?php if (!empty($row['primary_image'])): ?>
                                    <img src="<?= $row['primary_image'] ?>" alt="" class="img-thumbnail"
                                        style="width:60px; height:60px; object-fit:cover;">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['category'] ?></td>
                            <td><?= $row['stock'] ?></td>
                            <td><?= $row['sold'] ?></td>
                            <td>₱<?= number_format($revenue, 2) ?></td>
                            <td><?= $status_badge ?></td>
                            <td><img src="barcode.php?id=<?= $row['product_id'] ?>" width="80"></td>
                            <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; else: ?>
                    <tr>
                        <td colspan="10" class="text-center">No items found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Prepared/Reviewed By -->
        <div class="d-flex justify-content-between mt-5">
            <div class="text-center">
                ________________________________<br>
                <strong>Prepared By</strong><br>
                <span style="font-size: 11pt;">(Name & Signature)</span>
            </div>
            <div class="text-center">
                ________________________________<br>
                <strong>Reviewed By</strong><br>
                <span style="font-size: 11pt;">(Name & Signature)</span>
            </div>
        </div>

    </div>
</body>

</html>