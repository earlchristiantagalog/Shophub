<?php
session_start();
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
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar py-4 px-3">
                <h4 class="text-white mb-4"><i class="bi bi-shop"></i> ShopHub</h4>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="./"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="products.php"><i class="bi bi-box-seam"></i> Products</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="orders.php"><i class="bi bi-cart-check"></i> Orders</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="customers.php"><i class="bi bi-people"></i> Customers</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white active" href="admin.php"><i class="bi bi-person"></i> Admin</a></li>
                    <li class="nav-item mb-2"><a class="nav-link text-white" href="settings.php"><i class="bi bi-gear"></i> Settings</a></li>
                </ul>
            </nav>

            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
                <!-- Top Navbar -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-bold">Edit Admin</h2>
                    <button class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</button>
                </div>

                <!-- Edit Admin Form Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Update Admin Profile</h5>
                    </div>
                    <div class="card-body">
                        <form action="code.php" method="POST" class="row g-3">
                            <?php
                            include 'db.php';

                            $sql = "SELECT * FROM admin";
                            $query = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($query) > 0) {
                                foreach ($query as $data) {
                            ?>
                                    <input type="hidden" name="id" value="<?= $data['admin_id'] ?>">

                                    <div class="col-md-6">
                                        <label for="adminName" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" value="<?= $data['full_name'] ?>" id="adminName" name="admin_name" placeholder="Enter full name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="adminEmail" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" value="<?= $data['email'] ?>" id="adminEmail" name="admin_email" placeholder="Enter email" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="adminPassword" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="adminPassword" name="admin_password" placeholder="Enter new password">
                                    </div>

                                    <div class="col-12 text-end">
                                        <button type="submit" name="update_admin" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Changes</button>
                                    </div>
                            <?php
                                }
                            }
                            ?>

                        </form>
                    </div>
                </div>
            </main>

        </div>
    </div>


    <!-- JavaScript -->
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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