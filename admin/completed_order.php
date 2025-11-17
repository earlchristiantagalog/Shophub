<?php
require 'db.php';
require 'includes/header.php';

$sql = "SELECT 
            o.order_id, 
            u.username, 
            o.total, 
            o.shipping_method, 
            o.order_date
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.status = 'Delivered'
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Completed Orders</h2>
        <a href="tracking.php" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left"></i> Back to Tracking
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Delivered Orders</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Shipping Method</th>
                        <th>Delivered On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['order_id']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td>₱<?= number_format($row['total'], 2) ?></td>
                                <td><?= htmlspecialchars($row['shipping_method']) ?></td>
                                <td><?= date("M d, Y", strtotime($row['order_date'])) ?></td>
                                <td>
                                    <button class="btn btn-dark btn-sm print-receipt-btn" data-id="<?= $row['order_id'] ?>">
                                        <i class="bi bi-printer"></i> Print
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No completed orders yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>