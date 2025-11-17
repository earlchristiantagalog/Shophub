<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="keywords" content="Shophub">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shophub</title>
    <link rel="shortcut icon" href="Shophub.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<style>
    body {
        background-color: hsla(0, 100%, 99%, 0.63);
    }

    :root {
        --primary-color: #ee4d2d;
        --secondary-color: #f5f5f5;
        --text-color: #333;
        --border-color: #e5e5e5;
    }

    body {
        background-color: var(--secondary-color);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
        background-color: var(--primary-color);
        padding: 1rem 0;
    }

    .navbar-brand {
        font-size: 1.5rem;
        font-weight: bold;
        color: white !important;
    }

    .checkout-header {
        background: white;
        padding: 1rem 0;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 1rem;
    }

    .card {
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
    }

    .card-header {
        background: white;
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .product-item {
        border-bottom: 1px solid var(--border-color);
        padding: 1rem 0;
    }

    .product-item:last-child {
        border-bottom: none;
    }

    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 5px;
    }

    .product-name {
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .product-specs {
        color: #666;
        font-size: 0.9rem;
    }

    .price {
        color: var(--primary-color);
        font-weight: bold;
        font-size: 1.1rem;
    }

    .original-price {
        color: #999;
        text-decoration: line-through;
        font-size: 0.9rem;
    }

    .address-section {
        background: #fff8f0;
        border-left: 3px solid var(--primary-color);
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .shipping-option {
        border: 1px solid var(--border-color);
        padding: 1rem;
        margin-bottom: 0.5rem;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .shipping-option:hover {
        border-color: var(--primary-color);
    }

    .shipping-option.selected {
        border-color: var(--primary-color);
        background-color: #fff8f0;
    }

    .payment-method {
        border: 1px solid var(--border-color);
        padding: 1rem;
        margin-bottom: 0.5rem;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .payment-method:hover {
        border-color: var(--primary-color);
    }

    .payment-method.selected {
        border-color: var(--primary-color);
        background-color: #fff8f0;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--primary-color);
        border-top: 1px solid var(--border-color);
        padding-top: 1rem;
        margin-top: 1rem;
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        padding: 0.75rem 2rem;
        font-size: 1.1rem;
        font-weight: bold;
    }

    .btn-primary:hover {
        background-color: #d63c1a;
        border-color: #d63c1a;
    }

    .voucher-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
    }

    .quantity-control {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quantity-btn {
        width: 30px;
        height: 30px;
        border: 1px solid var(--border-color);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .quantity-input {
        width: 50px;
        text-align: center;
        border: 1px solid var(--border-color);
        height: 30px;
    }

    @media (max-width: 768px) {
        .container {
            padding: 0 10px;
        }

        .card-body {
            padding: 1rem;
        }

        .product-image {
            width: 60px;
            height: 60px;
        }

        .row.g-4 {
            margin: 0;
        }

        .col-md-8,
        .col-md-4 {
            padding: 0;
        }
    }

    .cart-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        background-color: #dc3545;
        /* Bootstrap red */
        color: white;
        border-radius: 50%;
        font-size: 0.7rem;
        width: 18px;
        height: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
    }

    .nav-link:hover .cart-badge {
        transform: scale(1.1);
        transition: transform 0.2s ease;
    }
</style>

<body>
    <!-- ✅ LOADING SPINNER -->
    <div id="loadingSpinner">
        <div class="spinner-content text-center">
            <img src="Shophub.png" alt="ShopHub Logo" class="spinner-logo mb-3">
            <div class="spinner-border text-warning" role="status"></div>
            <p class="mt-3 text-muted fw-semibold">Loading ShopHub...</p>
        </div>
    </div>

    <style>
        /* Fullscreen Background Overlay */
        #loadingSpinner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at center, #fff 0%, #f8f9fa 100%);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: opacity 0.4s ease, visibility 0.4s ease;
        }

        /* Hide smoothly */
        #loadingSpinner.hidden {
            opacity: 0;
            visibility: hidden;
        }

        /* Inner content alignment */
        .spinner-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            animation: fadeIn 0.6s ease;
        }

        /* Logo Animation */
        .spinner-logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            animation: pulse 1.5s infinite ease-in-out;
        }

        /* Spinner style */
        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 4px;
        }

        /* Subtle fade animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Pulse animation for logo */
        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.1);
                opacity: 0.85;
            }
        }

        /* Mobile adjustments */
        @media (max-width: 768px) {
            .spinner-logo {
                width: 60px;
                height: 60px;
            }

            .spinner-border {
                width: 2.5rem;
                height: 2.5rem;
            }

            .spinner-content p {
                font-size: 0.9rem;
            }
        }
    </style>

    <script>
        // Hide the spinner smoothly after page load
        window.addEventListener("load", function() {
            const spinner = document.getElementById("loadingSpinner");
            if (spinner) {
                spinner.classList.add("hidden");
                setTimeout(() => spinner.remove(), 500); // fully remove after fade
            }
        });
    </script>


    <!-- ✅ MODERN HEADER -->
    <nav class="navbar navbar-expand-lg navbar-dark py-3 shadow-sm sticky-top" style="background: linear-gradient(135deg, #ee4d2d, #ff7043);">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="./">
                <img src="Shophub.png" alt="ShopHub Logo"
                    class="me-2" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                <span class="fw-bold fs-4 text-white">ShopHub</span>
            </a>

            <!-- Mobile toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars text-white"></i>
            </button>

            <!-- Navigation content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Search -->
                <div class="mx-auto col-lg-6 mt-3 mt-lg-0">
                    <form action="index.php" method="GET" class="d-flex">
                        <input type="text" name="query" class="form-control rounded-start-pill border-0 px-3"
                            placeholder="Search products..." required>
                        <button type="submit" class="btn btn-light rounded-end-pill px-4">
                            <i class="fas fa-search text-danger"></i>
                        </button>
                    </form>
                </div>

                <!-- Icons -->
                <ul class="navbar-nav ms-auto align-items-center mt-3 mt-lg-0">
                    <?php

                    // If you store cart items in session
                    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

                    // OR if you store cart in database for logged-in user

                    include 'includes/db.php';
                    $user_id = $_SESSION['user_id'] ?? 0;
                    $result = mysqli_query($conn, "SELECT COUNT(*) AS count FROM cart WHERE user_id = $user_id");
                    $row = mysqli_fetch_assoc($result);
                    $cart_count = $row['count'];

                    ?>
                    <!-- Cart -->
                    <li class="nav-item position-relative me-3">
                        <a class="nav-link text-white position-relative" href="cart.php">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <?php if ($cart_count > 0): ?>
                                <span class="cart-badge"><?= $cart_count ?></span>
                            <?php endif; ?>
                        </a>
                    </li>

                    <!-- User -->
                    <?php if (isset($_SESSION['auth'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle me-1"></i> <?= htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                                <li><a class="dropdown-item" href="my_purchases.php"><i class="fas fa-box me-2"></i>My Purchases</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="btn btn-light text-danger fw-semibold px-4 ms-2" href="login.php">
                                <i class="fas fa-user me-1"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <style>
        .cart-badge {
            position: absolute;
            top: -5px;
            right: -8px;
            background-color: #ffc107;
            color: #000;
            border-radius: 50%;
            font-size: 0.7rem;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.3);
        }

        .navbar-toggler {
            border: none;
            outline: none;
        }

        .navbar-toggler:focus {
            box-shadow: none;
        }
    </style>