<?php
session_start();
include 'includes/db.php';

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$product = null;
$images = [];

if ($product_id > 0) {
    // Fetch product info
    $sql = "SELECT * FROM products WHERE product_id = $product_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        // Fetch images
        $image_query = "SELECT image_path FROM product_images WHERE product_id = $product_id ORDER BY image_id ASC";
        $image_result = mysqli_query($conn, $image_query);
        while ($img_row = mysqli_fetch_assoc($image_result)) {
            $images[] = $img_row['image_path'];
        }
    }
}
include 'includes/header.php';
?>

<div class="container mt-4">
    <?php if ($product): ?>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <div class="row mt-4">
            <!-- Product Images -->
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center">
                        <img id="mainImage" src="admin/<?= htmlspecialchars($images[0] ?? 'assets/img/placeholder.jpg') ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid rounded"
                            style="max-height: 450px; object-fit: contain;"
                            onerror="this.src='assets/img/placeholder.jpg';">
                    </div>
                    <div class="d-flex justify-content-center flex-wrap gap-2 px-3 pb-3">
                        <?php foreach ($images as $index => $img): ?>
                            <div class="thumb border rounded p-1" style="cursor: pointer;"
                                onclick="document.getElementById('mainImage').src='admin/<?= htmlspecialchars($img) ?>'; highlightThumb(this)">
                                <img src="admin/<?= htmlspecialchars($img) ?>" class="rounded"
                                    style="width: 70px; height: 70px; object-fit: cover;"
                                    onerror="this.src='assets/img/placeholder.jpg';">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Description -->
                <div class="card mt-4 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-2">Description</h5>
                        <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                </div>

                <!-- Reviews -->
                <div class="card mt-4 shadow-sm" id="reviews-section">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Customer Reviews</h5>
                        <div id="reviews-list">
                            <p class="text-muted fst-italic">Loading reviews...</p>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .btn-outline-dark {
                    border-color: #ccc;
                    color: #333;
                    transition: all 0.2s ease;
                }

                .btn-outline-dark:hover,
                .btn-check:checked+.btn-outline-dark {
                    background: linear-gradient(90deg, #ff6b35, #ff3c00);
                    color: #fff;
                    border-color: transparent;
                }

                .text-decoration-line-through {
                    opacity: 0.7;
                }

                .badge.bg-warning.text-dark {
                    font-size: 0.85rem;
                    border-radius: 0.4rem;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                }
            </style>

            <?php
            $isNew = (new DateTime())->diff(new DateTime($product['created_at']))->days < 2;

            // Determine discount
            $discount = 0;

            // Check if user is new (no orders yet)
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $check_orders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id = $user_id");
                $user_orders = $check_orders->fetch_assoc()['total'] ?? 0;

                if ($user_orders == 0) {
                    $discount = 0.20; // 10% discount for new users
                }
            }

            // If product is new
            if ($isNew) {
                $discount = max($discount, 0.20); // 10% for new products
            }

            // Compute final prices
            $original_price = $product['price'];
            $discounted_price = $discount > 0 ? $original_price * (1 - $discount) : $original_price;
            ?>


            <!-- Product Info & Variants -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <!-- Product Name -->
                        <h1 class="h4 fw-bold mb-2 text-dark"><?= htmlspecialchars($product['name']) ?></h1>

                        <div class="mb-3">
                            <?php if ($discount > 0): ?>
                                <span class="fs-3 fw-bold text-danger">₱<?= number_format($discounted_price, 2) ?></span>
                                <span
                                    class="text-muted text-decoration-line-through ms-2 fs-5">₱<?= number_format($original_price, 2) ?></span>
                                <span class="badge bg-warning text-dark ms-2 fw-semibold"><?= intval($discount * 100) ?>%
                                    OFF</span>
                            <?php else: ?>
                                <span class="fs-3 fw-bold text-danger">₱<?= number_format($original_price, 2) ?></span>
                            <?php endif; ?>
                        </div>


                        <form action="cart_add.php" method="POST">
                            <?php
                            $variant_query = "SELECT variant_type, variant_value FROM product_variants WHERE product_id = $product_id";
                            $variant_result = $conn->query($variant_query);

                            $variants = [];
                            if ($variant_result && $variant_result->num_rows > 0) {
                                while ($row = $variant_result->fetch_assoc()) {
                                    $type = strtolower($row['variant_type']);
                                    $value = $row['variant_value'];
                                    $variants[$type][] = $value;
                                }
                            }
                            ?>

                            <!-- Variants -->
                            <?php foreach ($variants as $type => $values): ?>
                                <div class="mb-2">
                                    <label class="form-label fw-semibold mb-1 text-secondary"><?= ucfirst($type) ?>:</label>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php foreach ($values as $index => $value): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="btn-check" type="radio" name="variant[<?= $type ?>]"
                                                    id="variant_<?= $type ?>_<?= $index ?>" value="<?= htmlspecialchars($value) ?>"
                                                    required>
                                                <label class="btn btn-outline-dark px-3 py-2 text-sm fw-semibold"
                                                    style="border-radius: 4px;" for="variant_<?= $type ?>_<?= $index ?>">
                                                    <?= htmlspecialchars($value) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- Quantity -->
                            <div class="d-flex align-items-center mb-3">
                                <span class="fw-semibold me-2">Quantity:</span>
                                <div class="input-group" style="width: 130px;">
                                    <button class="btn btn-outline-dark" type="button"
                                        onclick="changeQuantity(-1)">−</button>
                                    <input type="number" class="form-control text-center" name="qty" id="quantity" value="1"
                                        min="1" max="99">
                                    <button class="btn btn-outline-dark" type="button"
                                        onclick="changeQuantity(1)">+</button>
                                </div>
                                <small class="text-muted ms-2"><?= $product['stock'] ?> available</small>
                            </div>

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            <input type="hidden" name="name" value="<?= $product['name'] ?>">
                            <input type="hidden" name="price" value="<?= $product['price'] ?>">
                            <input type="hidden" name="price" value="<?= $discounted_price ?>">

                            <!-- <input type="hidden" name="qty" id="form_quantity" value="1"> -->
                            <input type="hidden" name="image" value="<?= $images[0] ?? 'assets/img/placeholder.jpg' ?>">

                            <div class="d-flex gap-2">
                                <button type="submit" name="add_to_cart" class="btn text-white fw-semibold flex-fill py-3"
                                    style="background: linear-gradient(90deg, #ff6b35, #ff3c00); border-radius: 6px;">
                                    <i class="fas fa-cart-plus me-2"></i> Add to Cart
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

        </div>
    <?php else: ?>
        <div class="alert alert-danger">Product not found.</div>
    <?php endif; ?>
