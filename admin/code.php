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
    // Clean input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if ($password == $user['password']) {
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['logged_in'] = true;

            // Handle "Remember me"
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
