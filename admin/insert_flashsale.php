<?php
include '../includes/db.php';

$title = $_POST['title'];
$discount = $_POST['discount'];
$start_time = $_POST['start_time'];
$end_time = $_POST['end_time'];

$stmt = $conn->prepare("INSERT INTO flash_sale (title, discount, start_time, end_time, status) VALUES (?, ?, ?, ?, 'upcoming')");
$stmt->bind_param("siss", $title, $discount, $start_time, $end_time);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
