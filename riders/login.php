<?php
session_start();
include 'db.php';

if (isset($_SESSION['rider_auth']) && isset($_SESSION['rider_id'])) {
    // Rider already logged in, redirect to rider dashboard
    header("Location: riders/dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT rider_id, full_name, password, status FROM riders WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($rider_id, $full_name, $hashed_password, $status);
        $stmt->fetch();

        if ($status !== 'active') {
            $error = "Your account is inactive. Contact admin.";
        } elseif (password_verify($password, $hashed_password)) {
            $_SESSION['rider_auth'] = true;
            $_SESSION['rider_id'] = $rider_id;
            $_SESSION['rider_name'] = $full_name;

            // Redirect to rider dashboard
            header("Location: ./");
            exit();
        } else {
            $error = "Invalid credentials.";
        }
    } else {
        $error = "Account not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rider Login | ShopHub</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #1f1f1f, #333);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 1rem;
    color: #fff;
}
.login-card {
    background: #222;
    border-radius: 20px;
    padding: 2rem;
    width: 100%;
    max-width: 400px;
    box-shadow: 0 6px 25px rgba(0, 0, 0, 0.5);
}
h3 { text-align: center; margin-bottom: 1.5rem; color: #ff914d; }
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
}
.btn-primary:hover { background-color: #ff7f32; }
.alert { border-radius: 12px; }
</style>
</head>
<body>
<div class="login-card">
    <h3>Rider Login</h3>

    <?php if($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Email" required>
        </div>
        <div class="mb-3">
            <input type="password" name="password" class="form-control" placeholder="Password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>
</body>
</html>
