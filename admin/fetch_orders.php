<?php
require '../includes/db.php';

$sql = "SELECT 
            o.order_id, 
            u.username, 
            o.total, 
            o.status, 
            o.order_date 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC";

$result = $conn->query($sql);

$output = '';
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusClass = match ($row['status']) {
            'Pending' => 'bg-warning',
            'Accepted' => 'bg-success',
            'Processing' => 'bg-info',
            'Shipped' => 'bg-primary',
            'Out for Delivery' => 'bg-secondary',
            'Delivered' => 'bg-success',
            'Cancelled' => 'bg-danger',
            default => 'bg-light text-dark'
        };

        $output .= '<tr>
            <td>' . htmlspecialchars($row['order_id']) . '</td>
            <td>' . htmlspecialchars($row['username']) . '</td>
            <td>₱' . number_format($row['total'], 2) . '</td>
            <td><span class="badge ' . $statusClass . '">' . htmlspecialchars($row['status']) . '</span></td>
            <td>' . date('F j, Y', strtotime($row['order_date'])) . '</td>
            <td>';

        // ✅ If status is Pending → show Accept + Cancel buttons
        if ($row['status'] === 'Pending') {
            $output .= '
                <button type="button" class="btn btn-success btn-sm accept-order-btn" data-id="' . $row['order_id'] . '">
                    <i class="bi bi-check-circle"></i> Accept
                </button>
                <button type="button" class="btn btn-danger btn-sm cancel-order-btn" data-id="' . $row['order_id'] . '">
                    <i class="bi bi-x-circle"></i> Cancel
                </button>';
        }
        // ✅ If status is NOT Pending → only show View and Print buttons
        else {
            $output .= '
                <button type="button" class="btn btn-primary btn-sm view-order-btn" data-id="' . $row['order_id'] . '">
                    <i class="bi bi-eye"></i> View
                </button>
                <a href="receipt.php?order_id=' . $row['order_id'] . '" class="btn btn-dark btn-sm" target="_blank">
                    <i class="bi bi-printer"></i> Print
                </a>
';
        }

        $output .= '</td></tr>';
    }
} else {
    $output = '<tr><td colspan="6" class="text-center">No orders found.</td></tr>';
}

echo $output;
