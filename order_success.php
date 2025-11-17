<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Thank You!</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * {
            font-family: 'Inter', sans-serif;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .success-animation {
            animation: successPulse 2s ease-in-out infinite;
        }

        @keyframes successPulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .order-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .checkmark-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, #10b981, #059669);
            display: flex;
            align-items: center;
            justify-content: center;
            animation: checkmarkAppear 0.8s ease-out;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        @keyframes checkmarkAppear {
            0% {
                transform: scale(0) rotate(180deg);
                opacity: 0;
            }

            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        .order-details {
            background: linear-gradient(145deg, #f8fafc, #e2e8f0);
            border-left: 4px solid #667eea;
        }

        .btn-home {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .celebration-text {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #10b981;
            transform: translateX(400px);
            animation: slideIn 0.5s ease-out 1s forwards;
            z-index: 1000;
        }

        @keyframes slideIn {
            to {
                transform: translateX(0);
            }
        }
    </style>
</head>

<body class="gradient-bg min-h-screen relative">

    <?php
    include 'includes/db.php';

    // ✅ Check if order_id is provided via URL (example: ?order_id=ES27161)
    if (!isset($_GET['order_id'])) {
        die('No order specified.');
    }

    $order_id = $_GET['order_id'];

    // ✅ Fetch order details from database
    $stmt = $conn->prepare("
    SELECT 
        o.order_id, 
        o.order_date, 
        o.shipping_method, 
        o.shipping_fee, 
        o.total, 
        o.status,
        u.username, 
        u.phone,
        a.address_line_1, 
        a.barangay, 
        a.city, 
        a.province, 
        a.region, 
        a.zip_code
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN addresses a ON o.address_id = a.address_id
    WHERE o.order_id = ?
");
    $stmt->bind_param("s", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        die('Order not found.');
    }

    $order = $result->fetch_assoc();
    $stmt->close();

    // ✅ Compute Estimated Delivery based on shipping method
    $shipping_method = strtolower(trim($order['shipping_method']));
    $order_date = new DateTime($order['order_date']);
    $estimated_delivery = '';

    switch ($shipping_method) {
        case 'standard':
        case 'standard delivery':
            // 3–5 business days
            $start = clone $order_date;
            $end = clone $order_date;
            $start->modify('+3 days');
            $end->modify('+5 days');
            $estimated_delivery = $start->format('F d, Y') . ' - ' . $end->format('F d, Y') . ' (3–5 business days)';
            break;

        case 'express':
        case 'express delivery':
            // 1–2 business days (Mon–Sat)
            $end = clone $order_date;
            $days_added = 0;
            while ($days_added < 2) {
                $end->modify('+1 day');
                if ($end->format('N') <= 6) $days_added++;
            }
            $estimated_delivery = $end->format('F d, Y') . ' (1–2 business days)';
            break;

        case 'same day':
        case 'same day delivery':
            $estimated_delivery = "Within 1–3 hours (Same Day Delivery)";
            break;

        default:
            $estimated_delivery = "N/A";
            break;
    }
    ?>


    <!-- Notification Toast -->
    <div class="notification-toast">
        <div class="flex items-center space-x-3">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div>
                <p class="font-semibold text-gray-800">Order Confirmed!</p>
                <p class="text-sm text-gray-600">Email confirmation sent</p>
            </div>
        </div>
    </div>

    <!-- Main Container -->
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="order-card rounded-3xl shadow-2xl p-8 max-w-2xl w-full mx-4">
            <!-- Success Icon -->
            <div class="text-center mb-8">
                <div class="checkmark-circle mx-auto mb-6 success-animation">
                    <i class="fas fa-check text-white text-3xl"></i>
                </div>
                <h1 class="text-4xl font-bold celebration-text mb-2">Thank You!</h1>
                <p class="text-xl text-gray-600">Your order has been successfully placed</p>
            </div>

            <!-- ✅ Order Details -->
            <div class="order-details rounded-2xl p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Order Details</h3>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-check-circle mr-1"></i> Confirmed
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-bold text-gray-800 text-lg">#<?= htmlspecialchars($order['order_id']); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Order Date:</span>
                        <span class="font-medium text-gray-800"><?= date('F d, Y', strtotime($order['order_date'])); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Estimated Delivery:</span>
                        <span class="font-medium text-gray-800"><?= htmlspecialchars($estimated_delivery); ?></span>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="space-y-4">
                <button class="btn-home w-full py-4 px-6 text-white font-semibold rounded-xl flex items-center justify-center space-x-2" onclick="goHome()">
                    <i class="fas fa-home"></i><span>Continue Shopping</span>
                </button>

                <div class="grid grid-cols-2 gap-4">
                    <button class="border-2 border-gray-300 py-3 px-4 rounded-xl font-medium text-gray-700 hover:border-gray-400 transition-colors flex items-center justify-center space-x-2" onclick="trackOrder()">
                        <i class="fas fa-search"></i><span>Track Order</span>
                    </button>
                    <button class="border-2 border-gray-300 py-3 px-4 rounded-xl font-medium text-gray-700 hover:border-gray-400 transition-colors flex items-center justify-center space-x-2" onclick="downloadReceipt()">
                        <i class="fas fa-download"></i><span>Receipt</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function goHome() {
            const button = event.target.closest('button');
            button.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i>Redirecting...';
            button.disabled = true;
            setTimeout(() => {
                window.location.href = './';
            }, 1000);
        }

        function trackOrder() {
            const button = event.target.closest('button');
            button.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i>Redirecting...';
            button.disabled = true;
            setTimeout(() => {
                window.location.href = 'my_purchases.php';
            }, 1000);
        }

        function downloadReceipt() {
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.innerHTML = '<i class="fas fa-download mr-2"></i>Receipt download started...';
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        // Auto-hide success toast
        setTimeout(() => {
            const toast = document.querySelector('.notification-toast');
            if (toast) {
                toast.style.transform = 'translateX(400px)';
                setTimeout(() => toast.remove(), 500);
            }
        }, 5000);
    </script>
</body>

</html>