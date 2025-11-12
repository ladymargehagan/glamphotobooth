<!-- Footer Component -->
<footer class="footer bg-navy" style="color: var(--white); padding: 3rem 0 1rem;">
    <div class="container">
        <!-- Footer Top -->
        <div class="row" style="margin-bottom: 2rem;">
            <!-- Company Info -->
            <div class="col-12 col-md-4" style="margin-bottom: 2rem;">
                <h4 style="color: var(--gold-primary); font-size: 1.5rem; margin-bottom: 1rem;">
                    <span style="color: var(--white);">Glam PhotoBooth</span> Accra
                </h4>
                <p style="color: rgba(255, 255, 255, 0.8); line-height: 1.8;">
                    Ghana's premier photography services marketplace. Connecting you with professional photographers and luxury photobooth experiences for your special moments.
                </p>
                <div style="margin-top: 1.5rem;">
                    <a href="#" style="color: var(--gold-primary); margin-right: 1rem; font-size: 1.5rem;">📘</a>
                    <a href="#" style="color: var(--gold-primary); margin-right: 1rem; font-size: 1.5rem;">📸</a>
                    <a href="#" style="color: var(--gold-primary); margin-right: 1rem; font-size: 1.5rem;">🐦</a>
                    <a href="#" style="color: var(--gold-primary); font-size: 1.5rem;">📧</a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-12 col-md-2" style="margin-bottom: 2rem;">
                <h5 style="color: var(--gold-primary); margin-bottom: 1rem; font-size: 1.125rem;">Quick Links</h5>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;"><a href="index.php" style="color: rgba(255, 255, 255, 0.8);">Home</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="packages.php" style="color: rgba(255, 255, 255, 0.8);">Packages</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="gallery.php" style="color: rgba(255, 255, 255, 0.8);">Gallery</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="about.php" style="color: rgba(255, 255, 255, 0.8);">About Us</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="contact.php" style="color: rgba(255, 255, 255, 0.8);">Contact</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div class="col-12 col-md-3" style="margin-bottom: 2rem;">
                <h5 style="color: var(--gold-primary); margin-bottom: 1rem; font-size: 1.125rem;">Our Services</h5>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.5rem;"><a href="packages.php#photography" style="color: rgba(255, 255, 255, 0.8);">Professional Photography</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="packages.php#photobooth" style="color: rgba(255, 255, 255, 0.8);">Photobooth Rental</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="packages.php#addons" style="color: rgba(255, 255, 255, 0.8);">Event Add-ons</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="provider-signup.php" style="color: rgba(255, 255, 255, 0.8);">Become a Provider</a></li>
                    <li style="margin-bottom: 0.5rem;"><a href="faq.php" style="color: rgba(255, 255, 255, 0.8);">FAQ</a></li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-12 col-md-3" style="margin-bottom: 2rem;">
                <h5 style="color: var(--gold-primary); margin-bottom: 1rem; font-size: 1.125rem;">Contact Us</h5>
                <ul style="list-style: none; padding: 0;">
                    <li style="margin-bottom: 0.75rem; color: rgba(255, 255, 255, 0.8);">
                        <strong>📍 Address:</strong><br>
                        123 Oxford Street<br>
                        Osu, Accra, Ghana
                    </li>
                    <li style="margin-bottom: 0.75rem; color: rgba(255, 255, 255, 0.8);">
                        <strong>📞 Phone:</strong><br>
                        +233 24 123 4567
                    </li>
                    <li style="margin-bottom: 0.75rem; color: rgba(255, 255, 255, 0.8);">
                        <strong>✉️ Email:</strong><br>
                        info@glamphotobooth.com
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div style="border-top: 1px solid rgba(212, 175, 120, 0.3); padding-top: 1.5rem; margin-top: 2rem;">
            <div class="row">
                <div class="col-12 col-md-6" style="text-align: center; margin-bottom: 1rem;">
                    <p style="color: rgba(255, 255, 255, 0.7); margin: 0; font-size: 0.875rem;">
                        &copy; <?php echo date('Y'); ?> Glam PhotoBooth Accra. All rights reserved.
                    </p>
                </div>
                <div class="col-12 col-md-6" style="text-align: center;">
                    <a href="privacy.php" style="color: rgba(255, 255, 255, 0.7); margin: 0 0.75rem; font-size: 0.875rem;">Privacy Policy</a>
                    <a href="terms.php" style="color: rgba(255, 255, 255, 0.7); margin: 0 0.75rem; font-size: 0.875rem;">Terms of Service</a>
                    <a href="cookies.php" style="color: rgba(255, 255, 255, 0.7); margin: 0 0.75rem; font-size: 0.875rem;">Cookie Policy</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
@media (max-width: 768px) {
    .footer .col-12 {
        text-align: center !important;
    }

    .footer .row > div {
        flex: 0 0 100%;
        max-width: 100%;
    }
}
</style>
