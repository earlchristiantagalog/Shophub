<?php
include 'db.php';
$result = $conn->query("SELECT * FROM notifications ORDER BY id DESC");

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['title']}</td>
        <td>{$row['message']}</td>
        <td>{$row['date']}</td>
        <td><button class='btn btn-sm btn-danger'>Delete</button></td>
    </tr>";
}
