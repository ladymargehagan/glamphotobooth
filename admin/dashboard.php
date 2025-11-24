<?php
require_once __DIR__ . '/../settings/core.php';

// Require login
requireLogin();

// Check if user is admin
if (getCurrentUserRole() != ROLE_ADMIN) {
    header('Location: ' . SITE_URL . '/index.php');
    exit();
}

$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Glam PhotoBooth Accra</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lavishly+Yours&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/global.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0;">
                <div class="navbar-brand">
                    <span>&</span> Glam PhotoBooth - Admin
                </div>
                <div>
                    <span style="margin-right: 1rem;">Welcome, <?php echo htmlspecialchars($userName); ?></span>
                    <a href="../login/logout.php" class="btn btn-sm btn-outline">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container" style="padding-top: 3rem;">
        <h1>Admin Dashboard</h1>
        <p style="color: var(--medium-gray); margin-bottom: 2rem;">Manage your platform, customers, and service providers.</p>

        <div class="row">
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-users"></i> Total Customers</h4>
                        <p style="font-size: 2rem; margin: 1rem 0;">0</p>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-camera"></i> Service Providers</h4>
                        <p style="font-size: 2rem; margin: 1rem 0;">0</p>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-credit-card"></i> Total Revenue</h4>
                        <p style="font-size: 2rem; margin: 1rem 0;">GH₵ 0.00</p>
                    </div>
                </div>
            </div>
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-tasks"></i> Pending Approvals</h4>
                        <p style="font-size: 2rem; margin: 1rem 0;">0</p>
                    </div>
                </div>
            </div>
        </div>

        <section style="margin-top: 3rem;">
            <h3>Quick Actions</h3>
            <div class="card">
                <div class="card-body">
                    <a href="#" class="btn btn-sm btn-secondary">View Customers</a>
                    <a href="#" class="btn btn-sm btn-secondary">View Providers</a>
                    <a href="#" class="btn btn-sm btn-secondary">View Bookings</a>
                    <a href="#" class="btn btn-sm btn-secondary">View Reports</a>
                </div>
            </div>
        </section>
    </main>

    <footer style="background-color: var(--navy-dark); color: var(--white); text-align: center; padding: 2rem; margin-top: 3rem;">
        <p>&copy; 2024 Glam PhotoBooth Accra. All rights reserved.</p>
    </footer>
</body>
</html>
