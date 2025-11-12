<!-- Navigation Component -->
<nav class="navbar">
    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0;">
            <!-- Logo -->
            <a href="index.php" class="navbar-brand">
                <span>✦</span> Glam PhotoBooth <span>Accra</span>
            </a>

            <!-- Desktop Navigation -->
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.php" class="nav-link active">Home</a></li>
                <li><a href="packages.php" class="nav-link">Packages</a></li>
                <li><a href="gallery.php" class="nav-link">Gallery</a></li>
                <li><a href="about.php" class="nav-link">About</a></li>
                <li><a href="contact.php" class="nav-link">Contact</a></li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- Logged In User -->
                    <?php if($_SESSION['user_role'] === 'admin'): ?>
                        <li><a href="views/admin/dashboard.php" class="btn btn-secondary btn-sm">Admin Panel</a></li>
                    <?php elseif($_SESSION['user_role'] === 'provider'): ?>
                        <li><a href="views/provider/dashboard.php" class="btn btn-secondary btn-sm">Provider Dashboard</a></li>
                    <?php else: ?>
                        <li><a href="views/customer/dashboard.php" class="btn btn-secondary btn-sm">My Dashboard</a></li>
                    <?php endif; ?>
                    <li><a href="controllers/auth_controller.php?action=logout" class="nav-link">Logout</a></li>
                <?php else: ?>
                    <!-- Guest User -->
                    <li><a href="login.php" class="nav-link">Login</a></li>
                    <li><a href="register.php" class="btn btn-primary btn-sm">Get Started</a></li>
                <?php endif; ?>
            </ul>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle" style="display: none;">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>
</nav>

<style>
/* Mobile Menu Styles */
.mobile-menu-toggle {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.mobile-menu-toggle span {
    width: 25px;
    height: 3px;
    background-color: var(--navy-dark);
    transition: all 0.3s ease;
    border-radius: 2px;
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: flex !important;
    }

    .nav-menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background-color: var(--white);
        flex-direction: column;
        padding: 1rem 0;
        box-shadow: var(--shadow-lg);
        transform: translateY(-100%);
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .nav-menu.active {
        transform: translateY(0);
        opacity: 1;
        visibility: visible;
    }

    .nav-menu li {
        width: 100%;
        text-align: center;
        padding: 0.5rem 0;
    }

    .nav-link {
        display: block;
        width: 100%;
    }

    .btn-sm {
        width: auto;
        margin: 0 auto;
    }
}
</style>

<script>
// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.getElementById('navMenu');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
        });
    }

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.navbar')) {
            navMenu.classList.remove('active');
            if (mobileMenuToggle) {
                mobileMenuToggle.classList.remove('active');
            }
        }
    });

    // Active link highlighting
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});
</script>
