<?php
session_start();
require 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders_result = $stmt->get_result();

$orders = [];

while ($order = $orders_result->fetch_assoc()) {
    $order_id = $order['order_id'];

    $stmt_items = $conn->prepare("
        SELECT 
            product_id,
            product_name,
            product_image,
            price,
            quantity,
            (price * quantity) AS subtotal,
            reviewed AS already_reviewed
        FROM order_items
        WHERE order_id = ?
    ");
    $stmt_items->bind_param("s", $order_id);
    $stmt_items->execute();
    $items_result = $stmt_items->get_result();

    $items = [];
    while ($item = $items_result->fetch_assoc()) {
        $items[] = $item;
    }

    $order['items'] = $items;
    $orders[] = $order;

    $stmt_items->close();
}

$stmt->close();
header('Content-Type: application/json');
echo json_encode($orders);
