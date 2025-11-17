<?php
require '../includes/db.php';
$result = $conn->query("SELECT id, username, email FROM users ORDER BY username ASC");
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
echo json_encode($users);
$conn->close();
