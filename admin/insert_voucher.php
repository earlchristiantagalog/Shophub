<?php
include 'db.php';

$code = $_POST['code'];
$discount = $_POST['discount'];
$expiry = $_POST['expiry'];

$stmt = $conn->prepare("INSERT INTO vouchers (code, discount, expiry) VALUES (?, ?, ?)");
$stmt->bind_param("sis", $code, $discount, $expiry);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
