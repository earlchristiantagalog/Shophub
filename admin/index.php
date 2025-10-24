<?php include 'includes/header.php'; ?>
<!-- Main Content -->
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Dashboard</h2>
        <a href="logout.php" class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>

    <!-- Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Total Sales</h5>
                <h3 class="text-success">₱35,000</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Orders</h5>
                <h3 class="text-primary">128</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm p-3">
                <h5>Customers</h5>
                <h3 class="text-warning">52</h3>
            </div>
        </div>
    </div>

    <!-- <div class="card shadow-sm mb-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Admin Login Logs</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Admin Name</th>
                        <th>Login Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Get login logs
                    $result = $conn->query("SELECT * FROM login_logs ORDER BY login_time DESC LIMIT 20");
                    $count = 1;
                    while ($row = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($row['admin_name']) ?></td>
                            <td><?= date("F j, Y, g:i A", strtotime($row['login_time'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div> -->
</main>
<?php include 'includes/footer.php' ?>