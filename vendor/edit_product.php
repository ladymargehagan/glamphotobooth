<?php
/**
 * Edit Product
 * customer/edit_product.php
 */
require_once __DIR__ . '/../settings/core.php';

requireLogin();
$user_role = isset($_SESSION['user_role']) ? intval($_SESSION['user_role']) : 4;
if ($user_role == 4) {
    header('Location: ' . SITE_URL . '/customer/dashboard.php');
    exit;
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header('Location: ' . SITE_URL . '/vendor/manage_products.php');
    exit;
}

$provider_class = new provider_class();
$provider = $provider_class->get_provider_by_customer($user_id);

if (!$provider) {
    header('Location: ' . SITE_URL . '/vendor/profile_setup.php');
    exit;
}

// Get product details
$product_class = new product_class();
$product = $product_class->get_product_by_id($product_id);

if (!$product || $product['provider_id'] != $provider['provider_id']) {
    header('Location: ' . SITE_URL . '/vendor/manage_products.php');
    exit;
}

// Get all categories
$category_class = new category_class();
$categories = $category_class->get_all_categories();

$pageTitle = 'Edit Product - PhotoMarket';
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
    <style>
        .product-form-container {
            max-width: 700px;
            margin: 0 auto;
            padding: var(--spacing-xxl) var(--spacing-xl);
        }

        .product-form-header {
            text-align: center;
            margin-bottom: var(--spacing-xxl);
        }

        .product-form-header h1 {
            color: var(--primary);
            font-family: var(--font-serif);
            font-size: 2rem;
            margin-bottom: var(--spacing-sm);
        }

        .product-form-header p {
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .product-card {
            background: var(--white);
            border-radius: var(--border-radius);
            padding: var(--spacing-xl);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }

        .form-section {
            margin-bottom: var(--spacing-xl);
        }

        .form-section-title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: var(--spacing-md);
            font-size: 0.95rem;
        }

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

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius);
            font-size: 0.95rem;
            font-family: var(--font-sans);
            transition: var(--transition);
            background: var(--white);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
            font-family: var(--font-sans);
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
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

        .form-hint {
            display: block;
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-top: 4px;
        }

        .image-upload-area {
            border: 2px dashed var(--border-color);
            border-radius: var(--border-radius);
            padding: var(--spacing-lg);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: #fafaf8;
        }

        .image-upload-area:hover {
            border-color: var(--primary);
            background: rgba(16, 33, 82, 0.02);
        }

        .image-upload-area.drag-over {
            border-color: var(--primary);
            background: rgba(16, 33, 82, 0.05);
        }

        .image-upload-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto var(--spacing-md);
            color: var(--text-secondary);
        }

        .image-upload-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .image-upload-text strong {
            color: var(--primary);
        }

        .image-preview {
            display: none;
            margin-top: var(--spacing-md);
            text-align: center;
        }

        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: var(--border-radius);
            border: 1px solid var(--border-color);
        }

        .image-preview-remove {
            display: inline-block;
            margin-top: var(--spacing-sm);
            padding: 0.5rem 1rem;
            background: #d32f2f;
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.8rem;
        }

        .image-preview-remove:hover {
            background: #c62828;
        }

        .form-actions {
            display: flex;
            gap: var(--spacing-md);
            margin-top: var(--spacing-xl);
        }

        .btn-submit {
            flex: 1;
            padding: 0.875rem 1.5rem;
            background: var(--primary);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn-submit:hover {
            background: #0d1a3a;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 33, 82, 0.2);
        }

        .btn-submit:disabled {
            background: var(--text-secondary);
            cursor: not-allowed;
            transform: none;
        }

        .btn-cancel {
            flex: 1;
            padding: 0.875rem 1.5rem;
            background: var(--light-gray);
            color: var(--text-primary);
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-align: center;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .btn-cancel:hover {
            background: #e8e6e1;
        }

        .message {
            padding: var(--spacing-md);
            border-radius: var(--border-radius);
            margin-bottom: var(--spacing-lg);
            display: none;
            align-items: center;
            gap: var(--spacing-sm);
            font-weight: 500;
        }

        .message.show {
            display: flex;
        }

        .message.success {
            background: rgba(76, 175, 80, 0.1);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.3);
        }

        .message.error {
            background: rgba(211, 47, 47, 0.1);
            color: #c62828;
            border: 1px solid rgba(211, 47, 47, 0.3);
        }

        .message svg {
            width: 20px;
            height: 20px;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .product-form-container {
                padding: var(--spacing-lg);
            }

            .product-form-header h1 {
                font-size: 1.5rem;
            }

            .product-card {
                padding: var(--spacing-lg);
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <?php require_once __DIR__ . '/../views/dashboard_sidebar.php'; ?>

        <!-- Main Content -->
        <main class="dashboard-content">
            <div class="product-form-container">
                <div class="product-form-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h1>Edit Product</h1>
                        <p>Update your product or service listing</p>
                    </div>
                    <a href="<?php echo SITE_URL; ?>/vendor/manage_products.php" class="btn btn-primary" style="padding: 0.5rem 1rem; text-decoration: none;">← Back to Dashboard</a>
                </div>

        <div class="product-card">
            <!-- Messages -->
            <div id="successMessage" class="message success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span id="successText"></span>
            </div>
            <div id="errorMessage" class="message error">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span id="errorText"></span>
            </div>

            <!-- Product Form -->
            <form id="productForm" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['product_id']); ?>">

                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="form-section-title">Basic Information</h3>

                    <div class="form-group">
                        <label for="title">Product Title</label>
                        <input type="text" id="title" name="title" placeholder="e.g., Professional Headshot Session" value="<?php echo htmlspecialchars($product['title']); ?>" required>
                        <span class="form-error" id="titleError"></span>
                        <span class="form-hint">3-255 characters</span>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" placeholder="Describe what's included, duration, deliverables..." required><?php echo htmlspecialchars($product['description']); ?></textarea>
                        <span class="form-error" id="descriptionError"></span>
                        <span class="form-hint">10-5000 characters</span>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="cat_id" required>
                            <option value="">Select a category</option>
                            <?php if ($categories): ?>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo htmlspecialchars($category['cat_id']); ?>" <?php echo $category['cat_id'] == $product['cat_id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['cat_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <span class="form-error" id="categoryError"></span>
                    </div>
                </div>

                <!-- Pricing & Type -->
                <div class="form-section">
                    <h3 class="form-section-title">Pricing & Type</h3>

                    <div class="form-group">
                        <label for="price">Price (₵)</label>
                        <input type="number" id="price" name="price" placeholder="e.g., 250.00" step="0.01" min="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                        <span class="form-error" id="priceError"></span>
                    </div>

                    <div class="form-group">
                        <label for="productType">Product Type</label>
                        <select id="productType" name="product_type" required>
                            <option value="">Select type</option>
                            <?php if ($user_role == 2): // Photographer - only service ?>
                                <option value="service" <?php echo $product['product_type'] == 'service' ? 'selected' : ''; ?>>Service</option>
                            <?php elseif ($user_role == 3): // Vendor - only sale ?>
                                <option value="sale" <?php echo $product['product_type'] == 'sale' ? 'selected' : ''; ?>>Sale</option>
                            <?php endif; ?>
                        </select>
                        <span class="form-error" id="productTypeError"></span>
                    </div>
                </div>

                <!-- Image & Keywords -->
                <div class="form-section">
                    <h3 class="form-section-title">Image & Keywords</h3>

                    <div class="form-group">
                        <label>Product Image</label>
                        <div class="image-upload-area" id="imageUploadArea">
                            <svg class="image-upload-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                                <circle cx="8.5" cy="8.5" r="1.5"></circle>
                                <polyline points="21 15 16 10 5 21"></polyline>
                            </svg>
                            <div class="image-upload-text">
                                Drag and drop or <strong>click to select</strong> an image
                            </div>
                        </div>
                        <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;">
                        <div class="image-preview" id="imagePreview">
                            <img id="previewImg" src="" alt="Preview">
                            <br>
                            <button type="button" class="image-preview-remove" id="removeImageBtn">Remove Image</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="keywords">Keywords (comma-separated)</label>
                        <input type="text" id="keywords" name="keywords" placeholder="e.g., portrait, headshot, professional" value="<?php echo htmlspecialchars($product['keywords'] ?? ''); ?>">
                        <span class="form-hint">Help customers find your product</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <a href="<?php echo SITE_URL; ?>/vendor/manage_products.php" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit" id="submitBtn">Update Product</button>
                </div>
            </form>
        </div>
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('productForm');
            const titleInput = document.getElementById('title');
            const descriptionInput = document.getElementById('description');
            const categoryInput = document.getElementById('category');
            const priceInput = document.getElementById('price');
            const productTypeInput = document.getElementById('productType');
            const submitBtn = document.getElementById('submitBtn');
            const imageUploadArea = document.getElementById('imageUploadArea');
            const imageInput = document.getElementById('imageInput');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeImageBtn = document.getElementById('removeImageBtn');

            // Image upload handlers
            imageUploadArea.addEventListener('click', () => imageInput.click());
            imageUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                imageUploadArea.classList.add('drag-over');
            });
            imageUploadArea.addEventListener('dragleave', () => {
                imageUploadArea.classList.remove('drag-over');
            });
            imageUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                imageUploadArea.classList.remove('drag-over');
                if (e.dataTransfer.files.length > 0) {
                    imageInput.files = e.dataTransfer.files;
                    handleImageSelect();
                }
            });

            imageInput.addEventListener('change', handleImageSelect);
            removeImageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                imageInput.value = '';
                imagePreview.style.display = 'none';
            });

            // Form validation
            form.addEventListener('submit', handleProductSubmit);
            titleInput.addEventListener('blur', validateTitle);
            descriptionInput.addEventListener('blur', validateDescription);
            categoryInput.addEventListener('change', validateCategory);
            priceInput.addEventListener('blur', validatePrice);
            productTypeInput.addEventListener('change', validateProductType);

            function handleImageSelect() {
                const file = imageInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            }

            function validateTitle() {
                const value = titleInput.value.trim();
                const errorEl = document.getElementById('titleError');
                if (!value) {
                    errorEl.textContent = 'Title is required';
                    return false;
                }
                if (value.length < 3 || value.length > 255) {
                    errorEl.textContent = 'Title must be between 3 and 255 characters';
                    return false;
                }
                errorEl.textContent = '';
                return true;
            }

            function validateDescription() {
                const value = descriptionInput.value.trim();
                const errorEl = document.getElementById('descriptionError');
                if (!value) {
                    errorEl.textContent = 'Description is required';
                    return false;
                }
                if (value.length < 10 || value.length > 5000) {
                    errorEl.textContent = 'Description must be between 10 and 5000 characters';
                    return false;
                }
                errorEl.textContent = '';
                return true;
            }

            function validateCategory() {
                const value = categoryInput.value;
                const errorEl = document.getElementById('categoryError');
                if (!value) {
                    errorEl.textContent = 'Please select a category';
                    return false;
                }
                errorEl.textContent = '';
                return true;
            }

            function validatePrice() {
                const value = parseFloat(priceInput.value);
                const errorEl = document.getElementById('priceError');
                if (!value || isNaN(value)) {
                    errorEl.textContent = 'Price is required';
                    return false;
                }
                if (value <= 0) {
                    errorEl.textContent = 'Price must be greater than 0';
                    return false;
                }
                errorEl.textContent = '';
                return true;
            }

            function validateProductType() {
                const value = productTypeInput.value;
                const errorEl = document.getElementById('productTypeError');
                if (!value) {
                    errorEl.textContent = 'Please select a product type';
                    return false;
                }
                errorEl.textContent = '';
                return true;
            }

            function handleProductSubmit(e) {
                e.preventDefault();

                document.getElementById('successMessage').classList.remove('show');
                document.getElementById('errorMessage').classList.remove('show');

                // Validate all fields
                const titleValid = validateTitle();
                const descriptionValid = validateDescription();
                const categoryValid = validateCategory();
                const priceValid = validatePrice();
                const typeValid = validateProductType();

                if (!titleValid || !descriptionValid || !categoryValid || !priceValid || !typeValid) {
                    return;
                }

                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Updating...';

                const formData = new FormData();
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
                formData.append('product_id', document.querySelector('input[name="product_id"]').value);
                formData.append('cat_id', categoryInput.value);
                formData.append('title', titleInput.value.trim());
                formData.append('description', descriptionInput.value.trim());
                formData.append('price', priceInput.value);
                formData.append('product_type', productTypeInput.value);
                formData.append('keywords', document.getElementById('keywords').value.trim());

                fetch('../actions/update_product_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('Product updated successfully');
                        // Upload image if selected
                        if (imageInput.files.length > 0) {
                            uploadProductImage(document.querySelector('input[name="product_id"]').value);
                        } else {
                            setTimeout(() => {
                                window.location.href = '../vendor/manage_products.php';
                            }, 1500);
                        }
                    } else {
                        showError(data.message || 'Failed to update product');
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Network error. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                });
            }

            function uploadProductImage(productId) {
                const formData = new FormData();
                formData.append('csrf_token', document.querySelector('input[name="csrf_token"]').value);
                formData.append('product_id', productId);
                formData.append('image', imageInput.files[0]);

                fetch('../actions/upload_product_image_action.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showSuccess('Image updated successfully');
                    } else {
                        showError('Warning: Image update failed - ' + (data.message || 'Unknown error'));
                        console.error('Image upload error:', data);
                    }
                    setTimeout(() => {
                        window.location.href = '../vendor/manage_products.php';
                    }, 1500);
                })
                .catch((error) => {
                    console.error('Image upload network error:', error);
                    setTimeout(() => {
                        window.location.href = '../vendor/manage_products.php';
                    }, 1500);
                });
            }

            function showSuccess(message) {
                const msg = document.getElementById('successMessage');
                document.getElementById('successText').textContent = message;
                msg.classList.add('show');
            }

            function showError(message) {
                const msg = document.getElementById('errorMessage');
                document.getElementById('errorText').textContent = message;
                msg.classList.add('show');
            }
        });
    </script>
</body>
</html>
