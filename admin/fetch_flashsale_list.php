<?php
include '../includes/db.php';

// Auto update statuses
$conn->query("UPDATE flash_sale 
              SET status = 'active' 
              WHERE NOW() BETWEEN start_time AND end_time");

$conn->query("UPDATE flash_sale 
              SET status = 'ended' 
              WHERE NOW() > end_time");

$conn->query("UPDATE flash_sale 
              SET status = 'upcoming' 
              WHERE NOW() < start_time");

// Fetch all sales
$result = $conn->query("SELECT * FROM flash_sale ORDER BY start_time DESC");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['title']}</td>
                <td>{$row['discount']}%</td>
                <td>{$row['start_time']}</td>
                <td>{$row['end_time']}</td>
                <td>
                    <span class='badge " .
            ($row['status'] == 'active' ? 'bg-success' : ($row['status'] == 'upcoming' ? 'bg-warning text-dark' : 'bg-secondary')) .
            "'>" . ucfirst($row['status']) . "</span>
                </td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center text-muted'>No flash sales yet.</td></tr>";
}
