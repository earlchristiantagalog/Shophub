<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';
?>

<!-- 🖼️ Hero Carousel -->
<div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=1920&h=700&fit=crop" class="d-block w-100" alt="Electronics Sale">
            <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center">
                <h2 class="fw-bold text-shadow">Mega Electronics Sale</h2>
                <p>Up to 70% off on the latest gadgets</p>
                <a href="shop.php" class="btn btn-warning btn-lg mt-2 shadow-sm">Shop Now</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1498049794561-7780e7231661?w=1920&h=700&fit=crop" class="d-block w-100" alt="Smartphones">
            <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center">
                <h2 class="fw-bold text-shadow">Latest Smartphones</h2>
                <p>Discover cutting-edge technology</p>
                <a href="shop.php?category=phones" class="btn btn-light btn-lg mt-2 shadow-sm">Browse Phones</a>
            </div>
        </div>
        <div class="carousel-item">
            <img src="https://images.unsplash.com/photo-1593305841991-05c297ba4575?w=1920&h=700&fit=crop" class="d-block w-100" alt="Gaming Setup">
            <div class="carousel-caption d-flex flex-column justify-content-center align-items-center text-center">
                <h2 class="fw-bold text-shadow">Gaming Paradise</h2>
                <p>Build your dream gaming rig</p>
                <a href="shop.php?category=gaming" class="btn btn-danger btn-lg mt-2 shadow-sm">Start Building</a>
            </div>
        </div>
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<style>
    #heroCarousel .carousel-item img {
        height: 70vh;
        object-fit: cover;
    }

    .carousel-caption {
        background: rgba(0, 0, 0, 0.4);
        border-radius: 1rem;
        padding: 1.5rem 2rem;
    }

    .text-shadow {
        text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.7);
    }
</style>
<!-- 🌟 FLASH SALE SECTION -->
<section id="flashSaleSection" class="flash-sale-section my-5 d-none">
    <div class="container py-5 px-4 rounded-4 shadow-lg text-white flash-sale-container text-center">
        <div class="mb-4">
            <h2 class="fw-bold mb-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-bolt me-2 text-warning flash-icon"></i>
                <span id="flashSaleTitle">FLASH SALE</span>
            </h2>
            <p class="mb-0 fs-5">
                ⚡ Grab <span id="flashSaleDiscount">0%</span> OFF on selected items!
            </p>
        </div>

        <small class="text-light opacity-75 fst-italic" id="flashSaleLabel">Ends soon!</small>
        <div id="flashCountdown" class="countdown-clock d-flex justify-content-center gap-3"></div>
    </div>
</section>

<!-- 🌈 STYLING -->
<style>
    .flash-sale-container {
        background: linear-gradient(135deg, #ff416c, #ff4b2b);
        position: relative;
        overflow: hidden;
        border: 2px solid rgba(255, 255, 255, 0.2);
    }

    .flash-icon {
        animation: flashGlow 1.2s infinite alternate;
    }

    @keyframes flashGlow {
        from {
            text-shadow: 0 0 10px #ffd700, 0 0 15px #ffae00;
        }

        to {
            text-shadow: 0 0 20px #ffdf00, 0 0 35px #ff6a00;
        }
    }

    .countdown-clock {
        margin-top: 1rem;
    }

    .time-box {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 10px;
        min-width: 70px;
        padding: 10px;
        text-align: center;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.15);
        transition: all 0.3s ease;
    }

    .time-box:hover {
        transform: scale(1.05);
        background: rgba(255, 255, 255, 0.15);
    }

    .time-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: #fff;
        display: block;
    }

    .time-label {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.75);
        text-transform: uppercase;
    }

    .flash-sale-container::before {
        content: "";
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 90deg at 50% 50%, rgba(255, 255, 255, 0.15), transparent 30%);
        animation: rotateGlow 6s linear infinite;
        z-index: 0;
    }

    .flash-sale-container>* {
        position: relative;
        z-index: 1;
    }

    @keyframes rotateGlow {
        to {
            transform: rotate(360deg);
        }
    }
