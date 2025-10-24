<?php
session_start();
include 'includes/db.php';

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

// ✅ Fetch default address
$address = [];
if ($user_id) {
    $stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id = ? ORDER BY is_default DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $address = $result->fetch_assoc();
    $stmt->close();
}

// ✅ Handle from cart session
$cart_products = [];
if (isset($_GET['from_cart']) && $_GET['from_cart'] == 1) {
    $query = "SELECT * FROM cart WHERE user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        while ($item = mysqli_fetch_assoc($result)) {
            $cart_id = $item['cart_id'];
            $variants_result = mysqli_query($conn, "SELECT * FROM cart_variants WHERE cart_id = $cart_id");
            $variants = [];
            while ($variant = mysqli_fetch_assoc($variants_result)) {
                $variants[] = $variant;
            }
            $item['variants'] = $variants;
            $cart_products[] = $item;
        }
        $_SESSION['checkout_cart'] = $cart_products;
        mysqli_query($conn, "DELETE FROM cart_variants WHERE cart_id IN (SELECT cart_id FROM cart WHERE user_id = '$user_id')");
        mysqli_query($conn, "DELETE FROM cart WHERE user_id = '$user_id'");
        header("Location: checkout.php");
        exit;
    }
}

$cart_products = $_SESSION['checkout_cart'] ?? [];

