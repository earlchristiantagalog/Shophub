<?php
session_start();
include 'db.php';

function redirect($url, $message)
{
    $_SESSION['message'] = $message;
    header("Location: $url");
}


// Add Admin
if (isset($_POST['add_admin'])) {
    $id1 = rand(100000, 999999);
    $id2 = rand(100000, 999999);

    $admin_id = $id1 . '-' . $id2;
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if ($full_name != '' && $email != '' && $password != '') {
        $sql = "INSERT INTO admin (admin_id, full_name, email, password) 
        VALUES ('$admin_id', '$full_name', '$email', '$password')";
        $query = mysqli_query($conn, $sql);

        if ($query) {
            redirect("admin.php", "Admin added successfully");
        }
    } else {
        redirect("admin.php", "Please fill all the details first");
    }
}


// Edit Admin
if (isset($_POST['update_admin'])) {
    $id = $_POST['id'];
    $name = $_POST['admin_name'];
    $email = $_POST['admin_email'];
    $password = $_POST['admin_password'];

    $sql = "UPDATE admin SET full_name= '$name', email='$email', password='$password' WHERE admin_id='$id'";
    $query = mysqli_query($conn, $sql);

    if ($query) {
        redirect("admin.php", "Admin updated successfully");
    } else {
        redirect("admin.php", "Something Went Wrong");
    }
}


// Login User
if (isset($_POST['login'])) {
    // Set timezone to GMT+8 (Philippine Time)
    date_default_timezone_set('Asia/Manila');

    // Clean input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    if (isset($_POST['remember'])) {
        setcookie('remember_email', $_POST['email'], time() + (86400 * 30), "/"); // 30 days
    } else {
        setcookie('remember_email', '', time() - 3600, "/");
    }

    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password (use password_verify if you store hashed passwords)
        if ($password == $user['password']) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;

            // ✅ Attendance (once per day, GMT+8)
            $admin_id = $user['id'];
            $today = date('Y-m-d');
            $now = date('Y-m-d H:i:s');

            $check = $conn->prepare("SELECT id FROM admin_attendance WHERE admin_id = ? AND login_date = ?");
            $check->bind_param("is", $admin_id, $today);
            $check->execute();
            $check->store_result();

            if ($check->num_rows == 0) {
                $insert = $conn->prepare("INSERT INTO admin_attendance (admin_id, login_date, login_time) VALUES (?, ?, ?)");
                $insert->bind_param("iss", $admin_id, $today, $now);
                $insert->execute();
            }

            // ✅ Remember Me (optional)
            if (isset($_POST['remember'])) {
                setcookie("email", $email, time() + (86400 * 30), "/"); // 30 days
            }

            redirect("index.php", "Logged in successfully");
            exit();
        } else {
            redirect("login.php", "Incorrect password");
        }
    } else {
        redirect("login.php", "Email not found");
    }
}


// Add Product
if (isset($_POST['add_product'])) {
    // Sanitize inputs
    $name = $conn->real_escape_string($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category = $conn->real_escape_string($_POST['category'] ?? '');
    $status = $conn->real_escape_string($_POST['status'] ?? '');
    $description = $conn->real_escape_string($_POST['description'] ?? '');

    // Insert product
    $sql = "INSERT INTO products (name, price, stock, category, status, description)
            VALUES ('$name', $price, $stock, '$category', '$status', '$description')";

    if ($conn->query($sql) === TRUE) {
        $product_id = $conn->insert_id;

        // === Handle Variants ===
        if (!empty($_POST['variant_types']) && !empty($_POST['variant_values'])) {
            $variant_types = $_POST['variant_types'];
            $variant_values = $_POST['variant_values'];

            for ($i = 0; $i < count($variant_types); $i++) {
                $type = $conn->real_escape_string($variant_types[$i]);
                $values = explode(',', $variant_values[$i]);

                foreach ($values as $value) {
                    $value = trim($conn->real_escape_string($value));

                    if ($type && $value) {
                        $conn->query("INSERT INTO product_variants (product_id, variant_type, variant_value)
                              VALUES ($product_id, '$type', '$value')");
                    }
                }
            }
        }


        // === Handle Images ===
        $upload_dir = 'uploads/';
        if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);

        $total = count($_FILES['images']['name']);
        $limit = min($total, 5); // Limit to 5 images

        for ($i = 0; $i < $limit; $i++) {
            $tmp_name = $_FILES['images']['tmp_name'][$i];
            $original_name = basename($_FILES['images']['name'][$i]);
            $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);
            $unique_name = uniqid('img_', true) . "." . $file_ext;
            $target_file = $upload_dir . $unique_name;

            if (move_uploaded_file($tmp_name, $target_file)) {
                $image_path = $conn->real_escape_string($target_file);
                $is_primary = ($i === 0) ? 1 : 0;

                $conn->query("INSERT INTO product_images (product_id, image_path, is_primary)
                              VALUES ($product_id, '$image_path', $is_primary)");
            }
        }

        redirect("products.php", "Product added successfully");
    } else {
        redirect("products.php", "Something went wrong while adding the product.");
    }

    $conn->close();
}




// Edit Product
if (isset($_POST['update_product'])) {
    $id = intval($_POST['id']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = $conn->real_escape_string($_POST['status']);
    $description = $conn->real_escape_string($_POST['description']);

    $sql = "UPDATE products SET 
        name = '$name',
        price = $price,
        stock = $stock,
        category = '$category',
        status = '$status',
        description = '$description'
        WHERE product_id = $id";

    if ($conn->query($sql)) {
        redirect("products.php", "Product updated successfully");
    } else {
        redirect("products.php", "Something went wrong");
    }

    $conn->close();
}


// Delete Product
if (isset($_POST['delete_product'])) {
    $id = intval($_POST['id']);

    // First delete images
    $conn->query("DELETE FROM product_images WHERE product_id = $id");

    // Then delete product
    $conn->query("DELETE FROM products WHERE product_id = $id");

    $conn->close();

    redirect("products.php", "Product deleted successfully");
}


// Add Category
if (isset($_POST['add_category'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);

    $check = mysqli_query($conn, "SELECT * FROM categories WHERE name = '$category_name'");
    if (mysqli_num_rows($check) > 0) {
        // Optional: return an error (already exists)
    } else {
        mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$category_name')");
        // Optional: return success
    }

    redirect("products.php", "Product Category added successfully");
    exit();
}

// Add Rider
if (isset($_POST['add_rider'])) {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($full_name && $email && $password) {

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Generate rider ID (12 characters: letters + numbers)
        function generateRiderID($length = 12) {
            $chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $id = '';
            for ($i = 0; $i < $length; $i++) {
                $id .= $chars[random_int(0, strlen($chars) - 1)];
            }
            return $id;
        }
        $rider_id = generateRiderID();

        // Insert rider into the table
        $stmt = $conn->prepare("INSERT INTO riders (rider_id, full_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $rider_id, $full_name, $email, $hashed_password);
        $stmt->execute();
        $stmt->close();

        redirect("admin.php", "Rider added successfully");
        exit();
    } else {
       redirect("admin.php", "Rider not added");
        exit();
    }
}
