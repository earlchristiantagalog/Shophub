<?php include 'includes/header.php'; ?>
<!-- Main Content -->
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Admin</h2>
        <a href="logout.php" class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>



    <!-- Admin Table -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Admin List</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="bi bi-plus-circle"></i> Add Admin
            </button>

        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered product-table">
                <thead>
                    <tr>
                        <th>ID Number</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    include 'db.php';
                    $sql = "SELECT * FROM admin";
                    $admin = mysqli_query($conn, $sql);

                    if (mysqli_num_rows($admin) > 0) {
                        foreach ($admin as $data) {
                    ?>
                            <tr>
                                <td><?= $data['admin_id'] ?></td>
                                <td><?= $data['full_name'] ?></td>
                                <td><?= $data['email'] ?></td>
                                <td>
                                    <a href="edit_admin.php?id=<?= $data['admin_id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                    <a href="" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>

                    <?php
                        }
                    }
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add Product Modal -->
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addProductModalLabel"><i class="bi bi-plus-circle me-2"></i>Add New Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form action="code.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" placeholder="Enter full name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="Please enter email">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Please enter password">
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_admin" class="btn btn-primary">Save Admin</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>