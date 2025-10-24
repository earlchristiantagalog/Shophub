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

        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
        }

        .floating-element {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) {
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-20px) rotate(120deg);
            }

            66% {
                transform: translateY(10px) rotate(240deg);
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
            background-clip: text;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin: 2rem 0;
            position: relative;
        }

        .progress-steps::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e2e8f0;
            z-index: 1;
        }

        .progress-steps::after {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            width: 33.33%;
            height: 2px;
            background: linear-gradient(135deg, #10b981, #059669);
            z-index: 2;
            animation: progressFill 1.5s ease-out;
        }

        @keyframes progressFill {
            0% {
                width: 0%;
            }

            100% {
                width: 33.33%;
            }
        }

        .step {
            background: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 3;
            border: 2px solid #e2e8f0;
        }

        .step.completed {
            background: linear-gradient(135deg, #10b981, #059669);
            border-color: #10b981;
            color: white;
        }

        .step.active {
            background: #667eea;
            border-color: #667eea;
            color: white;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7);
            }

            50% {
                box-shadow: 0 0 0 10px rgba(102, 126, 234, 0);
            }
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
    <!-- Floating Elements -->
    <div class="floating-elements">
        <div class="floating-element">
            <i class="fas fa-gift text-6xl text-white"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-heart text-4xl text-white"></i>
        </div>
        <div class="floating-element">
            <i class="fas fa-star text-5xl text-white"></i>
        </div>
    </div>

    <!-- Success Notification Toast -->
    <div class="notification-toast">
        <div class="flex items-center space-x-3">
            <i class="fas fa-check-circle text-green-500 text-xl"></i>
            <div>
                <p class="font-semibold text-gray-800">Order Confirmed!</p>
                <p class="text-sm text-gray-600">Email confirmation sent</p>
            </div>
        </div>
    </div>

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

            <!-- Order Details -->
            <div class="order-details rounded-2xl p-6 mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Order Details</h3>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-medium">
                        <i class="fas fa-check-circle mr-1"></i>
                        Confirmed
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Order ID:</span>
                        <span class="font-bold text-gray-800 text-lg" id="order-id">#ORD001234</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Order Date:</span>
                        <span class="font-medium text-gray-800" id="order-date"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Estimated Delivery:</span>
                        <span class="font-medium text-gray-800" id="delivery-date"></span>
                    </div>
                </div>
            </div>

            <!-- Progress Steps -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Progress</h3>
                <div class="progress-steps">
                    <div class="text-center">
                        <div class="step completed">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <p class="text-xs mt-2 text-gray-600">Order Placed</p>
                    </div>
                    <div class="text-center">
                        <div class="step active">
                            <i class="fas fa-box text-sm"></i>
                        </div>
                        <p class="text-xs mt-2 text-gray-600">Processing</p>
                    </div>
                    <div class="text-center">
                        <div class="step">
                            <i class="fas fa-truck text-sm"></i>
                        </div>
                        <p class="text-xs mt-2 text-gray-600">Shipping</p>
                    </div>
                    <div class="text-center">
                        <div class="step">
                            <i class="fas fa-home text-sm"></i>
                        </div>
                        <p class="text-xs mt-2 text-gray-600">Delivered</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary Image
            <div class="mb-8">
                <img src="https://storage.googleapis.com/workspace-0f70711f-8b4e-4d94-86f1-2a93ccde5887/image/7a62f85f-35c2-461c-aeb3-fe2b3b46cea2.png" alt="Colorful ecommerce package with shopping items including electronics, clothing, and accessories arranged in an attractive display with soft lighting and modern packaging design" class="w-full rounded-2xl shadow-lg" />
            </div> -->

            <!-- Action Buttons -->
            <div class="space-y-4">
                <button class="btn-home w-full py-4 px-6 text-white font-semibold rounded-xl flex items-center justify-center space-x-2" onclick="goHome()">
                    <i class="fas fa-home"></i>
                    <span>Continue Shopping</span>
                </button>

                <div class="grid grid-cols-2 gap-4">
                    <button class="border-2 border-gray-300 py-3 px-4 rounded-xl font-medium text-gray-700 hover:border-gray-400 transition-colors flex items-center justify-center space-x-2" onclick="trackOrder()">
                        <i class="fas fa-search"></i>
                        <span>Track Order</span>
                    </button>
                    <button class="border-2 border-gray-300 py-3 px-4 rounded-xl font-medium text-gray-700 hover:border-gray-400 transition-colors flex items-center justify-center space-x-2" onclick="downloadReceipt()">
                        <i class="fas fa-download"></i>
                        <span>Receipt</span>
                    </button>
                </div>
            </div>

            <!-- Contact Support -->
            <div class="mt-8 text-center">
                <p class="text-gray-600 mb-2">Need help with your order?</p>
                <button class="text-blue-600 hover:text-blue-800 font-medium flex items-center justify-center mx-auto space-x-1" onclick="contactSupport()">
                    <i class="fas fa-headset"></i>
                    <span>Contact Support</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // Initialize page with dynamic content
        document.addEventListener('DOMContentLoaded', function() {
            // Get order ID from URL parameters or generate random one
            const urlParams = new URLSearchParams(window.location.search);
            const orderId = urlParams.get('order_id') || generateOrderId();

            // Update order details
            document.getElementById('order-id').textContent = '#' + orderId;
            document.getElementById('order-date').textContent = formatDate(new Date());

            // Calculate delivery date (5-7 business days)
            const deliveryDate = new Date();
            deliveryDate.setDate(deliveryDate.getDate() + 5);
            document.getElementById('delivery-date').textContent = formatDate(deliveryDate);

            // Animate progress after page load
            setTimeout(animateProgress, 1000);

            // Show success message
            showSuccessMessage();
        });

        function generateOrderId() {
            return 'ORD' + Math.floor(Math.random() * 1000000).toString().padStart(6, '0');
        }

        function formatDate(date) {
            return date.toLocaleDateString('en-US', {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function animateProgress() {
            const steps = document.querySelectorAll('.step');
            steps.forEach((step, index) => {
                setTimeout(() => {
                    if (index === 0) {
                        step.classList.add('completed');
                    } else if (index === 1) {
                        step.classList.add('active');
                    }
                }, index * 300);
            });
        }

        function showSuccessMessage() {
            // Confetti effect simulation
            createConfetti();
        }

        function createConfetti() {
            const colors = ['#667eea', '#764ba2', '#10b981', '#f59e0b', '#ef4444'];

            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.position = 'fixed';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.top = '-10px';
                    confetti.style.width = '10px';
                    confetti.style.height = '10px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.transform = 'rotate(' + Math.random() * 360 + 'deg)';
                    confetti.style.animation = 'fall 3s linear forwards';
                    confetti.style.pointerEvents = 'none';
                    confetti.style.zIndex = '9999';

                    document.body.appendChild(confetti);

                    setTimeout(() => {
                        confetti.remove();
                    }, 3000);
                }, i * 100);
            }
        }

        // Add CSS for confetti animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fall {
                to {
                    transform: translateY(100vh) rotate(720deg);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        function goHome() {
            // Add loading state
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i>Redirecting...';
            button.disabled = true;

            setTimeout(() => {
                window.location.href = './';
            }, 1000);
        }

        function trackOrder() {
            // Add loading state
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner animate-spin mr-2"></i>Redirecting...';
            button.disabled = true;

            setTimeout(() => {
                window.location.href = 'my_purchases.php';
            }, 1000);
        }

        function downloadReceipt() {
            // Simulate receipt download
            const orderId = document.getElementById('order-id').textContent.replace('#', '');
            const link = document.createElement('a');
            link.href = `#`; // In real implementation, this would be the receipt PDF URL
            link.download = `receipt-${orderId}.pdf`;

            // Show download message
            const toast = document.createElement('div');
            toast.className = 'fixed top-20 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
            toast.innerHTML = '<i class="fas fa-download mr-2"></i>Receipt download started...';
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        }

        function contactSupport() {
            const orderId = document.getElementById('order-id').textContent;
            const message = `Hello! I need help with my order ${orderId}.`;

            // In real implementation, this could open a chat widget or support form
            alert(`Support will be contacted regarding order ${orderId}. You can also email us at support@example.com or call 1-800-SUPPORT.`);
        }

        // Add some interactive hover effects
        document.querySelectorAll('button').forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-1px)';
            });

            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Auto-hide notification toast
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