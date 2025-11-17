<?php
require '../includes/db.php';

if (isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];

    // Fetch order details + address
    $stmt = $conn->prepare("
        SELECT o.*, u.username, u.email, a.first_name, a.last_name, a.address_line_1, 
               a.city, a.province, a.region, a.zip_code, a.phone
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN addresses a ON o.address_id = a.address_id
        WHERE o.order_id = ?
    ");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    $stmt->close();

    if (!$order) {
        echo "<div class='alert alert-danger'>Order not found.</div>";
        exit;
    }

    // Fetch items
    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $items = $stmt->get_result();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f6fa;
            font-family: "Poppins", sans-serif;
        }

        .order-container {
            /* max-width: 1100px; */
            margin: 30px auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
        }

        .section-title {
            font-weight: 600;
            font-size: 18px;
            margin-bottom: 15px;
            border-left: 4px solid #ff6b00;
            padding-left: 10px;
        }

        .info-row p {
            margin: 3px 0;
            color: #555;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            color: #fff;
            font-size: 14px;
        }

        .status-Pending {
            background: #ffc107;
        }

        .status-Processing {
            background: #17a2b8;
        }

        .status-Shipped {
            background: #007bff;
        }

        .status-Out {
            background: #6f42c1;
        }

        .status-Delivered {
            background: #28a745;
        }

        .status-Cancelled {
            background: #dc3545;
        }

        .product-card {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 12px;
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            background: #fafafa;
        }

        .product-card img {
            width: 70px;
            height: 70px;
            border-radius: 6px;
            object-fit: cover;
            border: 1px solid #ddd;
            margin-right: 15px;
        }

        .product-card .info {
            flex-grow: 1;
        }

        .product-card .info h6 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }

        .product-card .info small {
            color: #888;
        }

        .price {
            color: #ff6b00;
            font-weight: 600;
        }

        .update-section {
            border-top: 2px solid #eee;
            padding-top: 20px;
            margin-top: 20px;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>

<body>
    <div class="order-container">
        <h4 class="mb-4"><i class="fa fa-box me-2 text-warning"></i>Order Details</h4>

        <!-- Customer + Address Info -->
        <div class="row">
            <div class="col-md-6">
                <h6 class="section-title">Customer Information</h6>
                <div class="info-row">
                    <p><strong>Name:</strong> <?= htmlspecialchars($order['first_name'] . ' ' . $order['last_name']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></p>
                    <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <h6 class="section-title">Shipping Address</h6>
                <div class="info-row">
                    <p><?= htmlspecialchars($order['address_line_1']) ?></p>
                    <p><?= htmlspecialchars($order['city']) ?>, <?= htmlspecialchars($order['province']) ?></p>
                    <p><?= htmlspecialchars($order['region']) ?>, <?= htmlspecialchars($order['zip_code']) ?></p>
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="mt-4">
            <h6 class="section-title">Order Information</h6>
            <p><strong>Order ID:</strong> <?= htmlspecialchars($order['order_id']) ?></p>
            <p><strong>Shipping Method:</strong> <?= htmlspecialchars($order['shipping_method']) ?></p>
            <p><strong>Date:</strong> <?= date('F d, Y h:i A', strtotime($order['order_date'])) ?></p>
            <p><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></p>
            <p><strong>Status:</strong>
                <span class="status-badge status-<?= str_replace(' ', '', $order['status']) ?>">
                    <?= htmlspecialchars($order['status']) ?>
                </span>
            </p>
        </div>

        <!-- Product List -->
        <div class="mt-4">
            <h6 class="section-title">Products</h6>
            <?php while ($item = $items->fetch_assoc()): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="">
                    <div class="info">
                        <h6><?= htmlspecialchars($item['product_name']) ?></h6>
                        <small>Qty: <?= htmlspecialchars($item['quantity']) ?></small>
                    </div>
                    <div class="text-end">
                        <p class="price">₱<?= number_format($item['price'], 2) ?></p>
                        <small class="text-muted">Subtotal: ₱<?= number_format($item['subtotal'], 2) ?></small>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <!-- Update Form -->
        <div class="update-section">
            <form id="updateOrderForm">
                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['order_id']) ?>">

                <div class="mb-3">
                    <label class="form-label">Update Status</label>
                    <select class="form-select" name="status" <?= ($order['status'] == 'Delivered' || $order['status'] == 'Cancelled') ? 'disabled' : '' ?>>
                        <option <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option <?= $order['status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                        <option <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                        <option <?= $order['status'] == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                        <option <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                        <option <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <select class="form-select" name="remarks" <?= ($order['status'] == 'Delivered' || $order['status'] == 'Cancelled') ? 'disabled' : '' ?>>
                        <option value="" <?= empty($order['remarks']) ? 'selected' : '' ?>>-- Select Remarks --</option>
                        <option <?= $order['remarks'] == 'Order received and processing' ? 'selected' : '' ?>>Order received and processing</option>
                        <option <?= $order['remarks'] == 'Packed and ready to ship' ? 'selected' : '' ?>>Packed and ready to ship</option>
                        <option <?= $order['remarks'] == 'Item has been shipped' ? 'selected' : '' ?>>Item has been shipped</option>
                        <option <?= $order['remarks'] == 'Out for delivery' ? 'selected' : '' ?>>Out for delivery</option>
                        <option <?= $order['remarks'] == 'Successfully delivered to customer' ? 'selected' : '' ?>>Successfully delivered to customer</option>
                        <option <?= $order['remarks'] == 'Order cancelled by seller' ? 'selected' : '' ?>>Order cancelled by seller</option>
                        <option <?= $order['remarks'] == 'Order cancelled by buyer' ? 'selected' : '' ?>>Order cancelled by buyer</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-success" <?= ($order['status'] == 'Delivered' || $order['status'] == 'Cancelled') ? 'disabled' : '' ?>>
                    <i class="fa fa-save me-1"></i> Update Order
                </button>
            </form>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>