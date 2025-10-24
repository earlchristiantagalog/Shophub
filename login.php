<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['auth'] = true;
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;

            // ✅ Redirect to intended page (if any), otherwise home
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: $redirect");
                exit();
            }

            header("Location: index.php");
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
    <title>Login | ShopHub</title>
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

        .login-card {
            background: #fff;
            border-radius: 20px;
            padding: 2rem;
            width: 100%;
            max-width: 400px;
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
            .login-card {
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
    <div class="login-card text-center">
        <a href="./"><img src="Shophub.png" alt="Logo" class="logo"></a>
        <h3>Welcome Back</h3>

        <?php
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        if (isset($error)) echo "<div class='alert alert-danger'>$error</div>";
        ?>

        <form method="POST" novalidate>
            <div class="mb-3">
                <input name="email" type="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input name="password" type="password" class="form-control" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>

        <p class="mt-3">
            Don't have an account? <a href="register.php">Register</a>
        </p>
    </div>
</body>
</html>
