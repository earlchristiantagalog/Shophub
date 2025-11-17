<?php
include 'includes/header.php';
include '../includes/db.php'; // adjust path if needed

date_default_timezone_set('Asia/Manila');
?>

<!-- Main Content -->
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="bi bi-people me-2"></i>Customers</h2>
    </div>

    <!-- Customers Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Customer List</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Total Orders</th>
                        <th>Total Spent</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "
                        SELECT 
                            u.id,
                            u.account_no, 
                            u.username, 
                            u.email, 
                            u.created_at,
                            u.is_banned,
                            COUNT(o.order_id) AS total_orders,
                            COALESCE(SUM(o.total), 0) AS total_spent
                        FROM users u
                        LEFT JOIN orders o ON u.id = o.user_id
                        GROUP BY u.id
                        ORDER BY u.created_at DESC
                    ";
                    $result = $conn->query($query);
                    $count = 1;

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                    ?>
                            <tr class="<?= $row['is_banned'] ? 'table-danger' : '' ?>">
                                <td><?= htmlspecialchars($row['account_no']) ?></td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= $row['total_orders'] ?></td>
                                <td>₱<?= number_format($row['total_spent'], 2) ?></td>
                                <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                                <td>
                                    <?php if ($row['is_banned']): ?>
                                        <span class="badge bg-danger">Banned</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['is_banned']): ?>
                                        <a href="toggle_ban.php?id=<?= $row['id'] ?>&action=unban"
                                            class="btn btn-sm btn-outline-success"
                                            onclick="return confirm('Unban this customer?');">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="toggle_ban.php?id=<?= $row['id'] ?>&action=ban"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Ban this customer?');">
                                            <i class="bi bi-slash-circle"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">No customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>