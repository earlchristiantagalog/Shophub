<?php include 'includes/header.php'; ?>
<!-- Main Content -->
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <!-- Top Navbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Settings</h2>
        <button class="btn btn-dark"><i class="bi bi-box-arrow-right"></i> Logout</button>
    </div>


    <!-- VOUCHERS Section -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Vouchers</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
                <i class="bi bi-plus-circle"></i> Add Voucher
            </button>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Voucher Code</th>
                    <th>Discount (%)</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="voucherTable">
                <!-- Dynamically loaded -->
            </tbody>
        </table>
    </div>

    <!-- NOTIFICATIONS Section -->
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold">Notifications</h4>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addNotificationModal">
                <i class="bi bi-bell"></i> Create Notification
            </button>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-success">
                <tr>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="notificationTable">
                <!-- Dynamically loaded -->
            </tbody>
        </table>
    </div>

</main>
<!-- Voucher Modal -->
<div class="modal fade" id="addVoucherModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="voucherForm">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add Voucher</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Voucher Code</label>
                    <input type="text" name="code" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Discount (%)</label>
                    <input type="number" name="discount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Expiry Date</label>
                    <input type="date" name="expiry" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Save Voucher</button>
            </div>
        </form>
    </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="addNotificationModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="notificationForm">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Create Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Title</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Message</label>
                    <textarea name="message" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Send Notification</button>
            </div>
        </form>
    </div>
</div>
<script>
    function loadVouchers() {
        fetch('fetch_vouchers.php')
            .then(res => res.text())
            .then(data => document.getElementById('voucherTable').innerHTML = data);
    }

    function loadNotifications() {
        fetch('fetch_notifications.php')
            .then(res => res.text())
            .then(data => document.getElementById('notificationTable').innerHTML = data);
    }

    document.getElementById('voucherForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('insert_voucher.php', {
                method: 'POST',
                body: formData
            }).then(res => res.text())
            .then(res => {
                if (res === "success") {
                    loadVouchers();
                    this.reset();
                    bootstrap.Modal.getInstance(document.getElementById('addVoucherModal')).hide();
                } else {
                    alert('Failed to add voucher');
                }
            });
    });

    document.getElementById('notificationForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('insert_notification.php', {
                method: 'POST',
                body: formData
            }).then(res => res.text())
            .then(res => {
                if (res === "success") {
                    loadNotifications();
                    this.reset();
                    bootstrap.Modal.getInstance(document.getElementById('addNotificationModal')).hide();
                } else {
                    alert('Failed to send notification');
                }
            });
    });

    // Load data on page load
    window.addEventListener('DOMContentLoaded', () => {
        loadVouchers();
        loadNotifications();
    });
</script>

<?php include 'includes/footer.php' ?>