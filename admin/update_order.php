<?php
require 'db.php'; // adjust path if needed

header("Content-Type: application/json");

// Check if POST data is received
if (empty($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing order_id']);
    exit;
}

$order_id = $_POST['order_id'];
$place = isset($_POST['place']) ? $_POST['place'] : 'warehouse';

// Set the new order status message
$new_status = "Order has been arrived at $place";

// Update order in the database
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
$stmt->bind_param("ss", $new_status, $order_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'message' => "Order #$order_id updated to: $new_status"]);
} else {
    echo json_encode(['success' => false, 'message' => 'Order not found or already updated']);
}

$stmt->close();
$conn->close();
