<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

$user_result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($user_result);

$address_result = mysqli_query($conn, "SELECT * FROM addresses WHERE user_id = $user_id");
$addresses = mysqli_fetch_all($address_result, MYSQLI_ASSOC);

include 'includes/header.php';
?>

<style>
    body {
        background: linear-gradient(135deg, #ffecd2, #fcb69f);
        min-height: 100vh;
    }

    .sidebar {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 20px;
        height: 100%;
    }

    .sidebar a {
        display: block;
        padding: 10px 15px;
        margin-bottom: 8px;
        border-radius: 8px;
        color: #333;
        text-decoration: none;
        font-weight: 500;
    }

    .sidebar a.active,
    .sidebar a:hover {
        background-color: #ff914d;
        color: white;
    }

    .profile-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }

    .address-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
        padding: 15px;
    }

    .btn-primary {
        background-color: #ff914d;
        border: none;
    }

    .btn-primary:hover {
        background-color: #ff7f32;
    }

    .badge-success {
        background-color: #28a745 !important;
    }

    h2,
    h4 {
        font-weight: bold;
        color: #333;
    }
</style>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 mb-4">
            <div class="sidebar">
                <a href="profile.php" class="active">My Profile</a>
                <!-- <a href="addresses.php">My Addresses</a> -->
                <a href="my_purchases.php">My Purchases</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9">
            <h2 class="mb-4 text-center">My Profile</h2>

            <div class="profile-card mb-5">
                <p><strong>Name:</strong> <?= htmlspecialchars($user['username']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
            </div>

            <div id="addresses" class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">My Addresses</h4>
                <a href="add_address.php" class="btn btn-primary">+ Add New Address</a>
            </div>

            <?php if (count($addresses) > 0): ?>
                <div class="row">
                    <?php foreach ($addresses as $address): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card address-card h-100 shadow-sm border-0 p-3">
                                <h6 class="fw-bold mb-2"><?= htmlspecialchars($address['first_name'] . ' ' . $address['last_name']) ?></h6>
                                <p class="mb-1 text-muted"><i class="bi bi-telephone-fill me-1"></i> <?= htmlspecialchars($address['phone']) ?></p>
                                <p class="mb-1 text-muted">
                                    <strong>Region:</strong> <?= htmlspecialchars($address['region']) ?><br>
                                    <strong>Province:</strong> <?= htmlspecialchars($address['province']) ?><br>
                                    <strong>City:</strong> <?= htmlspecialchars($address['city']) ?><br>
                                    <strong>Barangay:</strong> <?= htmlspecialchars($address['barangay']) ?><br>
                                    <strong>Street:</strong> <?= htmlspecialchars($address['address_line_1']) ?><br>
                                    <strong>Zip Code:</strong> <?= htmlspecialchars($address['zip_code']) ?>
                                </p>

                                <div class="mt-3 d-flex gap-2">
                                    <?php if ($address['is_default']): ?>
    <span class="badge badge-default">
        <!-- <i class="bi bi-check-circle-fill me-1"></i>  -->
        Default
    </span>
<?php else: ?>
                                        <a href="set_default_address.php?id=<?= $address['address_id'] ?>"
                                            class="btn btn-sm btn-outline-primary flex-fill">Set as Default</a>
                                    <?php endif; ?>
                                    <a href="delete_address.php?id=<?= $address['address_id'] ?>"
                                        class="btn btn-sm btn-outline-danger flex-fill">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    You have no saved addresses. <a href="add_address.php" class="fw-bold text-decoration-none">Add a new
                        address</a>
                </div>
            <?php endif; ?>

            <style>
                .address-card {
                    border-radius: 12px;
                    transition: all 0.3s ease;
                }

                .address-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
                }

                .address-card .btn {
                    border-radius: 6px;
                    padding: 6px 10px;
                    font-size: 0.85rem;
                }
                .badge-default {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background-color: #28a745;
    color: #fff;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}

.badge-default i {
    font-size: 0.85rem;
}

            </style>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>