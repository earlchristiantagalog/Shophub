<?php
session_start();
include 'includes/db.php';

$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;
$product = null;
$images = [];

if ($product_id > 0) {
    $sql = "SELECT * FROM products WHERE product_id = $product_id LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);

        $image_query = "SELECT image_path FROM product_images WHERE product_id = $product_id ORDER BY image_id ASC";
        $image_result = mysqli_query($conn, $image_query);
        while ($img_row = mysqli_fetch_assoc($image_result)) {
            $images[] = $img_row['image_path'];
        }
    }
}
include 'includes/header.php';
?>

<style>
    /* --- PRODUCT PAGE DESIGN --- */
    .product-gallery img {
        max-height: 450px;
        object-fit: contain;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .thumb img {
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .thumb:hover img {
        transform: scale(1.05);
    }

    .thumb.border-primary {
        border: 2px solid #ff6b35 !important;
    }

    /* Pricing Section */
    .price-main {
        font-size: 2rem;
        font-weight: 700;
        color: #e63946;
    }

    .price-old {
        text-decoration: line-through;
        color: #888;
        font-size: 1rem;
        margin-left: 8px;
    }

    .badge-discount {
        background: linear-gradient(90deg, #ff6b35, #ff3c00);
        color: #fff;
        font-size: 0.85rem;
        font-weight: 600;
        border-radius: 6px;
        padding: 0.3rem 0.6rem;
    }

    /* Buttons */
    .btn-add-cart {
        background: linear-gradient(90deg, #ff6b35, #ff3c00);
        color: #fff;
        font-weight: 600;
        border: none;
        border-radius: 8px;
        padding: 0.75rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .btn-add-cart:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
    }

    /* Reviews */
    .review-card {
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    /* Mobile Adjustments */
    @media (max-width: 768px) {
        .price-main {
            font-size: 1.6rem;
        }

        .product-gallery img {
            max-height: 300px;
        }
    }
</style>
<div class="container mt-4 mb-5">
    <?php if ($product): ?>
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-white px-3 py-2 rounded shadow-sm">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></li>
            </ol>
        </nav>

        <div class="row mt-4 g-4 align-items-start">
            <!-- 🖼️ Product Images -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center product-gallery">
                        <img id="mainImage"
                            src="admin/<?= htmlspecialchars($images[0] ?? 'assets/img/placeholder.jpg') ?>"
                            alt="<?= htmlspecialchars($product['name']) ?>"
                            class="img-fluid"
                            onerror="this.src='assets/img/placeholder.jpg';">
                    </div>
                    <div class="d-flex justify-content-center flex-wrap gap-2 px-3 pb-3">
                        <?php foreach ($images as $img): ?>
                            <div class="thumb border rounded p-1" style="cursor: pointer;"
                                onclick="document.getElementById('mainImage').src='admin/<?= htmlspecialchars($img) ?>'; highlightThumb(this)">
                                <img src="admin/<?= htmlspecialchars($img) ?>"
                                    style="width:70px; height:70px; object-fit:cover;"
                                    onerror="this.src='assets/img/placeholder.jpg';">
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- 💬 Product Info -->
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h2 class="fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h2>
                        <?php
                        $flash_sale = null;
                        $fs_result = $conn->query("SELECT * FROM flash_sale WHERE NOW() BETWEEN start_time AND end_time LIMIT 1");
                        if ($fs_result && $fs_result->num_rows > 0) $flash_sale = $fs_result->fetch_assoc();

                        $isNew = (new DateTime())->diff(new DateTime($product['created_at']))->days < 2;
                        $discount = 0;

                        if ($flash_sale) $discount = max($discount, ((float)$flash_sale['discount'] / 100));

                        if (isset($_SESSION['user_id'])) {
                            $user_id = $_SESSION['user_id'];
                            $check_orders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id = $user_id");
                            $user_orders = $check_orders->fetch_assoc()['total'] ?? 0;
                            if ($user_orders == 0) $discount = max($discount, 0.20);
                        }

                        if ($isNew) $discount = max($discount, 0.20);

                        $original_price = $product['price'];
                        $discounted_price = $discount > 0 ? $original_price * (1 - $discount) : $original_price;
                        ?>
                        <!-- 💸 Pricing -->
                        <div class="mb-4">
                            <?php if ($discount > 0): ?>
                                <span class="price-main">₱<?= number_format($discounted_price, 2) ?></span>
                                <span class="price-old">₱<?= number_format($original_price, 2) ?></span>
                                <span class="badge-discount"><?= intval($discount * 100) ?>% OFF</span>
                            <?php else: ?>
                                <span class="price-main">₱<?= number_format($original_price, 2) ?></span>
                            <?php endif; ?>
                        </div>

                        <!-- 🧩 Variants -->
                        <?php
                        $variant_query = "SELECT variant_type, variant_value FROM product_variants WHERE product_id = $product_id";
                        $variant_result = $conn->query($variant_query);
                        $variants = [];
                        if ($variant_result && $variant_result->num_rows > 0) {
                            while ($row = $variant_result->fetch_assoc()) {
                                $variants[strtolower($row['variant_type'])][] = $row['variant_value'];
                            }
                        }
                        ?>

                        <form action="cart_add.php" method="POST">
                            <?php foreach ($variants as $type => $values): ?>
                                <div class="mb-3">
                                    <label class="fw-semibold text-secondary mb-1"><?= ucfirst($type) ?>:</label>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($values as $index => $value): ?>
                                            <input type="radio" class="btn-check"
                                                name="variant[<?= $type ?>]"
                                                id="variant_<?= $type ?>_<?= $index ?>"
                                                value="<?= htmlspecialchars($value) ?>" required>
                                            <label class="btn btn-outline-dark fw-semibold py-2 px-3"
                                                for="variant_<?= $type ?>_<?= $index ?>"><?= htmlspecialchars($value) ?></label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <!-- 🔢 Quantity -->
                            <div class="d-flex align-items-center mb-4">
                                <span class="fw-semibold me-2">Quantity:</span>
                                <div class="input-group" style="width: 130px;">
                                    <button class="btn btn-outline-dark" type="button" onclick="changeQuantity(-1)">−</button>
                                    <input type="number" class="form-control text-center" name="qty" id="quantity" value="1" min="1" max="99">
                                    <button class="btn btn-outline-dark" type="button" onclick="changeQuantity(1)">+</button>
                                </div>
                                <small class="text-muted ms-2"><?= $product['stock'] ?> available</small>
                            </div>

                            <!-- Hidden Inputs -->
                            <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                            <input type="hidden" name="name" value="<?= $product['name'] ?>">
                            <input type="hidden" name="price" value="<?= $discounted_price ?>">
                            <input type="hidden" name="image" value="<?= $images[0] ?? 'assets/img/placeholder.jpg' ?>">

                            <button type="submit" name="add_to_cart" class="btn-add-cart w-100">
                                <i class="fas fa-cart-plus me-2"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- 📘 Product Tabs (Description & Reviews) -->
        <div class="card mt-5 shadow-sm border-0">
            <div class="card-body">
                <ul class="nav nav-tabs" id="productTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" id="desc-tab" data-bs-toggle="tab" data-bs-target="#description"
                            type="button" role="tab" aria-controls="description" aria-selected="true">
                            Description
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                            type="button" role="tab" aria-controls="reviews" aria-selected="false">
                            Customer Reviews
                        </button>
                    </li>
                </ul>

                <div class="tab-content mt-3" id="productTabContent">
                    <div class="tab-pane fade show active" id="description" role="tabpanel">
                        <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div id="reviews-list">
                            <p class="text-muted fst-italic">Loading reviews...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="alert alert-danger mt-5 text-center">Product not found.</div>
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

    function highlightThumb(el) {
        document.querySelectorAll(".thumb").forEach(t => t.classList.remove("border-primary"));
        el.classList.add("border-primary");
    }

    function loadReviews(productId) {
        fetch(`fetch_reviews.php?product_id=${productId}`)
            .then(res => res.json())
            .then(data => {
                const reviewsList = document.getElementById('reviews-list');
                reviewsList.innerHTML = '';
                if (!data.length) {
                    reviewsList.innerHTML = `<p class="text-muted fst-italic">No reviews yet. Be the first to review this product.</p>`;
                    return;
                }
                data.forEach(r => {
                    reviewsList.innerHTML += `
                    <div class="review-card">
                        <strong>${r.email}</strong> 
                        <span class="text-warning">${'★'.repeat(r.rating)}</span>
                        <p>${r.review}</p>
                        <small class="text-muted">${new Date(r.created_at).toLocaleDateString()}</small>
                    </div>`;
                });
            })
            .catch(() => document.getElementById('reviews-list').innerHTML = `<p class="text-danger">Failed to load reviews.</p>`);
    }

    document.addEventListener("DOMContentLoaded", () => loadReviews(<?= (int)$product_id ?>));
</script>

<?php include 'includes/footer.php'; ?>