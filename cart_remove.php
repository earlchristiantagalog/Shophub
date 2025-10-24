<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index'])) {
    $cart_id = intval($_POST['index']);

    // Delete from cart and related cart_variants
    mysqli_query($conn, "DELETE FROM cart_variants WHERE cart_id = $cart_id");
    mysqli_query($conn, "DELETE FROM cart WHERE cart_id = $cart_id AND user_id = $user_id");
}

header("Location: cart.php");
exit;
