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

    .flash-sale-bg {
        background: linear-gradient(90deg, #ff4d4d, #ff9933);
        position: relative;
        overflow: hidden;
    }

    .flash-sale-bg::before {
        content: "";
        position: absolute;
        top: 0;
        left: -50%;
        width: 200%;
        height: 100%;
        background: rgba(255, 255, 255, 0.05);
        transform: skewX(-20deg);
        animation: slideLight 6s linear infinite;
    }

    @keyframes slideLight {
        0% {
            left: -50%;
        }

        100% {
            left: 150%;
        }
    }

    .glow-icon {
        text-shadow: 0 0 10px rgba(255, 255, 0, 0.8);
    }

    .countdown-box {
        display: inline-block;
        background: rgba(0, 0, 0, 0.3);
        border-radius: 1rem;
        padding: 1rem 1.2rem;
        color: #fff;
        font-weight: bold;
        text-align: center;
        font-family: "Roboto Mono", monospace;
        box-shadow: 0 0 12px rgba(255, 255, 255, 0.1);
        /* Prevent flicker by hinting browser */
        will-change: contents;
    }

    .countdown-time {
        display: inline-block;
        width: 2ch;
        text-align: center;
        font-family: "Roboto Mono", monospace;
        transition: none;
        /* remove transition to prevent flicker */
        will-change: contents;
    }



    .countdown-time.update {
        transform: scale(1.2);
    }



    .countdown {
        font-family: "Roboto Mono", monospace;
        /* fixed-width font = no jumping */
        font-size: 1.8rem;
        letter-spacing: 1px;
        color: #fff;
        will-change: textContent;
        /* hint to browser for stable rendering */
    }

    .countdown span {
        display: inline-block;
        width: 2ch;
        /* keeps consistent space for two digits */
        text-align: center;
    }

    .countdown-progress {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        overflow: hidden;
        height: 8px;
        width: 100%;
        margin-top: 8px;
    }

    #progressBar {
        background: tomato;
        height: 100%;
        width: 100%;
        border-radius: 10px;
        transition: width 1s linear;
    }
</style>

<!-- FLASH SALE SECTION -->
<section class="flash-sale-section my-5">
    <div class="container text-white py-4 px-4 rounded-4 shadow-lg flash-sale-bg">
        <div class="row align-items-center justify-content-between">
            <div class="col-md-7 mb-3 mb-md-0">
                <h2 class="fw-bold mb-2 d-flex align-items-center">
                    <i class="fas fa-bolt me-2 text-warning glow-icon"></i> FLASH SALE
                </h2>
                <p class="mb-0 fs-5 text-light">🔥 Hot deals available for a limited time only! Don’t miss out.</p>
            </div>
            <div class="col-md-5 text-md-end">
                <div id="flashCountdown" class="countdown-box">
                    <span id="h1" class="countdown-time">0</span>
                    <span id="h2" class="countdown-time">0</span> :
                    <span id="m1" class="countdown-time">0</span>
                    <span id="m2" class="countdown-time">0</span> :
                    <span id="s1" class="countdown-time">0</span>
                    <span id="s2" class="countdown-time">0</span>
                </div>
                <div class="countdown-progress mt-2">
                    <div id="progressBar"></div>
                </div>
                <small class="text-light-50 fst-italic">Ends soon!</small>
            </div>
        </div>
    </div>
</section>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const totalDuration = 60 * 60 * 1000; // 1 hour in milliseconds

        // ✅ Only set endTime if it doesn't exist in localStorage
        let endTime = localStorage.getItem("flashSaleEndTime");
        if (!endTime) {
            endTime = Date.now() + totalDuration;
            localStorage.setItem("flashSaleEndTime", endTime);
        } else {
            endTime = parseInt(endTime, 10);
        }

        const h1 = document.getElementById("h1"),
            h2 = document.getElementById("h2"),
            m1 = document.getElementById("m1"),
            m2 = document.getElementById("m2"),
            s1 = document.getElementById("s1"),
            s2 = document.getElementById("s2"),
            progressBar = document.getElementById("progressBar"),
            countdownContainer = document.getElementById("flashCountdown");

        let lastH = "",
            lastM = "",
            lastS = "";

        const updateCountdown = () => {
            const now = Date.now();
            const distance = endTime - now;

            if (distance <= 0) {
                clearInterval(timer);
                countdownContainer.innerHTML = "<span class='text-warning fw-bold'>SALE ENDED</span>";
                progressBar.style.width = "0%";
                localStorage.removeItem("flashSaleEndTime"); // reset for next sale
                return;
            }

            const h = String(Math.floor((distance / (1000 * 60 * 60)) % 24)).padStart(2, "0");
            const m = String(Math.floor((distance / (1000 * 60)) % 60)).padStart(2, "0");
            const s = String(Math.floor((distance / 1000) % 60)).padStart(2, "0");

            if (h !== lastH) {
                h1.textContent = h[0];
                h2.textContent = h[1];
                lastH = h;
            }
            if (m !== lastM) {
                m1.textContent = m[0];
                m2.textContent = m[1];
                lastM = m;
            }
            if (s !== lastS) {
                s1.textContent = s[0];
                s2.textContent = s[1];
                lastS = s;
            }

            const progressPercent = Math.max(0, (distance / totalDuration) * 100);
            progressBar.style.width = `${progressPercent}%`;
        };

        const timer = setInterval(updateCountdown, 1000);
        updateCountdown();
    });
</script>

<!-- 🛍️ Featured Products -->
<div class="container mt-5">
    <h2 class="text-center mb-4 fw-bold">Featured Products</h2>
    <div class="row g-4">

        <?php
        $conn = new mysqli("localhost", "root", "", "shophub");
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

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

                // Determine discount
                $discount = 0;

                // If user is new (first login or first order)
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    $check_orders = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE user_id = $user_id");
                    $user_orders = $check_orders->fetch_assoc()['total'] ?? 0;

                    if ($user_orders == 0) {
                        $discount = 0.20; // 10% for new users
                    }
                }

                // If product is new
                if ($isNew) {
                    $discount = max($discount, 0.20); // ensure 10% minimum discount for new products
                }

                // Compute discounted price if applicable
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
    .product-img {
        height: 220px;
        object-fit: cover;
        border-radius: 0.5rem 0.5rem 0 0;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-img {
        transform: scale(1.05);
    }

    .product-card {
        border-radius: 0.75rem;
        transition: all 0.3s ease;
    }

    .product-card:hover {
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .badge.bg-warning.text-dark {
        font-weight: bold;
        font-size: 0.85rem;
        border-radius: 0.4rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .text-decoration-line-through {
        margin-left: 5px;
        font-size: 0.85em;
    }
</style>

<?php include 'includes/footer.php'; ?>