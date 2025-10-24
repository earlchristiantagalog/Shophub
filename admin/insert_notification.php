<?php
include 'db.php';

$title = $_POST['title'];
$message = $_POST['message'];
$date = $_POST['date'];

$stmt = $conn->prepare("INSERT INTO notifications (title, message, date) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $title, $message, $date);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error";
}
