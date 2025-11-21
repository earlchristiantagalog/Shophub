<?php
// inventory.php  (Sales Dashboard)
// Includes DB connection and header/footer from your project
include 'db.php';
include 'includes/header.php';

// Safety: ensure $conn exists
if (!isset($conn)) {
    die("Database connection not available.");
}

/* ---------------------------
   SALES SUMMARY QUERIES
   Use IFNULL so aggregates return 0 instead of NULL
--------------------------- */

/* Overall totals */
$sales_q = "
    SELECT
        COUNT(*) AS total_products,
        IFNULL(SUM(stock),0) AS total_stock,
        IFNULL(SUM(sold),0) AS total_sold,
        IFNULL(SUM(sold * price),0) AS total_revenue
    FROM products
";
$sales_res = $conn->query($sales_q);
$sales_data = $sales_res ? $sales_res->fetch_assoc() : [
    'total_products'=>0, 'total_stock'=>0, 'total_sold'=>0, 'total_revenue'=>0
];


/* Today */
$today_q = "
    SELECT
        IFNULL(SUM(oi.quantity),0) AS sold_today,
        IFNULL(SUM(oi.quantity * oi.price),0) AS revenue_today
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    WHERE o.status = 'Delivered'
      AND DATE(o.order_date) = CURDATE()
";
$today_res = $conn->query($today_q);
$today = $today_res ? $today_res->fetch_assoc() : ['sold_today' => 0, 'revenue_today' => 0];

/* This week (ISO week, YEARWEEK with mode 1) */
$week_q = "
    SELECT
        IFNULL(SUM(oi.quantity),0) AS sold_week,
        IFNULL(SUM(oi.quantity * oi.price),0) AS revenue_week
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    WHERE o.status = 'Delivered'
      AND YEARWEEK(o.order_date, 1) = YEARWEEK(CURDATE(), 1)
";
$week_res = $conn->query($week_q);
$week = $week_res ? $week_res->fetch_assoc() : ['sold_week' => 0, 'revenue_week' => 0];

/* This month */
$month_q = "
    SELECT
        IFNULL(SUM(oi.quantity),0) AS sold_month,
        IFNULL(SUM(oi.quantity * oi.price),0) AS revenue_month
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    WHERE o.status = 'Delivered'
      AND MONTH(o.order_date) = MONTH(CURDATE())
      AND YEAR(o.order_date) = YEAR(CURDATE())
";
$month_res = $conn->query($month_q);
$month = $month_res ? $month_res->fetch_assoc() : ['sold_month' => 0, 'revenue_month' => 0];

/* Small helper to format currency (Philippine Peso) */
function peso($n) {
    return '₱' . number_format((float)$n, 2);
}

/* For the chart: show last 7 days sales (quantity). We'll query orders grouped by date.
   If your dataset is huge you might want to limit or optimize this later. */
$chart_days = [];
$chart_values = [];

$chart_q = "
    SELECT DATE(o.order_date) AS dt, IFNULL(SUM(oi.quantity),0) AS qty
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.status = 'Delivered'
      AND DATE(o.order_date) >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY DATE(o.order_date)
    ORDER BY DATE(o.order_date) ASC
";
$chart_res = $conn->query($chart_q);

$days_map = [];
if ($chart_res) {
    while ($r = $chart_res->fetch_assoc()) {
        $days_map[$r['dt']] = (int)$r['qty'];
    }
}
// build last 7 days labels & values (even if some days have 0)
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $label = date('M j', strtotime($d)); // e.g. Nov 21
    $chart_days[] = $label;
    $chart_values[] = isset($days_map[$d]) ? (int)$days_map[$d] : 0;
}

/* Recently Added Products (latest 10) */
$recent_q = "
    SELECT 
        p.*, 
        pi.image_path AS primary_image
    FROM products p
    LEFT JOIN product_images pi 
        ON p.product_id = pi.product_id AND pi.is_primary = 1
    ORDER BY p.created_at DESC
    LIMIT 10
";

$recent_res = $conn->query($recent_q);

?>

