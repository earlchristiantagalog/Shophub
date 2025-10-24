<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$address_id = $_GET['id'] ?? null;

if (!$user_id || !$address_id) {
    header("Location: login.php");
    exit;
}

// First, verify that this address belongs to the logged-in user
$check = mysqli_query($conn, "SELECT * FROM addresses WHERE address_id = '$address_id' AND user_id = '$user_id'");
if (mysqli_num_rows($check) === 0) {
    $_SESSION['error'] = "Invalid address selection.";
    header("Location: profile.php");
    exit;
}

// Set all addresses for this user to not default
mysqli_query($conn, "UPDATE addresses SET is_default = 0 WHERE user_id = '$user_id'");

// Set the selected address as default
mysqli_query($conn, "UPDATE addresses SET is_default = 1 WHERE address_id = '$address_id' AND user_id = '$user_id'");

$_SESSION['success'] = "Default address updated successfully.";
header("Location: profile.php");
exit;
