<?php
session_start();
include 'includes/db.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require 'PHPMailer/src/Exception.php';
// require 'PHPMailer/src/PHPMailer.php';
// require 'PHPMailer/src/SMTP.php';

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit();
}

$email = $_SESSION['email'];
$username = $_SESSION['username'];
$code = rand(100000, 999999);

$update = $conn->prepare("UPDATE users SET verification_code=? WHERE email=?");
$update->bind_param("ss", $code, $email);
$update->execute();

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'shophubincorp@gmail.com';
    $mail->Password = 'qlxt wjou dskq iofw';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('shophubincorp@gmail.com', 'Shophub');
    $mail->addAddress($email, $username);
    $mail->isHTML(true);
    $mail->Subject = 'Your New Shophub Verification Code';
    $mail->Body = "<h3>Your new code is:</h3><h2>$code</h2>";

    $mail->send();
    $_SESSION['info'] = "A new code was sent to your email.";
    header("Location: verify.php");
    exit();
} catch (Exception $e) {
    echo "Resend failed: {$mail->ErrorInfo}";
}