// ✅ Handle order submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $address_id = $_POST['address_id'] ?? null;
    $shipping_method = $_POST['shipping_method'] ?? 'Standard';
    $payment_method = $_POST['payment_method'] ?? 'COD';
    $subtotal = floatval($_POST['subtotal'] ?? 0);
    $shipping_fee = floatval($_POST['shipping_fee'] ?? 0);
    $promo_discount = floatval($_POST['voucher'] ?? 0);
    $total = floatval($_POST['total'] ?? 0);
    $promo_code = $_POST['promo_code'] ?? '';

    $order_uid = 'ES' . rand(10000, 99999);

    // ✅ Insert into orders
    $stmt = $conn->prepare("INSERT INTO orders 
    (order_id, user_id, address_id, shipping_method, payment_method, subtotal, shipping_fee, promo_code, promo_discount, total, order_date, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'Pending')");
    $stmt->bind_param(
        "siisssddds",
        $order_uid,
        $user_id,
        $address_id,
        $shipping_method,
        $payment_method,
        $subtotal,
        $shipping_fee,
        $promo_code,
        $promo_discount,
        $total
    );
    $stmt->execute();
    $stmt->close();

    // ✅ Insert order items
    if (!empty($_SESSION['checkout_cart'])) {
        foreach ($_SESSION['checkout_cart'] as $item) {
            $product_id = $item['product_id'];
            $product_name = $item['product_name'];
            $product_image = $item['product_image'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $subtotal_item = $quantity * $price;

            $stmt = $conn->prepare("INSERT INTO order_items 
                (order_id, product_id, product_name, product_image, quantity, price, subtotal) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sissidd", $order_uid, $product_id, $product_name, $product_image, $quantity, $price, $subtotal_item);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE products 
                SET stock = stock - ?, sold = sold + ? 
                WHERE product_id = ?");
            $stmt->bind_param("iii", $quantity, $quantity, $product_id);
            $stmt->execute();
            $stmt->close();

            if (!empty($item['variants'])) {
                foreach ($item['variants'] as $variant) {
                    $variant_type = $variant['variant_type'];
                    $variant_value = $variant['variant_value'];
                    $stmt = $conn->prepare("INSERT INTO order_item_variants 
                        (order_id, product_id, variant_type, variant_value) 
                        VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("siss", $order_uid, $product_id, $variant_type, $variant_value);
                    $stmt->execute();
                    $stmt->close();
                }
            }

            if (!empty($promo_code)) {
                $stmt = $conn->prepare("UPDATE vouchers SET is_used = 1 WHERE code = ?");
                $stmt->bind_param("s", $promo_code);
                $stmt->execute();
                $stmt->close();
            }
        }
    }

    // ✅ Send confirmation email
    $user_query = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_result = $user_query->get_result();
    $user = $user_result->fetch_assoc();
    $user_query->close();

    $username = $user['username'];
    $email = $user['email'];

    $product_rows = '';
    $items_query = $conn->prepare("SELECT product_name, quantity, price, subtotal FROM order_items WHERE order_id = ?");
    $items_query->bind_param("s", $order_uid);
    $items_query->execute();
    $result = $items_query->get_result();
    while ($row = $result->fetch_assoc()) {
        $product_rows .= "
        <tr>
            <td style='padding:8px 10px; border-bottom:1px solid #eee;'>{$row['product_name']}</td>
            <td align='center'>{$row['quantity']}</td>
            <td align='right'>₱" . number_format($row['price'], 2) . "</td>
            <td align='right'>₱" . number_format($row['subtotal'], 2) . "</td>
        </tr>";
    }
    $items_query->close();

    // ✅ Configure PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'shophubincorp@gmail.com';
        $mail->Password = 'qlxt wjou dskq iofw'; // App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('shophubincorp@gmail.com', 'ShopHub');
        $mail->addAddress($email, $username);
        $mail->isHTML(true);
        $mail->Subject = "Order Confirmation - #$order_uid";

        $mail->Body = "
        <div style='font-family:Arial,sans-serif;background:#fff7f1;padding:25px;border-radius:10px;max-width:600px;margin:auto;border:1px solid #ffe0c0;'>
            <div style='text-align:center;margin-bottom:20px;'>
                <img src='Shophub.png' alt='ShopHub' width='100' style='border-radius:50px;'>
                <h2 style='color:#ff7f32;margin-top:10px;'>Thank You for Your Order!</h2>
                <p style='color:#555;'>Hi <strong>{$username}</strong>, your order <strong>#{$order_uid}</strong> has been successfully placed.</p>
            </div>

            <table width='100%' cellspacing='0' cellpadding='8' style='border-collapse:collapse;background:white;border-radius:10px;overflow:hidden;'>
                <thead style='background:#ff914d;color:white;'>
                    <tr>
                        <th align='left'>Product</th>
                        <th>Qty</th>
                        <th align='right'>Price</th>
                        <th align='right'>Subtotal</th>
                    </tr>
                </thead>
                <tbody>{$product_rows}</tbody>
            </table>

            <div style='margin-top:20px;font-size:15px;color:#444;'>
                <p><strong>Subtotal:</strong> ₱" . number_format($subtotal, 2) . "</p>
                <p><strong>Shipping:</strong> ₱" . number_format($shipping_fee, 2) . "</p>
                <p><strong>Discount:</strong> ₱" . number_format($promo_discount, 2) . "</p>
                <p style='font-size:18px;color:#e85d04;'><strong>Total:</strong> ₱" . number_format($total, 2) . "</p>
            </div>

            <div style='text-align:center;margin-top:30px;'>
                <a href='http://192.168.100.18:8080/shophub/my_purchases.php' 
                   style='background:#ff7f32;color:white;text-decoration:none;padding:12px 25px;border-radius:8px;font-weight:bold;'>
                   View My Order
                </a>
            </div>

            <p style='margin-top:25px;font-size:13px;color:#666;text-align:center;'>
                We'll notify you again once your order is being prepared.<br>
                Thank you for shopping with <strong style='color:#ff7f32;'>ShopHub</strong>!
            </p>
        </div>";

        $mail->send();
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
    }

    // ✅ Finish
    unset($_SESSION['checkout_cart']);
    header("Location: order_success.php?order_id=$order_uid");
    exit();
}

include 'includes/header.php';
?>

<style>
    body {
        background: linear-gradient(135deg, #ffecd2, #fcb69f);
        min-height: 100vh;
    }

    .card {
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .total {
        font-size: 1.1rem;
    }
</style>
<div class="container my-4">
    <div class="row g-4">
        <!-- Left pColumn - Main Checkout Form -->
        <div class="col-md-8">
            <!-- Delivery Address -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-map-marker-alt text-primary me-2"></i>
                        Delivery Address
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($address)): ?>
                        <div class="address-section">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= htmlspecialchars($address['first_name'] . ' ' . $address['last_name']) ?> (<?= htmlspecialchars($address['phone']) ?>)</h6>
                                    <p class="mb-1"><?= htmlspecialchars($address['address_line_1']) ?></p>
                                    <p class="mb-0">
                                        <?= htmlspecialchars($address['city']) ?>,
                                        <?= htmlspecialchars($address['province']) ?>,
                                        <?= htmlspecialchars($address['region']) ?> <?= htmlspecialchars($address['zip_code']) ?>
                                    </p>
                                    <?php if ($address['is_default']): ?>
                                        <span class="badge bg-primary">Default</span>
                                    <?php endif; ?>
                                </div>
                                <a href="profile.php#addresses" class="btn btn-outline-primary btn-sm">Change</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <p>No delivery address found. <a href="profile.php#addresses" class="btn btn-sm btn-primary">Add Address</a></p>
                    <?php endif; ?>
                </div>
            </div>



            <!-- Products Ordered -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-box text-primary me-2"></i>
                        Products Ordered
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($cart_products)): ?>
                        <?php foreach ($cart_products as $item): ?>
                            <div class="product-item">
                                <div class="row align-items-center">
                                    <div class="col-md-2 col-3">
                                        <img src="admin/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="product-image">
                                    </div>
                                    <div class="col-md-6 col-9">
                                        <div class="product-name"><?= htmlspecialchars($item['product_name']) ?></div>
                                        <div class="product-specs">
                                            <?php foreach ($item['variants'] as $variant): ?>
                                                <span class="badge bg-secondary me-1"><?= htmlspecialchars(ucfirst($variant['variant_type'])) ?>: <?= htmlspecialchars($variant['variant_value']) ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6">
                                        <div class="quantity-control">
                                            <input type="number" class="quantity-input" value="<?= $item['quantity'] ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-6 text-end">
                                        <div class="price">₱<?= number_format($item['price'], 2) ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No product selected for checkout.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            // $region = $address['region'] ?? '';
            // $city = $address['city'] ?? '';
            // $province = $address['province'] ?? '';

            // Example condition: Only Metro Manila cities can use Same Day Delivery
            // $is_same_day_available = in_array(strtolower($city), [
            // 'Manila',
            // 'MANILA',
            // 'manila',
            // 'quezon city',
            // 'Quezon City',
            // 'quezon city',
            // 'makati',
            // 'taguig',
            // 'pasay',
            // 'mandaluyong',
            // 'pasig',
            // 'caloocan',
            // 'san juan',
            // 'marikina',
            // 'parañaque',
            // 'las piñas',
            //     'malabon',
            //     'navotas',
            //     'valenzuela'
            // ]);

            // Example condition: Only Luzon region can use Express
            // $is_express_available = stripos($region, 'Luzon') !== false;
            ?>
            <style>
                .shipping-option {
                    border: 2px solid #ddd;
                    border-radius: 10px;
                    padding: 14px 16px;
                    margin-bottom: 10px;
                    transition: all 0.3s ease;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    position: relative;
                    background-color: #fff;
                }

                .shipping-option:hover {
                    border-color: #0d6efd;
                    background-color: #f8faff;
                    transform: translateY(-2px);
                }

                .shipping-option.selected {
                    border-color: #0d6efd;
                    background-color: #e9f3ff;
                    box-shadow: 0 3px 10px rgba(13, 110, 253, 0.15);
                }

                /* .shipping-option .checkmark {
                    position: absolute;
                    top: 12px;
                    right: 14px;
                    background-color: #0d6efd;
                    color: white;
                    font-size: 0.9rem;
                    border-radius: 50%;
                    width: 22px;
                    height: 22px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transform: scale(0.5);
                    transition: all 0.25s ease;
                } */

                .shipping-option.selected .checkmark {
                    opacity: 1;
                    transform: scale(1);
                }

                .shipping-option input[type="radio"] {
                    display: none;
                }

                .shipping-icon {
                    font-size: 1.6rem;
                    margin-right: 12px;
                    color: #0d6efd;
                }

                .shipping-text {
                    flex: 1;
                    font-size: 0.95rem;
                }

                .shipping-text .text-muted {
                    font-size: 0.8rem;
                }

                .shipping-price {
                    font-weight: bold;
                    color: #333;
                }
            </style>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-truck text-primary me-2"></i>
                        Shipping Method
                    </h5>
                </div>
                <div class="card-body">

                    <!-- Standard Delivery -->
                    <label class="shipping-option selected">
                        <input type="radio" name="shipping_option" value="50" checked>
                        <i class="fas fa-box-open shipping-icon"></i>
                        <div class="shipping-text">
                            Standard Delivery
                            <div class="text-muted small">Arrives in 3-5 business days</div>
                        </div>
                        <div class="shipping-price">₱50.00</div>
                        <!-- <div class="checkmark"><i class="fas fa-check"></i></div> -->
                    </label>

                    <!-- Express Delivery -->
                    <label class="shipping-option">
                        <input type="radio" name="shipping_option" value="100">
                        <i class="fas fa-shipping-fast shipping-icon"></i>
                        <div class="shipping-text">
                            Express Delivery
                            <div class="text-muted small">Arrives in 1-2 business days</div>
                        </div>
                        <div class="shipping-price">₱100.00</div>
                        <!-- <div class="checkmark"><i class="fas fa-check"></i></div> -->
                    </label>
                    <?php // endif; 
                    ?>

                    <!-- Same Day Delivery -->
                    <?php
                    date_default_timezone_set("Asia/Manila");
                    $arrival_time = date("h:i A", strtotime("+3 hours"));
                    ?>
                    <label class="shipping-option">
                        <input type="radio" name="shipping_option" value="150">
                        <i class="fas fa-bolt shipping-icon"></i>
                        <div class="shipping-text">
                            Same Day Delivery
                            <div class="text-muted small">Arrives at <?= $arrival_time ?></div>
                        </div>
                        <div class="shipping-price">₱150.00</div>
                        <!-- <div class="checkmark"><i class="fas fa-check"></i></div> -->
                    </label>
                    <?php //endif; 
                    ?>

                    <!-- <?php if (!$is_express_available && !$is_same_day_available): ?>
                        <div class="alert alert-info mt-2">
                            Only Standard Delivery is available for your area.
                        </div>
                    <?php endif; ?> -->
                </div>
            </div>

            <script>
                document.querySelectorAll('.shipping-option').forEach(option => {
                    option.addEventListener('click', () => {
                        document.querySelectorAll('.shipping-option').forEach(o => o.classList.remove('selected'));
                        option.classList.add('selected');
                        option.querySelector('input[type="radio"]').checked = true;
                    });
                });
            </script>


            <style>
                .payment-method {
                    border: 2px solid #ddd;
                    border-radius: 10px;
                    padding: 14px 16px;
                    margin-bottom: 10px;
                    transition: all 0.3s ease;
                    cursor: pointer;
                    background-color: #fff;
                    display: flex;
                    align-items: center;
                }

                .payment-method:hover {
                    border-color: #0d6efd;
                    background-color: #f8faff;
                    transform: translateY(-2px);
                }

                .payment-method.selected {
                    border-color: #0d6efd;
                    background-color: #e9f3ff;
                    box-shadow: 0 3px 10px rgba(13, 110, 253, 0.15);
                }

                .payment-method input[type="radio"] {
                    display: none;
                }

                .payment-method img,
                .payment-method i {
                    width: 42px;
                    height: 42px;
                    margin-right: 14px;
                    object-fit: cover;
                }

                .payment-text strong {
                    font-size: 1rem;
                }

                .payment-text .text-muted {
                    font-size: 0.85rem;
                }
            </style>

            <!-- Payment Method -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Payment Method
                    </h5>
                </div>
                <div class="card-body">

                <!-- GCash -->
        <label class="payment-method">
            <input type="radio" name="payment" value="GCash">
            <img src="https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?w=40&h=40&fit=crop" alt="GCash">
            <div class="payment-text">
                <strong>GCash</strong>
                <div class="text-muted">Pay with your GCash e-wallet</div>
            </div>
        </label>

                    <!-- Credit/Debit Card -->
        <label class="payment-method">
            <input type="radio" name="payment" value="Card">
            <i class="fas fa-credit-card text-primary"></i>
            <div class="payment-text">
                <strong>Credit/Debit Card</strong>
                <div class="text-muted">Visa, Mastercard, American Express</div>
            </div>
        </label>

                   <!-- Cash on Delivery -->
        <label class="payment-method selected">
            <input type="radio" name="payment" value="COD" checked>
            <i class="fas fa-money-bill-wave text-success"></i>
            <div class="payment-text">
                <strong>Cash on Delivery</strong>
                <div class="text-muted">Pay when you receive your order</div>
            </div>
        </label>
                </div>
            </div>
        </div>
        <?php
        $subtotal = 0;
        $shipping_fee = isset($_POST['shipping_fee']) ? floatval($_POST['shipping_fee']) : 0;
        $promo_code = $_POST['promo_code'] ?? '';
        $promo_discount = 0;

        foreach ($cart_products as $item) {
            $price = floatval($item['price']);
            $qty = intval($item['quantity']);
            $subtotal += ($price * $qty);
        }

        if (isset($_POST['apply_voucher']) && !empty($promo_code)) {
            $stmt = $conn->prepare("SELECT discount, expiry, is_used FROM vouchers WHERE code = ?");
            $stmt->bind_param("s", $promo_code);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $current_date = date('Y-m-d');

                if ($row['is_used']) {
                    $voucher_error = "This voucher has already been used.";
                    $promo_code = '';
                } elseif ($row['expiry'] < $current_date) {
                    $voucher_error = "This voucher has expired.";
                    $promo_code = '';
                } else {
                    $promo_discount = floatval($row['discount']);
                }
            } else {
                $voucher_error = "Invalid voucher code.";
                $promo_code = '';
            }
            $stmt->close();
        }


        $total = ($subtotal + $shipping_fee) - $promo_discount;



        ?>

        <!-- Right Column - Order Summary -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt text-primary me-2"></i>
                        Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span>₱<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping Fee:</span>
                        <span id="shipping-fee-display">₱0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Promo Discount:</span>
                        <span class="text-success">-₱<?= number_format($promo_discount, 2) ?></span>
                    </div>
                    <hr>
                    <div class="summary-row total">
                        <strong>Total:</strong>
                        <strong id="total-display">₱<?= number_format($subtotal - $promo_discount, 2) ?></strong>
                    </div>

                    <form id="orderForm" method="POST" action="checkout.php">
                        <input type="hidden" name="address_id" value="<?= htmlspecialchars($address['address_id'] ?? '') ?>">
                        <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                        <input type="hidden" id="shipping_fee" name="shipping_fee" value="0">
                        <input type="hidden" name="voucher" id="voucher_discount" value="<?= $promo_discount ?>">
                        <input type="hidden" id="total" name="total" value="<?= $subtotal - $promo_discount ?>">
                        <input type="hidden" id="promo_code" name="promo_code" value="<?= htmlspecialchars($promo_code) ?>">
                        <!-- New Hidden Inputs -->
                        <input type="hidden" id="shipping_method" name="shipping_method" value="Standard">
                         <!-- Hidden field for PHP -->
        <input type="hidden" id="payment_method" name="payment_method" value="COD">
                        <button type="submit" id="placeOrderBtn" name="place_order" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-lock me-2"></i>Place Order
                        </button>
                    </form>
                </div>
            </div>

            <!-- Voucher Selection -->
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-ticket-alt text-danger me-2"></i>Available Vouchers</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <!-- Example Voucher Card -->
                        <div class="col-12">
                            <div class="voucher-card border rounded p-2 d-flex align-items-center justify-content-between"
                                style="cursor:pointer;" onclick="applyVoucher('DISCOUNT50', 50)">
                                <div>
                                    <h6 class="mb-0 text-danger">₱50 OFF</h6>
                                    <small class="text-muted">Min. spend ₱500</small>
                                </div>
                                <i class="fas fa-check-circle text-success d-none" id="voucher-DISCOUNT50"></i>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="voucher-card border rounded p-2 d-flex align-items-center justify-content-between"
                                style="cursor:pointer;" onclick="applyVoucher('DISCOUNT100', 100)">
                                <div>
                                    <h6 class="mb-0 text-danger">₱100 OFF</h6>
                                    <small class="text-muted">Min. spend ₱1000</small>
                                </div>
                                <i class="fas fa-check-circle text-success d-none" id="voucher-DISCOUNT100"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Trust Badges -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <h6 class="mb-3">Why Choose ElectroShop?</h6>
                    <div class="row text-center">
                        <div class="col-4">
                            <i class="fas fa-shipping-fast text-primary mb-2" style="font-size: 1.5rem;"></i>
                            <small>Fast Delivery</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo-alt text-success mb-2" style="font-size: 1.5rem;"></i>
                            <small>Easy Returns</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-headset text-info mb-2" style="font-size: 1.5rem;"></i>
                            <small>24/7 Support</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-shield-alt me-1"></i>
                    Your payment information is secure
                </small>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const subtotal = parseFloat(<?= $subtotal ?>);
                const shippingInput = document.getElementById('shipping_fee');
                const totalDisplay = document.getElementById('total-display');
                const voucherDiscountInput = document.getElementById('voucher_discount');
                const promoCodeInput = document.getElementById('promo_code');

                function updateTotal() {
                    const shipping = parseFloat(shippingInput.value) || 0;
                    const voucherDiscount = parseFloat(voucherDiscountInput.value) || 0;

                    let total = subtotal + shipping - voucherDiscount;
                    if (total < 0) total = 0;

                    totalDisplay.textContent = "₱" + total.toFixed(2);
                    document.getElementById("total").value = total;
                }

                window.applyVoucher = function(code, discount) {
                    // Remove all check icons
                    document.querySelectorAll('.voucher-card i').forEach(el => el.classList.add('d-none'));

                    // Show selected check
                    document.getElementById("voucher-" + code).classList.remove("d-none");

                    // Update hidden inputs
                    voucherDiscountInput.value = discount;
                    promoCodeInput.value = code;

                    // Update discount display
                    const discountDisplay = document.querySelector(".summary-row .text-success");
                    if (discountDisplay) discountDisplay.textContent = "-₱" + discount.toFixed(2);

                    updateTotal();
                }

                // Update total when shipping changes
                const shippingRadios = document.querySelectorAll('input[name="shipping_option"]');
                shippingRadios.forEach(option => {
                    option.addEventListener("change", updateTotal);
                });

                // Initial total
                updateTotal();
            });
        </script>


    </div>


