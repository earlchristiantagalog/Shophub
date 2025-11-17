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
            o.shipping_method,
            o.order_date 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.order_date DESC";
$result = $conn->query($sql);
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">

    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Orders Management</h2>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>

    <!-- ===================== ALL ORDERS ===================== -->
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
                        <th>Shipping Method</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="orders-table-body">
                    <!-- Loaded dynamically via JS -->
                </tbody>
            </table>
        </div>
    </div>

    

    <!-- ===================== MODAL ===================== -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="order-details-content">
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
        const readyToShipBody = document.getElementById('ready-to-ship-body');

        function fetchOrders() {
            fetch('fetch_orders.php')
                .then(response => response.text())
                .then(data => {
                    ordersTableBody.innerHTML = data;
                })
                .catch(err => console.error('Error fetching orders:', err));
        }

        function fetchReadyToShip() {
            fetch('fetch_ready_to_ship.php')
                .then(response => response.text())
                .then(data => {
                    readyToShipBody.innerHTML = data;
                })
                .catch(err => console.error('Error fetching ready-to-ship orders:', err));
        }

        // Refresh both tables every 10 seconds
        fetchOrders();
        fetchReadyToShip();
        setInterval(() => {
            fetchOrders();
            fetchReadyToShip();
        }, 10000);
    });

    // ===================== PRINT READY TO SHIP =====================
    function printReadyToShip() {
        const section = document.getElementById('ready-to-ship-section');
        const clone = section.cloneNode(true);

        // 🔹 Remove the Print button
        const printButton = clone.querySelector('button');
        if (printButton) printButton.remove();

        // 🔹 Remove all View buttons
        const viewButtons = clone.querySelectorAll('.view-order-btn');
        viewButtons.forEach(btn => btn.remove());

        // 🔹 Remove Action column header and cells
        const actionHeaders = clone.querySelectorAll('th');
        actionHeaders.forEach(th => {
            if (th.textContent.trim().toLowerCase() === 'action') th.remove();
        });

        const actionCells = clone.querySelectorAll('td:last-child');
        actionCells.forEach(td => td.remove());

        // 🔹 Extract table HTML
        const tableHTML = clone.innerHTML;

        // 🔹 Store details (edit as needed)
        const storeInfo = `
        <div style="text-align: center; margin-bottom: 10px;">
            <h3 style="margin-bottom: 4px;">Shophub Gadget and Electronics</h3>
            <p style="margin: 0;">Malunhaw St., Consolacion, Cebu, Philippines</p>
            <p style="margin: 0;">Contact: (032) 555-0123 | shophub@gmail.com</p>
        </div>
    `;

        // 🔹 Inspection / checklist section
        const inspectionTable = `
        <table style="width:100%; border-collapse: collapse; margin-top: 40px; font-size: 14px;">
            <thead>
                <tr style="background:#f8f9fa;">
                    <th style="border:1px solid #ddd; padding:8px;">Checked by</th>
                    <th style="border:1px solid #ddd; padding:8px;">Packed by</th>
                    <th style="border:1px solid #ddd; padding:8px;">Inspected by</th>
                    <th style="border:1px solid #ddd; padding:8px;">Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #ddd; height:40px;"></td>
                    <td style="border:1px solid #ddd;"></td>
                    <td style="border:1px solid #ddd;"></td>
                    <td style="border:1px solid #ddd;"></td>
                </tr>
            </tbody>
        </table>
    `;

        // 🔹 Open clean print window
        const printWindow = window.open('', '', 'width=900,height=700');
        printWindow.document.write(`
        <html>
        <head>
            <title>Ready to Ship Orders</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                @media print {
                    @page {
                        size: A4;
                        margin: 15mm;
                    }
                    body {
                        -webkit-print-color-adjust: exact;
                        color-adjust: exact;
                    }
                    .no-print, button, .view-order-btn {
                        display: none !important;
                    }
                }
                body {
                    font-family: Arial, sans-serif;
                    padding: 20px;
                    font-size: 14px;
                }
                h4 {
                    text-align: center;
                    margin: 20px 0;
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }
                th, td {
                    border: 1px solid #ddd;
                    padding: 8px;
                }
                th {
                    background-color: #f1f3f4;
                    font-weight: bold;
                    text-align: left;
                }
                tr:nth-child(even) {
                    background-color: #fafafa;
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            ${storeInfo}
            ${tableHTML}
            ${inspectionTable}
        </body>
        </html>
    `);
        printWindow.document.close();
    }
</script>

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
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
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