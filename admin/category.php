<?php
/**
 * Admin Category Management
 * admin/category.php
 */
require_once __DIR__ . '/../settings/core.php';

// Require admin access
requireAdmin();

$pageTitle = 'Manage Categories - GlamPhotobooth Accra Admin';
$cssPath = SITE_URL . '/css/style.css';
$dashboardCss = SITE_URL . '/css/dashboard.css';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($cssPath); ?>">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($dashboardCss); ?>">
    <!-- SweetAlert2 Library -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/admin_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="dashboard-header">
                <div>
                    <h1 class="dashboard-title">Categories</h1>
                    <p class="dashboard-subtitle">Manage product and service categories</p>
                </div>
                <div class="dashboard-actions">
                    <button type="button" class="btn btn-primary" id="addCategoryBtn">Add Category</button>
                </div>
            </div>

            <!-- Categories Table -->
            <div class="dashboard-card">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">#</th>
                            <th>Category Name</th>
                            <th style="width: 120px;">Products</th>
                            <th style="width: 150px;">Created</th>
                            <th style="width: 120px; text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTable">
                        <tr>
                            <td colspan="5" style="text-align: center; padding: var(--spacing-xl); color: var(--text-secondary);">
                                Loading categories...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Add/Edit Modal -->
    <div id="categoryModal" class="modal hidden">
        <div class="modal-overlay"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add Category</h2>
                <button type="button" class="modal-close" id="closeModalBtn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>

            <form id="categoryForm" class="modal-body">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="cat_id" id="catId" value="">

                <div class="form-group">
                    <label for="catName">Category Name</label>
                    <input type="text" id="catName" name="cat_name" placeholder="e.g., Wedding Photography" required>
                    <span class="form-error" id="catNameError"></span>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-outline" id="cancelBtn">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Add Category</button>
                </div>
            </form>

            <!-- Success Message -->
            <div id="successMessage" class="modal-message success-message hidden">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span id="successText">Operation completed successfully</span>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="modal-message error-message hidden">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span id="errorText">An error occurred</span>
            </div>
        </div>
    </div>

    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal.hidden {
            display: none !important;
        }

        .modal-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 90%;
            position: relative;
            z-index: 1001;
            animation: slideUp 0.3s ease;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-color);
        }

        .modal-header h2 {
            margin: 0;
            color: var(--primary);
            font-size: 1.35rem;
        }

        .modal-close {
            background: none;
            border: none;
            cursor: pointer;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            transition: var(--transition);
            padding: 0;
        }

        .modal-close:hover {
            color: var(--primary);
        }

        .modal-close svg {
            width: 20px;
            height: 20px;
            stroke-width: 2;
        }

        .modal-body {
            padding: var(--spacing-lg);
        }

        .modal-footer {
            display: flex;
            gap: var(--spacing-md);
            justify-content: flex-end;
            padding: var(--spacing-lg);
            border-top: 1px solid var(--border-color);
        }

        .modal-message {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-md);
            margin: var(--spacing-md);
            border-radius: var(--border-radius);
            font-weight: 500;
            animation: slideUp 0.3s ease;
        }

        .modal-message.hidden {
            display: none !important;
        }

        .modal-message svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        .success-message {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .success-message svg {
            stroke: #2e7d32;
        }

        .error-message {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }

        .error-message svg {
            stroke: #c62828;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: var(--spacing-lg);
        }

        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: var(--spacing-xs);
            color: var(--text-primary);
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            font-family: var(--font-sans);
            transition: var(--transition);
            background: var(--white);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 33, 82, 0.1);
        }

        .form-error {
            display: block;
            color: #d32f2f;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        /* Action Buttons */
        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border-radius: var(--border-radius);
        }

        .edit-btn {
            color: var(--primary);
            margin-right: var(--spacing-sm);
        }

        .edit-btn:hover {
            background: rgba(16, 33, 82, 0.1);
        }

        .delete-btn {
            color: #d32f2f;
        }

        .delete-btn:hover {
            background: rgba(211, 47, 47, 0.1);
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
            }

            .dashboard-table {
                font-size: 0.85rem;
            }

            .dashboard-table th,
            .dashboard-table td {
                padding: var(--spacing-sm);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script src="<?php echo SITE_URL; ?>/js/sweetalert.js"></script>
    <script src="<?php echo SITE_URL; ?>/js/category.js"></script>
</body>
</html>
