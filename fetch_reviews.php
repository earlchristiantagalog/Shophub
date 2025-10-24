<?php
session_start();
require 'includes/db.php';

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid product ID"]);
    exit;
}

$stmt = $conn->prepare("
    SELECT r.rating, r.review, r.created_at, u.email
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    WHERE r.product_id = ?
    ORDER BY r.created_at DESC
");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$reviews = [];
while ($row = $result->fetch_assoc()) {
    $reviews[] = $row;
}

header('Content-Type: application/json');
echo json_encode($reviews);
