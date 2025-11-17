<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    $stmt = $conn->prepare("UPDATE orders SET status = 'Delivered' WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Order marked as Delivered.',
            'new_status' => 'Delivered'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update order status.'
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
