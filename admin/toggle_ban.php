<?php
include '../includes/db.php';

if (!isset($_GET['id']) || !isset($_GET['action'])) {
    die('Invalid request.');
}

$id = intval($_GET['id']);
$action = $_GET['action'];

if ($action === 'ban') {
    $stmt = $conn->prepare("UPDATE users SET is_banned = 1 WHERE id = ?");
} elseif ($action === 'unban') {
    $stmt = $conn->prepare("UPDATE users SET is_banned = 0 WHERE id = ?");
} else {
    die('Invalid action.');
}

$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

header("Location: customers.php");
exit;
?>
