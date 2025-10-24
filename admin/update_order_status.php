<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $stmt = $conn->prepare("UPDATE orders SET status = ?, remarks = ? WHERE order_id = ?");
    $stmt->bind_param("sss", $status, $remarks, $order_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Order updated.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update.']);
    }
}
