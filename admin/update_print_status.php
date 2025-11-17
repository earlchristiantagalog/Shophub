<?php
require '../includes/db.php';

if (isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];

    $stmt = $conn->prepare("UPDATE orders SET printed = 1 WHERE order_id = ?");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $stmt->close();
}
