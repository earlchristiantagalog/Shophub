<?php
require 'db.php';

$sql = "SELECT 
            o.order_id, 
            u.username, 
            o.total, 
            o.status, 
            o.shipping_method,
            o.order_date,
            t.tracking_number
        FROM orders o
        JOIN users u ON o.user_id = u.id
        LEFT JOIN tracking t ON o.order_id = t.order_id
        WHERE o.status IN ('Ready to Ship', 'Shipped', 'In Transit')
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $badgeClass = match ($row['status']) {
            'Accepted' => 'bg-success',
            'Ready to Ship' => 'bg-primary',
            'Shipped' => 'bg-info text-dark',
            default => 'bg-secondary'
        };

        $tracking = !empty($row['tracking_number'])
            ? $row['tracking_number']
            : '<span class="text-muted">Pending</span>';

        echo "
        <tr>
            <td>{$tracking}</td>
            <td>{$row['order_id']}</td>
            <td>{$row['username']}</td>
            <td>₱" . number_format($row['total'], 2) . "</td>
            <td><span class='badge {$badgeClass}'>{$row['status']}</span></td>
            <td>{$row['shipping_method']}</td>
            <td>" . date('M d, Y', strtotime($row['order_date'])) . "</td>
            <td>
                <button class='btn btn-primary btn-sm view-order-btn' data-id='{$row['order_id']}'>
                    <i class='bi bi-eye'></i> View
                </button>
                <button class='btn btn-dark btn-sm print-receipt-btn' data-id='{$row['order_id']}'>
                    <i class='bi bi-printer'></i> Print
                </button>
                <button class='btn btn-success btn-sm mark-delivered-btn' data-id='{$row['order_id']}'>
                    <i class='bi bi-check2-circle'></i> Delivered
                </button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8' class='text-center text-muted'>No accepted or in-transit orders yet.</td></tr>";
}