</div>
<script>
    const qtyInput = document.getElementById("quantity");

    function changeQuantity(delta) {
        let value = parseInt(qtyInput.value) || 1;
        value = Math.max(1, Math.min(99, value + delta));
        qtyInput.value = value;
    }

    qtyInput.addEventListener("input", function() {
        let value = parseInt(this.value);
        this.value = isNaN(value) ? 1 : Math.max(1, Math.min(99, value));
    });
</script>

<script>
    const PRODUCT_ID = <?= (int) $product['product_id'] ?>;

    function loadReviews(productId) {
        fetch(`fetch_reviews.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                const reviewsList = document.getElementById('reviews-list');
                reviewsList.innerHTML = '';

                if (data.length === 0) {
                    reviewsList.innerHTML = `<p class="text-muted fst-italic">No reviews yet. Be the first to review this product.</p>`;
                    return;
                }

                data.forEach(review => {
                    reviewsList.innerHTML += `
                        <div class="mb-3 pb-2 border-bottom">
                            <strong>${review.email}</strong> 
                            <span class="text-warning">${'★'.repeat(review.rating)}</span>
                            <p class="mb-1">${review.review}</p>
                            <small class="text-muted">${new Date(review.created_at).toLocaleDateString()}</small>
                        </div>
                    `;
                });
            })
            .catch(() => {
                document.getElementById('reviews-list').innerHTML = `<p class="text-danger">Failed to load reviews.</p>`;
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        loadReviews(PRODUCT_ID);
    });


    // Highlight selected thumbnail
    function highlightThumb(el) {
        document.querySelectorAll(".thumb").forEach(t => t.classList.remove("border-primary"));
        el.classList.add("border-primary");
    }
</script>

<?php include 'includes/footer.php'; ?>