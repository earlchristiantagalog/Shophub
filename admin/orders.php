<?php
require 'db.php';
require 'includes/header.php';

// Optional: Admin session check
// if (!isset($_SESSION['admin_id'])) {
//     header("Location: login.php");
//     exit();
// }

$sql = "SELECT 
            o.order_id, 
            u.username, 
            o.total, 
            o.status, 
            o.order_date 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>


<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Products</h2>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>



    <!-- Product Table -->
    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Latest Orders</h5>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="orders-table-body">
                    <!-- Data inserted here -->
                </tbody>
            </table>
            <!-- View Order Modal -->
            <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Order Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="order-details-content">
                            <!-- Order content loaded here via AJAX -->
                            <div class="text-center">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>

</main>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ordersTableBody = document.getElementById('orders-table-body');

        // Fetch and display orders
        function fetchOrders() {
            fetch('fetch_orders.php')
                .then(response => response.text())
                .then(data => {
                    ordersTableBody.innerHTML = data;
                    attachAcceptHandlers();
                })
                .catch(err => console.error('Error fetching orders:', err));
        }

        $(document).on('click', '.accept-order-btn', function() {
            const orderId = $(this).data('id'); // should contain ES46033 or similar

            if (confirm("Are you sure you want to accept this order?")) {
                $.ajax({
                    url: 'accept_order.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        order_id: orderId
                    },
                    success: function(response) {
                        if (response.success) {
                            alert(response.message);
                            fetchOrders(); // Refresh order list
                        } else {
                            alert(response.message || 'Failed to accept order.');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('Server error while accepting order: ' + error);
                    }
                });
            }
        });

        // Initial fetch
        fetchOrders();

        // Refresh every 30 seconds (not too frequent)
        setInterval(fetchOrders, 10000);
    });


    
    // View order details
    $(document).on('click', '.view-order-btn', function() {
        const orderId = $(this).data('id');
        $('#viewOrderModal').modal('show');

        $.get('get_order_details.php', {
            order_id: orderId
        }, function(data) {
            $('#order-details-content').html(data);
        });
    });

    // Update order status
    $(document).on('submit', '#updateOrderForm', function(e) {
        e.preventDefault();
        $.post('update_order_status.php', $(this).serialize(), function(response) {
            alert(response.message);
            $('#viewOrderModal').modal('hide');
            fetchOrders();
        }, 'json');
    });

    // Print receipt
    $(document).on('click', '.print-receipt-btn', function() {
        const orderId = $(this).data('id');

        $.ajax({
            url: 'print_receipt.php',
            type: 'GET',
            data: {
                order_id: orderId
            },
            success: function(receiptHtml) {
                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.open();
                printWindow.document.write(receiptHtml);
                printWindow.document.close();

                printWindow.onload = function() {
                    printWindow.focus();
                    printWindow.print();
                };
            },
            error: function() {
                alert('Failed to load receipt.');
            }
        });
    });
</script>
<script>
document.addEventListener('click', function(e) {
    // Cancel button
    if (e.target.closest('.cancel-order-btn')) {
        const btn = e.target.closest('.cancel-order-btn');
        const orderId = btn.dataset.id;
        if (!orderId) return;

        if (!confirm('Are you sure you want to cancel this order? This cannot be undone.')) return;

        // disable button immediately to prevent double clicks
        btn.disabled = true;

        const formData = new FormData();
        formData.append('order_id', orderId);

        fetch('cancel_order.php', {
            method: 'POST',
            body: formData,
        })
        .then(resp => resp.json())
        .then(data => {
            if (data.success) {
                // find the table row and update UI nicely
                const row = btn.closest('tr');
                if (row) {
                    // Update status badge
                    const badge = row.querySelector('td:nth-child(4) .badge');
                    if (badge) {
                        badge.className = 'badge bg-danger';
                        badge.textContent = 'Cancelled';
                    }
                    // remove action buttons and replace with View + Print only
                    const actionsCell = row.querySelector('td:last-child');
                    if (actionsCell) {
                        actionsCell.innerHTML = `
                            <button type="button" class="btn btn-primary btn-sm view-order-btn" data-id="${orderId}">
                                <i class="bi bi-eye"></i> View
                            </button>
                            <button type="button" class="btn btn-dark btn-sm print-receipt-btn" data-id="${orderId}">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        `;
                    }
                }
                alert(data.message || 'Order cancelled.');
            } else {
                alert(data.message || 'Unable to cancel order.');
                btn.disabled = false; // re-enable on failure
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network or server error. Check console.');
            btn.disabled = false;
        });
    }

    // Accept button (optional) — example to disable Cancel after accept
    if (e.target.closest('.accept-order-btn')) {
        const btn = e.target.closest('.accept-order-btn');
        const orderId = btn.dataset.id;
        if (!orderId) return;
        if (!confirm('Accept this order?')) return;

        // Example accept flow (you can replace with your existing accept AJAX)
        fetch('accept_order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'order_id=' + encodeURIComponent(orderId)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const row = btn.closest('tr');
                if (row) {
                    const badge = row.querySelector('td:nth-child(4) .badge');
                    if (badge) {
                        badge.className = 'badge bg-success';
                        badge.textContent = data.new_status || 'Accepted';
                    }
                    // After accepted, remove Cancel button (if present)
                    const cancelBtn = row.querySelector('.cancel-order-btn');
                    if (cancelBtn) cancelBtn.remove();
                    // Optionally replace Accept with View/Print
                    btn.outerHTML = `<button type="button" class="btn btn-primary btn-sm view-order-btn" data-id="${orderId}">
                                        <i class="bi bi-eye"></i> View
                                    </button>`;
                }
                alert(data.message || 'Order accepted.');
            } else {
                alert(data.message || 'Unable to accept order.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Network or server error.');
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>