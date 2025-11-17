<?php
session_start();
require '../includes/db.php';

if(!isset($_SESSION['rider_auth']) || !isset($_SESSION['rider_id'])){
    echo json_encode(['success'=>false, 'message'=>'Not authorized']);
    exit;
}

$rider_id = $_SESSION['rider_id'];
$order_id = $_GET['order_id'] ?? '';

$stmt = $conn->prepare("
    SELECT o.order_id, a.first_name, a.last_name, u.phone, a.address_line_1, a.barangay, a.city, a.province
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN addresses a ON o.address_id = a.address_id
    WHERE o.rider_id=? AND o.order_id=?
");
$stmt->bind_param("is", $rider_id, $order_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows === 1){
    $o = $res->fetch_assoc();
    echo json_encode([
        'success'=>true,
        'order_id'=>$o['order_id'],
        'customer_name'=>$o['first_name'].' '.$o['last_name'],
        'phone'=>$o['phone'],
        'address'=>$o['address_line_1'].', '.$o['barangay'].', '.$o['city'].', '.$o['province']
    ]);
} else {
    echo json_encode(['success'=>false, 'message'=>'Order not found']);
}
$stmt->close();
