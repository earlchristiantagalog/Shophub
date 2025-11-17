<?php
session_start();

// Remember Me logic
if (isset($_COOKIE['remember_email'])) {
    $remembered_email = $_COOKIE['remember_email'];
} else {
    $remembered_email = '';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroHub Admin - Login</title>

    <!-- Libraries -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #ee4d2d;
            --secondary-color: #ff6b35;
            --text-dark: #222;
            --text-light: #777;
            --bg-light: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--bg-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            width: 95%;
            max-width: 950px;
        }

        .left-panel {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }

        .left-panel i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .left-panel h1 {
            font-size: 2rem;
            font-weight: 700;
        }

        .left-panel p {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .right-panel {
            padding: 60px 50px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            font-weight: 600;
            color: var(--text-dark);
        }

        .login-header p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .form-floating {
            margin-bottom: 1.3rem;
        }

        .form-control {
            border-radius: 10px;
            padding: 1rem;
            border: 1.5px solid #dee2e6;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.15rem rgba(238, 77, 45, 0.2);
        }

        .btn-login {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            padding: 0.9rem;
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(238, 77, 45, 0.3);
        }

        .form-check-label {
            font-size: 0.9rem;
            color: var(--text-light);
        }

        .forgot-password {
            font-size: 0.9rem;
            text-decoration: none;
            color: var(--primary-color);
        }

        .forgot-password:hover {
            color: var(--secondary-color);
        }

        @media (max-width: 768px) {
            .left-panel {
                display: none;
            }

            .right-panel {
                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>
    <div class="login-card row g-0">
        <div class="col-lg-6 left-panel">
            <i class="fas fa-bolt"></i>
            <h1>ElectroHub Admin</h1>
            <p>Manage your system with efficiency and style.</p>
            <ul class="list-unstyled text-start mx-auto" style="max-width: 300px;">
                <li><i class="fas fa-check-circle me-2"></i> Smart Inventory</li>
                <li><i class="fas fa-check-circle me-2"></i> Real-Time Reports</li>
                <li><i class="fas fa-check-circle me-2"></i> Secure Access</li>
            </ul>
        </div>

        <div class="col-lg-6 right-panel">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your Admin Dashboard</p>
            </div>

            <form id="loginForm" method="POST" action="code.php">
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="<?= htmlspecialchars($remembered_email) ?>" required>
                    <label for="email"><i class="fas fa-envelope me-2"></i>Email</label>
                </div>

                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember" name="remember" <?= $remembered_email ? 'checked' : '' ?>>
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <a href="#" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" name="login" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i>Sign In
                </button>
            </form>
        </div>
    </div>

    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
    <script>
        <?php if (isset($_SESSION['message'])): ?>
            alertify.set('notifier', 'position', 'top-center');
            alertify.success('<?= $_SESSION['message']; ?>');
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
    </script>
</body>

</html>