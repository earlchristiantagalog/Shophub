<?php include 'includes/header.php'; ?>
<!-- Main Content -->
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Products</h2>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>



    <!-- Product Table -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Latest Products</h5>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="bi bi-tags"></i> Add Category
                </button>
                <a href="add-product.php" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Product
                </a>
            </div>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-bordered product-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Stocks</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    $conn = new mysqli("localhost", "root", "", "shophub");

                    $sql = "SELECT * FROM products ORDER BY created_at DESC";
                    $result = $conn->query($sql);
                    $count = 1;

                    // Inside your product loop in the table row
                    while ($row = $result->fetch_assoc()):
                        $productId = $row['product_id'];
                    ?>
                        <tr>
                            <td><?= $count++ ?></td>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>₱<?= number_format($row['price'], 2) ?></td>
                            <td><?= $row['stock'] ?></td>
                            <td><span class="badge bg-<?= $row['status'] === 'active' ? 'success' : 'secondary' ?>"><?= ucfirst($row['status']) ?></span></td>
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $productId ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <!-- Delete Button -->
                                <form action="code.php" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="id" value="<?= $productId ?>">
                                    <button type="submit" name="delete_product" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Modal -->
                        <div class="modal fade" id="editModal<?= $productId ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $productId ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form method="POST" action="code.php">
                                        <input type="hidden" name="id" value="<?= $productId ?>">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title" id="editModalLabel<?= $productId ?>"><i class="bi bi-pencil-square me-2"></i>Edit Product</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Product Name</label>
                                                    <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Price</label>
                                                    <input type="number" class="form-control" name="price" step="0.01" value="<?= $row['price'] ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Stock</label>
                                                    <input type="number" class="form-control" name="stock" value="<?= $row['stock'] ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Category</label>
                                                    <input type="text" class="form-control" name="category" value="<?= htmlspecialchars($row['category']) ?>" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Status</label>
                                                    <select class="form-select" name="status" required>
                                                        <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($row['description']) ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" name="update_product" class="btn btn-warning text-white">Update Product</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

</main>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addProductModalLabel"><i class="bi bi-plus-circle me-2"></i>Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="addProductForm" method="POST" action="code.php" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="name" required>
                        </div>
                        <div class="col-md-3">
                            <label for="productPrice" class="form-label">Price (₱)</label>
                            <input type="number" class="form-control" id="productPrice" name="price" step="0.01" required>
                        </div>
                        <div class="col-md-3">
                            <label for="productStock" class="form-label">Stock</label>
                            <input type="number" class="form-control" id="productStock" name="stock" required>
                        </div>
                        <div class="col-md-6">
                            <label for="productCategory" class="form-label">Category</label>
                            <select class="form-select" id="productCategory" name="category_id" required onchange="loadVariantOptions(this.value)">
                                <option selected disabled>Select category</option>
                                <?php
                                $categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
                                while ($cat = mysqli_fetch_assoc($categories)) {
                                    echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                }
                                ?>
                            </select>

                        </div>
                        <div class="col-12" id="variantContainer">
                            <!-- Variant inputs will be loaded here -->
                        </div>


                        <div class="col-md-6">
                            <label for="productStatus" class="form-label">Status</label>
                            <select class="form-select" id="productStatus" name="status" required>
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="productDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="productDescription" name="description" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label for="productImages" class="form-label">Product Images (up to 5)</label>
                            <input type="file" class="form-control" id="productImages" name="images[]" multiple accept="image/*">
                        </div>
                    </div>

                    <div class="modal-footer bg-light mt-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="add_product" class="btn btn-primary">Save Product</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addCategoryModalLabel"><i class="bi bi-tags me-2"></i>Add New Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="code.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Category Name</label>
                        <input type="text" name="category_name" id="categoryName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_category" class="btn btn-success">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function loadVariantOptions(categoryId) {
        const variantContainer = document.getElementById("variantContainer");
        variantContainer.innerHTML = ""; // Clear previous

        // You can define category-based variants here
        const variants = {
            // category_id: ['Size', 'Color', ...]
            1: ['Size', 'Color'], // Electronics
            2: ['Size'], // Fashion
            3: ['Color'] // Accessories
        };

        if (variants[categoryId]) {
            variants[categoryId].forEach(variant => {
                const label = document.createElement("label");
                label.textContent = variant + " (comma-separated)";
                label.classList.add("form-label");

                const input = document.createElement("input");
                input.type = "text";
                input.name = `variant_${variant.toLowerCase()}[]`;
                input.classList.add("form-control");
                input.placeholder = `Enter ${variant.toLowerCase()}s, e.g., Small, Medium, Large`;

                const wrapper = document.createElement("div");
                wrapper.classList.add("mb-3");
                wrapper.appendChild(label);
                wrapper.appendChild(input);

                variantContainer.appendChild(wrapper);
            });
        }
    }
</script>


<?php include 'includes/footer.php'; ?>