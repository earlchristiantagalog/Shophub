<?php
include 'db.php';

if (isset($_POST['id'], $_POST['title'], $_POST['type'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $type = $conn->real_escape_string($_POST['type']);
    $status = $conn->real_escape_string($_POST['status']);

    $sql = "UPDATE notifications SET title = '$title', type = '$type', status = '$status' WHERE id = $id";

    if ($conn->query($sql)) {
        echo "Notification updated successfully.";
    } else {
        echo "Error updating notification.";
    }
}
