<!-- Footer -->
<footer class="footer mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3 mb-4">
                <h5><i class="fas fa-bolt me-2"></i>Shophub</h5>
                <p>Your ultimate destination for the latest electronics and gadgets.</p>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <div class="col-md-3 mb-4">
                <h5>Customer Service</h5>
                <ul class="list-unstyled">
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Returns</a></li>
                    <li><a href="#">Shipping Info</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h5>About</h5>
                <ul class="list-unstyled">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Press</a></li>
                    <li><a href="#">Sustainability</a></li>
                </ul>
            </div>

            <div class="col-md-3 mb-4">
                <h5>Follow Us</h5>
                <p>Stay updated with our latest offers and new arrivals!</p>
                <div class="input-group">
                    <input type="email" class="form-control" placeholder="Enter your email">
                    <button class="btn btn-primary">Subscribe</button>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">&copy; 2024 Shophub. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    <a href="#">Privacy Policy</a> |
                    <a href="#">Terms of Service</a> |
                    <a href="#">Cookie Policy</a>
                </p>
            </div>
        </div>
    </div>
</footer>
<script>
    window.addEventListener("load", function() {
        const spinner = document.getElementById("loadingSpinner");
        if (spinner) {
            spinner.style.display = "none";
        }
    });
</script>
<script>
    function changeImage(thumbnail) {
        const mainImage = document.getElementById('mainImage');
        const thumbnails = document.querySelectorAll('.thumbnail');

        // Update main image
        mainImage.src = thumbnail.src.replace('w=80&h=80', 'w=500&h=400');

        // Update active thumbnail
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        thumbnail.classList.add('active');
    }

    function selectVariant(element) {
        const siblings = element.parentNode.querySelectorAll('.variant-option');
        siblings.forEach(sibling => sibling.classList.remove('active'));
        element.classList.add('active');
    }

    function changeQuantity(change) {
        const quantityInput = document.getElementById('quantity');
        let currentValue = parseInt(quantityInput.value);
        let newValue = currentValue + change;

        if (newValue >= 1 && newValue <= 99) {
            quantityInput.value = newValue;
        }
    }

    // Add to cart functionality
    document.querySelector('.btn-add-cart').addEventListener('click', function() {
        this.innerHTML = '<i class="fas fa-check me-2"></i>Added to Cart';
        this.style.background = 'linear-gradient(135deg, #4caf50, #45a049)';

        setTimeout(() => {
            this.innerHTML = '<i class="fas fa-cart-plus me-2"></i>Add to Cart';
            this.style.background = 'linear-gradient(135deg, #ffa726, #ff9800)';
        }, 2000);
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hoursSpan = document.getElementById('hours');
        const minutesSpan = document.getElementById('minutes');
        const secondsSpan = document.getElementById('seconds');
        const flashSaleContainer = document.getElementById('flashSaleContainer');

        // If flash sale has ended before
        if (localStorage.getItem('flashSaleExpired') === 'true') {
            flashSaleContainer.remove(); // Remove it immediately
            return;
        }

        let endTime;
        const storedEndTime = localStorage.getItem('flashSaleEndTime');

        if (storedEndTime) {
            endTime = new Date(parseInt(storedEndTime));
        } else {
            // Set countdown to 3 hours from now
            endTime = new Date();
            endTime.setHours(endTime.getHours() + 9);
            localStorage.setItem('flashSaleEndTime', endTime.getTime());
        }

        function updateCountdown() {
            const now = new Date();
            const diff = endTime - now;

            if (diff <= 0) {
                hoursSpan.textContent = '00';
                minutesSpan.textContent = '00';
                secondsSpan.textContent = '00';

                // Show Sale Ended message
                flashSaleContainer.innerHTML = `
                <div class="alert alert-warning text-center">
                    <strong>Flash Sale Ended</strong>
                </div>
            `;

                // Mark sale as expired
                localStorage.setItem('flashSaleExpired', 'true');

                // Optionally auto-remove after delay
                setTimeout(() => {
                    flashSaleContainer.remove();
                }, 3000); // Remove after 3 seconds
                return;
            }

            const hours = Math.floor(diff / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            hoursSpan.textContent = String(hours).padStart(2, '0');
            minutesSpan.textContent = String(minutes).padStart(2, '0');
            secondsSpan.textContent = String(seconds).padStart(2, '0');
        }

        updateCountdown();
        const interval = setInterval(updateCountdown, 1000);
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
    // Countdown timer for flash sale
    function updateCountdown() {
        const now = new Date().getTime();
        const flashSaleEnd = now + (9 * 60 * 60 * 1000) + (59 * 60 * 1000) + (59 * 1000); // 2h 45m 30s from now

        const distance = flashSaleEnd - now;

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    }

    // Update countdown every second
    setInterval(updateCountdown, 1000);
    updateCountdown(); // Initial call
</script>
</body>

</html>