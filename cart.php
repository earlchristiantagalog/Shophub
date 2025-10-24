<?php
session_start();
include 'includes/db.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// Remove item if requested
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    mysqli_query($conn, "DELETE FROM cart WHERE product_id = $remove_id AND user_id = '$user_id'");
    header("Location: cart.php");
    exit;
}

// Fetch cart items
$cart_items = [];
$variant_map = [];

$sql = "SELECT * FROM cart WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cart_items[] = $row;
    }

    $cart_ids = array_column($cart_items, 'cart_id');
    if (!empty($cart_ids)) {
        $cart_ids_str = implode(',', array_map('intval', $cart_ids));
        $variant_sql = "SELECT * FROM cart_variants WHERE cart_id IN ($cart_ids_str)";
        $variant_result = mysqli_query($conn, $variant_sql);
        if ($variant_result) {
            while ($variant = mysqli_fetch_assoc($variant_result)) {
                $variant_map[$variant['cart_id']][] = $variant;
            }
        }
    }
}

include 'includes/header.php';
?>

<div class="container py-5">
    <h2 class="fw-bold mb-4 text-dark"><i class="fas fa-shopping-cart text-warning me-2"></i>Your Shopping Cart</h2>

    <?php if (!empty($cart_items)): ?>
        <div class="row g-4">
            <!-- Cart Items -->
            <div class="col-lg-8">
                <?php foreach ($cart_items as $item): ?>
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="row g-0 align-items-center">
                            <!-- Product Image -->
                            <div class="col-4 col-md-3 text-center bg-light p-2">
                                <img src="admin/<?= htmlspecialchars($item['product_image']) ?>"
                                    class="img-fluid rounded-3"
                                    style="max-height: 120px; object-fit: contain;"
                                    onerror="this.src='assets/img/placeholder.jpg';">
                            </div>

                            <!-- Product Details -->
                            <div class="col-8 col-md-9">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                                        <div class="mb-2">
                                            <h5 class="fw-semibold mb-1 text-dark"><?= htmlspecialchars($item['product_name']) ?></h5>

                                            <?php if (!empty($variant_map[$item['cart_id']])): ?>
                                                <div class="mb-2">
                                                    <?php foreach ($variant_map[$item['cart_id']] as $variant): ?>
                                                        <span class="badge bg-dark text-light me-1">
                                                            <?= htmlspecialchars(ucfirst($variant['variant_type'])) ?>: <?= htmlspecialchars($variant['variant_value']) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="text-muted small mb-1">
                                                Price: <span class="text-danger fw-bold">₱<?= number_format($item['price'], 2) ?></span>
                                            </div>
                                            <div class="text-muted small">Quantity: <strong><?= $item['quantity'] ?></strong></div>
                                        </div>

                                        <a href="cart.php?remove=<?= $item['product_id'] ?>"
                                            class="btn btn-sm btn-outline-danger mt-1"
                                            onclick="return confirm('Remove this item from your cart?')">
                                            <i class="fas fa-trash me-1"></i>Remove
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3 text-dark"><i class="fas fa-receipt text-warning me-2"></i>Order Summary</h5>

                        <?php
                        $subtotal = 0;
                        foreach ($cart_items as $item) {
                            $subtotal += $item['price'] * $item['quantity'];
                        }
                        ?>

                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Subtotal</span>
                                <span class="fw-semibold">₱<?= number_format($subtotal, 2) ?></span>
                            </li>
                            <!-- <li class="list-group-item d-flex justify-content-between">
                                <span>Shipping</span>
                                <span>₱0.00</span>
                            </li> -->
                            <li class="list-group-item d-flex justify-content-between fw-bold text-dark fs-5">
                                <span>Total</span>
                                <span class="text-danger">₱<?= number_format($subtotal, 2) ?></span>
                            </li>
                        </ul>

                        <a href="checkout.php?from_cart=1" class="btn btn-warning w-100 py-2 fw-semibold text-dark shadow-sm">
                            <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="text-center mt-5">
            <img src="assets/img/empty-cart.svg" alt="Empty Cart" class="img-fluid mb-3" style="max-width: 200px;">
            <h5 class="text-muted">Your cart is empty 🛍️</h5>
            <a href="index.php" class="btn btn-warning mt-3 fw-semibold">
                <i class="fas fa-arrow-left me-1"></i> Continue Shopping
            </a>
        </div>
    <?php endif; ?>
</div>

<style>
    .card {
        border-radius: 0.75rem;
    }

    .btn-warning {
        background: linear-gradient(90deg, #FFA726, #FB8C00);
        border: none;
        transition: all 0.2s ease-in-out;
    }

    .btn-warning:hover {
        background: linear-gradient(90deg, #FB8C00, #F57C00);
        color: #fff;
    }

    .list-group-item {
        border: none;
        padding: 0.75rem 0;
    }

    @media (max-width: 576px) {
        h2 {
            font-size: 1.25rem;
        }

        .card-body {
            padding: 0.75rem;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>