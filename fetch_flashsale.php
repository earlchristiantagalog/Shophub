<?php
require 'includes/db.php';

$query = $conn->query("SELECT * FROM flash_sale WHERE NOW() BETWEEN start_time AND end_time LIMIT 1");
if ($query->num_rows > 0) {
    $row = $query->fetch_assoc();
    echo json_encode([
        'active' => true,
        'end_time' => $row['end_time']
    ]);
} else {
    echo json_encode(['active' => false]);
}
