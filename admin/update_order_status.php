<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    // Always update the order status in the orders table
    $update = $conn->prepare("UPDATE orders SET status = ?, remarks = ? WHERE order_id = ?");
    $update->bind_param("sss", $status, $remarks, $order_id);

    if ($update->execute()) {
        // ✅ If status is "Shipped", insert a new tracking record
        if (strtolower($status) === 'shipped') {
            $tracking_number = mt_rand(10000000000, 99999999999); // 11-digit random number

            $insert = $conn->prepare("INSERT INTO tracking (order_id, tracking_number, status, remarks) VALUES (?, ?, ?, ?)");
            $insert->bind_param("ssss", $order_id, $tracking_number, $status, $remarks);
            $insert->execute();
            $insert->close();

            echo json_encode([
                'status' => 'success',
                'message' => 'Order marked as shipped and tracking info added.',
                'tracking_number' => $tracking_number
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Order updated successfully.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update order.'
        ]);
    }

    $update->close();
}