<style>
/* Small style tweaks for the dashboard cards */
.stat-card { border-radius: 0.75rem; box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
.stat-card .card-body { padding: 1.1rem; }
.kpi-small { font-size: 0.85rem; color: rgba(255,255,255,0.95); opacity: 0.95; }
.panel-row { gap: 1rem; display: flex; flex-wrap: wrap; }
.panel-row .col { min-width: 220px; flex: 1 1 220px; }
</style>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Sales Dashboard</h2>
        <div>
            <a href="inventory.php" class="btn btn-outline-secondary me-2"><i class="bi bi-box-seam"></i> Inventory</a>
            <a href="add_item.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Add Product</a>
            <a href="print_report.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Print</a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row panel-row mb-4">
        <div class="col">
            <div class="card stat-card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="kpi-small">Total Products</h6>
                            <h3 class="mb-0"><?= (int)$sales_data['total_products'] ?></h3>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-box-seam" style="font-size:28px; opacity:0.9"></i>
                        </div>
                    </div>
                    <small class="kpi-small">Total distinct products</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card stat-card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="kpi-small">Total Stock</h6>
                            <h3 class="mb-0"><?= (int)$sales_data['total_stock'] ?></h3>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-stack" style="font-size:28px; opacity:0.9"></i>
                        </div>
                    </div>
                    <small class="kpi-small">Sum of stock across inventory</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card stat-card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="kpi-small">Sold Today</h6>
                            <h3 class="mb-0"><?= (int)$today['sold_today'] ?></h3>
                            <small class="d-block mt-1"><?= peso($today['revenue_today']) ?> revenue</small>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-calendar-day" style="font-size:28px; opacity:0.9"></i>
                        </div>
                    </div>
                    <small class="kpi-small">Sales with status 'Accepted' today</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card stat-card text-white bg-danger">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="kpi-small">Sold This Week</h6>
                            <h3 class="mb-0"><?= (int)$week['sold_week'] ?></h3>
                            <small class="d-block mt-1"><?= peso($week['revenue_week']) ?> revenue</small>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-calendar-week" style="font-size:28px; opacity:0.9"></i>
                        </div>
                    </div>
                    <small class="kpi-small">Current ISO week sales (Accepted)</small>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card stat-card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="kpi-small">Sold This Month</h6>
                            <h3 class="mb-0"><?= (int)$month['sold_month'] ?></h3>
                            <small class="d-block mt-1"><?= peso($month['revenue_month']) ?> revenue</small>
                        </div>
                        <div class="text-end">
                            <i class="bi bi-calendar3" style="font-size:28px; opacity:0.9"></i>
                        </div>
                    </div>
                    <small class="kpi-small">Current month sales (Accepted)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Totals row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card stat-card">
                <div class="card-body">
                    <h6>Total Sold (all time)</h6>
                    <h2><?= (int)$sales_data['total_sold'] ?></h2>
                    <small class="text-muted">Revenue: <?= peso($sales_data['total_revenue']) ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart: Last 7 days sold -->
    <div class="card mb-4">
        <div class="card-header">
            <strong>Sales (last 7 days)</strong>
        </div>
        <div class="card-body">
            <canvas id="salesChart" height="110"></canvas>
        </div>
    </div>

    <div class="card mb-4">
    <div class="card-header">
        <strong>Recently Added Products</strong>
    </div>

    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px">Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th style="width: 160px">Date Added</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recent_res && $recent_res->num_rows > 0): ?>
                    <?php while ($r = $recent_res->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if (!empty($r['primary_image'])): ?>
                                    <img src="<?= $r['primary_image'] ?>" 
                                         alt="" 
                                         class="img-thumbnail" 
                                         style="width:60px; height:60px; object-fit:cover;">
                                <?php else: ?>
                                    <span class="text-muted">No Image</span>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($r['name']) ?></td>
                            <td><?= htmlspecialchars($r['category']) ?></td>
                            <td><?= peso($r['price']) ?></td>
                            <td><?= (int)$r['stock'] ?></td>

                            <td>
                                <?php if ($r['status'] === 'Inactive'): ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php else: ?>
                                    <span class="badge bg-primary">Active</span>
                                <?php endif; ?>
                            </td>

                            <td><?= date("M d, Y h:i A", strtotime($r['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No recently added products</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Toast container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="syncToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Products synced successfully!
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>


</main>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Check if sync was successful
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('sync') === 'success') {
        const toastEl = document.getElementById('syncToast');
        const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
        toast.show();

        // Remove query param to prevent toast showing again on refresh
        window.history.replaceState({}, document.title, window.location.pathname);
    }
});
</script>
<!-- Chart.js from CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?= json_encode($chart_days) ?>;
    const data = <?= json_encode($chart_values) ?>;

    const ctx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Qty sold',
                data: data,
                // use default colors (do not force color)
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>

<?php
// footer
include 'includes/footer.php';
