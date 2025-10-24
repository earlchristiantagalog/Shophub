<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['order_id'])) {
    $orderId = trim($_POST['order_id']);

    // Debug log (optional)
    // file_put_contents('log.txt', date('Y-m-d H:i:s') . " | Accepting order: $orderId\n", FILE_APPEND);

    // Make sure order_id is treated as a string (since it's alphanumeric like ES46033)
    $stmt = $conn->prepare("UPDATE orders SET status = 'Accepted' WHERE order_id = ? AND status = 'Pending'");
    $stmt->bind_param("s", $orderId);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Order accepted successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No matching pending order found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