</style>

<!-- ⚙️ SCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", () => {
        fetch("fetch_active_flashsale.php")
            .then(res => res.json())
            .then(data => {
                if (!data) return;

                const section = document.getElementById("flashSaleSection");
                section.classList.remove("d-none");

                const title = document.getElementById("flashSaleTitle");
                const discount = document.getElementById("flashSaleDiscount");
                const countdown = document.getElementById("flashCountdown");
                const label = document.getElementById("flashSaleLabel");

                title.textContent = data.title;
                discount.textContent = data.discount + "%";

                const startTime = new Date(data.start_time).getTime();
                const endTime = new Date(data.end_time).getTime();
                let status = data.status;

                function updateCountdown() {
                    const now = Date.now();
                    let distance, textLabel;

                    // Check status
                    if (status === "upcoming" && now < startTime) {
                        distance = startTime - now;
                        textLabel = "Starts in";
                    } else {
                        distance = endTime - now;
                        textLabel = "Ends in";
                        status = "active";
                    }

                    if (distance <= 0 && status === "active") {
                        clearInterval(timer);
                        section.remove();
                        return;
                    }

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
                    const minutes = Math.floor((distance / (1000 * 60)) % 60);
                    const seconds = Math.floor((distance / 1000) % 60);

                    label.textContent = `${textLabel}!`;

                    countdown.innerHTML = `
          <div class="time-box"><span class="time-number">${String(days).padStart(2, "0")}</span><span class="time-label">Days</span></div>
          <div class="time-box"><span class="time-number">${String(hours).padStart(2, "0")}</span><span class="time-label">Hours</span></div>
          <div class="time-box"><span class="time-number">${String(minutes).padStart(2, "0")}</span><span class="time-label">Mins</span></div>
          <div class="time-box"><span class="time-number">${String(seconds).padStart(2, "0")}</span><span class="time-label">Secs</span></div>
        `;
                }

                updateCountdown();
                const timer = setInterval(updateCountdown, 1000);
            })
            .catch(err => console.error("Error loading flash sale:", err));
    });
</script>


