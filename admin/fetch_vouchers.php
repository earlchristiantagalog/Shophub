<?php
include 'db.php';
$result = $conn->query("SELECT * FROM vouchers ORDER BY id DESC");

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['code']}</td>
        <td>{$row['discount']}%</td>
        <td>{$row['expiry']}</td>
        <td><button class='btn btn-sm btn-danger'>Delete</button></td>
    </tr>";
}
