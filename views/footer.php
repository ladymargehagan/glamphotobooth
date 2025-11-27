<?php
/**
 * Footer Component
 * views/footer.php
 */
?>
<footer class="footer">
    <div class="container">
        <div class="grid grid-4 gap-lg mb-xl">
            <div class="footer-section">
                <h5 class="text-primary mb-md">PhotoMarket</h5>
                <p class="text-muted text-sm">Premium photography services and equipment marketplace for Ghana's creative professionals.</p>
            </div>

            <div class="footer-section">
                <h6 class="mb-md">Browse</h6>
                <ul class="footer-links">
                    <li><a href="<?php echo SITE_URL; ?>/services.php">Services</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/shop.php">Products</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h6 class="mb-md">Company</h6>
                <ul class="footer-links">
                    <li><a href="<?php echo SITE_URL; ?>/about.php">About Us</a></li>
                    <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-divider mb-lg"></div>

        <div class="flex-between text-muted text-sm">
            <p>&copy; 2024 PhotoMarket Ghana. All rights reserved.</p>
            <p>Crafted with elegance for creative minds.</p>
        </div>
    </div>
</footer>

<style>
.footer {
    background: var(--primary);
    color: var(--white);
    padding: var(--spacing-2xl) 0;
    margin-top: var(--spacing-2xl);
    border-top: 1px solid rgba(226, 196, 146, 0.2);
}

.footer-section h5,
.footer-section h6 {
    color: var(--secondary);
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: var(--spacing-sm);
}

.footer-links a {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.95rem;
    transition: var(--transition);
}

.footer-links a:hover {
    color: var(--secondary);
}

.footer-divider {
    height: 1px;
    background: rgba(255, 255, 255, 0.1);
}

.text-sm {
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .grid-4 {
        grid-template-columns: repeat(2, 1fr);
    }

    .footer {
        padding: var(--spacing-xl) 0;
    }
}

@media (max-width: 480px) {
    .grid-4 {
        grid-template-columns: 1fr;
    }

    .flex-between {
        flex-direction: column;
        gap: var(--spacing-md);
        text-align: center;
    }
}
</style>
