<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

$order_id = $_GET['order_id'] ?? '';
if (empty($order_id)) {
    echo "<div class='alert alert-danger'>Invalid order.</div>";
    exit;
}

$stmt = $conn->prepare("SELECT status, remarks, created_at FROM order_tracking WHERE order_id = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$tracking = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (count($tracking) === 0) {
    echo "<div class='alert alert-info'>No tracking information available yet.</div>";
} else {
    echo "<div class='timeline'>";
    foreach ($tracking as $track) {
        echo "<div class='timeline-item'>
                <h6 class='mb-1'>" . htmlspecialchars($track['status']) . "</h6>
                <p class='mb-1 text-muted'>" . htmlspecialchars($track['remarks']) . "</p>
                <small class='text-muted'>" . date("F j, Y g:i A", strtotime($track['created_at'])) . "</small>
              </div>";
    }
    echo "<div class='timeline-end-dot'></div>";
    echo "</div>";
}
