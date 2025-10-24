<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroHub Admin - Login</title>
    <!-- CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ee4d2d;
            --secondary-color: #ff6b35;
            --accent-color: #00bcd4;
            --dark-bg: #1a1a1a;
            --light-bg: #f5f5f5;
            --text-dark: #333;
            --text-light: #666;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 1000px;
            width: 100%;
            min-height: 600px;
        }

        .login-left {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="circuit" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M10 0v20M0 10h20" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/><circle cx="5" cy="5" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="15" cy="15" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23circuit)"/></svg>') repeat;
            opacity: 0.3;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .brand-logo {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 15px;
            z-index: 1;
        }

        .brand-logo i {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }

        .brand-subtitle {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
            z-index: 1;
        }

        .feature-list {
            list-style: none;
            text-align: left;
            z-index: 1;
        }

        .feature-list li {
            padding: 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
            opacity: 0.9;
        }

        .feature-list i {
            background: rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .login-right {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h2 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: var(--text-light);
            font-size: 0.95rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-floating .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1rem 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-floating .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(238, 77, 45, 0.25);
        }

        .form-floating label {
            color: var(--text-light);
            font-weight: 500;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border: none;
            padding: 1rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 12px;
            color: white;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(238, 77, 45, 0.3);
            color: white;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }

        .forgot-password a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--secondary-color);
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
            color: var(--text-light);
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e9ecef;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            font-size: 0.9rem;
        }

        .social-login {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .btn-social {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            background: white;
            color: var(--text-dark);
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-social:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .security-badge {
            background: var(--light-bg);
            padding: 1rem;
            border-radius: 12px;
            text-align: center;
            font-size: 0.85rem;
            color: var(--text-light);
            margin-top: 1.5rem;
        }

        .security-badge i {
            color: #28a745;
            margin-right: 0.5rem;
        }

        @media (max-width: 768px) {
            .login-container {
                margin: 10px;
                border-radius: 15px;
            }

            .login-left {
                padding: 40px 30px;
            }

            .login-right {
                padding: 40px 30px;
            }

            .brand-logo {
                font-size: 2rem;
            }

            .feature-list {
                display: none;
            }

            .social-login {
                flex-direction: column;
            }
        }

        @media (max-width: 992px) {
            .login-left {
                text-align: center;
                padding: 40px 30px;
            }

            .feature-list {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="login-container">
            <div class="row g-0 h-100">
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="login-left h-100">
                        <div class="brand-logo">
                            <i class="fas fa-bolt"></i>
                            ElectroHub
                        </div>
                        <div class="brand-subtitle">
                            Advanced Electronics Management System
                        </div>
                        <ul class="feature-list">
                            <li>
                                <i class="fas fa-microchip"></i>
                                <span>Smart Inventory Management</span>
                            </li>
                            <li>
                                <i class="fas fa-chart-line"></i>
                                <span>Real-time Analytics</span>
                            </li>
                            <li>
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure Payment Processing</span>
                            </li>
                            <li>
                                <i class="fas fa-mobile-alt"></i>
                                <span>Mobile-First Design</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="login-right">
                        <div class="login-header">
                            <h2>Welcome Back, Admin</h2>
                            <p>Sign in to your ElectroHub dashboard</p>
                        </div>

                        <form id="loginForm" method="POST" action="code.php">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                <label for="email"><i class="fas fa-envelope me-2"></i>Email Address</label>
                            </div>
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                                <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>
                                <a href="#" class="text-decoration-none" style="color: var(--primary-color);">Forgot password?</a>
                            </div>
                            <button type="submit" name="login" class="btn btn-login w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In to Dashboard
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add floating label animation
        document.querySelectorAll('.form-floating .form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });

            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });

        // Add social login functionality
        document.querySelectorAll('.btn-social').forEach(btn => {
            btn.addEventListener('click', function() {
                const provider = this.textContent.trim();
                alert(`${provider} authentication would be implemented here`);
            });
        });
    </script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
    <script>
        <?php
        if ($_SESSION['message']) {
        ?>
            alertify.set('notifier', 'position', 'top-center');
            alertify.success('<?= $_SESSION['message']; ?>');
        <?php
            unset($_SESSION['message']);
        }
        ?>
    </script>
</body>

</html>