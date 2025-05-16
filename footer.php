<footer>
    <div class="footer-container">
        <div>
            <h2 class="footer-logo">BITRONICS</h2>
            <address class="footer-address">
                Quezon Avenue, Cotabato City
            </address>
            <p class="footer-contact">
                <i class="fas fa-phone-alt"></i>+639998893894
            </p>
            <p class="footer-contact">
                <i class="fas fa-envelope"></i>sales@bitronics-electronics.com
            </p>
        </div>

        <div>
            <h3 class="footer-heading">Company</h3>
            <ul class="footer-links">
                <li><a href="#">
                    <i class="fas fa-map-marker-alt"></i>Store Locations
                </a></li>
                <li><a href="#">
                    <i class="fas fa-star"></i>Reviews
                </a></li>
                <li><a href="#">
                    <i class="fas fa-info-circle"></i>About Us
                </a></li>
            </ul>
        </div>
        
        <div>
            <h3 class="footer-heading">Links</h3>
            <ul class="footer-links">
                <li><a href="#" target="_blank">
                    <i class="fas fa-external-link-alt"></i>Shopee Official Store
                </a></li>
                <li><a href="#" target="_blank">
                    <i class="fas fa-external-link-alt"></i>Lazada Official Store
                </a></li>
            </ul>
        </div>
        
        <div>
            <h3 class="footer-heading">Follow Us</h3>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
            </div>
        </div>
    </div>
</footer>

<style>
    footer {
        background-color: var(--white);
        padding: 32px 0;
        border-top: 1px solid var(--border);
    }
    
    .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 16px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .footer-logo {
        font-family: 'Orbitron', sans-serif;
        font-size: 24px;
        letter-spacing: 0.1em;
        color: var(--primary);
        margin-bottom: 8px;
    }
    
    .footer-address {
        font-style: normal;
        font-size: 14px;
        color: var(--text);
        margin-bottom: 8px;
    }
    
    .footer-contact {
        font-size: 14px;
        color: var(--text);
        margin-bottom: 4px;
    }
    
    .footer-contact i {
        margin-right: 8px;
    }
    
    .footer-heading {
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 12px;
    }
    
    .footer-links {
        font-size: 14px;
        color: var(--text);
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 0;
        list-style: none;
    }
    
    .footer-links a {
        color: inherit;
        text-decoration: none;
        display: flex;
        align-items: center;
    }
    
    .footer-links a:hover {
        text-decoration: underline;
        color: var(--primary);
    }
    
    .footer-links i {
        font-size: 12px;
        margin-right: 8px;
    }
    
    .social-links {
        display: flex;
        gap: 16px;
    }
    
    .social-links a {
        color: var(--primary);
        font-size: 20px;
    }
    
    .social-links a:hover {
        color: var(--primary-light);
    }
    
    @media (min-width: 640px) {
        .footer-container {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>

<script>
    // Update cart count
    function updateCartCount() {
        fetch('get_cart_count.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCountElements = document.querySelectorAll('.cart-count');
                    cartCountElements.forEach(element => {
                        element.textContent = data.count;
                    });
                }
            });
    }
</script>
</body>
</html>