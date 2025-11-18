<?php
include 'db.php';
include 'includes/header.php';

// Pagination setup
$limit = 10; // items per page
$page = $_GET['page'] ?? 1;
$start = ($page - 1) * $limit;

// Filters
$search = $_GET['search'] ?? "";
$category = $_GET['category'] ?? "";

// Base query with LEFT JOIN to fetch primary image
$sql = "SELECT i.*, pi.image_path AS primary_image
        FROM inventory i
        LEFT JOIN product_images pi 
            ON i.product_id = pi.product_id AND pi.is_primary = 1
        WHERE 1";

if ($search !== "") {
    $sql .= " AND i.name LIKE '%$search%'";
}
if ($category !== "") {
    $sql .= " AND i.category = '$category'";
}

// Total sales / revenue overall
$sales = $conn->query("SELECT 
            COUNT(*) AS total_products,
            SUM(stock) AS total_stock,
            SUM(sold) AS total_sold,
            SUM(sold * price) AS total_revenue
        FROM inventory");
$sales_data = $sales->fetch_assoc();

// Today
$today_sales = $conn->query("SELECT SUM(sold * price) AS revenue_today, SUM(sold) AS sold_today
    FROM inventory
    WHERE DATE(created_at) = CURDATE()");
$today = $today_sales->fetch_assoc();

// This week
$week_sales = $conn->query("SELECT SUM(sold * price) AS revenue_week, SUM(sold) AS sold_week
    FROM inventory
    WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
$week = $week_sales->fetch_assoc();

// This month
$month_sales = $conn->query("SELECT SUM(sold * price) AS revenue_month, SUM(sold) AS sold_month
    FROM inventory
    WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())");
$month = $month_sales->fetch_assoc();


// Count total rows for pagination
$total_result = $conn->query($sql);
$total = $total_result->num_rows;
$pages = ceil($total / $limit);

// Add ORDER BY and LIMIT for pagination
$sql .= " ORDER BY i.product_id DESC LIMIT $start, $limit";
$result = $conn->query($sql);

// Fetch categories for filter dropdown
$cats_result = $conn->query("SELECT DISTINCT category FROM inventory");
?>
<style>
    @media print {
    body * {
        visibility: hidden; /* hide everything by default */
    }
    main, main * {
        visibility: visible; /* show the main content */
    }
    main {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
    .btn, .pagination, .form-control, .form-select {
        display: none !important; /* hide buttons, filters, pagination */
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table, table th, table td {
        border: 1px solid #000;
    }
    th, td {
        padding: 5px;
        font-size: 12pt;
    }
    img {
        max-width: 100px; /* resize product & barcode images for print */
        height: auto;
    }
}

</style>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Sales & Inventory</h2>
        <a href="add_item.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Product</a>
        <a href="print_report.php" class="btn btn-secondary"><i class="bi bi-printer"></i> Print</a>
    </div>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Products</h5>
                <p class="card-text fs-4"><?= $sales_data['total_products'] ?? 0 ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">Total Stock</h5>
                <p class="card-text fs-4"><?= $sales_data['total_stock'] ?? 0 ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">Sold Today</h5>
                <p class="card-text fs-4"><?= $today['sold_today'] ?? 0 ?></p>
                <small>Revenue: ₱<?= number_format($today['revenue_today'] ?? 0,2) ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-body">
                <h5 class="card-title">Sold This Week</h5>
                <p class="card-text fs-4"><?= $week['sold_week'] ?? 0 ?></p>
                <small>Revenue: ₱<?= number_format($week['revenue_week'] ?? 0,2) ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">Sold This Month</h5>
                <p class="card-text fs-4"><?= $month['sold_month'] ?? 0 ?></p>
                <small>Revenue: ₱<?= number_format($month['revenue_month'] ?? 0,2) ?></small>
            </div>
        </div>
    </div>
</div>

    <!-- Filters -->
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Search product..."
                value="<?= htmlspecialchars($search) ?>">
        </div>

        <div class="col-md-3">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php while ($cat = $cats_result->fetch_assoc()): ?>
                    <option value="<?= $cat['category'] ?>" <?= ($category == $cat['category']) ? 'selected' : '' ?>>
                        <?= $cat['category'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-dark w-100"><i class="bi bi-filter"></i> Filter</button>
        </div>
    </form>

    <!-- Inventory Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Inventory List</h5>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Sold</th>
                        <th>Revenue</th>
                        <th>Status</th>
                        <th>Barcode</th>
                        <th>Date Added</th>
                        <th width="200">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $count = $start + 1;
                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            $revenue = $row['sold'] * $row['price'];
                            ?>
                            <tr class="<?= ($row['stock'] < 5) ? 'table-danger' : '' ?>">
                                <td><?= $count++ ?></td>
                                <td>
                                    <?php if ($row['primary_image'] && file_exists('admin/' . $row['primary_image'])): ?>
                                        <img src="admin/<?= $row['primary_image'] ?>" width="50">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['category'] ?></td>
                                <td><b><?= $row['stock'] ?></b></td>
                                <td><?= $row['sold'] ?></td>
                                <td>₱<?= number_format($revenue, 2) ?></td>
                                <td><?= $row['status'] ?></td>
                                <td>
                                    <img src="barcode.php?id=<?= $row['product_id'] ?>" width="120">
                                </td>

                                <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
                                <td>
                                    <a href="edit_item.php?id=<?= $row['product_id'] ?>" class="btn btn-primary btn-sm mb-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="delete_item.php?id=<?= $row['product_id'] ?>" class="btn btn-danger btn-sm mb-1"
                                        onclick="return confirm('Delete this item?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                            <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="11" class="text-center text-muted">No items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($pages > 1): ?>
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $pages; $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link"
                                    href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category=<?= urlencode($category) ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>