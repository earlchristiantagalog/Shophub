<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $user_id = $_SESSION['user_id'];

    // Validate order belongs to user
    $stmt = $conn->prepare("
    SELECT o.order_id 
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.order_id = ? 
      AND oi.product_id = ?
      AND o.user_id = ?
    LIMIT 1
");
    $stmt->bind_param("iii", $order_id, $product_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die("Invalid order or product.");
    }

    // Insert review
    $stmt = $conn->prepare("INSERT INTO reviews (order_id, product_id, user_id, rating, review) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $order_id, $product_id, $user_id, $rating, $review);
    $stmt->execute();

    // Mark as reviewed
    $updateReviewed = $conn->prepare("UPDATE order_items SET reviewed = 1 WHERE order_id = ? AND product_id = ?");
    $updateReviewed->bind_param("ii", $order_id, $product_id);
    $updateReviewed->execute();

    header("Location: my_purchases.php");
    exit();
}
