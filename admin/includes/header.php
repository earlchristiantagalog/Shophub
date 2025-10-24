<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
// DB connection
$conn = new mysqli("localhost", "root", "", "shophub");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$adminName = $_SESSION['admin_name'] ?? 'Admin'; // Make sure this session is set on login
// Insert login record
// $sql = "INSERT INTO login_logs (admin_name) VALUES (?)";
// $stmt = $conn->prepare($sql);
// $stmt->bind_param("s", $adminName);
// $stmt->execute();
// $stmt->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopee-Style Admin Dashboard</title>
    <!-- CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/alertify.min.css" />
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/css/themes/bootstrap.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #f15b2a;
            min-height: 100vh;
        }

        .sidebar a {
            color: white;
        }

        .sidebar a:hover {
            background-color: #e04e1f;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
        }

        .product-table thead {
            background-color: #f1f1f1;
        }
    </style>
    <style>
        .sidebar {
            background-color: #f15b2a;
            min-height: 100vh;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: white;
            padding: 12px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #e04e1f;
            font-weight: 500;
            box-shadow: inset 2px 0 0 white;
        }
    </style>
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #f15b2a;
            min-height: 100vh;
        }

        .sidebar a {
            color: white;
        }

        .sidebar a:hover {
            background-color: #e04e1f;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
        }

        .product-table thead {
            background-color: #f1f1f1;
        }
    </style>
    <style>
        .sidebar {
            background-color: #f15b2a;
            min-height: 100vh;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: white;
            padding: 12px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #e04e1f;
            font-weight: 500;
            box-shadow: inset 4px 0 0 white;
        }

        .modal-content {
            border-radius: 1rem;
        }

        .modal-header,
        .modal-footer {
            border: none;
        }

        .modal-body label {
            font-weight: 500;
        }
    </style>
    <style>
        body {
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .sidebar {
            background-color: #f15b2a;
            min-height: 100vh;
        }

        .sidebar a {
            color: white;
        }

        .sidebar a:hover {
            background-color: #e04e1f;
        }

        .card {
            border: none;
            border-radius: 0.75rem;
        }

        .product-table thead {
            background-color: #f1f1f1;
        }
    </style>
    <style>
        .sidebar {
            background-color: #f15b2a;
            min-height: 100vh;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: white;
            padding: 12px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #e04e1f;
            font-weight: 500;
            box-shadow: inset 4px 0 0 white;
        }

        .modal-content {
            border-radius: 1rem;
        }

        .modal-header,
        .modal-footer {
            border: none;
        }

        .modal-body label {
            font-weight: 500;
        }
    </style>
    <style>
        :root {
            --primary-color: #ee4d2d;
            --secondary-color: #f53d2d;
            --light-bg: #f5f5f5;
            --border-color: #e0e0e0;
        }

        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .cart-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .cart-header {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        .cart-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
        }

        .cart-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            overflow: hidden;
        }

        .quantity-btn {
            background: white;
            border: none;
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .quantity-btn:hover {
            background-color: var(--light-bg);
        }

        .quantity-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .quantity-input {
            border: none;
            width: 60px;
            text-align: center;
            padding: 0.5rem;
            outline: none;
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

        .discount-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: bold;
        }

        .cart-summary {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 2rem;
        }

        .checkout-btn {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: bold;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(238, 77, 45, 0.3);
        }

        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(238, 77, 45, 0.4);
        }

        .free-shipping {
            color: #00a650;
            font-weight: bold;
        }

        .voucher-section {
            background: #fff8e1;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .remove-item {
            color: #999;
            cursor: pointer;
            transition: color 0.2s;
        }

        .remove-item:hover {
            color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .product-image {
                width: 80px;
                height: 80px;
            }

            .cart-item {
                padding: 0.75rem;
            }

            .quantity-controls {
                transform: scale(0.9);
            }
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar py-4 px-3">
                <h4 class="text-white mb-4"><i class="bi bi-shop"></i> ShopHub</h4>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a class="nav-link text-white active" href="./"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="orders.php"><i class="bi bi-cart-check"></i> Orders</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="customers.php"><i class="bi bi-people"></i> Customers</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="admin.php"><i class="bi bi-person"></i> Admin</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
                </ul>
            </nav>