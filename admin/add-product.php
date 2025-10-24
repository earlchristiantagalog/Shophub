<?php include 'includes/header.php'; ?>
<!-- Main Content -->
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Products</h2>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>

    <div class="card border-0 shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Product</h5>
            <a href="products.php" class="btn btn-light btn-sm"><i class="bi bi-arrow-left"></i> Back</a>
        </div>
        <div class="card-body">
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
                        <select class="form-select" id="productCategory" name="category" required>
                            <option selected disabled>Select category</option>
                            <?php
                            $category_query = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
                            while ($row = mysqli_fetch_assoc($category_query)) {
                                echo '<option value="' . htmlspecialchars($row['name']) . '">' . htmlspecialchars($row['name']) . '</option>';
                            }
                            ?>
                        </select>
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

                    <!-- Variants Section -->
                    <div class="col-12">
                        <label class="form-label">Variants</label>
                        <div id="variantFields">
                            <div class="variant-row border rounded p-3 mb-2 bg-light">
                                <div class="row g-2 align-items-end">
                                    <div class="col-md-5">
                                        <label class="form-label">Type</label>
                                        <input type="text" name="variant_types[]" class="form-control" placeholder="e.g. Size, Color, Meter" required>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">Values <small class="text-muted">(separate with commas)</small></label>
                                        <input type="text" name="variant_values[]" class="form-control" placeholder="e.g. Small, Medium, Large" required>
                                    </div>
                                    <div class="col-md-2 d-grid">
                                        <button type="button" class="btn btn-outline-danger" onclick="this.closest('.variant-row').remove()">Remove</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="addVariant()">+ Add Variant</button>
                    </div>


                </div>

                <div class="text-end mt-4">
                    <button type="submit" name="add_product" class="btn btn-primary">Save Product</button>
                </div>
            </form>
        </div>
    </div>

</main>
<script>
    function addVariant() {
        const variantHTML = `
    <div class="variant-row border rounded p-3 mb-2 bg-light">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Type</label>
                <input type="text" name="variant_types[]" class="form-control" placeholder="e.g. Size, Color, Meter" required>
            </div>
            <div class="col-md-5">
                <label class="form-label">Values <small class="text-muted">(separate with commas)</small></label>
                <input type="text" name="variant_values[]" class="form-control" placeholder="e.g. Small, Medium, Large" required>
            </div>
            <div class="col-md-2 d-grid">
                <button type="button" class="btn btn-outline-danger" onclick="this.closest('.variant-row').remove()">Remove</button>
            </div>
        </div>
    </div>
    `;
        document.getElementById("variantFields").insertAdjacentHTML("beforeend", variantHTML);
    }
</script>


<?php include('includes/footer.php'); ?>


<?php include 'includes/footer.php'; ?>