<?php
// cancel_order.php
header('Content-Type: application/json');
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$order_id = $_POST['order_id'] ?? '';
if (empty($order_id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing order_id']);
    exit;
}

try {
    // Start transaction
    $conn->begin_transaction();

    // 1) Check current status
    $stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = ? FOR UPDATE");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows === 0) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Order not found.']);
        exit;
    }
    $row = $res->fetch_assoc();
    $currentStatus = $row['status'];
    $stmt->close();

    // Only allow cancel if it's strictly 'Pending'
    if ($currentStatus !== 'Pending') {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => "Cannot cancel order. Current status: {$currentStatus}"]);
        exit;
    }

    // 2) Update order status to Cancelled (and optionally set remarks/updated_at)
    $newRemarks = 'Order cancelled by admin';
    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled', remarks = ? WHERE order_id = ?");
    $stmt->bind_param("ss", $newRemarks, $order_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Failed to update order.']);
        exit;
    }
    $stmt->close();

    // 3) Optionally insert an order_tracking row (helpful for timeline)
    $stmt = $conn->prepare("INSERT INTO order_tracking (order_id, status, remarks, created_at) VALUES (?, 'Cancelled', ?, NOW())");
    $stmt->bind_param("ss", $order_id, $newRemarks);
    $stmt->execute();
    $stmt->close();

    $conn->commit();
    echo json_encode(['success' => true, 'message' => 'Order cancelled successfully.']);
    exit;
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
    exit;
}
