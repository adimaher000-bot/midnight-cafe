<!-- includes/footer.php -->
</main>
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <!-- Brand Section -->
            <div class="footer-brand">
                <div class="footer-logo">
                    <i class="fas fa-coffee"></i> <span>Midnight Cafe</span>
                </div>
                <p class="footer-tagline">Brewing moments of happiness, one cup at a time. Experience the perfect blend
                    of tradition and modern vibes.</p>
                <div class="footer-social">
                    <a href="<?php echo get_setting('social_instagram', '#'); ?>"><i class="fab fa-instagram"></i></a>
                    <a href="<?php echo get_setting('social_facebook', '#'); ?>"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="menu.php">Our Menu</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="cart.php">My Cart</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <ul class="contact-list">
                    <li><i class="fas fa-map-marker-alt"></i> 123 Coffee Street, Flavor Town</li>
                    <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                    <li><i class="fas fa-envelope"></i> hello@midnightcafe.com</li>
                </ul>
            </div>

            <!-- Opening Hours -->
            <div class="footer-hours">
                <h3>Opening Hours</h3>
                <ul class="hours-list">
                    <li><span>Mon - Fri:</span> 7:00 AM - 10:00 PM</li>
                    <li><span>Sat - Sun:</span> 8:00 AM - 11:00 PM</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y') . " " . get_setting('footer_text', 'Midnight Cyber Cafe'); ?>. All rights
                reserved. | Made with ❤️ by VAdi <br> — Sangharsh · sushil —</p>
        </div>
    </div>
</footer>

<script src="<?php echo BASE_URL; ?>js/script.js?v=<?php echo time(); ?>"></script>
</body>

</html>