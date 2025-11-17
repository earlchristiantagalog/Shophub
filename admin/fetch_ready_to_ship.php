<?php
require 'db.php';

$query = "
    SELECT 
        o.order_id, 
        u.username, 
        o.total, 
        o.shipping_method, 
        o.order_date 
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.status = 'Processing'
    ORDER BY o.order_date DESC
";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['order_id']}</td>
                <td>{$row['username']}</td>
                <td>₱" . number_format($row['total'], 2) . "</td>
                <td>{$row['shipping_method']}</td>
                <td>{$row['order_date']}</td>
                <td>
                    <button class='btn btn-primary btn-sm view-order-btn' data-id='{$row['order_id']}'>
                        <i class='bi bi-eye'></i> View
                    </button>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center text-muted'>No ready-to-ship orders yet.</td></tr>";
}
