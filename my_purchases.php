<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Purchases</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #f97316;
            --secondary-color: #fb923c;
            --light-color: #fff7ed;
            --dark-text: #78350f;
            --light-text: #fef3c7;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 6px 18px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
        }

        body {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
        }

        .main-container {
            background: var(--light-color);
            min-height: 100vh;
        }

        /* HEADER */
        .page-header {
            background: linear-gradient(135deg, #fb923c, #f97316);
            color: white;
            padding: 1.8rem 1rem;
            border-radius: 0 0 20px 20px;
            box-shadow: var(--card-shadow);
            text-align: center;
            position: relative;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }

        .back-btn {
            color: white;
            font-size: 1.5rem;
            transition: 0.3s;
            text-decoration: none;
            position: absolute;
            left: 1rem;
            top: 1.3rem;
        }

        .back-btn:hover {
            color: #fff;
            transform: translateX(-4px);
        }

        /* ORDER CARD */
        .order-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: 0.3s ease;
            border: none;
        }

        .order-card:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-3px);
        }

        .order-header {
            background: #fff7ed;
            border-bottom: 1px solid #fde68a;
            padding: 1rem 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .order-id {
            font-weight: 600;
            color: var(--dark-text);
        }

        .order-date {
            color: #92400e;
            font-size: 0.9rem;
        }

        .status-badge {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.4rem 1rem;
            border-radius: 50px;
            text-transform: uppercase;
            display: inline-block;
            margin-bottom: 0.3rem;
        }

        .status-pending {
            background: #fff7ed;
            color: #b45309;
        }

        .status-processing {
            background: #fde68a;
            color: #78350f;
        }

        .status-shipped {
            background: #fef3c7;
            color: #92400e;
        }

        .status-delivered {
            background: #d9f99d;
            color: #14532d;
        }

        .status-cancelled {
            background: #fecaca;
            color: #991b1b;
        }

        .product-item {
            padding: 1rem;
            border-bottom: 1px solid #fcd34d;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid #fed7aa;
        }

        .product-info {
            flex: 1;
            min-width: 180px;
        }

        .product-name {
            font-weight: 600;
            color: #78350f;
        }

        .product-details {
            color: #92400e;
            font-size: 0.9rem;
        }

        .order-total {
            background: #fff7ed;
            padding: 1rem;
            text-align: right;
            border-top: 1px solid #fde68a;
        }

        .total-amount {
            font-size: 1.1rem;
            font-weight: 700;
            color: #92400e;
        }

        /* Buttons */
        .btn-modern {
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1.25rem;
            border: none;
            transition: 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
        }

        .btn-track {
            background: linear-gradient(135deg, #fb923c, #f97316);
            color: white;
        }

        .btn-track:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .btn-review {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .btn-review:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
        }

        .btn-reviewed {
            background: #fcd34d;
            color: #92400e;
            cursor: not-allowed;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            color: #78350f;
        }

        /* MOBILE RESPONSIVENESS */
        @media (max-width: 768px) {
            .page-title {
                font-size: 1.6rem;
            }

            .order-header {
                flex-direction: column;
                text-align: center;
                gap: 0.5rem;
            }

            .product-item {
                flex-direction: column;
                text-align: center;
            }

            .product-image {
                width: 100px;
                height: 100px;
            }

            .order-total {
                text-align: center;
            }

            .action-buttons {
                display: flex;
                justify-content: center;
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .btn-modern {
                width: 100%;
                justify-content: center;
            }
        }

        /* Extra Small Screens */
        @media (max-width: 480px) {
            .page-header {
                padding: 1.2rem 0.5rem;
            }

            .page-title {
                font-size: 1.4rem;
            }

            .back-btn {
                top: 1rem;
                left: 0.8rem;
                font-size: 1.2rem;
            }

            .product-item {
                padding: 0.75rem;
            }

            .product-name {
                font-size: 1rem;
            }

            .total-amount {
                font-size: 1rem;
            }

            .status-badge {
                font-size: 0.75rem;
                padding: 0.3rem 0.8rem;
            }
        }
    </style>

</head>

<body>
    <div class="main-container">
        <div class="page-header position-relative">
            <a href="./" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <h2 class="page-title">My Purchases</h2>
        </div>

        <div class="container py-4" id="orders-container">
            <div class="text-center py-5">
                <div class="spinner-border text-warning" role="status"></div>
                <div class="mt-3 text-muted">Loading your orders...</div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">
                        <i class="fas fa-star me-2"></i>Submit Your Review
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form id="reviewForm" action="submit_review.php" method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="modalOrderId">
                        <input type="hidden" name="product_id" id="modalProductId">

                        <!-- Star Rating -->
                        <div class="mb-4 text-center">
                            <label class="form-label d-block"><strong>How was your experience?</strong></label>
                            <div class="star-rating d-flex justify-content-center gap-1">
                                <input type="radio" id="star5" name="rating" value="5" required>
                                <label for="star5" title="Excellent"><i class="bi bi-star"></i></label>

                                <input type="radio" id="star4" name="rating" value="4">
                                <label for="star4" title="Good"><i class="bi bi-star"></i></label>

                                <input type="radio" id="star3" name="rating" value="3">
                                <label for="star3" title="Average"><i class="bi bi-star"></i></label>

                                <input type="radio" id="star2" name="rating" value="2">
                                <label for="star2" title="Poor"><i class="bi bi-star"></i></label>

                                <input type="radio" id="star1" name="rating" value="1">
                                <label for="star1" title="Very Poor"><i class="bi bi-star"></i></label>
                            </div>
                        </div>

                        <style>
                            .star-rating {
                                direction: rtl;
                                /* Reverse order for easier hover */
                            }

                            .star-rating input {
                                display: none;
                                /* Hide radio buttons */
                            }

                            .star-rating label {
                                font-size: 2rem;
                                color: #ffd700;
                                /* Gold color for stars */
                                cursor: pointer;
                                transition: transform 0.2s, color 0.2s;
                            }

                            .star-rating label:hover,
                            .star-rating label:hover~label {
                                color: #ffc107;
                                /* Lighter gold on hover */
                                transform: scale(1.2);
                            }

                            .star-rating input:checked~label,
                            .star-rating input:checked~label~label {
                                color: #ffb300;
                                /* Selected star color */
                            }
                        </style>

                        <script>
                            document.querySelectorAll('.star-rating input').forEach(input => {
                                input.addEventListener('change', () => {
                                    const stars = input.closest('.star-rating').querySelectorAll('label i');
                                    stars.forEach((star, index) => {
                                        const val = parseInt(input.value);
                                        if (stars.length - index <= val) {
                                            star.classList.remove('bi-star');
                                            star.classList.add('bi-star-fill');
                                        } else {
                                            star.classList.remove('bi-star-fill');
                                            star.classList.add('bi-star');
                                        }
                                    });
                                });
                            });
                        </script>


                        <!-- Review Text -->
                        <div class="mb-3">
                            <label for="reviewText" class="form-label"><strong>Share your thoughts:</strong></label>
                            <textarea class="form-control" id="reviewText" name="review" rows="4"
                                placeholder="Tell others about your experience with this product..." required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Submit Review
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetchOrders();
            setInterval(fetchOrders, 10000); // Reduced frequency for better UX
        });

        function getStatusClass(status) {
            switch (status.toLowerCase()) {
                case 'pending':
                    return 'status-pending';
                case 'processing':
                    return 'status-processing';
                case 'shipped':
                    return 'status-shipped';
                case 'delivered':
                    return 'status-delivered';
                case 'cancelled':
                    return 'status-cancelled';
                default:
                    return 'status-pending';
            }
        }

        function fetchOrders() {
            fetch('fetch_orders.php')
                .then(response => {
                    if (!response.ok) throw new Error("Failed to fetch orders.");
                    return response.json();
                })
                .then(orders => {
                    const container = document.getElementById('orders-container');
                    container.innerHTML = '';

                    if (!orders || orders.length === 0) {
                        container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h3 class="empty-title">No orders yet</h3>
                        <p class="empty-text">When you place orders, they will appear here.</p>
                    </div>
                `;
                        return;
                    }

                    orders.forEach(order => {
                        const itemsHTML = (order.items && order.items.length > 0) ? order.items.map(item => `
                    <div class="product-item">
                        <img src="admin/${item.product_image}" alt="${item.product_name}" class="product-image">
                        <div class="product-info">
                            <div class="product-name">${item.product_name}</div>
                            <div class="product-details">
                                <div class="mb-1">
                                    <i class="fas fa-cube me-1"></i>
                                    Quantity: <strong>${item.quantity}</strong> × ₱${parseFloat(item.price).toFixed(2)}
                                </div>
                                <div>
                                    <i class="fas fa-calculator me-1"></i>
                                    Subtotal: <strong>₱${parseFloat(item.subtotal).toFixed(2)}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('') : '<p class="text-muted">No products found.</p>';

                        let reviewButtonHTML = '';
                        if (order.status.toLowerCase() === "delivered" && order.items && order.items.length > 0) {
                            const firstItem = order.items[0];
                            if (!firstItem.already_reviewed) {
                                reviewButtonHTML = `
                            <button class="btn-modern btn-review review-btn"
                                data-order-id="${order.order_id}"
                                data-product-id="${firstItem.product_id}" 
                                data-bs-toggle="modal"
                                data-bs-target="#reviewModal">
                                <i class="fas fa-star"></i>
                                Review
                            </button>
                        `;
                            } else {
                                reviewButtonHTML = `
                            <button class="btn-modern btn-reviewed" disabled>
                                <i class="fas fa-check"></i>
                                Reviewed
                            </button>
                        `;
                            }
                        }

                        const card = document.createElement('div');
                        card.className = 'order-card';
                        card.innerHTML = `
                    <div class="order-header d-flex justify-content-between align-items-center">
                        <div>
                            <div class="order-id">Order #${order.order_id}</div>
                            <div class="order-date">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Placed on ${new Date(order.order_date).toLocaleDateString('en-US', {
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                })}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="status-badge ${getStatusClass(order.status)} mb-3">
                                ${order.status}
                            </div>
                            <div class="action-buttons">
                                <a href="track_order.php?order_id=${order.order_id}" class="btn-modern btn-track">
                                    <i class="fas fa-truck"></i>
                                    Track Order
                                </a>
                                ${reviewButtonHTML}
                            </div>
                        </div>
                    </div>

                    ${itemsHTML}

                    <div class="order-total">
                        <div class="total-amount">
                            <i class="fas fa-receipt me-2"></i>
                            Total Paid: ₱${parseFloat(order.total).toFixed(2)}
                        </div>
                    </div>
                `;
                        container.appendChild(card);
                    });
                })
                .catch(error => {
                    document.getElementById('orders-container').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    ${error.message}
                </div>
            `;
                });
        }


        // Review modal functionality
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('review-btn') || e.target.closest('.review-btn')) {
                const btn = e.target.classList.contains('review-btn') ? e.target : e.target.closest('.review-btn');
                document.getElementById('modalOrderId').value = btn.dataset.orderId;
                document.getElementById('modalProductId').value = btn.dataset.productId;
            }
        });

        // Star rating functionality
        document.querySelectorAll('.star-rating input').forEach(input => {
            input.addEventListener('change', () => {
                const value = parseInt(input.value);
                const labels = document.querySelectorAll('.star-rating label i');
                labels.forEach((icon, index) => {
                    if (index < value) {
                        icon.classList.remove('bi-star');
                        icon.classList.add('bi-star-fill');
                    } else {
                        icon.classList.remove('bi-star-fill');
                        icon.classList.add('bi-star');
                    }
                });
            });
        });

        // Form submission handling
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';

            // Update the review button
            const orderId = document.getElementById('modalOrderId').value;
            const productId = document.getElementById('modalProductId').value;

            const reviewBtn = document.querySelector(
                `.review-btn[data-order-id="${orderId}"][data-product-id="${productId}"]`
            );
            if (reviewBtn) {
                reviewBtn.disabled = true;
                reviewBtn.className = 'btn-modern btn-reviewed';
                reviewBtn.innerHTML = '<i class="fas fa-check"></i>Reviewed';
            }
        });

        // Smooth scrolling for back button
        document.querySelector('.back-btn').addEventListener('click', function(e) {
            if (this.getAttribute('href') === './') {
                e.preventDefault();
                window.location.href = './';
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>