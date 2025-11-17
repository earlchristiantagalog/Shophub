<?php include 'includes/header.php'; ?>
<!-- Main Content -->
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Dashboard</h2>
        <a href="logout.php" class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <!-- Dashboard Summary Cards -->
    <div class="row g-3 mb-4">
        <?php
        date_default_timezone_set('Asia/Manila');

        // --- Total Sales ---
        $sales_result = $conn->query("SELECT SUM(total) AS total_sales FROM orders WHERE status = 'Completed'");
        $sales_row = $sales_result->fetch_assoc();
        $total_sales = $sales_row['total_sales'] ?? 0;

        // --- Total Orders ---
        $orders_result = $conn->query("SELECT COUNT(*) AS total_orders FROM orders");
        $orders_row = $orders_result->fetch_assoc();
        $total_orders = $orders_row['total_orders'] ?? 0;

        // --- Total Customers ---
        $customers_result = $conn->query("SELECT COUNT(*) AS total_customers FROM users");
        $customers_row = $customers_result->fetch_assoc();
        $total_customers = $customers_row['total_customers'] ?? 0;
        ?>

        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Total Sales</h5>
                <h3 class="text-success">₱<?= number_format($total_sales, 2) ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Orders</h5>
                <h3 class="text-primary"><?= $total_orders ?></h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Customers</h5>
                <h3 class="text-warning"><?= $total_customers ?></h3>
            </div>
        </div>
    </div>


    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Today's Admin Attendance</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Admin Name</th>
                        <th>Login Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get today's date
                    $today = date('Y-m-d');

                    // Query to get admin attendance data for today
                    $query = "SELECT a.login_time, a.status, ad.full_name 
              FROM admin_attendance a
              JOIN admin ad ON a.admin_id = ad.id
              WHERE a.login_date = ? 
              ORDER BY a.login_time DESC";

                    // Prepare the statement to avoid SQL injection
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("s", $today);  // Bind the parameter (string type)
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $count = 1;

                    // Check if the current time is 8 AM or any other time condition you want to check
                    if (date('H') == 8) {
                        // Prepare the query for inserting a "Late" record
                        $insertQuery = "INSERT INTO admin_attendance (status) VALUES ('Late')";
                        $conn->query($insertQuery);  // Execute the insert
                    }

                    // Fetch the results and display the attendance records
                    while ($row = $result->fetch_assoc()):
                        ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= date("g:i A", strtotime($row['login_time'])) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                        </tr>
                    <?php endwhile; ?>

                </tbody>
            </table>
        </div>
    </div>

</main>
<?php include 'includes/footer.php' ?>