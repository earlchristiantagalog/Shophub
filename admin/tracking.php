<?php
require 'includes/header.php';
?>

<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Order Tracking</h2>
        <a href="orders.php" class="btn btn-outline-dark">
            <i class="bi bi-arrow-left"></i> Back to Orders
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Accepted / In-Transit Orders</h5>
            <button class="btn btn-outline-dark btn-sm" onclick="printTrackingList()">
                <i class="bi bi-printer"></i> Print List
            </button>
        </div>

        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Tracking Number</th>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Shipping Method</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tracking-orders-body">
                    <tr>
                        <td colspan="7" class="text-center text-muted">Loading orders...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL for viewing order -->
    <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="order-details-content">
                    <div class="text-center">Loading...</div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const trackingBody = document.getElementById('tracking-orders-body');

        function fetchTrackingOrders() {
            fetch('fetch_tracking_orders.php')
                .then(res => res.text())
                .then(html => trackingBody.innerHTML = html)
                .catch(err => {
                    console.error('Error loading tracking orders:', err);
                    trackingBody.innerHTML = "<tr><td colspan='7' class='text-center text-danger'>Error loading data</td></tr>";
                });
        }

        // Initial load + auto refresh every 10 seconds
        fetchTrackingOrders();
        setInterval(fetchTrackingOrders, 10000);

        // View order details
        $(document).on('click', '.view-order-btn', function() {
            const orderId = $(this).data('id');
            $('#viewOrderModal').modal('show');
            $.get('get_order_details.php', {
                order_id: orderId
            }, data => {
                $('#order-details-content').html(data);
            });
        });

        // Print single receipt
        $(document).on('click', '.print-receipt-btn', function() {
            const orderId = $(this).data('id');
            $.get('print_receipt.php', {
                order_id: orderId
            }, receiptHtml => {
                const printWindow = window.open('', '', 'width=800,height=600');
                printWindow.document.write(receiptHtml);
                printWindow.document.close();
                printWindow.onload = () => {
                    printWindow.print();
                };
            });
        });
    });

    // ✅ MARK AS DELIVERED
    $(document).on('click', '.mark-delivered-btn', function() {
        const orderId = $(this).data('id');
        if (!confirm('Confirm marking this order as Delivered?')) return;

        $.ajax({
            url: 'mark_delivered.php',
            type: 'POST',
            dataType: 'json',
            data: {
                order_id: orderId
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    // Refresh tracking list
                    fetchTrackingOrders();
                } else {
                    alert(response.message || 'Failed to mark as delivered.');
                }
            },
            error: function(xhr, status, error) {
                alert('Server error: ' + error);
            }
        });
    });


    // Print entire tracking list
    function printTrackingList() {
        const section = document.querySelector('.card-body');
        const tableHTML = section.innerHTML;

        const storeInfo = `
        <div style="text-align:center;margin-bottom:10px;">
            <h3>Shophub Gadget and Electronics</h3>
            <p>Malunhaw St., Consolacion, Cebu</p>
            <p>Contact: (032) 555-0123 | shophub@gmail.com</p>
        </div>
    `;

        const printWindow = window.open('', '', 'width=900,height=700');
        printWindow.document.write(`
        <html>
        <head>
            <title>Tracking Orders</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <style>
                body { font-family: Arial; padding: 20px; font-size: 14px; }
                th, td { border: 1px solid #ddd; padding: 8px; }
                th { background-color: #f1f3f4; }
                table { width: 100%; border-collapse: collapse; }
                @media print {
                    @page { size: A4; margin: 15mm; }
                    button { display: none; }
                }
            </style>
        </head>
        <body onload="window.print(); window.close();">
            ${storeInfo}
            ${tableHTML}
        </body>
        </html>
    `);
        printWindow.document.close();
    }
</script>

<?php include 'includes/footer.php'; ?>