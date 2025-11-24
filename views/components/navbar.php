<!-- Navigation Component -->
<nav class="navbar" style="background-color: white; box-shadow: 0 4px 6px rgba(27, 43, 77, 0.1); position: sticky; top: 0; z-index: 1000;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0;">
            <!-- Logo -->
            <a href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'views/') !== false) ? '../../index.php' : 'index.php'; ?>" style="font-family: 'Playfair Display', serif; font-size: 1.75rem; font-weight: 800; color: var(--navy-dark); display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <span style="color: var(--gold-primary);">&</span> Glam PhotoBooth Accra
            </a>

            <!-- Desktop Navigation -->
            <ul class="nav-menu" id="navMenu" style="list-style: none; display: flex; gap: 2rem; align-items: center; margin: 0;">
                <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'views/') !== false) ? '../../index.php' : 'index.php'; ?>" class="nav-link" style="font-weight: 500; color: var(--navy-medium); text-decoration: none; transition: color 150ms ease;">Home</a></li>
                <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'views/') !== false) ? '../../packages.php' : 'packages.php'; ?>" class="nav-link" style="font-weight: 500; color: var(--navy-medium); text-decoration: none; transition: color 150ms ease;">Services</a></li>
                <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'views/') !== false) ? '../../gallery.php' : 'gallery.php'; ?>" class="nav-link" style="font-weight: 500; color: var(--navy-medium); text-decoration: none; transition: color 150ms ease;">Gallery</a></li>

                <?php if(isset($_SESSION['user_id'])): ?>
                    <!-- Logged In User -->
                    <?php
                        $userType = $_SESSION['user_type'] ?? 'customer';
                        $isDashboardPath = (strpos($_SERVER['REQUEST_URI'], 'views/') !== false);
                        $dashboardLink = ($userType === 'photographer' || $userType === 'vendor')
                            ? ($isDashboardPath ? 'dashboard.php' : 'views/provider/dashboard.php')
                            : ($isDashboardPath ? 'dashboard.php' : 'views/customer/dashboard.php');
                    ?>
                    <li><a href="<?php echo $dashboardLink; ?>" class="btn btn-secondary btn-sm" style="padding: 0.75rem 1.5rem; background-color: var(--navy-dark); color: white; border-radius: 8px; font-weight: 600; text-decoration: none; transition: all 300ms ease;">Dashboard</a></li>
                    <li>
                        <form action="<?php echo (strpos($_SERVER['REQUEST_URI'], 'views/') !== false) ? '../../actions/logout_action.php' : 'actions/logout_action.php'; ?>" method="POST" style="margin: 0;">
                            <button type="submit" class="nav-link" style="background: none; border: none; font-weight: 500; color: var(--navy-medium); cursor: pointer; padding: 0; transition: color 150ms ease;">Logout</button>
                        </form>
                    </li>
                <?php else: ?>
                    <!-- Guest User -->
                    <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'views/') !== false) ? '../../login.php' : 'login.php'; ?>" class="nav-link" style="font-weight: 500; color: var(--navy-medium); text-decoration: none; transition: color 150ms ease;">Sign In</a></li>
                    <li><a href="<?php echo (strpos($_SERVER['REQUEST_URI'], 'views/') !== false) ? '../../register.php' : 'register.php'; ?>" class="btn btn-primary btn-sm" style="padding: 0.75rem 1.5rem; background: linear-gradient(135deg, var(--gold-primary), var(--gold-accent)); color: white; border-radius: 8px; font-weight: 600; text-decoration: none; box-shadow: 0 4px 20px rgba(212, 175, 120, 0.3); transition: all 300ms ease;">Get Started</a></li>
                <?php endif; ?>
            </ul>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle" style="background: none; border: none; cursor: pointer; padding: 0.5rem; display: none; flex-direction: column; gap: 4px;">
                <span style="width: 25px; height: 3px; background-color: var(--navy-dark); border-radius: 2px; transition: all 300ms ease;"></span>
                <span style="width: 25px; height: 3px; background-color: var(--navy-dark); border-radius: 2px; transition: all 300ms ease;"></span>
                <span style="width: 25px; height: 3px; background-color: var(--navy-dark); border-radius: 2px; transition: all 300ms ease;"></span>
            </button>
        </div>
    </div>
</nav>

<style>
.nav-link:hover {
    color: var(--gold-primary) !important;
}

.btn:hover {
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .mobile-menu-toggle {
        display: flex !important;
    }

    .nav-menu {
        position: absolute !important;
        top: 100% !important;
        left: 0 !important;
        width: 100% !important;
        background-color: white !important;
        flex-direction: column !important;
        padding: 1rem 0 !important;
        box-shadow: 0 10px 15px rgba(27, 43, 77, 0.15) !important;
        transform: translateY(-100%) !important;
        opacity: 0 !important;
        visibility: hidden !important;
        transition: all 300ms ease !important;
        gap: 0 !important;
    }

    .nav-menu.active {
        transform: translateY(0) !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .nav-menu li {
        width: 100%;
        text-align: center;
        padding: 0.5rem 0;
    }

    .nav-link {
        display: block;
        width: 100%;
        padding: 0.5rem 0;
    }

    .btn {
        width: auto !important;
        margin: 0 auto !important;
    }
}
</style>

<script>
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

    // Highlight active link
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('.nav-menu .nav-link').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href.includes(currentPage)) {
            link.style.color = 'var(--gold-primary)';
        }
    });
});
</script>
