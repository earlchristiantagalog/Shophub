<?php
include 'includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['add_to_cart']) || isset($_POST['buy_now']))) {
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error'] = "Please log in to continue.";
        // Remember user’s intent for redirect after login
        $_SESSION['redirect_after_login'] = isset($_POST['buy_now']) ? 'checkout.php' : 'cart.php';
        header("Location: login.php");
        exit();
    }

    $user_id = intval($_SESSION['user_id']);
    $product_id = intval($_POST['product_id']);
    $product_name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['qty']);
    $product_image = mysqli_real_escape_string($conn, $_POST['image']);
    $variants = $_POST['variant'] ?? [];

    // 1. Insert into cart table
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, product_image, quantity, price, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iissid", $user_id, $product_id, $product_name, $product_image, $quantity, $price);

    if ($stmt->execute()) {
        $cart_id = $stmt->insert_id;
        $stmt->close();

        // 2. Insert each variant into cart_variants
        foreach ($variants as $type => $value) {
            $stmt_variant = $conn->prepare("INSERT INTO cart_variants (cart_id, variant_type, variant_value)
                                            VALUES (?, ?, ?)");
            $stmt_variant->bind_param("iss", $cart_id, $type, $value);
            $stmt_variant->execute();
            $stmt_variant->close();
        }

        $_SESSION['success'] = "Product added to cart.";
    } else {
        $_SESSION['error'] = "Error adding to cart: " . $stmt->error;
        $stmt->close();
    }

    // ✅ If "Buy Now" clicked → go straight to checkout
    if (isset($_POST['buy_now'])) {
        header("Location: checkout.php");
        exit();
    }

    // ✅ Otherwise → go to cart
    header("Location: cart.php");
    exit();
}
?>
