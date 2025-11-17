<?php
include 'includes/db.php';
header('Content-Type: application/json');

// Always normalize to UTC
date_default_timezone_set('Asia/Manila');


// Try to get active flash sale
$activeSale = $conn->query("
    SELECT * FROM flash_sale 
    WHERE NOW() BETWEEN start_time AND end_time 
    ORDER BY start_time ASC 
    LIMIT 1
");

if ($activeSale && $activeSale->num_rows > 0) {
    $sale = $activeSale->fetch_assoc();
    echo json_encode([
        'status' => 'active',
        'title' => $sale['title'],
        'discount' => (float)$sale['discount'],
        'start_time' => gmdate("Y-m-d\TH:i:s\Z", strtotime($sale['start_time'])),
        'end_time' => gmdate("Y-m-d\TH:i:s\Z", strtotime($sale['end_time']))
    ]);
    exit;
}

// If none active, find the next upcoming
$upcomingSale = $conn->query("
    SELECT * FROM flash_sale 
    WHERE start_time > NOW() 
    ORDER BY start_time ASC 
    LIMIT 1
");

if ($upcomingSale && $upcomingSale->num_rows > 0) {
    $sale = $upcomingSale->fetch_assoc();
    echo json_encode([
        'status' => 'upcoming',
        'title' => $sale['title'],
        'discount' => (float)$sale['discount'],
        'start_time' => gmdate("Y-m-d\TH:i:s\Z", strtotime($sale['start_time'])),
        'end_time' => gmdate("Y-m-d\TH:i:s\Z", strtotime($sale['end_time']))
    ]);
    exit;
}

// No sales
echo json_encode(null);
