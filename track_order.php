<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? '';
if (empty($order_id)) {
    echo "Invalid order.";
    exit;
}


// ✅ Fetch order details (with items)
$order_stmt = $conn->prepare("
    SELECT o.order_id, t.tracking_number, o.total, o.status, o.shipping_method, o.payment_method,
           oi.product_name, oi.product_image, oi.quantity, oi.price
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    LEFT JOIN tracking t ON o.order_id = t.order_id
    WHERE o.order_id = ?
");
$order_stmt->bind_param("s", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();
$order_items = $order_result->fetch_all(MYSQLI_ASSOC);
$order_stmt->close();

// ✅ Fetch tracking history
$stmt = $conn->prepare("
    SELECT status, remarks, created_at 
    FROM order_tracking 
    WHERE order_id = ? 
    ORDER BY created_at DESC
");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$tracking = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// ✅ Get current status and remarks
$currentStatus = $tracking[0]['status'] ?? 'Pending';
$remarks = $tracking[0]['remarks'] ?? 'Awaiting update...';

// ✅ Fetch shipping/payment info
$shipping_method = $order_items[0]['shipping_method'] ?? 'Standard Delivery';
$payment_method = $order_items[0]['payment_method'] ?? 'Cash on Delivery';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Track Order | Shophub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f5f5;
            font-family: "Poppins", sans-serif;
        }

        .track-container {
            max-width: 1100px;
            background: #fff;
            margin: 40px auto;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        /* Product Card + Status */
        .product-card {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fafafa;
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .product-card img {
            width: 90px;
            height: 90px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .product-details h6 {
            margin: 0;
            font-weight: 600;
        }

        .product-price {
            margin-left: auto;
            font-weight: 600;
            color: #ff6b00;
        }

        .status-box {
            background: linear-gradient(90deg, #ff6b00, #ff9f43);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 30px 0;
        }

        /* ✅ FIXED STEP BAR */
        .progress-track {
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 30px 0;
        }

        .progress-line {
            position: absolute;
            top: 35%;
            left: 10%;
            right: 10%;
            height: 3px;
            background: #e0e0e0;
            transform: translateY(-50%);
            z-index: 1;
            border-radius: 5px;
        }

        .progress-line-active {
            position: absolute;
            top: 35%;
            left: calc(10% + 25px);
            height: 5px;
            background: linear-gradient(90deg, #ff6b00, #ff9f43);
            transform: translateY(-50%);
            z-index: 2;
            border-radius: 5px;
            transition: width 0.4s ease;
        }

        .track-step {
            z-index: 3;
            width: 20%;
            text-align: center;
            position: relative;
        }

        .step-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #fff;
            border: 3px solid #ccc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 26px;
            margin: 0 auto;
            transition: 0.3s;
        }

        .track-step.active .step-circle,
        .track-step.completed .step-circle {
            background: #ff6b00;
            border-color: #ff6b00;
            color: #fff;
        }

        .step-label {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #666;
        }

        .track-step.active .step-label,
        .track-step.completed .step-label {
            color: #ff6b00;
            font-weight: 600;
        }

        .timeline {
            position: relative;
            padding-top: 30px;
            border-top: 2px solid #eee;
        }

        .timeline-wrapper {
            position: relative;
            margin-left: 30px;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .timeline-icon {
            position: relative;
            z-index: 2;
            width: 45px;
            height: 45px;
            min-width: 45px;
            border-radius: 50%;
            background: #ff6b00;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .timeline-line {
            position: absolute;
            top: 45px;
            left: 22px;
            width: 3px;
            height: calc(100% - 20px);
            background: #ddd;
            z-index: 1;
        }

        .timeline-item:last-child .timeline-line {
            display: none;
        }

        .timeline-content {
            background: #fff;
            border-radius: 8px;
            padding: 12px 18px;
            margin-left: 20px;
            flex-grow: 1;
            border: 1px solid #eee;
        }

        .timeline-content h6 {
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }

        .timeline-content small {
            color: #888;
        }

        /* Highlight most recent update */
        .timeline-item.latest .timeline-icon {
            background: linear-gradient(90deg, #ff6b00, #ff914d);
            box-shadow: 0 0 10px rgba(255, 107, 0, 0.4);
        }

        .timeline-item.latest .timeline-content {
            border-color: #ff914d;
            background: #fffaf5;
        }


        .status-box {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 12px;
            margin: 30px 0;
            overflow: hidden;
        }

        .status-header {
            background: linear-gradient(90deg, #ff6b00, #ff914d);
            color: #fff;
            font-weight: 600;
            font-size: 16px;
            padding: 15px 20px;
            display: flex;
            align-items: center;
            letter-spacing: 0.3px;
        }

        .status-content {
            padding: 20px;
        }

        .status-item {
            display: flex;
            align-items: center;
            font-size: 15px;
            color: #444;
        }

        .remarks-box {
            background: #f9f9f9;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 14px;
            color: #555;
            display: flex;
            align-items: center;
            border-left: 3px solid #ff6b00;
        }

        /* 🌐 RESPONSIVE STYLES FOR TRACK ORDER PAGE */

        /* Tablet (≤992px) */
        @media (max-width: 992px) {
            .track-container {
                margin: 20px;
                padding: 20px;
            }

            .product-card {
                flex-wrap: wrap;
                text-align: center;
                justify-content: center;
            }

            .product-card img {
                width: 80px;
                height: 80px;
                margin-bottom: 10px;
            }

            .product-details {
                width: 100%;
                text-align: center;
            }

            .product-price {
                margin: 10px auto 0;
                font-size: 1rem;
            }

            .progress-track {
                flex-direction: column;
                align-items: center;
                gap: 1rem;
            }

            .progress-line,
            .progress-line-active {
                display: none;
            }

            .track-step {
                width: auto;
            }

            .timeline-content {
                font-size: 0.95rem;
            }

            .status-content .row {
                flex-direction: column;
            }
        }

        /* Mobile (≤768px) */
        @media (max-width: 768px) {
            .track-container {
                margin: 15px;
                padding: 18px;
            }

            .header h3 {
                font-size: 1.4rem;
                text-align: center;
            }

            .order-id {
                text-align: center;
                font-size: 0.9rem;
            }

            .product-card {
                flex-direction: column;
                align-items: center;
            }

            .product-details h6 {
                font-size: 1rem;
            }

            .status-box {
                margin: 20px 0;
            }

            .status-header {
                font-size: 1rem;
                padding: 10px 15px;
                justify-content: center;
            }

            .status-item {
                font-size: 0.9rem;
                flex-direction: row;
                justify-content: flex-start;
            }

            .remarks-box {
                font-size: 0.85rem;
            }

            .step-circle {
                width: 60px;
                height: 60px;
                font-size: 22px;
            }

            .step-label {
                font-size: 13px;
            }

            .timeline-wrapper {
                margin-left: 10px;
                padding-left: 10px;
            }

            .timeline-icon {
                width: 38px;
                height: 38px;
                font-size: 15px;
            }

            .timeline-content {
                margin-left: 15px;
                padding: 10px 12px;
            }

            .timeline-content h6 {
                font-size: 0.95rem;
            }

            .timeline-content p {
                font-size: 0.85rem;
            }

            .timeline-content small {
                font-size: 0.8rem;
            }

            .btn-back {
                display: inline-block;
                width: 100%;
                text-align: center;
                font-size: 0.9rem;
                padding: 10px;
            }
        }

        /* Extra small phones (≤480px) */
        @media (max-width: 480px) {
            .track-container {
                padding: 15px;
                margin: 10px;
                border-radius: 8px;
            }

            .product-card img {
                width: 70px;
                height: 70px;
            }

            .product-details h6 {
                font-size: 0.9rem;
            }

            .status-header {
                font-size: 0.9rem;
                padding: 8px 12px;
            }

            .status-item {
                font-size: 0.8rem;
                gap: 6px;
            }

            .step-circle {
                width: 50px;
                height: 50px;
                font-size: 18px;
            }

            .step-label {
                font-size: 12px;
            }

            .timeline-icon {
                width: 32px;
                height: 32px;
                font-size: 14px;
            }

            .timeline-content {
                padding: 8px 10px;
            }

            .timeline-content h6 {
                font-size: 0.9rem;
            }

            .timeline-content p {
                font-size: 0.8rem;
            }

            .timeline-content small {
                font-size: 0.75rem;
            }

            .remarks-box {
                font-size: 0.8rem;
                padding: 10px;
            }
        }

        /* Ultra small (≤360px) */
        @media (max-width: 360px) {
            .header h3 {
                font-size: 1.2rem;
            }

            .order-id {
                font-size: 0.8rem;
            }

            .step-circle {
                width: 45px;
                height: 45px;
                font-size: 16px;
            }

            .step-label {
                font-size: 11px;
            }

            .product-card img {
                width: 60px;
                height: 60px;
            }

            .timeline-content {
                padding: 6px 8px;
            }
        }
    </style>
</head>

<body>
    <div class="page-container">
        <div class="track-container">
            <div class="header">
                <!-- Product Card -->
                <?php if (!empty($order_items)): ?>
                    <?php foreach ($order_items as $item): ?>
                        <h3>Track Your Order</h3>
                        <p class="order-id">Tracking Number: <?= htmlspecialchars($item['tracking_number']) ?></p>
            </div>

            <div class="product-card">
                <img src="admin/<?= htmlspecialchars($item['product_image']) ?>" alt="Product Image">
                <div class="product-details">
                    <h6><?= htmlspecialchars($item['product_name']) ?></h6>
                    <p>Qty: <?= htmlspecialchars($item['quantity']) ?></p>
                </div>
                <div class="product-price">₱<?= number_format($item['price'], 2) ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ✅ Enhanced Status Box -->
    <?php
    $currentStatus = $tracking[0]['status'] ?? 'Pending';
    $remarks = $tracking[0]['remarks'] ?? 'Awaiting update...';

    // Fetch shipping and payment info
    $shipping_stmt = $conn->prepare("SELECT shipping_method, payment_method FROM orders WHERE order_id = ?");
    $shipping_stmt->bind_param("s", $order_id);
    $shipping_stmt->execute();
    $shipping_info = $shipping_stmt->get_result()->fetch_assoc();
    $shipping_stmt->close();

    $shippingOption = $shipping_info['shipping_option'] ?? 'Standard Delivery';
    $paymentMethod = $shipping_info['payment_method'] ?? 'Cash on Delivery';
    ?>

    <div class="status-box shadow-sm">
        <div class="status-header">
            <i class="fa-solid fa-truck-fast me-2"></i>
            <span>Order Tracking Summary</span>
        </div>
        <div class="status-content">
            <div class="row">
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="status-item">
                        <i class="fa-solid fa-circle-check text-success me-2"></i>
                        <span><strong>Status:</strong> <?= htmlspecialchars($currentStatus) ?></span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="status-item">
                        <i class="fa-solid fa-truck text-primary me-2"></i>
                        <span><strong>Shipping:</strong> <?= htmlspecialchars($shippingOption) ?></span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="status-item">
                        <i class="fa-solid fa-wallet text-warning me-2"></i>
                        <span><strong>Payment:</strong> <?= htmlspecialchars($paymentMethod) ?></span>
                    </div>
                </div>
            </div>

            <div class="remarks-box mt-3">
                <i class="fa-solid fa-comment-dots me-2 text-secondary"></i>
                <span><?= htmlspecialchars($remarks) ?></span>
            </div>
        </div>
    </div>


    <!-- Step Progress Bar -->
    <?php
    $statuses = [
        'Accepted' => ['icon' => 'fa-receipt', 'label' => 'Order Placed'],
        'Processing' => ['icon' => 'fa-box', 'label' => 'Packed'],
        'Shipped' => ['icon' => 'fa-truck', 'label' => 'Shipped'],
        'Out for Delivery' => ['icon' => 'fa-motorcycle', 'label' => 'On the Way'],
        'Delivered' => ['icon' => 'fa-check-circle', 'label' => 'Delivered']
    ];
    $statusKeys = array_keys($statuses);
    $currentIndex = array_search($currentStatus, $statusKeys);
    ?>
    <div class="progress-track">
        <div class="progress-line"></div>
        <div class="progress-line-active" style="width: <?= ($currentIndex / (count($statusKeys) - 1)) * 75 ?>%;"></div>

        <?php foreach ($statuses as $key => $data):
            $index = array_search($key, $statusKeys);
            $stepClass = ($index < $currentIndex) ? 'completed' : (($index == $currentIndex) ? 'active' : '');
        ?>
            <div class="track-step <?= $stepClass ?>">
                <div class="step-circle"><i class="fa <?= $data['icon'] ?>"></i></div>
                <div class="step-label"><?= $data['label'] ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- ✅ Enhanced Timeline with Matching Status Icons -->
    <div class="timeline">
        <h5 class="mb-3 fw-bold">Tracking History</h5>
        <?php if (count($tracking) === 0): ?>
            <div class="alert alert-light text-center">No tracking updates yet.</div>
        <?php else: ?>
            <div class="timeline-wrapper">
                <?php
                // Match icons with statuses
                $statusIcons = [
                    'Accepted' => 'fa-receipt',
                    'Processing' => 'fa-box',
                    'Shipped' => 'fa-truck',
                    'Out for Delivery' => 'fa-motorcycle',
                    'Delivered' => 'fa-check-circle'
                ];
                ?>

                <?php foreach ($tracking as $index => $track):
                    $icon = $statusIcons[$track['status']] ?? 'fa-circle-info';
                ?>
                    <div class="timeline-item <?= $index === 0 ? 'latest' : '' ?>">
                        <div class="timeline-icon">
                            <i class="fa <?= $icon ?>"></i>
                        </div>
                        <div class="timeline-line"></div>
                        <div class="timeline-content">
                            <h6><?= htmlspecialchars($track['status']) ?></h6>
                            <p><?= htmlspecialchars($track['remarks']) ?></p>
                            <small><?= date('F d, Y h:i A', strtotime($track['created_at'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>


    <div class="text-center mt-4">
        <a href="my_purchases.php" class="btn-back"><i class="fa fa-arrow-left me-2"></i> Back to My Purchases</a>
    </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set progress line width based on current status
            const activeSteps = document.querySelectorAll('.step.completed, .step.active').length;
            const totalSteps = document.querySelectorAll('.step').length;
            const progressPercentage = activeSteps > 0 ? ((activeSteps - 1) / (totalSteps - 1)) * 100 : 0;

            const progressLineActive = document.getElementById('progress-active');
            if (progressLineActive) {
                progressLineActive.style.width = progressPercentage + '%';
            }

            // Define the fetchTracking function inside DOMContentLoaded
            function fetchTracking() {
                const orderId = "<?= htmlspecialchars($order_id) ?>";
                fetch(`fetch_tracking.php?order_id=${orderId}`)
                    .then(response => response.text())
                    .then(data => {
                        const trackingContainer = document.getElementById('tracking-container');
                        if (trackingContainer) {
                            trackingContainer.innerHTML = data;
                        } else {
                            console.warn("Tracking container not found — skipping reload to prevent loop.");
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching tracking data:', error);
                    });
            }

            // Run once after page fully loads
            fetchTracking();

            // Refresh every 4 minutes (240000 ms)
            setInterval(fetchTracking, 2000);
        });
    </script>

</body>

</html>