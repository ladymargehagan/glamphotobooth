<?php
require_once __DIR__ . '/../settings/core.php';

// Require login
requireLogin();

// Check if user is a customer
if (getCurrentUserRole() != ROLE_CUSTOMER) {
    header('Location: ' . SITE_URL . '/index.php');
    exit();
}

$userId = getCurrentUserId();
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Customer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Glam PhotoBooth Accra</title>
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
                    <span>&</span> Glam PhotoBooth
                </div>
                <div>
                    <span style="margin-right: 1rem;">Welcome, <?php echo htmlspecialchars($userName); ?></span>
                    <a href="../login/logout.php" class="btn btn-sm btn-outline">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="container" style="padding-top: 3rem;">
        <h1>Customer Dashboard</h1>
        <p style="color: var(--medium-gray); margin-bottom: 2rem;">Welcome to your personal dashboard. Here you can manage your bookings and view your galleries.</p>

        <div class="row">
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-calendar"></i> Upcoming Bookings</h4>
                        <p style="font-size: 2rem; margin: 1rem 0;">0</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-images"></i> Galleries</h4>
                        <p style="font-size: 2rem; margin: 1rem 0;">0</p>
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card">
                    <div class="card-body">
                        <h4><i class="fas fa-credit-card"></i> Total Spent</h4>
                        <p style="font-size: 2rem; margin: 1rem 0;">GH₵ 0.00</p>
                    </div>
                </div>
            </div>
        </div>

        <section style="margin-top: 3rem;">
            <h3>Account Settings</h3>
            <div class="card">
                <div class="card-body">
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                    <p><strong>User ID:</strong> <?php echo $userId; ?></p>
                    <button class="btn btn-sm btn-secondary">Edit Profile</button>
                    <button class="btn btn-sm btn-outline">Change Password</button>
                </div>
            </div>
        </section>
    </main>

    <footer style="background-color: var(--navy-dark); color: var(--white); text-align: center; padding: 2rem; margin-top: 3rem;">
        <p>&copy; 2024 Glam PhotoBooth Accra. All rights reserved.</p>
    </footer>
</body>
</html>
