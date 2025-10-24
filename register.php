<?php
session_start();
include 'includes/db.php';
require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_no = rand(10000, 99999);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (account_no, username, email, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $account_no, $username, $email, $phone, $password);

    if ($stmt->execute()) {
        $_SESSION['email'] = $email;
        $_SESSION['username'] = $username;

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
            $mail->Subject = 'Your Shophub Verification Code';
            $mail->Body = "
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');
</style>
<div style='font-family:Poppins,Arial,sans-serif;background:#fefefe;padding:40px 0;text-align:center;'>
  <div style='max-width:420px;margin:auto;background:white;border-radius:16px;
              box-shadow:0 4px 25px rgba(0,0,0,0.08);overflow:hidden;'>
    <div style='background:linear-gradient(135deg,#ffecd2,#fcb69f);padding:20px;'>
      <img src='https://i.ibb.co/B3ZbfvL/Shophub.png' alt='Shophub Logo'
           style='width:80px;height:80px;border-radius:50%;margin-top:10px;'>
      <h2 style='color:#333;font-weight:700;margin:15px 0 5px;'>Welcome to Shophub!</h2>
      <p style='color:#555;margin:0;font-size:14px;'>Secure your account with verification</p>
    </div>
    <div style='padding:30px 20px;'>
      <p style='font-size:15px;color:#333;text-align:left;'>
        Hi <strong>$username</strong> 👋,<br><br>
        Thank you for registering at <strong>Shophub</strong>! Please use the code below to verify your email address.
      </p>
      <div style='margin:25px auto;background:#ff914d;color:white;
                  font-size:28px;letter-spacing:6px;font-weight:bold;
                  padding:15px 0;border-radius:10px;width:80%;'>$code</div>
      <p style='font-size:14px;color:#666;'>
        Enter this code on the verification page to activate your account.<br>
        If you didn’t sign up, please ignore this message.
      </p>
      <a href='http://localhost/Shophub/verify.php'
         style='display:inline-block;margin-top:20px;background:#ff914d;
                color:white;text-decoration:none;padding:12px 24px;
                border-radius:8px;font-weight:600;'>Verify My Account</a>
    </div>
    <div style='background:#f9f9f9;padding:12px;font-size:12px;color:#888;'>
      © " . date('Y') . " Shophub Inc. All rights reserved.
    </div>
  </div>
</div>";

            $mail->send();
            header("Location: verify.php");
            exit();
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error = "Email already registered.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Shophub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #ffecd2, #fcb69f);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .register-card {
            background: #fff;
            border-radius: 20px;
            padding: 2rem;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
        }

        .logo {
            display: block;
            margin: 0 auto 1rem auto;
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
        }

        h3 {
            font-weight: 700;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .form-control {
            border-radius: 12px;
            padding: 0.9rem;
            font-size: 1rem;
        }

        .btn-primary {
            border-radius: 12px;
            padding: 0.9rem;
            font-size: 1rem;
            font-weight: 600;
            background-color: #ff914d;
            border: none;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: #ff7f32;
            transform: translateY(-1px);
        }

        .alert {
            border-radius: 12px;
        }

        p {
            font-size: 0.9rem;
            color: #666;
        }

        a {
            text-decoration: none;
            color: #ff7f32;
            font-weight: 600;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 576px) {
            .register-card {
                padding: 1.5rem;
                border-radius: 15px;
            }

            .logo {
                width: 80px;
                height: 80px;
            }

            h3 {
                font-size: 1.5rem;
            }

            .btn-primary {
                font-size: 0.95rem;
                padding: 0.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="register-card text-center">
        <a href="./"><img src="Shophub.png" alt="Shophub" class="logo"></a>
        <h3>Create an Account</h3>

        <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <input name="username" class="form-control" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input name="email" type="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input name="phone" type="tel" class="form-control" placeholder="Phone" required>
            </div>
            <div class="mb-3">
                <input name="password" type="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>

        <p class="mt-3">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</body>

</html>