</div>

</div>
</div>
<script>
  document.querySelectorAll('.payment-method').forEach(method => {
    method.addEventListener('click', () => {
        // Remove highlight from all payment options
        document.querySelectorAll('.payment-method').forEach(m => m.classList.remove('selected'));

        // Highlight selected one
        method.classList.add('selected');

        // Update the hidden input with selected method
        const selectedValue = method.querySelector('input[type="radio"]').value;
        document.getElementById('payment_method').value = selectedValue;
    });
});

</script>
<script>
    document.querySelector(".btn-outline-primary").addEventListener("click", function() {
        const voucherInput = document.querySelector('input[placeholder="Enter voucher code"]').value;
        let voucherDiscount = 0;

        // Example fixed promo code logic
        if (voucherInput.toLowerCase() === "save50") {
            voucherDiscount = 50;
        }

        // Update DOM
        document.querySelector(".text-success").textContent = "-₱" + voucherDiscount.toFixed(2);

        // Update total display
        const subtotal = <?= $subtotal ?>;
        const shipping = <?= $shipping_fee ?>;
        const total = subtotal + shipping - voucherDiscount;

        document.getElementById("total-amount").textContent = "₱" + total.toFixed(2);
        document.getElementById("final-total").textContent = "₱" + total.toFixed(2);
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const shippingRadios = document.querySelectorAll('input[name="shipping_option"]');
        const paymentRadios = document.querySelectorAll('input[name="payment"]');
        const shippingFeeDisplay = document.getElementById('shipping-fee-display');
        const totalDisplay = document.getElementById('total-display');
        const hiddenShippingFee = document.getElementById('shipping_fee');
        const hiddenTotal = document.getElementById('total');
        const hiddenShippingMethod = document.getElementById('shipping_method');
        const hiddenPaymentMethod = document.getElementById('payment_method');

        const subtotal = <?= $subtotal ?>;
        const promoDiscount = <?= $promo_discount ?>;

        function updateShippingAndTotal() {
            const selected = document.querySelector('input[name="shipping_option"]:checked');
            const shippingFee = parseFloat(selected.value);
            const methodLabel = selected.closest('.shipping-option').querySelector('.shipping-text').childNodes[0].textContent.trim();
            const total = subtotal + shippingFee - promoDiscount;

            shippingFeeDisplay.textContent = `₱${shippingFee.toFixed(2)}`;
            totalDisplay.textContent = `₱${total.toFixed(2)}`;
            hiddenShippingFee.value = shippingFee;
            hiddenTotal.value = total;
            hiddenShippingMethod.value = methodLabel;
        }

        // Update payment method when selected
        paymentRadios.forEach(radio => {
            radio.addEventListener("change", function() {
                const selectedLabel = this.closest('.payment-method').querySelector('strong').textContent.trim();
                hiddenPaymentMethod.value = selectedLabel;
            });
        });

        // Handle shipping selection
        document.querySelectorAll(".shipping-option").forEach(option => {
            option.addEventListener("click", () => {
                document.querySelectorAll(".shipping-option").forEach(opt => opt.classList.remove("selected"));
                option.classList.add("selected");
                option.querySelector("input").checked = true;
                updateShippingAndTotal();
            });
        });

        // Initial setup
        updateShippingAndTotal();
    });
</script>

<?php include 'includes/footer.php' ?>