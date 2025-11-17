<!-- 🌟 Modern ShopHub Footer -->
<footer class="footer text-light pt-5 pb-3">
    <div class="container">
        <div class="row gy-4">
            <!-- Brand & Description -->
            <div class="col-md-3">
                <h4 class="footer-brand mb-3"><i class="fas fa-bolt me-2 text-warning"></i>ShopHub</h4>
                <p class="text-white-50">
                    Your ultimate destination for the latest electronics and gadgets. Explore, shop, and experience innovation.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Customer Service -->
            <div class="col-md-3">
                <h5 class="fw-bold mb-3 text-warning">Customer Service</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">Returns</a></li>
                    <li><a href="#">Shipping Info</a></li>
                </ul>
            </div>

            <!-- About Section -->
            <div class="col-md-3">
                <h5 class="fw-bold mb-3 text-warning">About</h5>
                <ul class="list-unstyled footer-links">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Press</a></li>
                    <li><a href="#">Sustainability</a></li>
                </ul>
            </div>

            <!-- Newsletter -->
            <div class="col-md-3">
                <h5 class="fw-bold mb-3 text-warning">Stay Updated</h5>
                <p class="text-white-50 mb-3">Subscribe to get the latest offers and updates!</p>
                <div class="input-group">
                    <input type="email" class="form-control" placeholder="Enter your email">
                    <button class="btn btn-warning fw-semibold">Subscribe</button>
                </div>
            </div>
        </div>

        <hr class="my-4 border-light">

        <div class="row align-items-center text-center text-md-start">
            <div class="col-md-6 mb-2 mb-md-0">
                <p class="mb-0 text-white-50">&copy; 2025 ShopHub. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="#" class="footer-policy">Privacy Policy</a>
                <span class="mx-2 text-white-50">|</span>
                <a href="#" class="footer-policy">Terms of Service</a>
                <span class="mx-2 text-white-50">|</span>
                <a href="#" class="footer-policy">Cookie Policy</a>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Footer Background Gradient */
    .footer {
        background: linear-gradient(135deg, #1a1a1a 0%, #212529 50%, #111 100%);
        font-size: 0.95rem;
        margin-top: 20px;
    }

    /* Footer Branding */
    .footer-brand {
        font-weight: 700;
        color: #fff;
    }

    /* Social Icons */
    .social-icon {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #2b2b2b;
        color: #f8f9fa;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .social-icon:hover {
        background: #ffc107;
        color: #000;
        transform: translateY(-3px);
    }

    /* Footer Links */
    .footer-links a {
        display: block;
        color: #bbb;
        text-decoration: none;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .footer-links a:hover {
        color: #ffc107;
        transform: translateX(5px);
    }

    /* Input Field */
    .footer .form-control {
        border: none;
        border-radius: 0.5rem 0 0 0.5rem;
        padding: 0.6rem;
    }

    .footer .btn {
        border-radius: 0 0.5rem 0.5rem 0;
    }

    /* Footer Policy Links */
    .footer-policy {
        color: #bbb;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .footer-policy:hover {
        color: #ffc107;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .footer {
            text-align: center;
        }

        .social-icon {
            margin: 0 auto;
        }
    }
</style>

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


<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>

</html>