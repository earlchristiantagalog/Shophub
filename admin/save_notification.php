<?php
include 'db.php'; // your DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $message = $_POST['message'];
    $type = $_POST['type'];
    $audience = $_POST['audience'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO notifications (title, message, type, audience, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $message, $type, $audience, $status);

    if ($stmt->execute()) {
        header("Location: settings.php?success=Notification added");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
