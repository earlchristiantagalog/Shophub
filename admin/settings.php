<?php include 'includes/header.php'; ?>
<main class="col-md-10 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Settings</h2>
        <button class="btn btn-dark" onclick="window.location.href='logout.php'">
            <i class="bi bi-box-arrow-right"></i> Logout
        </button>
    </div>

    <!-- FLASH SALE Section -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-danger"><i class="bi bi-lightning-charge"></i> Flash Sale</h4>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#flashSaleModal">
                <i class="bi bi-plus-circle"></i> Set Flash Sale
            </button>
        </div>


        <table class="table table-bordered table-striped">
            <thead class="table-danger">
                <tr>
                    <th>Title</th>
                    <th>Discount (%)</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="flashSaleTable"></tbody>
        </table>

    </div>

    <!-- VOUCHERS Section -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold"><i class="bi bi-ticket-perforated"></i> Vouchers</h4>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVoucherModal">
                <i class="bi bi-plus-circle"></i> Add Voucher
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Voucher Code</th>
                        <th>Discount (%)</th>
                        <th>Expiry Date</th>
                        <!-- <th>Status</th> -->
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="voucherTable"></tbody>
            </table>
        </div>
    </div>

    <!-- NOTIFICATIONS Section -->
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="fw-bold text-success"><i class="bi bi-bell"></i> Notifications</h4>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addNotificationModal">
                <i class="bi bi-plus-circle"></i> Create Notification
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-success text-center">
                    <tr>
                        <th>Title</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="notificationTable"></tbody>
            </table>
        </div>
    </div>
</main>

<!-- Flash Sale Modal -->
<div class="modal fade" id="flashSaleModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" id="flashSaleForm">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Set Flash Sale</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Halloween Mega Sale" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Discount (%)</label>
                    <input type="number" name="discount" class="form-control" placeholder="Enter discount percentage (e.g. 20)" min="1" max="100" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Start Time</label>
                    <input type="datetime-local" name="start_time" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">End Time</label>
                    <input type="datetime-local" name="end_time" class="form-control" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="submit" class="btn btn-danger">
                    <i class="bi bi-save2"></i> Save Flash Sale
                </button>
            </div>
        </form>
    </div>
</div>


<script>

</script>


<!-- 🎟️ Add Voucher Modal -->
<div class="modal fade" id="addVoucherModal" tabindex="-1" aria-labelledby="addVoucherLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content border-0 shadow-lg" id="voucherForm">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="addVoucherLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add New Voucher
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold"><i class="fas fa-ticket-alt me-2 text-danger"></i>Voucher Code</label>
                        <input type="text" name="code" class="form-control rounded-3" placeholder="Enter voucher code" required>
                    </div>

                    <div class="col-6">
                        <label class="form-label fw-semibold"><i class="fas fa-percentage me-2 text-danger"></i>Discount</label>
                        <input type="number" name="discount" min="1" class="form-control rounded-3" placeholder="Value" required>
                    </div>

                    <div class="col-6">
                        <label class="form-label fw-semibold">Discount Type</label>
                        <select name="discount_type" class="form-select rounded-3" required>
                            <option value="fixed">₱ Fixed</option>
                            <option value="percent">% Percent</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold"><i class="fas fa-shopping-cart me-2 text-danger"></i>Minimum Spend</label>
                        <input type="number" name="min_spend" min="0" class="form-control rounded-3" placeholder="₱0.00">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-2 text-danger"></i>Expiry Date</label>
                        <input type="date" name="expiry" class="form-control rounded-3" required>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold"><i class="fas fa-user me-2 text-danger"></i>Assign to User</label>
                        <select name="user_id" class="form-select rounded-3" id="voucherUserSelect">
                            <option value="">All Users</option>
                            <!-- populated dynamically -->
                        </select>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-3 px-4" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-danger rounded-3 px-4">
                    <i class="fas fa-save me-2"></i>Save Voucher
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    #addVoucherModal .modal-content {
        border-radius: 1rem;
    }

    #addVoucherModal .modal-header {
        border-top-left-radius: 1rem;
        border-top-right-radius: 1rem;
    }

    #addVoucherModal .form-control:focus,
    #addVoucherModal .form-select:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    #addVoucherModal label {
        font-size: 0.9rem;
        color: #444;
    }
