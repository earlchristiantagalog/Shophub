<?php
require 'db.php';

$vouchers = "";
$notifications = "";

// Vouchers
$result = $conn->query("SELECT * FROM vouchers ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $vouchers .= "<tr>
        <td>{$row['code']}</td>
        <td>{$row['discount_percentage']}</td>
        <td>{$row['expiry_date']}</td>
        <td>{$row['created_at']}</td>
    </tr>";
}

// Notifications
$result = $conn->query("SELECT * FROM notifications ORDER BY id DESC");
while ($row = $result->fetch_assoc()) {
    $notifications .= "<tr>
        <td>{$row['title']}</td>
        <td>{$row['message']}</td>
        <td>{$row['created_at']}</td>
    </tr>";
}

echo json_encode([
    'vouchers' => $vouchers,
    'notifications' => $notifications
]);