<!-- 🛍️ Featured Products -->
<div class="container mt-5">
    <h2 class="text-center mb-4 fw-bold">Featured Products</h2>
    <div class="row g-4">

        <?php
        $conn = new mysqli("localhost", "root", "", "shophub");
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
        // ✅ Check if there’s an active flash sale
        $flash_sale = null;
        $fs_result = $conn->query("SELECT * FROM flash_sale WHERE NOW() BETWEEN start_time AND end_time LIMIT 1");
        if ($fs_result && $fs_result->num_rows > 0) {
            $flash_sale = $fs_result->fetch_assoc();
        }

        $sql = "SELECT p.product_id, p.name, p.price, p.sold, p.created_at, img.image_path
        FROM products p
        LEFT JOIN (
            SELECT product_id, image_path FROM product_images WHERE is_primary = 1
        ) img ON p.product_id = img.product_id
        WHERE p.status = 'active'
        ORDER BY p.created_at DESC LIMIT 12";

        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $product_id = $row['product_id'];
                $name = htmlspecialchars($row['name']);
                $price = number_format($row['price'], 2);
                $sold = number_format($row['sold']);
                $image = !empty($row['image_path']) ? 'admin/' . $row['image_path'] : 'assets/img/placeholder.jpg';

                // Check if product is new (added within 2 days)
                $isNew = (new DateTime())->diff(new DateTime($row['created_at']))->days < 2;

                $discount = 0;

                // Apply flash sale discount if active
                if ($flash_sale) {
                    $discount = max($discount, ((float)$flash_sale['discount'] / 100));
                }

                // If user is new
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $check_orders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id = $user_id");
                    $user_orders = $check_orders->fetch_assoc()['total'] ?? 0;

                    if ($user_orders == 0) {
                        $discount = max($discount, 0.20);
                    }
                }

                // If product is new
                if ($isNew) {
                    $discount = max($discount, 0.20);
                }
                $original_price = $row['price'];
                $discounted_price = $discount > 0 ? $original_price * (1 - $discount) : $original_price;



                // Get ratings
                $stmt = $conn->prepare("SELECT AVG(rating), COUNT(*) FROM reviews WHERE product_id = ?");
                $stmt->bind_param("i", $product_id);
                $stmt->execute();
                $stmt->bind_result($avg, $total);
                $stmt->fetch();
                $stmt->close();
                $avg_rating = number_format($avg ?? 0, 1);
        ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="card product-card border-0 shadow-sm h-100 position-relative">
                        <a href="product-view.php?product_id=<?= $product_id ?>" class="stretched-link"></a>
                        <?php if ($isNew): ?>
                            <span class="badge bg-success position-absolute top-0 start-0 m-2">NEW</span>
                        <?php endif; ?>
                        <img src="<?= $image ?>" alt="<?= $name ?>" class="card-img-top product-img" onerror="this.src='assets/img/placeholder.jpg';">
                        <div class="card-body text-center">
                            <h6 class="fw-semibold"><?= $name ?></h6>
                            <div class="text-warning mb-1">
                                <?php
                                for ($i = 1; $i <= 5; $i++) {
                                    echo $i <= round($avg_rating) ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                                }
                                ?>
                                <span class="text-muted small">(<?= $avg_rating ?>)</span>
                            </div>
                            <?php if ($discount > 0): ?>
                                <div class="fw-bold text-danger fs-5 mb-1">
                                    ₱<?= number_format($discounted_price, 2) ?>
                                    <span class="text-muted text-decoration-line-through small">₱<?= number_format($original_price, 2) ?></span>
                                </div>
                                <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-2">
                                    <?= intval($discount * 100) ?>% OFF
                                </span>
                            <?php else: ?>
                                <div class="fw-bold text-danger fs-5 mb-1">₱<?= number_format($original_price, 2) ?></div>
                            <?php endif; ?>

                            <small class="text-muted">Sold: <?= $sold ?></small>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo '<p class="text-center text-muted">No active products found.</p>';
        }
        $conn->close();
        ?>
    </div>
</div>

<style>
    /* --- Product Card Base Styles --- */
    .product-card {
        border-radius: 0.75rem;
        overflow: hidden;
        background: #fff;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .product-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
    }

    /* --- Product Image --- */
    .product-img {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 0.5rem 0.5rem 0 0;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-img {
        transform: scale(1.05);
    }

    /* --- Product Details --- */
    .product-body {
        padding: 1rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .product-title {
        font-size: 1rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
        line-height: 1.3;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    .product-price {
        font-size: 1.1rem;
        font-weight: 700;
        color: #ee4d2d;
    }

    .text-decoration-line-through {
        color: #888;
        margin-left: 6px;
        font-size: 0.85rem;
    }

    .badge.bg-warning.text-dark {
        font-weight: 600;
        font-size: 0.85rem;
        border-radius: 0.4rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    /* --- Responsive Grid Layout --- */
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.5rem;
    }

    /* --- Mobile Optimizations --- */
    @media (max-width: 375px) {
        .product-img {
            height: 180px;
        }

        .product-title {
            font-size: 0.95rem;
        }

        .product-price {
            font-size: 1rem;
        }

        .badge.bg-warning.text-dark {
            font-size: 0.75rem;
        }
    }

    @media (max-width: 480px) {
        .product-img {
            height: 160px;
        }

        .product-title {
            font-size: 0.9rem;
        }

        .product-price {
            font-size: 0.95rem;
        }

        .product-body {
            padding: 0.8rem;
        }
    }
</style>


<?php include 'includes/footer.php'; ?>