</style>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const voucherForm = document.getElementById("voucherForm");
        const userSelect = document.getElementById("voucherUserSelect");

        // 🧑‍🤝‍🧑 Load users dynamically
        fetch("fetch_users.php")
            .then(res => res.json())
            .then(users => {
                users.forEach(u => {
                    const opt = document.createElement("option");
                    opt.value = u.id;
                    opt.textContent = `${u.username} (${u.email})`;
                    userSelect.appendChild(opt);
                });
            });

        // 🧾 Handle voucher submission
        voucherForm.addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(voucherForm);

            fetch("add_voucher.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === "success") {
                        alert("🎟️ Voucher added successfully!");
                        voucherForm.reset();

                        // Reload voucher list
                        fetch("fetch_vouchers.php")
                            .then(res => res.text())
                            .then(html => {
                                document.querySelector("#voucherModal .modal-body .row").innerHTML = html;
                            });

                        const modal = bootstrap.Modal.getInstance(document.getElementById("addVoucherModal"));
                        modal.hide();
                    } else {
                        alert(data.message || "Error saving voucher.");
                    }
                });
        });
    });
</script>


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

<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.14.0/build/alertify.min.js"></script>
<script>
    // Load Data
    function loadVouchers() {
        fetch('fetch_vouchers.php').then(r => r.text()).then(html => document.getElementById('voucherTable').innerHTML = html);
    }

    function loadNotifications() {
        fetch('fetch_notifications.php').then(r => r.text()).then(html => document.getElementById('notificationTable').innerHTML = html);
    }

    function loadFlashSales() {
        fetch('fetch_flashsale_list.php')
            .then(res => res.text())
            .then(data => document.getElementById('flashSaleTable').innerHTML = data);
    }

    // Submit handler
    document.getElementById('flashSaleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('insert_flashsale.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(res => {
                if (res.trim() === "success") {
                    loadFlashSales();
                    this.reset();
                    bootstrap.Modal.getInstance(document.getElementById('flashSaleModal')).hide();
                } else {
                    alert('Failed to save flash sale.');
                }
            });
    });

    // Auto-refresh every minute to update statuses live
    setInterval(loadFlashSales, 60000);


    window.addEventListener('DOMContentLoaded', () => {
        loadFlashSales();
        loadVouchers();
        loadNotifications();
    });
    // Submit Forms
    document.getElementById('voucherForm').addEventListener('submit', e => {
        e.preventDefault();
        fetch('insert_voucher.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
            .then(r => r.text()).then(res => {
                if (res.trim() === "success") {
                    alertify.success("Voucher added successfully!");
                    loadVouchers();
                    e.target.reset();
                    bootstrap.Modal.getInstance(document.getElementById('addVoucherModal')).hide();
                } else alertify.error("Failed to add voucher.");
            });
    });

    document.getElementById('notificationForm').addEventListener('submit', e => {
        e.preventDefault();
        fetch('insert_notification.php', {
                method: 'POST',
                body: new FormData(e.target)
            })
            .then(r => r.text()).then(res => {
                if (res.trim() === "success") {
                    alertify.success("Notification sent!");
                    loadNotifications();
                    e.target.reset();
                    bootstrap.Modal.getInstance(document.getElementById('addNotificationModal')).hide();
                } else alertify.error("Failed to send notification.");
            });
    });


    window.addEventListener('DOMContentLoaded', () => {
        loadVouchers();
        loadNotifications();
        loadFlashSale();
    });
</script>

<?php include 'includes/footer.php'; ?>