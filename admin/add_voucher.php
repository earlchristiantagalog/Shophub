<?php
require '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = strtoupper(trim($_POST['code']));
    $discount = floatval($_POST['discount']);
    $discount_type = $_POST['discount_type'];
    $min_spend = floatval($_POST['min_spend']);
    $expiry = $_POST['expiry'];
    $user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;

    // Check if voucher already exists
    $check = $conn->prepare("SELECT id FROM vouchers WHERE code = ?");
    $check->bind_param("s", $code);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["status" => "exists", "message" => "Voucher code already exists!"]);
        exit;
    }

    // Insert new voucher
    $stmt = $conn->prepare("INSERT INTO vouchers (code, discount, discount_type, min_spend, expiry, user_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdsssi", $code, $discount, $discount_type, $min_spend, $expiry, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Voucher added successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to save voucher."]);
    }

    $stmt->close();
    $check->close();
    $conn->close();
}
